<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsApplicationDocumentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cs-application-document-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'application_doc_id') ?>

    <?= $form->field($model, 'application_id') ?>

    <?= $form->field($model, 'document_location') ?>

    <?= $form->field($model, 'document_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
