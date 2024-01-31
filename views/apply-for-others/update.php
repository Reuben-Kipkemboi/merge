<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplication $model */

$this->title = 'Update Cs Carsticker Application: ' . $model->application_id;
$this->params['breadcrumbs'][] = ['label' => 'Cs Carsticker Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->application_id, 'url' => ['view', 'application_id' => $model->application_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cs-carsticker-application-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
