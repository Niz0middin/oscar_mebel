<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m200517_062536_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull(),
            'tree'       => $this->integer()->notNull(),
            'lft'        => $this->integer()->notNull(),
            'rgt'        => $this->integer()->notNull(),
            'depth'      => $this->integer()->notNull(),
            'position'   => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}
