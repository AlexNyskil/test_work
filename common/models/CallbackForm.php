<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "callback-form".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $text
 * @property string $phone
 */
class CallbackForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'callback_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'text'], 'required'],
            [['email', 'name', 'text', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Name',
            'text' => 'Text',
            'phone' => 'Phone',
        ];
    }
}
