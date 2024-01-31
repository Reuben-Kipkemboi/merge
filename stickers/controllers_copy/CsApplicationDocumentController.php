<?php

namespace app\modules\stickers\controllers;


use app\modules\stickers\models\CsApplicationDocument;
use app\modules\stickers\models\CsApplicationDocumentSearch;
use app\modules\approval\models\CsCarstickerApproval;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use kartik\growl\Growl;

/**
 * CsApplicationDocumentController implements the CRUD actions for CsApplicationDocument model.
 */
class CsApplicationDocumentController extends Controller
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
    public $level_id;

    /**
     * Lists all CsApplicationDocument models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CsApplicationDocumentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CsApplicationDocument model.
     * @param int $application_doc_id Application Doc ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($application_doc_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($application_doc_id),
        ]);
    }

    /**
     * Creates a new CsApplicationDocument model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */


    public function actionCreate($application_id)
    {
        $model = new CsApplicationDocument(['application_id' => $application_id]);

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {
                $model->file = UploadedFile::getInstance($model, 'file');

                if ($model->file) {
                    $fileName = $model->document_id;
                    $extension = $model->file->extension;

                    // Save the file
                    $model->file->saveAs(Yii::getAlias('@webroot') . '/uploads/documents/' . $fileName . '.' . $extension);

                    $model->document_location = 'uploads/documents/' . $fileName . '.' . $extension;
                    $model->save(false);
                    $this->forwardApplicationForApproval($application_id);
                    Yii::$app->session->setFlash('growl', [
                        'type' => Growl::TYPE_SUCCESS,
                        'icon' => 'bi bi-check-lg',
                        'title' => 'Success!',
                        'message' => 'Supporting documents added successfully. Application forwarded for approval.',
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

                    // Yii::$app->session->setFlash('success', 'Supporting documents added successfully. Application forwarded for approval.');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', 'File upload failed.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Failed to save vehicle details. Errors: ' . json_encode($model->errors));
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    // Forwading function for approval
    protected function forwardApplicationForApproval($application_id)
    {
        // Check conditions for approval
        if ($this->checkApprovalConditions($application_id)) {
            // Create a new Approval record
            $approval = new CsCarstickerApproval([
                'approval_date' => date('Y-m-d H:i:s'),
                // 'level_id' => 1,
                'status_id' => 1,
                // 'user_id' => Yii::$app->user->id,
                'user_id' => 22,
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



    /**
     * Updates an existing CsApplicationDocument model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $application_doc_id Application Doc ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionUpdate($application_doc_id)
    // {
    //     $model = $this->findModel($application_doc_id);

    //     if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
    //         return $this->redirect(['index']);
    //     }

    //     return $this->render('update', [
    //         'model' => $model,
    //         'documentModel' => $documentModel
    //     ]);
    // }
    public function actionUpdate($application_doc_id)
    {
        $model = $this->findModel($application_doc_id);
        $documentModel = new CsApplicationDocument(); // Create a new instance

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'documentModel' => $documentModel, // Pass the instance to the view
        ]);
    }

    /**
     * Deletes an existing CsApplicationDocument model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $application_doc_id Application Doc ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($application_doc_id)
    {
        $this->findModel($application_doc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Check conditions for approval.
     * @param int $application_id
     * @return bool Whether conditions are met for approval.
     */
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


    protected function findModel($application_doc_id)
    {
        if (($model = CsApplicationDocument::findOne(['application_doc_id' => $application_doc_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
