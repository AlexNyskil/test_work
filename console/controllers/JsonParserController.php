<?php

namespace console\controllers;

use backend\models\Setting;
use common\models\Author;
use common\models\Book;
use common\models\Tree;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\ArrayHelper;

/**
 * Class JsonParserController
 * @package console\controllers
 */
class JsonParserController extends Controller
{
    protected $defaultCategory;
    protected $authors = [];
    protected $statusOk = 200;

    /**
     * JsonParserController constructor.
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        $defaultCategory = 'New items';
        $this->defaultCategory = Tree::find()->where(['name' => $defaultCategory])->one();

        if (!$this->defaultCategory) {
            $this->defaultCategory = new Tree();
            $this->defaultCategory->name = $defaultCategory;
            $this->defaultCategory->lvl = 0;
            $this->defaultCategory->lft = 1;
            $this->defaultCategory->rgt = 1;
            $this->defaultCategory->makeRoot();
        }

        parent::__construct($id, $module, $config);
    }

    /**
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $client = new \GuzzleHttp\Client();
        $resource = Setting::find()->where(['key' => 'source'])->one();

        if (!$resource) {
            echo "A problem occurred!\n";
            return ExitCode::CONFIG;
        }

        $response = $client->request('GET', $resource->value);

        if ($response->getStatusCode() != $this->statusOk) {
            echo "A problem occurred!\n";
            return ExitCode::UNAVAILABLE;
        }

        $books = Json::decode($response->getBody(), true);
        $columns = array_keys(Book::getTableSchema()->columns);
        Console::startProgress(0, count($books));
        $countProgress = 0;

        foreach ($books as $book) {
            if (ArrayHelper::keyExists('isbn', $book)) {
                $builderBook = Book::find()->where(['isbn' => $book['isbn']]);
                $isBookExist = $builderBook->exists();

                if (!$isBookExist) {
                    $classBook = new Book();
                } else {
                    $classBook = $builderBook->one();
                }

                foreach ($book as $key => $attribute) {
                    $attr = strtolower(preg_replace('/([A-Z])/', '_${1}', $key));

                    if (ArrayHelper::isIn($attr, $columns)) {
                        $classBook->$attr = $attribute;
                    }

                    if (is_array($attribute)) {
                        $functionName = 'set' . ucfirst($key);
                        $this->$functionName($attribute, $classBook);
                    }
                }

                $this->setStatus($classBook);
                $this->uploadImage($classBook);
                $classBook->save();
                $this->bookLinkAuthor($classBook);
                $countProgress++;
                Console::updateProgress($countProgress, count($books));
            }
        }

        Console::endProgress('end' . PHP_EOL);

        return parent::EXIT_CODE_NORMAL;
    }

    /**
     * @param array $data
     * @param Book $book
     */
    private function setPublishedDate(array $data, Book $book)
    {
        if (array_key_exists('$date', $data)) {
            $time = strtotime($data['$date']);
            $convertTime = date("Y-m-d H:i:s", $time);
        }

        $book->published_date = $convertTime ?? null;
    }

    /**
     * @param Book $book
     */
    private function setStatus(Book $book)
    {
        $book->status = array_flip(Book::getStatuses())[$book->status];
    }

    /**
     * @param array $authors
     * @param Book $book
     */
    private function setAuthors(array $authors, Book $book)
    {
        foreach ($authors as $author) {
            if ($author) {
                $builderAuthorQuery = Author::find()->where(['name' => $author]);
                $isAuthorExist = $builderAuthorQuery->exists();

                if (!$isAuthorExist) {
                    $this->authors[$author] = new Author();
                    $this->authors[$author]->name = $author;
                    $this->authors[$author]->save();
                } else {
                    $this->authors[$author] = $builderAuthorQuery->one();
                }
            }
        }
    }

    /**
     * @param array $categories
     * @param Book $book
     */
    private function setCategories(array $categories, Book $book)
    {
        if (!empty($categories)) {
            $category_id = $this->recursiveCreate($categories);
        }

        $book->category_id = $category_id ?? $this->defaultCategory->id;
    }

    /**
     * @param Book $book
     * @throws \yii\base\InvalidConfigException
     */
    private function bookLinkAuthor(Book $book)
    {
        foreach ($this->authors as $key => $author) {
            $linkBuilder = $book->getAuthors()->where(['name' => $author->name]);
            $isLinkExist = $linkBuilder->exists();

            if (!$isLinkExist) {
                $book->link('authors', $author);
            }

            unset($this->authors[$key]);
        }
    }

    /**
     * @param Book $book
     * @throws \yii\base\Exception
     */
    private function uploadImage(Book $book)
    {
        if ($book->thumbnail_url) {
            $names = explode('/', $book->thumbnail_url);
            $nameFile = array_pop($names);
            $relativePath = '/uploads/' . $book->id . '/';
            $pathDirectory = \Yii::getAlias('@frontend') . '/web/' . $relativePath;

            FileHelper::createDirectory($pathDirectory);
            $path = $pathDirectory . $nameFile;

            $file_path = fopen($path, 'w');

            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->get($book->thumbnail_url, ['sink' => $file_path]);

                if ($response->getStatusCode() == $this->statusOk) {
                    $book->url = $relativePath . $nameFile;
                }
            } catch (\Throwable $e) {
                echo "image not found!\n";
            }
        }
    }

    /**
     * @param array $categories
     * @param bool $root
     * @param int $lvl
     * @return int
     */
    private function recursiveCreate(array $categories, $root = false, $lvl = 0)
    {
        $treeName = array_pop($categories);

        if (!$root) {
            $treeBuilder = Tree::find()->roots()->andWhere(['name' => $treeName]);
        } else {
            $parent = Tree::find()->where(['id' => $root])->one();
            $treeBuilder = $parent->children()->andWhere(['name' => $treeName]);
        }

        $isTreeExist = $treeBuilder->exists();

        if ($isTreeExist) {
            $tree = $treeBuilder->one();
        } else {
            $tree = new Tree();
            $tree->lvl = $lvl;
            $tree->lft = 1;
            $tree->rgt = 1;
            $tree->name = $treeName;

            if (!$root) {
                $tree->makeRoot();
            } else {
                $tree->appendTo($parent);
            }
        }

        $lvl++;

        if (empty($categories)) {
            return $tree->id;
        } else {
            return $this->recursiveCreate($categories, $tree->id, $lvl);
        }
    }
}