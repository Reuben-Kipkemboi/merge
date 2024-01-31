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
    'dataProvider' => $dataProvider,  // Assuming $dataProvider is configured to fetch only expired applications
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
        'application_ref_no',
        'vehicle_regno',
        [
            'attribute' => 'application_date',
            'value' => function ($model) {
                return date('F j, Y', strtotime($model->application_date));
            },
        ],
        // 'applicationType.application_type',
        [
            'class' => ActionColumn::className(),
            'template' => '{renew} {view}',  // Change the template to include 'renew'
            'urlCreator' => function ($action, CsCarstickerApplication $model, $key, $index, $column) {
                if ($action === 'renew' && $model->isUpdateDisabled()) {
                    return null; // Disable renew button if model->isUpdateDisabled() returns true
                }
                return Url::toRoute(['renew', 'application_id' => $model->application_id]); // Change the action to 'renew'
            },
            'buttons' => [
                'renew' => function ($url, $model, $key) {
                    return Html::a('Renew Application', ['/stickers/cs-carsticker-application/renew', 'application_id' => $model->application_id], ['class' => 'bi bi-pencil-square btn btn-outline-primary btn-sm']);
                },
                'view' => function ($url, $model, $key) {
                    return Html::a('View Status', ['/stickers/cs-carsticker-application/view-status', 'application_id' => $model->application_id], ['class' => 'bi bi-eye btn btn-outline-info btn-sm']);
                },
            ],
        ],
    ],
]); ?>

