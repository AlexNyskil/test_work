<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property string $isbn
 * @property int $page_count
 * @property string $published_date
 * @property string $thumbnail_url
 * @property string $short_description
 * @property string $long_description
 * @property int $status
 * @property string $url
 * @property int $category_id
 */
class Book extends \yii\db\ActiveRecord
{
    const PUBLISH = 'PUBLISH';
    const MEAP = 'MEAP';

    public $authors;
    public $image;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_count', 'status', 'category_id'], 'integer'],
            [['image'], 'file', 'extensions' => 'png, jpg'],
            [['image'], 'file', 'maxSize' => '20000'],
            [['published_date'], 'safe'],
            [['long_description', 'short_description'], 'string'],
            [['title', 'thumbnail_url', 'url'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 50],
            [['isbn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'isbn' => 'Isbn',
            'page_count' => 'Page Count',
            'published_date' => 'Published Date',
            'thumbnail_url' => 'Thumbnail Url',
            'short_description' => 'Short Description',
            'long_description' => 'Long Description',
            'status' => 'Status',
            'url' => 'Url',
            'category_id' => 'Category'
        ];
    }

    public static function getStatuses()
    {
        return [
            self::MEAP,
            self::PUBLISH,
        ];
    }

    public function getBookStatus()
    {
        return self::getStatuses()[$this->status];
    }

    /**
     * @param array $newAuthors
     * @throws \yii\base\InvalidConfigException
     */
    public function linkAuthors(array $newAuthors)
    {
        $pastAuthorIds = ArrayHelper::getColumn($this->getAuthors()->all(), 'id');

        foreach ($newAuthors as $authorId) {
            if (!ArrayHelper::isIn($authorId, $pastAuthorIds)) {
                $author = Author::find()->where(['id' => $authorId])->one();
                $this->link('authors', $author);
            }
        }
    }

    /**
     * @param array $newAuthors
     * @throws \yii\base\InvalidConfigException
     */
    public function unlinkAuthors(array $newAuthors)
    {
        $pastAuthorIds = ArrayHelper::getColumn($this->getAuthors()->all(), 'id');

        foreach ($pastAuthorIds as $authorId) {
            if (!ArrayHelper::isIn($authorId, $newAuthors)) {
                $author = Author::find()->where(['id' => $authorId])->one();
                $this->unlink('authors', $author);
            }
        }
    }

    /**
     * @return bool|UploadedFile|null
     * @throws \yii\base\Exception
     */
    public function uploadImage()
    {
        $image = UploadedFile::getInstance($this, 'image');

        if (empty($image)) {
            return false;
        }

        $partOfName = explode(".", $image->name);
        $ext = end($partOfName);
        $this->image = Yii::$app->security->generateRandomString().".{$ext}";

        return $image;
    }

    /**
     * @return string|null
     */
    public function getImageFile()
    {
        return isset($this->image) ?  $this->getDirectoryImageFile() . $this->image : null;
    }

    /**
     * @param $pathDirectory
     * @throws \yii\base\Exception
     */
    public function createDirectoryImage($pathDirectory)
    {
        FileHelper::createDirectory($pathDirectory);
    }

    /**
     * @return string
     */
    public function getDirectoryImageFile()
    {
        return Yii::getAlias(Yii::$app->params['uploadPath'] . $this->id . '/');
    }

    public function getRelativeFilePath()
    {
        return '/uploads/' . $this->id . '/' . $this->image;
    }

    public function getAbsolutFilePath()
    {
        return Yii::getAlias('@frontend' .'/web' . $this->url);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::classname(), ['id' => 'author_id'])
            ->viaTable('authors_books', ['book_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Tree::className(), ['id' => 'category_id']);
    }
}
