<?php

namespace app\modules\stickers\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stickers\models\CsCarstickerApplication;

class CsCarstickerApplicationSearch extends CsCarstickerApplication
{
    public function rules()
    {
        return [
            [['application_id', 'application_type'], 'integer'],
            [['application_ref_no', 'vehicle_regno', 'application_date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $statusId = null)
    {
        $query = CsCarstickerApplication::find();
        $dataProvider = new ActiveDataProvider(['query' => $query]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'application_id' => $this->application_id,
            'application_date' => $this->application_date,
            'application_type' => $this->application_type,
        ]);

        $query->andFilterWhere(['ilike', 'application_ref_no', $this->application_ref_no])
            ->andFilterWhere(['ilike', 'vehicle_regno', $this->vehicle_regno]);

            if ($statusId !== null) {
                $query->leftJoin('cs_carsticker_approval "latestApproval"', 'cs_carsticker_application.application_id = "latestApproval".application_id');
                $query->andWhere(['latestApproval.status_id' => $statusId]);
            }

        return $dataProvider;
    }
}
