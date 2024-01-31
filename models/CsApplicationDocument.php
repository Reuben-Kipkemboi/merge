<?php

namespace app\modules\stickers\models;

use app\modules\setup\models\CsRequiredDocument;
use app\modules\stickers\models\CsCarstickerApplication;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "csmis.cs_application_document".
 *
 * @property int $application_doc_id
 * @property int $application_id
 * @property string $document_location
 * @property int $document_id
 * @property UploadedFile $file
 */
class CsApplicationDocument extends \yii\db\ActiveRecord
{
    public $file; // Declare the file property

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'csmis.cs_application_document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id'], 'required'],
            [['application_id'], 'default', 'value' => null],
            [['application_id'], 'integer'],
            [['document_location'], 'string', 'max' => 100],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsCarstickerApplication::class, 'targetAttribute' => ['application_id' => 'application_id']],
            // [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsRequiredDocument::class, 'targetAttribute' => ['document_id' => 'document_id']],
            // [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => CsRequiredDocument::class, 'targetAttribute' => ['document_id' => 'document_id']],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf'],
            // [['file'], 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'application_doc_id' => 'Application Doc ID',
            'application_id' => 'Application ID',
            'document_location' => 'Document Location',
            'document_id' => 'Document ID',
        ];
    }

    public function getApplication()
    {
        return $this->hasOne(CsCarstickerApplication::class, ['application_id' => 'application_id']);
    }







}
