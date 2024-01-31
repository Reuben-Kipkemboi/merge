<?php

namespace app\modules\stickers\models;

use Yii;

/**
 * This is the model class for table "csmis.cs_carsticker_history".
 *
 * @property int $history_id
 * @property int $application_id
 * @property int $attended_by
 * @property int $level_id
 * @property int $status_id
 * @property string $history_date
 * @property string|null $remark
 */
class CsCarstickerHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'csmis.cs_carsticker_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'attended_by', 'level_id', 'status_id', 'history_date'], 'required'],
            [['application_id', 'attended_by', 'level_id', 'status_id'], 'default', 'value' => null],
            [['application_id', 'attended_by', 'level_id', 'status_id'], 'integer'],
            [['history_date'], 'safe'],
            [['remark'], 'string', 'max' => 100],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsCarstickerApplication::class, 'targetAttribute' => ['application_id' => 'application_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'history_id' => 'History ID',
            'application_id' => 'Application ID',
            'attended_by' => 'Attended By',
            'level_id' => 'Level ID',
            'status_id' => 'Status ID',
            'history_date' => 'History Date',
            'remark' => 'Remark',
        ];
    }
}
