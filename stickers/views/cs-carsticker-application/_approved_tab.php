<?php

use app\modules\stickers\models\CsCarstickerApplication;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplicationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
        'application_ref_no',
        'vehicle_regno',
        // 'application_date',
        [
            'attribute' => 'application_date',
            'value' => function ($model) {
                // return date('Y-m-d', strtotime($model->application_date));
                return date('F j, Y', strtotime($model->application_date));
            },
            // 'contentOptions' => ['class' => 'text-center'],
        ],
        'applicationType.application_type',
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {view}',
            'urlCreator' => function ($action, CsCarstickerApplication $model, $key, $index, $column) {
                if ($action === 'update' && $model->isUpdateDisabled()) {
                    return null; // Disable update button if model->isUpdateDisabled() returns true
                }
                return Url::toRoute([$action, 'application_id' => $model->application_id]);
            },
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    if ($model->isUpdateDisabled()) {
                        
                        return Html::a('Update', null, ['class' => 'disabled bi bi-pencil-square btn btn-outline-primary btn-sm']);
                    }
                    return Html::a('Update', ['/stickers/cs-carsticker-application/update', 'application_id' => $model->application_id], ['class' => 'bi bi-pencil-square btn btn-outline-primary btn-sm']);
                },
                'view' => function ($url, $model, $key) {
                    return Html::a('View Status', ['/stickers/cs-carsticker-application/view-status', 'application_id' => $model->application_id], ['class' => 'bi bi-eye btn btn-outline-info btn-sm']);
                },
            ],
        ],
    ],
]); ?>
