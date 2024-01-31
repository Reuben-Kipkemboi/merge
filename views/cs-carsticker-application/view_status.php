<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CsCarstickerApplication */
/* @var $approvalStatus app\models\CsCarstickerApproval */

$this->title = 'Application Details';
$this->params['breadcrumbs'][] = ['label' => 'Carsticker Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-carsticker-application-view">
    <h3>
        <?= Html::encode($this->title) ?>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'application_ref_no',
            'vehicle_regno',
            'application_date',
            'applicationType.application_type',
        ],
    ]) ?>

    <h3 class="text-center">Application status</h3>
    <?= DetailView::widget([
        'model' => $approvalStatus,
        'attributes' => [
            // 'remark',
            // [
            //     'attribute' => 'Application status',
            //     'value' => function ($model) {
            //         return $model->status->approval_status;
            //     },
            //     'contentOptions' => ['style' => 'background-color: ' . ($model->status->status_color ?? '#2a68af') . '; color: white; font-weight: bold;'],
            // ],
            [
                'attribute' => 'Current Application status',
                'value' => function ($model) {
                    return $model->status->approval_status;
                },
                'contentOptions' => [
                    'style' => 'background-color: ' . ($model->status->status_color ?? '#2a68af') . '; color: white; font-weight: bold; width: 50%;',
                ],
                'content' => function ($model) {
                    $statusValue = $model->status->approval_status;
                    $statusColor = $model->status->status_color ?? '#2a68af';

                    return "<div style='background-color: $statusColor; color: white; font-weight: bold; width: 50%;'>$statusValue</div>";
                },
            ],

        ],
    ]) ?>

    <div class="container mt-4 mb-4">


    </div>



</div>