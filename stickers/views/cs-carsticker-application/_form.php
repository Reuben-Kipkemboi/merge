<?php

use app\modules\setup\models\CsApplicationType;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Alert;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplication $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="container">
    <div class="cs-carsticker-application-form col-md-12">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <!-- Application Data Fields -->
        <?= $form->field($model, 'application_id')->hiddenInput()->label(false) ?>
        <div class="form-group">
            <?= $form->field($model, 'application_ref_no')->textInput(['maxlength' => true])->label('Application Reference Number', ['class' => 'mb-2 fw-bold']) ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'vehicle_regno')->textInput(['maxlength' => true])->label('Vehicle Registration Number', ['class' => 'mb-2 fw-bold']) ?>
            
        </div>

        <div class="form-group">
            <?= $form->field($model, 'application_date')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Select application date ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'startDate' => date('Y-m-d'),
                    'endDate' => date('Y-m-d'),
                ],
                'value' => date('Y-m-d H:i:s'),
            ])->label('Application Date', ['class' => 'mb-2 fw-bold']) ?>
        </div>

        <div class="form-group">
            <?php
            $application_type = CsApplicationType::find()->select(['type_id', 'application_type'])->asArray()->all();
            $data = \yii\helpers\ArrayHelper::map($application_type, 'type_id', 'application_type');
            echo $form
                ->field($model, 'application_type')
                ->widget(Select2::classname(), [
                    'data' => $data,
                    'language' => 'en',
                    'options' => ['placeholder' => 'Select Application Type ...'],
                ])
                ->label('Application Type', ['class' => 'mb-2 fw-bold']);
            ?>
        </div>

        <!-- Document Attachment Fields -->
        <div class="form-group" id="documentFields" style="display: none;">
            <?= $form->field($documentModel, 'file')->fileInput(['class' => 'form-control'])->label('Upload Document', ['class' => 'fw-bold']) ?>
            <?= $form->field($documentModel, 'document_id')->hiddenInput()->label(false) ?>
        </div>

        <!-- Button to toggle Document Attachment Fields -->
        <div class="form-group" id="showDocumentFields">
            <button type="button" class="btn btn-primary">Upload Docs</button>
        </div>

        <!-- Submit Button -->
        <div class="form-group" id="submitButton" style="display: none;">
            <?= Html::submitButton(
                'Save Vehicle Details and Forward for Approval >>',
                [
                    'class' => 'btn btn-primary',
                    'disabled' => $isUpdateDisabled || !$model->isNewRecord ? 'disabled' : null,
                ]
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$script = <<<JS
//Script to toggle Document Attachment Fields and Submit Button
document.getElementById('showDocumentFields').addEventListener('click', function() {
    document.getElementById('documentFields').style.display = 'block';
    document.getElementById('submitButton').style.display = 'block';
    document.getElementById('submitButton').removeAttribute('disabled'); // Remove disabled attribute
    this.style.display = 'none';
});

JS;

$this->registerJs($script);
?>