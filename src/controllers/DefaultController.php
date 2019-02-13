<?php
/**
 * Play Tracker plugin for Craft CMS 3.x
 *
 * Tracks plays of videos.
 *
 * @link      https://mijingo.com
 * @copyright Copyright (c) 2018 Ryan Irelan
 */

namespace mijingo\playtracker\controllers;

use mijingo\playtracker\PlayTracker;

use Craft;
use craft\web\Controller;
use mijingo\playtracker\twigextensions\PlayTrackerTwigExtension;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Ryan Irelan
 * @package   PlayTracker
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['save'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/play-tracker/default/do-something
     *
     * @return mixed
     */
    public function actionSave()
    {

        // check that it's a logged in user session

        // get current user data
        $currentUserId = craft::$app->user->getId();
        // get data
        $params =  craft::$app->request->getQueryParams();
        $save_data = array(
            'userId' => $currentUserId,
            'entryId' => $params['entryId'],
            'rowId' => $params['rowId'],
            'status' => $params['status'],
            'siteId' => $params['siteId'],
            'currentTimestamp' => $params['currentTimestamp'],
            'courseUrlTitle' => isset($params['courseUrlTitle'])
        );

        $hasStarted = PlayTracker::$plugin->playTrackerService->hasStarted($save_data);
        $hasCompleted = PlayTracker::$plugin->playTrackerService->hasCompleted($save_data);

        if ($hasStarted && !$hasCompleted) {
            return PlayTracker::$plugin->playTrackerService->updatePlay($save_data);
        }
        elseif (!$hasStarted && !$hasCompleted) {
            return PlayTracker::$plugin->playTrackerService->savePlay($save_data);
        }
        else {
            return false;
        }
    }
}
