<?php
/**
 * Play Tracker plugin for Craft CMS 3.x
 *
 * Tracks plays of videos.
 *
 * @link      https://mijingo.com
 * @copyright Copyright (c) 2018 Ryan Irelan
 */

namespace mijingo\playtracker\services;

use mijingo\playtracker\PlayTracker;

use Craft;
use craft\base\Component;
use craft\db\Query;


/**
 * PlayTrackerService Service
 *
 * @author    Ryan Irelan
 * @package   PlayTracker
 * @since     1.0.0
 */

class PlayTrackerService extends Component
{
    // Public Methods
    // =========================================================================


    /**
     * @param $playData
     * @return bool
     */
    public function hasStarted($playData) // checks if this video has been started but not completed status=0
    {
        $count = (new Query())
            ->select(['entryId', 'rowId', 'userId', 'siteId', 'status'])
            ->from(['{{%playtracker_playtrackerrecord}}'])
            ->where(['entryId' => $playData['entryId'], 'status' => 0, 'userId' => craft::$app->user->getId(), 'rowId' => $playData['rowId']])
            ->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $playData
     * @return bool
     */
    public function hasCompleted($playData) // checks if the video is completed - the player `ended` event fired
    {
        $count = (new Query())
            ->select(['entryId', 'rowId', 'userId', 'siteId', 'status'])
            ->from(['{{%playtracker_playtrackerrecord}}'])
            ->where(['entryId' => $playData['entryId'], 'status' => 1, 'userId' => craft::$app->user->getId(), 'rowId' => $playData['rowId']])
            ->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $entryId
     * @return array
     */
    public function getPlayedVideos($entryId)
    {
        $videosInCourse = (new Query())
            ->select(['rowId', 'entryId', 'userId', 'siteId', 'status'])
            ->from (['{{%playtracker_playtrackerrecord}}'])
            ->where(['entryId' => $entryId, 'status' => 1, 'userId' => craft::$app->user->getId()])
            ->all();

        return $videosInCourse;
    }


    /**
     * @param $userId
     * @return array
     */
    public function getInProgressVideos($userId) {

        $inProgressVideos = (new Query())
            ->select(['entryId'])
            ->from (['{{%playtracker_playtrackerrecord}}'])
            ->where(['status' => 0, 'userId' => $userId])
            ->all();

        return $inProgressVideos;

    }

    /**
     * @param $userId
     * @return array
     */
    public function getInProgressCourseVideos($userId) {

        $inProgressVideos = (new Query())
            ->select(['{{%playtracker_playtrackerrecord}}.rowId, {{%playtracker_playtrackerrecord}}.courseUrlTitle, {{%matrixcontent_coursevideos}}.field_video_videoTitle, {{%matrixcontent_coursevideos}}.elementId, {{%playtracker_playtrackerrecord}}.userId' ])
            ->distinct()
            ->from (['{{%playtracker_playtrackerrecord}}'])
            ->join('LEFT JOIN', '{{%matrixcontent_coursevideos}}', '{{%playtracker_playtrackerrecord}}.rowId = {{%matrixcontent_coursevideos}}.elementId')
            ->where(['{{%playtracker_playtrackerrecord}}.status' => 0])
            ->andWhere('{{%playtracker_playtrackerrecord}}.rowId > 0')
            ->andWhere(['{{%playtracker_playtrackerrecord}}.userId' => $userId])
            ->all();
        return $inProgressVideos;

    }


    public function getInProgressCourseVideosByEntryId($entryId, $userId) {
        $inProgressVideos = (new Query())
            ->select(['{{%playtracker_playtrackerrecord}}.rowId, {{%playtracker_playtrackerrecord}}.courseUrlTitle, {{%matrixcontent_coursevideos}}.field_video_videoTitle, {{%matrixcontent_coursevideos}}.elementId, {{%playtracker_playtrackerrecord}}.userId, {{%playtracker_playtrackerrecord}}.entryId' ])
            ->distinct()
            ->from (['{{%playtracker_playtrackerrecord}}'])
            ->join('LEFT JOIN', '{{%matrixcontent_coursevideos}}', '{{%playtracker_playtrackerrecord}}.rowId = {{%matrixcontent_coursevideos}}.elementId')
            ->where(['{{%playtracker_playtrackerrecord}}.status' => 0])
            ->andWhere('{{%playtracker_playtrackerrecord}}.rowId > 0')
            ->andWhere(['{{%playtracker_playtrackerrecord}}.userId' => $userId])
            ->andWhere(['{{%playtracker_playtrackerrecord}}.entryId' => $entryId])
            ->all();
        return $inProgressVideos;
    }

    /**
     * @param $playData
     * @return bool
     */
    public function savePlay($playData)
    {
        // only save play data if the video isn't yet complete in order to avoid a out of order XHR request that resets the played status to 0 after setting it to 1
        if($playData['status'] != 1)
        {
            $result = \Craft::$app->db->createCommand()
            ->insert('{{%playtracker_playtrackerrecord}}', $playData)
            ->execute();

        return true;
        }

        return false;

    }


    /**
     * @param $playData
     * @return bool
     */
    public function updatePlay($playData)
    {
        $result = \Craft::$app->db->createCommand()
            ->update('{{%playtracker_playtrackerrecord}}', $playData, array('entryId' => $playData['entryId'], 'rowId' => $playData['rowId'], 'userId' => craft::$app->user->getId()))
            ->execute();
        return true;

    }


    /**
     * @param $platData
     * @return string
     */
    public function getCurrentTimestamp($playData) {
        $timestamp = (new Query())
            ->select(['currentTimestamp'])
            ->from(['{{%playtracker_playtrackerrecord}}'])
            ->where(['entryId' => $playData['entryId'], 'status' => 0, 'userId' => craft::$app->user->getId(), 'rowId' => $playData['rowId']])
            ->all();

        if($timestamp)
        {
            return $timestamp[0]['currentTimestamp'];
        }

        return false;

    }
}
