<?php

namespace app\modules\stickers\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stickers\models\CsCarstickerApplication;

/**
 * CsCarstickerApplicationSearch represents the model behind the search form of `app\modules\stickers\models\CsCarstickerApplication`.
 */
class CsCarstickerApplicationSearchDirector extends CsCarstickerApplication
{
    public $status_id;
    public $level_id;    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'application_type'], 'integer'],
            [['application_ref_no', 'vehicle_regno', 'application_date', 'status_id', 'level_id'], 'safe'],
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
$loggedInUser = Yii::$app->user->identity;
    $query = CsCarstickerApplication::find();
    $query->select(['cs_carsticker_application.*', 'aa.status_id AS status_id']);
    $query->leftJoin('cs_carsticker_approval aa', 
    'cs_carsticker_application.application_id = aa.application_id');


    $query->andWhere(['aa.status_id' => 4, 'aa.level_id' => 1]);

    // Add additional conditions based on search parameters
    $query->andFilterWhere([
        'cs_carsticker_application.application_id' => $this->application_id,
        'cs_carsticker_application.application_date' => $this->application_date,
        'cs_carsticker_application.application_type' => $this->application_type,
    ]);

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
        // Uncomment the following line if you do not want to return any records when validation fails
        // $query->where('0=1');
        return $dataProvider;
    }

    $query->andFilterWhere([
        'cs_carsticker_application.application_id' => $this->application_id,
        'cs_carsticker_application.application_date' => $this->application_date,
        'cs_carsticker_application.application_type' => $this->application_type,
    ]);

    $dataProvider->setSort([
        'attributes' => [
            'application_ref_no',
            'vehicle_regno',
            'application_date',
            'application_type',
            'status_id' => [
                'asc' => ['aa.status_id' => SORT_ASC],
                'desc' => ['aa.status_id' => SORT_DESC],
            ],
            'level_id' => [
                'asc' => ['aa.level_id' => SORT_ASC],
                'desc' => ['aa.level_id' => SORT_DESC],
            ],            
        ]
    ]);

    $query->andFilterWhere(['like', 'cs_carsticker_application.application_ref_no', $this->application_ref_no])
        ->andFilterWhere(['like', 'cs_carsticker_application.vehicle_regno', $this->vehicle_regno]);

    $query->andFilterWhere(['aa.status_id' => $this->status_id]);
    $query->andFilterWhere(['aa.level_id' => $this->level_id]);
    

    return $dataProvider;
}
}
