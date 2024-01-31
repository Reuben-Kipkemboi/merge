<?php

use app\modules\setup\models\CsApplicationType;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplication $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cs-carsticker-application-form">

<div class="container">
    <div class="col-md-3"></div>
    <div class="cs-carsticker-application-form col-md-12">

        <?php $form = ActiveForm::begin(); ?>

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
                'value' => date('Y-m-d'),  
            ])->label('Application Date', ['class' => 'mb-2 fw-bold']) ?>
        </div>

        <div class="form-group">
            <?php
            $application_type = CsApplicationType::find()->select(['type_id', 'application_type'])->asArray()->all();
            $data = ArrayHelper::map($application_type, 'type_id', 'application_type');
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

        <div class="form-group">
            <?= Html::submitButton('Save Vehicle Details >>', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <div class="col-md-3"></div>
</div>
