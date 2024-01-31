<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplication $model */
/** @var app\modules\approval\models\CsCarstickerApproval $latestApproval */
/** @var bool $isUpdateDisabled */

$this->title = 'Update Application Details';
$this->params['breadcrumbs'][] = ['label' => 'Cs Carsticker Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->application_id, 'url' => ['view', 'application_id' => $model->application_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cs-carsticker-application-update">

    <h3 class="text-center text-primary">
        <?= Html::encode($this->title) ?>
    </h3>

    <?= $this->render('_form', [
        'model' => $model,
        'documentModel' => $documentModel,
        'isUpdateDisabled' => $isUpdateDisabled,
    ]) ?>

</div>