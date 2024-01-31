<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplicationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cs-carsticker-application-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'application_id') ?>

    <?= $form->field($model, 'application_ref_no') ?>

    <?= $form->field($model, 'vehicle_regno') ?>

    <?= $form->field($model, 'application_date') ?>

    <?= $form->field($model, 'application_type') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
