<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $authors */
/* @var $selectedAuthors */
/* @var $categories */

$this->title = 'Create Book';
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-create">

    <?= $this->render('_form', [
        'model' => $model,
        'authors' => $authors,
        'selectedAuthors' => $selectedAuthors,
        'categories' => $categories,
    ]) ?>

</div>
