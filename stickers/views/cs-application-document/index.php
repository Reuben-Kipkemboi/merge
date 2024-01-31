<?php

use app\modules\stickers\models\CsApplicationDocument;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsApplicationDocumentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Application Documents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-application-document-index">

    <h3 class="text-center text-primary">
        <?= Html::encode($this->title) ?>
    </h3>

    <!-- <p>
        <//?= Html::a('Create Cs Application Document', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            'application_doc_id',
            'application_id',
            'document_location',
            'document_id',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}', 
                'buttons' => [
                    // 'view' => function ($url, $model, $key) {
                    //     return Html::a(
                    //         'View to forward for approval',
                    //         ['/stickers/cs-application-document/view', 'application_doc_id' => $model->application_doc_id],
                    //         ['class' => 'bi bi-eye btn btn-outline-info btn-sm']
                    //     );
                    // },
                    'update' => function ($url, $model, $key) {
                        return Html::a(
                            'Update application',
                            ['/stickers/cs-application-document/update', 'document_id' => $model->document_id],
                            ['class' => 'bi bi-pencil-square btn btn-outline-primary btn-sm']
                        );
                    },
                ],
            ],
        ],
    ]); ?>


</div>

