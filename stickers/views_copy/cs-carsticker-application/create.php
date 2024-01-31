<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplication $model */

$this->title = 'Add Vehicle Details here';
$this->params['breadcrumbs'][] = ['label' => 'Cs Carsticker Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
<?php if (!empty($modelErrors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($modelErrors as $error): ?>
                <li><?= $error[0] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Display document model errors -->
<?php if (!empty($documentErrors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($documentErrors as $error): ?>
                <li><?= $error[0] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="row justify-content-center">
<div class="cs-carsticker-application-create">

    <h3 class="text-center text-primary"><?= Html::encode($this->title) ?></h3>
    <h5 class="text-danger text-center">Please ensure you attach all documents as a single PDF file when submitting.</h5>
    <?= $this->render('_form', [
    'model' => $model,
    'documentModel' => $documentModel,
    'isUpdateDisabled' => $isUpdateDisabled,

]) ?>

</div>
</div>
</div>