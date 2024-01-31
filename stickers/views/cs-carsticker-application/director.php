<?php

use app\modules\stickers\models\CsCarstickerApplicationSearchDirector;
use app\modules\stickers\models\CsCarstickerApplication;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\web\Response;
use yii\grid\ActionColumn;
use app\modules\approval\models\CsCarstickerApproval;

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

            <h1><?= Html::encode($this->title) ?></h1>


            <div class="d-flex justify-content-end mb-2">                           
<?= Html::button('Review Applications', ['class' => 'btn btn-lg btn-info mx-3', 'id' => 'review-applications']) ?>
<?= Html::button('Approve Applications', ['class' => 'btn btn-lg btn-primary mx-3', 'id' => 'approve-applications']) ?>
<?php
$approvalUrl = Url::to(['stickers/cs-carsticker-application/approve-applications']);
$approval = json_encode($approvalUrl);

?>


</div>


<!-- Bootstrap Modal Markup -->
<div class="modal" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bootstrap Modal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Your modal content goes here...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
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
        [
            'attribute' => 'vehicle_regno',
            'width' => '300px',
        ],
        // 'application_date',
       [
            'label' => 'Remark',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                return Html::textInput("CsCarstickerApplication[{$key}][remark]", '', ['class' => 'form-control']);
            },
        ],        
[
    'class' => 'kartik\grid\ActionColumn',
    'template' => '{view},{approve}, {review}, {dissaprove}',
    'width' => '400px',
    'buttons' => [
'view' => function ($url, $model, $key) {
    return Html::a('View', ['cs-carsticker-application/update', 'application_id' => $model['application_id']], [
        'class' => 'bi bi-eye btn btn-outline-primary btn-sm',
    ]);
},
 
        'approve' => function ($url, $model, $key) {
            $approveUrl = Url::to(['cs-carsticker-application/approve-application']);

            $js = <<<JS
                // This function will be called when the 'Approve' button is clicked
                function approveApplication() {
                    // Retrieve the application ID from the data attribute
                    var applicationId = $(this).data('application-id');
                    var remarkValue = $('input[name="CsCarstickerApplication[' + applicationId + '][remark]"]').val();
                    // Perform AJAX request
                     if(remarkValue === ""){
                        alert("You have to set a remark before you approve")
                        window.location.reload();
                    } else if(remarkValue !== ""){
                    var confirmed = confirm("Are you sure you want to Approve the record?");
                    if(confirmed){
                         $.ajax({
                        type: 'POST',
                        url: '$approveUrl',
                        data: {
                            applicationId: applicationId,
                            remarkValue: remarkValue
                        },
                        success: function(data) {
                            // Handle success, if needed
                            console.log('Approval updated successfully');
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error, if needed
                            console.error('Error updating approval:', error);
                        }
                    });

                    // Prevent the default behavior of the anchor tag
                    return false;
                    }
            }                    
                }

                // Attach the approveCustomer function to the click event of the 'Approve' button
                $(document).on('click', '.approve-button', approveApplication);
JS;

            // Register the JavaScript code
            $this->registerJs($js, yii\web\View::POS_READY);

            return Html::a('Approve', '#', [
                'class' => 'bi bi-pencil-square btn btn-outline-primary btn-sm approve-button',
                'data-application-id' => $model->application_id,
            ]);
        },
        'review' => function ($url, $model, $key) {
            $reviewUrl = Url::to(['cs-carsticker-application/review-application']);

            $js = <<<JS
                // This function will be called when the 'Approve' button is clicked
                function reviewApplication() {
                    // Retrieve the application ID from the data attribute
                    var applicationId = $(this).data('application-id');
                    var remarkValue = $('input[name="CsCarstickerApplication[' + applicationId + '][remark]"]').val();
                    // Perform AJAX request
                    
                    if(remarkValue === ""){
                        alert("You have to set a remark before you review")
                        window.location.reload();
                    }else if(remarkValue !== ""){
                    var confirmed = confirm("Are you sure you want to Review the record?");                        
                    if(confirmed){
                        $.ajax({
                        type: 'POST',
                        url: '$reviewUrl',
                        data: {
                            applicationId: applicationId,
                            remarkValue: remarkValue
                        },
                        success: function(data) {
                            // Handle success, if needed
                            console.log('Approval updated successfully');
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error, if needed
                            console.error('Error updating approval:', error);
                        }
                    });

                    // Prevent the default behavior of the anchor tag
                    return false;
                    }
                    }
                }

                // Attach the reviewCustomer function to the click event of the 'Review' button
                $(document).on('click', '.review-button', reviewApplication);
JS;

            // Register the JavaScript code
            $this->registerJs($js, yii\web\View::POS_READY);

            return Html::a('Review', '#', [
                'class' => 'bi bi-pencil-square btn btn-outline-info btn-sm review-button',
                'data-application-id' => $model->application_id,
            ]);
        },   
        'dissaprove' => function ($url, $model, $key) {
            $dissaproveUrl = Url::to(['cs-carsticker-application/dissaprove-application']);

            $js = <<<JS
                // This function will be called when the 'Approve' button is clicked
                function dissaproveApplication() {
                    // Retrieve the application ID from the data attribute
                    var applicationId = $(this).data('application-id');
                    var remarkValue = $('input[name="CsCarstickerApplication[' + applicationId + '][remark]"]').val();
                    var confirmed = confirm("Are you sure you want to Disapprove the record?");
                    // Perform AJAX request
                   if(confirmed){

                    if(remarkValue === ""){
                        alert("You have to set a remark before you dissaprove")
                        window.location.reload();
                    } else if(remarkValue !== ""){
                     $.ajax({
                        type: 'POST',
                        url: '$dissaproveUrl',
                        data: {
                        applicationId: applicationId,
                        remarkValue: remarkValue
                        },
                        success: function(data) {
                            // Handle success, if needed
                            console.log('Approval updated successfully');
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error, if needed
                            console.error('Error updating approval:', error);
                        }
                    });

                    // Prevent the default behavior of the anchor tag
                    return false;
                    }
                   }
                }

                // Attach the reviewCustomer function to the click event of the 'Review' button
                $(document).on('click', '.dissaprove-button', dissaproveApplication);
JS;

            // Register the JavaScript code
            $this->registerJs($js, yii\web\View::POS_READY);

            return Html::a('Dissaprove', '#', [
                'class' => 'bi bi-pencil-square btn btn-outline-danger btn-sm dissaprove-button',
                'data-application-id' => $model->application_id,
            ]);
        },                     
    ],
    
],
    ],
];



?>


<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
   <?= GridView::widget($gridViewOptions); ?>

        </div>
    </div>
</div>

<?php

$script = <<< JS
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
                    alert('Application(s) succesfully approved for the carsticker');
                window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert(error.message);
                }
            });
            }
        }
    });

    $('#review-applications').on('click', function() {
        if (selectedIds.length === 0) {
            alert('No Applications selected.');
        } else {
            var confirmed = confirm("Are you sure you want to REVIEW the application(s)?");
            if(confirmed){
            $.ajax({
                url: 'http://csmis-dev.uonbi.ac.ke/index.php?r=stickers/cs-carsticker-application/review-applications',
                method: 'POST',
                data: { selectedIds: selectedIds },
                success: function(response) {
                    alert('Applications succesfully reviewed to applicant');
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





