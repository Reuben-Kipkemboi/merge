<?php

use app\modules\setup\models\CsRequiredDocument;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsApplicationDocument $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cs-application-document-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'application_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= $form->field($model, 'file')->fileInput(['class' => 'form-control'])->label('Choose File', ['class' => 'fw-bold']) ?>
        <?= $form->field($model, 'document_location')->hiddenInput()->label(false) ?>
    </div>

    <div class="row mb-2">
        <div class="col-md-12">
            <?= $form->field($model, 'document_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(
                    CsRequiredDocument::find()->all(),
                    'document_id',
                    'document_name'
                ),
                [
                    'prompt' => 'Select Document',
                    'class' => 'form-control',
                ]
            )->label('Select Document', ['class' => 'fw-bold']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save Attached Document and Forward for Approval', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
