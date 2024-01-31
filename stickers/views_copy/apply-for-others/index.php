<?php

use app\modules\stickers\models\CsCarstickerApplication;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplicationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Cs Carsticker Applications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-carsticker-application-index">
    <h2 class="text-center text-primary"><?=Html::encode($this->title)?></h2>

    <p style="text-align:right;">
        <?=Html::a('Apply for a Sticker', ['create'], ['class' => 'btn btn-primary bi bi-send'])?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            ['class' => 'kartik\grid\SerialColumn'],

            'application_id',
            'application_ref_no',
            'vehicle_regno',
            'application_date',
            'application_type',
            //'user_id',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, CsCarstickerApplication $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'application_id' => $model->application_id]);
            //      }
            // ],
            [
            'class' => ActionColumn::className(),
            'template' => '{update}', 
            'urlCreator' => function ($action, CsCarstickerApplication $model, $key, $index, $column) {
                return Url::toRoute([$action, 'application_id' => $model->application_id]);
            },
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('Update', ['/stickers/apply-for-others/update', 'application_id' => $model->application_id], ['class' => 'bi bi-pencil-square btn btn-outline-primary btn-sm']);
                },
            ],
            ]
        ],
    ]); ?>


</div>
