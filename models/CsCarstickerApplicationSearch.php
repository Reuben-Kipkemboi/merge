<?php

namespace app\modules\stickers\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stickers\models\CsCarstickerApplication;

/**
 * CsCarstickerApplicationSearch represents the model behind the search form of `app\modules\stickers\models\CsCarstickerApplication`.
 */
class CsCarstickerApplicationSearch extends CsCarstickerApplication
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'application_type'], 'integer'],
            [['application_ref_no', 'vehicle_regno', 'application_date'], 'safe'],
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
     * @param array $additionalConditions Additional conditions to apply to the query
     *
     * @return ActiveDataProvider
     */
    public function search($params, $additionalConditions = [])
    {
        $query = CsCarstickerApplication::find()
            ->leftJoin('csmis.cs_carsticker_qrcode', 'csmis.cs_carsticker_application.application_id = csmis.cs_carsticker_qrcode.application_id')
            ->leftJoin('csmis.cs_carsticker_approval', 'csmis.cs_carsticker_application.application_id = csmis.cs_carsticker_approval.application_id')  // Update the join condition
            ->groupBy('csmis.cs_carsticker_application.application_id');

        // Add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Grid filtering conditions
        $query->andFilterWhere([
            'application_id' => $this->application_id,
            'application_date' => $this->application_date,
            'application_type' => $this->application_type,
        ]);

        $query->andFilterWhere(['ilike', 'application_ref_no', $this->application_ref_no])
            ->andFilterWhere(['ilike', 'vehicle_regno', $this->vehicle_regno]);

        // Apply additional conditions
        $query->andWhere($additionalConditions);

        // Filter expired applications
        if (isset($params['expired']) && $params['expired']) {
            $query->andWhere(['<=', 'csmis.cs_carsticker_qrcode.expiry_date', date('Y-m-d')]);
        }

        // Add a condition for status_id
        if (isset($additionalConditions['status_id'])) {
            $query->andWhere(['csmis.cs_carsticker_approval.status_id' => $additionalConditions['status_id']]);
        }

        return $dataProvider;
    }
}
