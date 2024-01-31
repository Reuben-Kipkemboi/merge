<?php

namespace app\modules\stickers\models;

use Yii;

/**
 * This is the model class for table "csmis.cs_carsticker_approval".
 *
 * @property int $approval_id
 * @property string $approval_date
 * @property int $level_id
 * @property int $status_id
 * @property int $user_id
 * @property string|null $remark
 * @property int $application_id
 */
class CsCarstickerApproval extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'csmis.cs_carsticker_approval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approval_date', 'level_id', 'status_id', 'user_id', 'application_id'], 'required'],
            [['approval_date'], 'safe'],
            [['level_id', 'status_id', 'user_id', 'application_id'], 'default', 'value' => null],
            [['level_id', 'status_id', 'user_id', 'application_id'], 'integer'],
            [['remark'], 'string', 'max' => 100],
            [['level_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsApprovalLevel::class, 'targetAttribute' => ['level_id' => 'level_id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsApprovalStatus::class, 'targetAttribute' => ['status_id' => 'status_id']],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsCarstickerApplication::class, 'targetAttribute' => ['application_id' => 'application_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'approval_id' => 'Approval ID',
            'approval_date' => 'Approval Date',
            'level_id' => 'Level ID',
            'status_id' => 'Status ID',
            'user_id' => 'User ID',
            'remark' => 'Remark',
            'application_id' => 'Application ID',
        ];
    }
}
