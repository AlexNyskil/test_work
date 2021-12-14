<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CallbackForm */

$this->title = 'Update Callback Form: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Callback Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="callback-form-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
