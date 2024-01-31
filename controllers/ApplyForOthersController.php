<?php

namespace app\modules\stickers\controllers;

use app\modules\stickers\models\CsCarstickerApplication;
use app\modules\stickers\models\CsCarstickerApplicationSearch;
use app\modules\setup\models\CsApplicationType;
use app\modules\approval\models\CsCarstickerApproval;
use app\modules\stickers\models\CsCarstickerApplicationSearchDirector;
use app\modules\stickers\models\CsApplicationDocument;
use yii\web\Response;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\growl\Growl;
use yii\base\DynamicModel;

/**
 * ApplyForOthersController implements the CRUD actions for CsCarstickerApplication model.
 */
class ApplyForOthersController extends Controller
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

        return $this->render('index', [
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
        return $this->render('view', [
            'model' => $this->findModel($application_id),
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
        // USER ID TO USE TEMPORARILY
        $temporaryUserId = 1;

        if ($this->request->isPost) {

            // $model->user_id = Yii::$app->user->id;
            $model->user_id = $temporaryUserId;
                $model = DynamicModel::validateData(compact('applicationDocument'), [
                    [['application_id', 'document_id'], 'required'],
                    [['application_id', 'document_id'], 'default', 'value' => null],
                    [['application_id', 'document_id'], 'integer'],
                    [['document_location'], 'string', 'max' => 100],
                    [['file'], 'file'], 
            
                    [['application_name', 'application_description'], 'required'], 
                ]);
            if ($model->load($this->request->post())) {
                $vehicleDetails = CsCarstickerApplication::find()->where([
                    'vehicle_regno' => $model->vehicle_regno,
                ])->one();
                if (!$vehicleDetails) {
                    if (!$model->save()) {
                        Yii::$app->session->setFlash('', [
                            'type' => Growl::TYPE_DANGER,
                            'icon' => 'bi bi-x-circle-fill',
                            'title' => 'Error',
                            'showSeparator' => true,
                            'delay' => 3000,
                            'message' => 'Could not add Vehicle details. Please try again.',
                            'closeButton' => null,
                        ]);
                    }

                    Yii::$app->session->setFlash('growl', [
                        'type' => Growl::TYPE_SUCCESS,
                        'icon' => 'bi bi-check-lg',
                        'title' => 'Success!',
                        'message' => 'Vehicle Details Added successfully. Please proceed to attach documents.',
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

                    return $this->redirect(['view', 'application_id' => $model->application_id]);

                } else {
                    Yii::$app->session->setFlash('', [
                        'type' => Growl::TYPE_DANGER,
                        'icon' => 'bi bi-exclamation-circle-fill',
                        'message' => 'There exists a car with the same Record. Please provide unique Vehicle Reg No.',
                        'closeButton' => null,
                        'title' => 'Notification',
                        'duration' => 5000,
                    ]);
                    return $this->redirect(['index']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'application_id' => $model->application_id]);
        }

        return $this->render('update', [
            'model' => $model,
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
}
