<?php

use execut\widget\TreeView;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel */

$this->title = 'My Yii Application';
$onSelect = new JsExpression(<<<JS
function (undefined, item) {
    console.log(item.id);
    $.pjax.reload({container:"#books", url: "/site/index", data: {category_id:item.id}});
}
JS
);

?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-sm-4">
                <?php
                $groupsContent = TreeView::widget([
                    'data' => $data,
                    'size' => TreeView::SIZE_SMALL,
                    'header' => 'Categories',
                    'searchOptions' => [
                        'inputOptions' => [
                            'placeholder' => 'Search category...'
                        ],
                        'clearButtonOptions' => [
                            'title' => 'Clear',
                        ],
                    ],
                    'clientOptions' => [
                        'onNodeSelected' => $onSelect,
                        'selectedBackColor' => 'rgb(40, 153, 57)',
                        'borderColor' => '#fff',
                    ],
                ]);


                echo $groupsContent;

                ?>
            </div>
            <div class="col-sm-8">
                <?php Pjax::begin(['id' => 'books']); ?>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [

                        'id',
                        'title',
                        'isbn',
                        'page_count',
                        'published_date',
                        //'thumbnail_url:url',
                        //'short_description',
                        //'long_description:ntext',
                        //'status',

                        ['class' => 'yii\grid\ActionColumn', 'template' => '{view}',],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>

    </div>
</div>
