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
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
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
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     PlayTracker::$plugin->playTrackerService->exampleService()
     *
     * @return mixed
     */
    public function hasPlayed($playData)
    {
        $count = (new Query())
            ->select(['entryId', 'rowId', 'userId', 'siteId', 'status'])
            ->from(['{{%playtracker_playtrackerrecord}}'])
            ->where($playData)
            ->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    public function getPlayedVideos($entryId)
    {
        $videosInCourse = (new Query())
            ->select(['rowId', 'entryId', 'userId', 'siteId', 'status'])
            ->from (['{{%playtracker_playtrackerrecord}}'])
            ->where(['entryId' => $entryId, 'status' => 1, 'userId' => craft::$app->user->getId()])
            ->all();

        return $videosInCourse;
    }

    public function savePlay($playData)
    {
        $result = \Craft::$app->db->createCommand()
            ->insert('{{%playtracker_playtrackerrecord}}', $playData)
            ->execute();

        return true;

    }

}
