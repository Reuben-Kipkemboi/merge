<?php

use app\modules\stickers\models\CsCarstickerApplicationSearchDirector;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\web\Response;

/** @var yii\web\View $this */
/** @var app\modules\stickers\models\CsCarstickerApplicationSearchDirector $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$loggedInUser = Yii::$app->user->identity;

$this->title = 'Level 1 Approvals - Director';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cs-carsticker-application-index">
    <div class="card">
        <div class="card-body">

            <h1>
                <?= Html::encode($this->title) ?>
            </h1>


            <div class="d-flex justify-content-end mb-2">
                <?= Html::button('Revoke Applications', ['class' => 'btn btn-lg btn-danger mx-3', 'id' => 'revoke-applications']) ?>
                <?= Html::button('Approve Applications', ['class' => 'btn btn-lg btn-primary mx-3', 'id' => 'approve-applications']) ?>
                <?php
                $approvalUrl = Url::to(['stickers/cs-carsticker-application/approve-applications']);
                $approval = json_encode($approvalUrl);

                ?>


            </div>
            <?php
            $gridViewOptions = [
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    ['class' => 'yii\grid\CheckboxColumn'],
                    'application_ref_no',
                    'vehicle_regno',
                    'application_date',
                ],
            ];
            ?>


            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?= GridView::widget($gridViewOptions); ?>

        </div>
    </div>
</div>

<?php

$script = <<<JS
$(document).ready(function () {






    var selectedIds = [];

    $('table.table tbody input[type="checkbox"]').on('change', function () {
        var id = $(this).closest('tr').data('key');

        if (this.checked) {
            if (!selectedIds.includes(id)) {
                selectedIds.push(id);
                console.log(selectedIds);
            }
        } else {
            var index = selectedIds.indexOf(id);
            if (index !== -1) {
                selectedIds.splice(index, 1);
                console.log(selectedIds);
            }
        }
    });


        $('#approve-applications').on('click', function() {
        if (selectedIds.length === 0) {
            alert('No Applications selected.');
        } else {
            var confirmed = confirm("Are you sure you want to Approve the application(s) Level 2?");
            if(confirmed){
            $.ajax({
                url: 'http://csmis-dev.uonbi.ac.ke/index.php?r=stickers/cs-carsticker-application/approve-applications',
                method: 'POST',
                data: { selectedIds: selectedIds },
                success: function(response) {
                    alert('Approval succesful');
                window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert(error.message);
                }
            });
            }
        }
    });

    $('#revoke-applications').on('click', function() {
        if (selectedIds.length === 0) {
            alert('No Applications selected.');
        } else {
            var confirmed = confirm("Are you sure you want to REVOKE the application(s)?");
            if(confirmed){
            $.ajax({
                url: 'http://csmis-dev.uonbi.ac.ke/index.php?r=stickers/cs-carsticker-application/revoke-applications',
                method: 'POST',
                data: { selectedIds: selectedIds },
                success: function(response) {
                    alert('Revocation succesful');
                window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert(error.message);
                }
            });
            }
        }
    });
//console.log(selectedIds);


});
JS;

$this->registerJs($script);
?>