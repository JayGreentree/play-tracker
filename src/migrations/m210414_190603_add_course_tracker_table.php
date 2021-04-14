<?php

namespace mijingo\playtracker\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210414_190603_add_course_tracker_table migration.
 */
class m210414_190603_add_course_tracker_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%playtracker_courseprogressrecord}}',
            [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                // Custom columns in the table
                'userId' => $this->integer()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'courseEntryId' => $this->integer()->notNull(),
                'status' => $this->integer()->notNull()->defaultValue(0),
                'totalRunningTime' => $this->integer()->notNull(),
                'completedRunningTime' => $this->integer()->notNull()->defaultValue(0),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210414_190603_add_course_tracker_table cannot be reverted.\n";
        return false;
    }
}
