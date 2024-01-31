<?php

namespace app\modules\stickers\controllers;

use app\modules\stickers\models\CsCarstickerApplication;
use app\modules\stickers\models\CsCarstickerApplicationSearch;
use app\modules\approval\models\CsCarstickerApproval;
use app\modules\stickers\models\CsApplicationDocument;
use app\modules\stickers\models\CarstickerQrcode;



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
                $isUpdateDisabled = true;
                break;
            }
        }

        // Expired applications
        $dataProviderExpired = $searchModel->search(['expired' => true] + Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderExpired' => $dataProviderExpired,
            'isUpdateDisabled' => $isUpdateDisabled,
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
        $temporaryUserId = 7;

        // Check if the user has two or more stickers with validity_id 5
        $activeStickerCount = CarstickerQrcode::find()
            ->where([
                'user_id' => $temporaryUserId,
                'validity_id' => 5,
            ])
            ->count();

        // Check if the user has more than two active stickers
        if ($activeStickerCount >= 2) {
            Yii::$app->session->setFlash('growl', [
                'type' => Growl::TYPE_DANGER,
                'icon' => 'bi bi-exclamation-triangle-fill',
                'title' => 'Failed Application!',
                'message' => 'You cannot have more than Two Valid Car stickers at a Time.',
                'showSeparator' => true,
                'delay' => 5000,
                'pluginOptions' => [
                    'showProgressbar' => true,
                    'placement' => [
                        'from' => 'top',
                        'align' => 'center',
                    ],
                ],
            ]);

            
            return $this->redirect(['index']);
        }


        if ($this->request->isPost) {
            $model->load($this->request->post());
            // $model->user_id = Yii::$app->user->id;
            $model->user_id = $temporaryUserId;
            $documentModel->file = UploadedFile::getInstance($documentModel, 'file');

            if ($model->validate() && $model->save()) {
                if ($documentModel->file) {
                    $fileName = $model->application_ref_no . '_' . 'application_' . $model->application_id;
                    $extension = $documentModel->file->extension;
                    $filePath = 'uploads/' . $fileName . '.' . $extension;
                    $documentModel->document_location = $filePath;
                    $documentModel->application_id = $model->application_id;

                    if ($documentModel->validate() && $documentModel->save()) {
                        // Forward for approval after saving the model and document
                        $this->forwardApplicationForApproval($model->application_id);

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
    public function actionUpdate($application_id)
    {
        $model = $this->findModel($application_id);
        $documentModel = new CsApplicationDocument();

        $latestApproval = CsCarstickerApproval::find()
            ->where(['application_id' => $model->application_id])
            ->orderBy(['approval_date' => SORT_DESC])
            ->one();

        // This is to check if the latest approval status is one of the statuses that should disable the update
        $disableUpdateStatuses = [4, 5];
        $isUpdateDisabled = $latestApproval && in_array($latestApproval->status_id, $disableUpdateStatuses);

        if ($this->request->isPost) {
            $postData = $this->request->post();

            // Load the application model with the posted data
            if ($model->load($postData)) {
                // Check if the application is in a status that allows updates
                if ($latestApproval && ($latestApproval->status_id == 1 || $latestApproval->status_id == 2 || $latestApproval->status_id == 3)) {
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
                'user_id' => 22,  // @Reuben Remember to make this dynamic after authentication feature
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

    // Renewing the application incase expired -->
    // Get details from the QR code table to  see the expiry date and if expired --> forward to director to approve with the previous details
    public function actionRenewApplication($qrcodeValue)
    {
        // Retrieve QR code details
        $qrcodeDetails = CarstickerQrcode::find()
            ->where(['qrcode_value' => $qrcodeValue])
            ->with('carstickerApplication')
            ->one();

        if ($qrcodeDetails) {
            // Use CsCarstickerApplicationSearch to search for the application details
            $searchModel = new CsCarstickerApplicationSearch();
            $searchModel->application_id = $qrcodeDetails->carstickerApplication->application_id;

            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            $applicationDetails = $dataProvider->getModels()[0] ?? null;

            // First Check if the application is expired
            if ($applicationDetails && strtotime($qrcodeDetails->expiry_date) < time()) {
                // Create a new application record with the details of the previous application
                $newApplication = new CsCarstickerApplication([
                    'application_ref_no' => $applicationDetails->application_ref_no,
                    'vehicle_regno' => $applicationDetails->vehicle_regno,
                    'application_date' => date('Y-m-d H:i:s'),
                    'application_type' => $applicationDetails->application_type,
                    'user_id' => Yii::$app->user->id, // to use when a user is logged in
                    // 'user_id' => 1
                ]);

                // Save the new application record
                if ($newApplication->save()) {
                    // Retrieve the documents associated with the previous application
                    $previousDocuments = CsApplicationDocument::find()->where(['application_id' => $applicationDetails->application_id])->all();

                    // Create new document records for the new application
                    foreach ($previousDocuments as $previousDocument) {
                        $newDocument = new CsApplicationDocument([
                            'file' => $previousDocument->file,
                            'document_location' => $previousDocument->document_location,
                            'application_id' => $newApplication->application_id,
                        ]);

                        // Save the new document record
                        $newDocument->save();
                    }

                    Yii::$app->session->setFlash('success', 'Application renewed successfully.');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to renew the application. Please check the form for errors.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'This application cannot be renewed.');
            }
        }
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

}