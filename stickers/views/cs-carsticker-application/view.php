<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplication $model */

$this->title = $model->vehicle_regno;
$this->params['breadcrumbs'][] = ['label' => 'Cs Carsticker Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container">
    <div class="col-md-2"></div>
    <div class="cs-carsticker-application-view">

        <h3 class="text-center">
            <?= Html::encode($this->title) ?>
        </h3>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'application_ref_no',
                'vehicle_regno',
                [
                    'attribute' => 'application_date',
                    'value' => function ($model) {
                                return $model->application_date ? Yii::$app->formatter->asDate($model->application_date, 'yyyy-MM-dd') : null;
                            },
                ],
                [
                    'attribute' => 'application_type',
                    'value' => function ($model) {
                                return $model->applicationType ? $model->applicationType->application_type : null;
                            },
                ],

            ],

        ]) ?>


        <div class="form-group">
            <?= Html::a('Update Details', ['/stickers/cs-carsticker-application/update', 'application_id' => $model->application_id], ['class' => 'btn btn-primary']) ?>
        </div>


    </div>
    <div class="col-md-2"></div>
</div>