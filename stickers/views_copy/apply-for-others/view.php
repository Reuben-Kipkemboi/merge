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
<div class="cs-carsticker-application-view">

    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>



    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'application_ref_no',
            'vehicle_regno',
            'application_date',
            'application_type',
        ],
    ]) ?>
    <div class="form-group">
        <?= Html::a('Update', ['update', 'application_id' => $model->application_id], ['class' => 'btn btn-primary']) ?>
    </div>

</div>