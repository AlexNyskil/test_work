<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%callback-form}}`.
 */
class m200728_143810_create_callback_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%callback_form}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull(),
            'name' => $this->string(),
            'text' => $this->string()->notNull(),
            'phone' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%callback_form}}');
    }
}
