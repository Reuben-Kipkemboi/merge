<!-- <?php

// use app\modules\stickers\models\CsCarstickerApplication;
// use app\modules\setup\models\CsApplicationType;
// use kartik\grid\GridView;
// use yii\grid\ActionColumn;
// use yii\helpers\Html;
// use yii\helpers\Url;
// use yii\helpers\ArrayHelper;

// /** @var yii\web\View $this */
// /** @var app\modules\stickers\models\CsCarstickerApplicationSearch $searchModel */
// /** @var yii\data\ActiveDataProvider $dataProvider */

// $this->title = 'My Car Sticker Applications';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-carsticker-application-index">

    <h2 class="text-center text-primary"><//?= Html::encode($this->title) ?></h2>

    <p style="text-align:right;">
        <//?= Html::a('Make a Car sticker Application', ['create'], ['class' => 'btn btn-primary bi bi-send']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <//?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'application_ref_no',
            'vehicle_regno',
            'application_date',
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
                            // Disable button with additional CSS class for styling
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

</div> -->
<?php
use app\modules\stickers\models\CsCarstickerApplication;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\bootstrap4\Tabs;

$this->title = 'My Car Sticker Applications';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="cs-carsticker-application-index">
    <h2 class="text-center text-primary">
        <?= Html::encode($this->title) ?>
    </h2>
    <p style="text-align:right;">
        <?= Html::a('Make an Application for a Car Sticker', ['create'], ['class' => 'btn btn-primary bi bi-send']) ?>
    </p>

    <?php
    $tabs = [
        [
            'label' => '<span class="font-weight-bold fs-6 p-2 rounded mb-2">APPLIED</span>',
            'content' => $this->render('_pending_tab', [
                'searchModel' => $searchModel,
                'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, ['status_id' => 1]),
            ]),
            'active' => true,
        ],

        [
            'label' => '<span class="font-weight-bold fs-6 p-2 rounded mb-2">RETURNED APPLICATIONS</span>',
            'content' => $this->render('_returned_tab', [
                'searchModel' => $searchModel,
                'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, ['status_id' => 3]),
            ]), 
        ],
        [
            'label' => '<span class="font-weight-bold fs-6 p-2 rounded mb-2">APPROVED APPLICATIONS</span>',
            'content' => $this->render('_approved_tab', [
                'searchModel' => $searchModel,
                'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, ['status_id' => 5]),
                // 'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, [4, 5]),
                
            ]),
            
        ],
        [
            'label' => '<span class="font-weight-bold fs-6 p-2 rounded mb-2">EXPIRED APPLICATIONS</span>',
            'content' => $this->render('_expired_tab', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProviderExpired,  
            ]),
        ],
    ];

    echo Tabs::widget([
        'items' => $tabs,
        'encodeLabels' => false,
    ]);
    ?>

</div>