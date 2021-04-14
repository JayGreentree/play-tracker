<?php
/**
 * Play Tracker plugin for Craft CMS 3.x
 *
 * Tracks plays of videos.
 *
 * @link      https://mijingo.com
 * @copyright Copyright (c) 2018 Ryan Irelan
 */

namespace mijingo\playtracker\records;

use mijingo\playtracker\PlayTracker;

use Craft;
use craft\db\ActiveRecord;
use nystudio107\seomatic\models\jsonld\ReceiveAction;

/**
 * PlayTrackerRecord Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Ryan Irelan
 * @package   PlayTracker
 * @since     1.0.0
 */

class CourseProgressRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%playtracker_courseprogressrecord}}';
    }
}