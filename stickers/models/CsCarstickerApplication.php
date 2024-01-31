<?php

namespace app\modules\stickers\models;

use Yii;
use app\modules\setup\models\CsApplicationType;
use app\modules\setup\models\CsRequiredDocument;

/**
 * This is the model class for table "csmis.cs_carsticker_application".
 *
 * @property int $application_id
 * @property string $application_ref_no
 * @property string $vehicle_regno
 * @property string $application_date
 * @property int $application_type
 * @property int $file
 */
class CsCarstickerApplication extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'csmis.cs_carsticker_application';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_ref_no', 'vehicle_regno', 'application_date', 'application_type'], 'required'],
            [['application_date'], 'safe'],
            [['application_type'], 'default', 'value' => null],
            [['application_type'], 'integer'],
            [['application_ref_no'], 'string', 'max' => 20],
            [['vehicle_regno'], 'string', 'max' => 7],
            [['application_type'], 'exist', 'skipOnError' => true, 'targetClass' => CsApplicationType::class, 'targetAttribute' => ['application_type' => 'type_id']],
            [['file'], 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'application_id' => 'Application ID',
            'application_ref_no' => 'Application Ref No',
            'vehicle_regno' => 'Vehicle Regno',
            'application_date' => 'Application Date',
            'application_type' => 'Application Type',
            // 'file' => 'Attachment File',
        ];
    }

    public function getApplicationType()
    {
        return $this->hasOne(CsApplicationType::className(), ['type_id' => 'application_type']);
    }

    public function getApplicationDocuments()
    {
        return $this->hasMany(CsApplicationDocument::class, ['application_id' => 'application_id']);
    }

    
    public function getApproval()
    {
        return $this->hasOne(CsCarstickerApproval::class, ['application_id' => 'application_id']);
    }

    public function getLatestApproval()
    {
        return $this->hasOne(CsCarstickerApproval::class, ['application_id' => 'application_id'])
            ->orderBy(['approval_date' => SORT_DESC]);
    }

    public function isUpdateDisabled() {
        // Disable update if status is 3, 4, or 5
        return in_array($this->latestApproval->status_id, [3, 4, 5]);
    }
    

    
  


}
