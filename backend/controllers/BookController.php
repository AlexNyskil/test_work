<?php

namespace backend\controllers;

use common\models\Author;
use common\models\Tree;
use Yii;
use common\models\Book;
use common\models\BookSearch;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {

            $image = $model->uploadImage();

            if ($model->save()) {
                $authors = Yii::$app->request->post()['Book']['authors'];
                $model->linkAuthors($authors);

                if ($image !== false) {
                    $path = $model->getImageFile();
                    $model->createDirectoryImage($model->getDirectoryImageFile());
                    $image->saveAs($path);
                    $model->url = $model->getRelativeFilePath();
                    $model->update();
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => ArrayHelper::map(Author::find()->all(), 'id', 'name'),
            'categories' => ArrayHelper::map(Tree::find()->where(['active' => 1])->all(), 'id', 'name'),
            'selectedAuthors' => [],
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $image = $model->uploadImage();

            if ($model->save())
            {
                if ($image !== false && unlink($model->getAbsolutFilePath())) {
                    $path = $model->getImageFile();
                    $model->createDirectoryImage($model->getDirectoryImageFile());
                    $image->saveAs($path);
                    $model->url = $model->getRelativeFilePath();
                    $model->update();
                }

                $authors = Yii::$app->request->post()['Book']['authors'];
                $model->linkAuthors($authors);
                $model->unlinkAuthors($authors);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => ArrayHelper::map(Author::find()->all(), 'id', 'name'),
            'categories' => ArrayHelper::map(Tree::find()->where(['active' => 1])->all(), 'id', 'name'),
            'selectedAuthors' => ArrayHelper::getColumn($model->getAuthors()->all(), 'id'),
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param null $q
     * @param null $id
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSearchAuthors($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, name AS text')
                ->from('authors')
                ->where(['like', 'name', '%' . $q . '%', false])
                ->limit(20);

            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Author::find($id)->name];
        }

        return $out;
    }

    public function actionFileUpload()
    {

    }
}
