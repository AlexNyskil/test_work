<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $authors */
/* @var $selectedAuthors */
/* @var $categories */

$this->title = 'Update Book: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="book-update">

    <?= $this->render('_form', [
        'model' => $model,
        'authors' => $authors,
        'selectedAuthors' => $selectedAuthors,
        'categories' => $categories,
    ]) ?>

</div>
