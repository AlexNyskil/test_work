<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "callback_form".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $text
 * @property string $phone
 */
class ContactForm extends \yii\db\ActiveRecord
{
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'callback_form';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email', 'text'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            [['phone', 'text', 'name'], 'string'],
            ['phone', 'match', 'pattern' => '/^(8)[(](\d{3})[)](\d{3})[-](\d{2})[-](\d{2})/', 'message' => 'Телефона, должно быть в формате 8(XXX)XXX-XX-XX'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
            'email' => 'email',
            'phone' => 'phone',
            'name' => 'name',
            'text' => 'text'
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$this->email => $this->name])
            ->setSubject($this->name)
            ->setTextBody($this->text)
            ->send();
    }
}
