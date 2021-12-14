<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m200728_123335_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'isbn' => $this->string(50)->unique(),
            'page_count' => $this->integer(),
            'published_date' => $this->dateTime()->null(),
            'thumbnail_url' => $this->string(),
            'url' => $this->string()->null(),
            'short_description' => $this->text()->null(),
            'long_description' => $this->text()->null(),
            'status' => $this->tinyInteger(),
            'category_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
