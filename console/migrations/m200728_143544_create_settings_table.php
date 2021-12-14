<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m200728_143544_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(),
            'value' => $this->string(),
            'description' => $this->string(),
        ]);

        $this->insert('{{%settings}}', ['key' => 'dimension', 'description' => 'Кол-во элементов на страницу', 'value' => '30']);

        $this->insert('{{%settings}}', ['key' => 'cf_email', 'description' => 'Email адрес получателя сообщения с формы обратной связи', 'value' => 'alextomson93@mail.ru']);

        $this->insert('{{%settings}}', ['key' => 'source', 'description' => 'Источник данных для парсинга', 'value' => 'https://gitlab.com/prog-positron/test-app-vacancy/-/raw/master/books.json']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
