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
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {view}',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return $model->isUpdateDisabled()
                        ? Html::a('Update', null, ['class' => 'disabled bi bi-pencil-square btn btn-outline-primary btn-sm'])
                        : Html::a('Update', ['/stickers/cs-carsticker-application/update', 'application_id' => $model->application_id], ['class' => 'bi bi-pencil-square btn btn-outline-primary btn-sm']);
                },
                'view' => function ($url, $model, $key) {
                    return Html::a('View Status', ['/stickers/cs-carsticker-application/view-status', 'application_id' => $model->application_id], ['class' => 'bi bi-eye btn btn-outline-info btn-sm']);
                },
            ],
        ],
    ],
    // 'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, ['status_id' => 1]),
]); ?>
