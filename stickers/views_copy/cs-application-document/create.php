<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsApplicationDocument $model */

$this->title = 'Attach Application Documents';
$this->params['breadcrumbs'][] = ['label' => 'Cs Application Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="cs-application-document-create col-md-8">

        <h3 class="text-center text-primary"><?= Html::encode($this->title) ?></h3>

        <?= $this->render('_form', [
            'model' => $model,
            
        ]) ?>

    </div>
    <div class="col-md-2"></div>
</div>