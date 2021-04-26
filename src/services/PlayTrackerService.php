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

use craft\elements\db\EntryQuery;
use mijingo\playtracker\PlayTracker;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\elements\Entry;


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

    public function getTotalPlayedVideos($userId)
    {
        return (new Query())
            ->from (['{{%playtracker_playtrackerrecord}}'])
            ->where(['status' => 1, 'userId' => $userId])
            ->count();
    }

    /**
     * @param $courseId
     * @return float
     */
    public function getCourseCompletionStatus($courseId) {

        $playedVideoCount = count($this->getPlayedVideos($courseId));
        $totalCourseVideos = $this->getTotalCourseVideos($courseId);
        return round(($playedVideoCount / $totalCourseVideos) * 100);
    }


    public function getTotalCourseVideos($courseId): int
    {
        $entry = Entry::find()->section('courses')->id($courseId)->one();
        return count($entry->courseVideos->all());

    }

    public function totalCourseVideosByCategory($categoryId): int
    {
        $courses = Entry::find()->section('courses')->relatedTo($categoryId)->all();
        $totalCourseVideos = 0;
        foreach ($courses as $course) {
            $videosCount = count($course->courseVideos->all());
            $totalCourseVideos = $totalCourseVideos + $videosCount;
        }
        return $totalCourseVideos;
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

    public function getInProgressCourses($userId, $limit)
    {
        $inProgressCourses = (new Query())
            ->select(['{{%playtracker_playtrackerrecord}}.entryId'])
            ->distinct()
            ->from(['{{%playtracker_playtrackerrecord}}'])
            ->where(['{{%playtracker_playtrackerrecord}}.userId' => $userId])
            ->andWhere('{{%playtracker_playtrackerrecord}}.rowId != 0')
            ->limit($limit)
            ->all();

        $entryIds = [];

        foreach ($inProgressCourses as $course) {
            $entryIds[] = $course['entryId'];
        }

        $entries = implode(", ", $entryIds);

        return Entry::find()->section('courses')->id($entryIds)->all();
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


    /**
     * @return int
     */
    public function getTotalCatalogVideos(): int
    {
        // get total all courses entries
        $courses = Entry::find()->section('courses')->status('live');
        $totalCourseVideos = 0;
        foreach ($courses as $course) {
            $videosCount = count($course->courseVideos->all());
            $totalCourseVideos = $totalCourseVideos + $videosCount;
        }

        $totalLessonVideos = Entry::find()->section('lessons')->status('live')->count();
        $totalLivestreamVideos = Entry::find()->section('livestreams')->status('live')->count();

        return $totalCourseVideos + $totalLessonVideos + $totalLivestreamVideos;
    }

    public function getTotalCatalogRunningTime(array $sections)
    {


        $totalCatalogRunningTime = 0;

        foreach ($sections as $section) {
            $entries = Entry::find()->section($section)->status('live');
            $runningTime = 0;

            if ($section == 'courses') {
                foreach ($entries as $entry) {
                    foreach ($entry->courseVideos->all() as $video)
                    {
                        $runningTimeSeconds = $this->_calcRunningTimeInSeconds($video->videoDuration);
                        $runningTime = $runningTime + $runningTimeSeconds;
                    }
                }
            } else {
                foreach ($entries as $entry) {
                    $runningTimeSeconds = $this->_calcRunningTimeInSeconds($entry->videoLength);
                    $runningTime = $runningTime + $runningTimeSeconds;
                }
            }
        }

        return floor($runningTime / 600);
    }


    /**
     * @param $time
     * @return mixed
     */
    private function _calcRunningTimeInSeconds($time)
    {
        sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        return $time_seconds;
    }
}
