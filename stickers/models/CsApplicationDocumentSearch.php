<?php

namespace app\modules\stickers\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stickers\models\CsApplicationDocument;

/**
 * CsApplicationDocumentSearch represents the model behind the search form of `app\modules\stickers\models\CsApplicationDocument`.
 */
class CsApplicationDocumentSearch extends CsApplicationDocument
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_doc_id', 'application_id', 'document_id'], 'integer'],
            [['document_location'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CsApplicationDocument::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'application_doc_id' => $this->application_doc_id,
            'application_id' => $this->application_id,
            'document_id' => $this->document_id,
        ]);

        $query->andFilterWhere(['ilike', 'document_location', $this->document_location]);

        return $dataProvider;
    }
}
