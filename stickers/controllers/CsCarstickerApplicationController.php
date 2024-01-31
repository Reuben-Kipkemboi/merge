<?php

namespace app\modules\stickers\controllers;

use app\modules\stickers\models\CsCarstickerApplication;
use app\modules\stickers\models\CsCarstickerApplicationSearch;
use app\modules\stickers\models\CsCarstickerApplicationSearchDirector;
use app\modules\setup\models\CsApplicationType;
use app\modules\approval\models\CsCarstickerApproval;
use app\modules\stickers\models\CsApplicationDocument;
use yii\web\Response;
use yii\helpers\Url;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\growl\Growl;
use yii\web\UploadedFile;

/**
 * CsCarstickerApplicationController implements the CRUD actions for CsCarstickerApplication model.
 */
class CsCarstickerApplicationController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all CsCarstickerApplication models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CsCarstickerApplicationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        // // Get the currently logged-in user ID
        // $userId = Yii::$app->user->id;
        // Dummy user ID
        $dummyUserId = 1;

        // Fetch the models from the dataProvider
    $models = $dataProvider->getModels();

    // Check the status_id for each model and set $isUpdateDisabled
    $isUpdateDisabled = false; // Default value
    foreach ($models as $model) {
        $latestApproval = CsCarstickerApproval::find()
            ->where(['application_id' => $model->application_id])
            ->orderBy(['approval_date' => SORT_DESC])
            ->one();

        // Check if the latest approval status is one of the statuses that should disable the update
        $disableUpdateStatuses = [3, 4, 5];
        if ($latestApproval && in_array($latestApproval->status_id, $disableUpdateStatuses)) {
            $isUpdateDisabled = true; // Set to true if any model has a status in [3, 4, 5]
            break; // No need to check further once we find a disabled status
        }
    }
        // Filter the dataProvider based on the dummy user's ID
        $dataProvider->query->andWhere(['user_id' => $dummyUserId]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isUpdateDisabled' => $isUpdateDisabled,
        ]);
    }


public function actionDirector()
    {
        $searchModel = new CsCarstickerApplicationSearchDirector();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('director', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    

    /**
     * Displays a single CsCarstickerApplication model.
     * @param int $application_id Application ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($application_id)
    {
        // Find the model using the provided application_id
        $model = $this->findModel($application_id);

        // Retrieve related documents for the application
        $documents = CsApplicationDocument::find()->where(['application_id' => $application_id])->all();

        return $this->render('view', [
            'model' => $model,
            'documents' => $documents,
        ]);
    }

    /**
     * Creates a new CsCarstickerApplication model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CsCarstickerApplication();
        $model->application_date = date('Y-m-d');
        $documentModel = new CsApplicationDocument();

        // Set $isUpdateDisabled to false for create action
    $isUpdateDisabled = false;

        // USER ID TO USE TEMPORARILY
        $temporaryUserId = 1;

        if ($this->request->isPost) {
            $model->load($this->request->post());
            // $model->user_id = Yii::$app->user->id;
            $model->user_id = $temporaryUserId;
            $model->file = 'DUMMY';
            $documentModel->file = UploadedFile::getInstance($documentModel, 'file');

            $model->file = UploadedFile::getInstance($documentModel, 'file');

            if ($model->validate() && $model->save()) {
                if ($documentModel->file) {
                    $fileName = $model ->application_ref_no . '_' .  'application_' . $model->application_id;
                    $extension = $documentModel->file->extension;
                    $filePath = 'uploads/documents/' . $fileName . '.' . $extension;
                    $model->file = $filePath;
                    $documentModel->document_location = $filePath;
                    $documentModel->application_id = $model->application_id;

                    if ($documentModel->validate() && $documentModel->save()) {
                        // Forward for approval after saving the model and document
                        $this->forwardApplicationForApproval($model->application_id);
                        $documentModel->file->saveAs($filePath);
                        Yii::$app->session->setFlash('growl', [
                            'type' => Growl::TYPE_SUCCESS,
                            'icon' => 'bi bi-check-lg',
                            'title' => 'Success!',
                            'message' => 'Vehicle Details, Document attached, and Application Forwarded for Approval.',
                            'showSeparator' => true,
                            'delay' => 2500,
                            'pluginOptions' => [
                                'showProgressbar' => true,
                                'placement' => [
                                    'from' => 'top',
                                    'align' => 'right',
                                ],
                                'delay' => 5000,
                                'timer' => 1000,
                            ],
                        ]);

                        // return $this->redirect(['view', 'application_id' => $model->application_id]);
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to save document. Please check the form for errors.');
                        $documentErrors = $documentModel->getErrors();
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'File not uploaded. Please select a file.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Failed to save model. Please check the form for errors.');
                $modelErrors = $model->getErrors();
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'documentModel' => $documentModel,
            'isUpdateDisabled' => $isUpdateDisabled,
            'modelErrors' => isset($modelErrors) ? $modelErrors : [],
            'documentErrors' => isset($documentErrors) ? $documentErrors : [],
            
        ]);
    }
    /**
     * Updates an existing CsCarstickerApplication model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $application_id Application ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionUpdate($application_id)
    // {
    //     $model = $this->findModel($application_id);
    //     $documentModel = new CsApplicationDocument(); 

    //     if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
    //         // return $this->redirect(['view', 'application_id' => $model->application_id]);
    //         return $this->redirect(['index']);
    //     }

    //     return $this->render('update', [
    //         'model' => $model,
    //         'documentModel' => $documentModel
    //     ]);
    // }
    // CsCarstickerApplicationController.php

    public function actionUpdate($application_id)
{
    $model = $this->findModel($application_id);
    $documentModel = new CsApplicationDocument();

    // Retrieve the latest approval
    $latestApproval = CsCarstickerApproval::find()
        ->where(['application_id' => $model->application_id])
        ->orderBy(['approval_date' => SORT_DESC])
        ->one();

    // This is to check if the latest approval status is one of the statuses that should disable the update
    $disableUpdateStatuses = [3, 4, 5]; // Status IDs that should disable the update button
    $isUpdateDisabled = $latestApproval && in_array($latestApproval->status_id, $disableUpdateStatuses);

    if ($this->request->isPost) {
        $postData = $this->request->post();

        // Load the application model with the posted data
        if ($model->load($postData)) {
            // Check if the application is in a status that allows updates
            if ($latestApproval && $latestApproval->status_id == 2) {
                // Save the updated application
                if ($model->save()) {
                    // Forward for approval again
                    $this->forwardApplicationForApproval($model->application_id);

                    Yii::$app->session->setFlash('success', 'Application updated and forwarded for approval.');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to update the application. Please check the form for errors.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'This application cannot be updated at the current status.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Failed to load application data. Please try again.');
        }
    }

    return $this->render('update', [
        'model' => $model,
        'documentModel' => $documentModel,
        'latestApproval' => $latestApproval,
        'isUpdateDisabled' => $isUpdateDisabled,
    ]);
}
    /**
     * Deletes an existing CsCarstickerApplication model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $application_id Application ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($application_id)
    {
        $this->findModel($application_id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Finds the CsCarstickerApplication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $application_id Application ID
     * @return CsCarstickerApplication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($application_id)
    {
        if (($model = CsCarstickerApplication::findOne(['application_id' => $application_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // Forwading function for approval
    protected function forwardApplicationForApproval($application_id)
    {
        // Check conditions for approval
        if ($this->checkApprovalConditions($application_id)) {
            // Create a new Approval record
            $approval = new CsCarstickerApproval([
                'approval_date' => date('Y-m-d H:i:s'),
                'status_id' => 1,
                'user_id' => 22,  // Update with the appropriate user ID
                'remark' => 'Applied',
                'application_id' => $application_id,
            ]);

            // Save the Approval record
            if ($approval->save()) {
                Yii::info('Approval record saved successfully.');
                return true;
            } else {
                Yii::error('Failed to save approval record. Errors: ' . json_encode($approval->errors));
            }
        }

        return false; // Conditions for approval not met
    }
    protected function checkApprovalConditions($application_id)
    {
        $documents = CsApplicationDocument::find()->where(['application_id' => $application_id])->all();

        foreach ($documents as $document) {
            if (!$document->document_location) {
                Yii::info('Document missing location. Document ID: ' . $document->document_id);
                return false; // Not all required documents are attached
            }
        }

        Yii::info('All documents have locations for Application ID: ' . $application_id);
        return true; // All conditions are met
    }

    // To get the status of application from the view status button
    public function actionViewStatus($application_id)
    {
        // Fetch the application model based on the $application_id
        $model = CsCarstickerApplication::findOne($application_id);

        // Fetch the approval status for the application
        $approvalStatus = CsCarstickerApproval::findOne(['application_id' => $application_id]);

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view_status', [
            'model' => $model,
            'approvalStatus' => $approvalStatus,
        ]);
    }


    public function actionApproveApplications()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        $selectedIds = Yii::$app->request->post('selectedIds', []);
    
        // Assuming you want to update the 'status' and 'approval_date' columns
        $updateColumns = [
            'level_id' => 2,
            'status_id' => 5,
        ];
    
        // Update rows for selected IDs
        $rowsUpdated = CsCarstickerApproval::updateAll($updateColumns, ['application_id' => $selectedIds]);
    
        if ($rowsUpdated > 0) {
    
            return ['success' => true, 'message' => 'Rows updated successfully.'];
        } else {
            // No rows updated
            return ['success' => false, 'message' => 'No rows updated.'];
        }
    }


    public function actionReviewApplications()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        $selectedIds = Yii::$app->request->post('selectedIds', []);
    
        // Assuming you want to update the 'status' and 'approval_date' columns
        $updateColumns = [
            'level_id' => 1,
            'status_id' => 3,
        ];
    
        // Update rows for selected IDs
        $rowsUpdated = CsCarstickerApproval::updateAll($updateColumns, ['application_id' => $selectedIds]);
    
        if ($rowsUpdated > 0) {
    
            return ['success' => true, 'message' => 'Rows updated successfully.'];
        } else {
            // No rows updated
            return ['success' => false, 'message' => 'No rows updated.'];
        }
    }


    // public function actionApproveApplication()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $model = $model = new CsCarstickerApproval();
    //     $applicationId = Yii::$app->request->post('application_id');
    //     $application = CsCarstickerApproval::findOne($applicationId);
    //         $application->level_id = 2;
    //         $application->status_id = 5;
    //         $application->save();

    //         dd("Yes");
    // }


public function actionApproveApplication()
{
  Yii::$app->response->format = Response::FORMAT_JSON;
    $applicationId = Yii::$app->request->post('applicationId');
    $remarkValue = Yii::$app->request->post('remarkValue');

    
    $application = CsCarstickerApproval::findOne(['application_id' => $applicationId]);

    if ($application === null) {
        // Handle the situation when the model is not found, e.g., return an error response
        dd('The requested application does not exist.');
    }

    $application->level_id = 2;
    $application->status_id = 5;
    $application->remark = $remarkValue;
    if ($application->save()) {
        // The model is updated
        return ['success' => true, 'message' => 'The application has been approved.'];
    } else {
        // The model is not updated, handle the error, e.g., return an error response
        return ['success' => false, 'message' => 'There was an error approving the application.'];
    }
}


public function actionReviewApplication()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    $applicationId = Yii::$app->request->post('applicationId');
    $remarkValue = Yii::$app->request->post('remarkValue');

    
    $application = CsCarstickerApproval::findOne(['application_id' => $applicationId]);

    if ($application === null) {
        // Handle the situation when the model is not found, e.g., return an error response
        dd('The requested application does not exist.');
    }

    $application->level_id = 1;
    $application->status_id = 3;
    $application->remark = $remarkValue;
    if ($application->save()) {
        // The model is updated
        return ['success' => true, 'message' => 'The application has been approved.'];
    } else {
        // The model is not updated, handle the error, e.g., return an error response
        return ['success' => false, 'message' => 'There was an error approving the application.'];
    }
}

public function actionDissaproveApplication()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    $applicationId = Yii::$app->request->post('applicationId');
    $remarkValue = Yii::$app->request->post('remarkValue');

    
    $application = CsCarstickerApproval::findOne(['application_id' => $applicationId]);

    if ($application === null) {
        // Handle the situation when the model is not found, e.g., return an error response
        dd('The requested application does not exist.');
    }

    $application->level_id = 0;
    $application->status_id = 6;
    $application->remark = $remarkValue;
    if ($application->save()) {
        // The model is updated
        return ['success' => true, 'message' => 'The application has been approved.'];
    } else {
        // The model is not updated, handle the error, e.g., return an error response
        return ['success' => false, 'message' => 'There was an error approving the application.'];
    }

}



}