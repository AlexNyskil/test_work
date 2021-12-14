<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $form yii\widgets\ActiveForm */
/* @var $authors */
/* @var $selectedAuthors */
/* @var $categories */
$initialPreview = $model->url ? [Url::to($model->url)] : [];
$model->authors = $selectedAuthors;
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'page_count')->textInput() ?>

    <?php echo $form->field($model, 'image')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'initialPreview' => $initialPreview,
            'initialPreviewAsData' => true,
            'overwriteInitial' => false,
            'maxFileSize' => 20000,
            'maxFileCount' => 1,
        ]
    ]); ?>
    <?php echo $form->field($model, 'authors')->widget(Select2::classname(), [
        'data' => $authors,
        'options' => ['placeholder' => 'Select a authors ...', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => false,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => \yii\helpers\Url::to(['search-authors']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'templateResult' => new JsExpression('function(author) { return author.text; }'),
            'templateSelection' => new JsExpression('function (author) { return author.text; }'),
        ],
    ])->label('Authors');
    ?>

    <?= $form->field($model, 'published_date')->textInput() ?>

    <?= $form->field($model, 'thumbnail_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'short_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'long_description')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'status')->widget(Select2::classname(), [
        'data' => $model::getStatuses(),
        'options' => ['placeholder' => 'Select a status ...'],
        'value' => $model::getStatuses()[$model->status ?? 0],
    ])->label('Status');
    ?>

    <?php echo $form->field($model, 'category_id')->widget(Select2::classname(), [
        'data' => $categories,
        'options' => ['placeholder' => 'Select a category ...'],
        'value' => $model->category_id,
    ])->label('Category');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
