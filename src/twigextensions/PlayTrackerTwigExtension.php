<?php
/**
 * Play Tracker plugin for Craft CMS 3.x
 *
 * Tracks plays of videos.
 *
 * @link      https://mijingo.com
 * @copyright Copyright (c) 2018 Ryan Irelan
 */

namespace mijingo\playtracker\twigextensions;

use mijingo\playtracker\PlayTracker;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Ryan Irelan
 * @package   PlayTracker
 * @since     1.0.0
 */
class PlayTrackerTwigExtension extends \Twig\Extension\AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'PlayTracker';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig\TwigFilter('someFilter', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('hasPlayed', [$this, 'hasPlayed']),
        ];
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @param null $text
     *
     * @return string
     */
    public function hasPlayed($text = null)
    {
        $result = $text . " in the way";

        return $result;
    }
}
