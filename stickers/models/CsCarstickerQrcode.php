<?php

namespace app\modules\stickers\models;

use Yii;

/**
 * This is the model class for table "csmis.cs_carsticker_qrcode".
 *
 * @property int $qrcode_id
 * @property string $qrcode_value
 * @property string $issue_date
 * @property string $expiry_date
 * @property string $location
 * @property string $serial_no
 * @property int $validity_id
 * @property int $user_id
 * @property int $application_id
 */
class CsCarstickerQrcode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'csmis.cs_carsticker_qrcode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qrcode_value', 'issue_date', 'expiry_date', 'location', 'serial_no', 'validity_id', 'user_id', 'application_id'], 'required'],
            [['issue_date', 'expiry_date'], 'safe'],
            [['validity_id', 'user_id', 'application_id'], 'default', 'value' => null],
            [['validity_id', 'user_id', 'application_id'], 'integer'],
            [['qrcode_value'], 'string', 'max' => 50],
            [['location', 'serial_no'], 'string', 'max' => 200],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsmisCsCarstickerApplication::class, 'targetAttribute' => ['application_id' => 'application_id']],
            [['validity_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsmisCsCarstickerValidity::class, 'targetAttribute' => ['validity_id' => 'validity_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qrcode_id' => 'Qrcode ID',
            'qrcode_value' => 'Qrcode Value',
            'issue_date' => 'Issue Date',
            'expiry_date' => 'Expiry Date',
            'location' => 'Location',
            'serial_no' => 'Serial No',
            'validity_id' => 'Validity ID',
            'user_id' => 'User ID',
            'application_id' => 'Application ID',
        ];
    }
}
