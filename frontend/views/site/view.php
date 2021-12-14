<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Book */

$this->title = $model->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <?=Html::a('Return', yii\helpers\Url::previous(), ['class' => 'btn btn-primary'])?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'isbn',
            'page_count',
            'published_date',
            'thumbnail_url:url',
            'short_description',
            'long_description:ntext',
            'bookStatus',
            [
                'attribute' => 'image',
                'value' => Url::to($model->url, false),
                'format' => ['image', ['width'=>'100','height'=>'100']],
            ]
        ],
    ]) ?>

</div>
