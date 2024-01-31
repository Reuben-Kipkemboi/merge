<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsApplicationDocument $model */

$this->title = 'Update Cs Application Document: ' . $model->application_doc_id;
$this->params['breadcrumbs'][] = ['label' => 'Cs Application Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->application_doc_id, 'url' => ['view', 'application_doc_id' => $model->application_doc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cs-application-document-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
