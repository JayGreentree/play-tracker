<?php

namespace mijingo\playtracker\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210414_183421_add_total_running_time migration.
 */
class m210414_183421_add_total_running_time extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%playtracker_playtrackerrecord}}',
            'totalRunningTime',
            $this->integer(255)->notNull()->defaultvalue(0)
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210414_183421_add_total_running_time cannot be reverted.\n";
        return false;
    }
}
