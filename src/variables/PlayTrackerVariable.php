<?php
/**
 * Play Tracker plugin for Craft CMS 3.x
 *
 * Tracks plays of videos.
 *
 * @link      https://mijingo.com
 * @copyright Copyright (c) 2018 Ryan Irelan
 */

namespace mijingo\playtracker\variables;

use mijingo\playtracker\PlayTracker;

use Craft;
use mijingo\playtracker\twigextensions\PlayTrackerTwigExtension;
use nystudio107\seomatic\models\jsonld\Play;

/**
 * Play Tracker Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.playTracker }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Ryan Irelan
 * @package   PlayTracker
 * @since     1.0.0
 */
class PlayTrackerVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.playTracker.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.playTracker.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function hasCompleted($playdata)
    {
        $result = PlayTracker::$plugin->playTrackerService->hasCompleted($playdata);
        return $result;
    }

    public function getPlayedVideos($entryId) {
        $result = PlayTracker::$plugin->playTrackerService->getPlayedVideos($entryId);
        return $result;
    }

    public function getInProgressCourseVideosByEntryId($entryId, $userId) {
        $result = PlayTracker::$plugin->playTrackerService->getInProgressCourseVideosByEntryId($entryId, $userId);
        return $result;
    }

    public function getInProgressCourseVideos($userId) {
        $result = PlayTracker::$plugin->playTrackerService->getInProgressCourseVideos($userId);
        return $result;
    }

    public function getInProgressVideos($userId) {
        $result = PlayTracker::$plugin->playTrackerService->getInProgressVideos($userId);
        return $result;
    }

    public function getInProgressCourses($userId, $limit) {
        return PlayTracker::$plugin->playTrackerService->getInProgressCourses($userId, $limit);
    }


    /**
     * @param $courseId
     * @param $userId
     */
    public function courseCompletionStatus($courseId, $userId)
    {
        return PlayTracker::$plugin->playTrackerService->getCourseCompletionStatus($courseId, $userId);
    }

    /**
     * Gets Current Timestamp
     *
     * @param $platdata
     * @return float
     */
    public function currentTimestamp($playdata) {
        $result = PlayTracker::$plugin->playTrackerService->getCurrentTimestamp($playdata);
        return $result;
    }
}
