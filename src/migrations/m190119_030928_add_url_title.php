<?php

namespace mijingo\playtracker\migrations;

use Craft;
use craft\db\Migration;

/**
 * m190119_030928_add_url_title migration.
 */
class m190119_030928_add_url_title extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%playtracker_playtrackerrecord}}',
            'courseUrlTitle',
            $this->string(256)->notNull()->defaultValue('')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190119_030928_add_url_title cannot be reverted.\n";
        return false;
    }
}
