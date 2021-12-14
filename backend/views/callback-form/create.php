<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CallbackForm */

$this->title = 'Create Callback Form';
$this->params['breadcrumbs'][] = ['label' => 'Callback Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="callback-form-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
