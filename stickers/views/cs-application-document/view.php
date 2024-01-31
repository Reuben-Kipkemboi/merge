<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsApplicationDocument $model */

$this->title = $model->application_doc_id;
$this->params['breadcrumbs'][] = ['label' => 'Cs Application Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cs-application-document-view">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <p>
        <!-- <//?= Html::a('Update', ['update', 'application_doc_id' => $model->application_doc_id], ['class' => 'btn btn-primary']) ?>
        <//?= Html::a('Delete', ['delete', 'application_doc_id' => $model->application_doc_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'application_doc_id',
            'application_id',
            'document_location',
            'document_id',
        ],
    ]) ?>

<?= Html::button('Forward for Approval', [
    'class' => 'btn btn-primary',
    'onclick' => 'forwardForApproval(' . $model->application_id . ')'
]) ?>

 <!-- Display success message -->
 <div id="success-message" class="alert alert-success" style="display: none;"></div>


  <!-- Display failure message -->
  <div id="failure-message" class="alert alert-danger" style="display: none;"></div>

</div>





    