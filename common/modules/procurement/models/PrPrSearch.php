<?php

namespace common\modules\procurement\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\procurement\models\PrPr;

/**
 * PrPrSearch represents the model behind the search form of `common\modules\procurement\models\PrPr`.
 */
class PrPrSearch extends PrPr
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['entity_name', 'dts_no', 'rc_code', 'date_requested', 'fund_cluster', 'purpose', 'requester', 'requester_designation', 'approver', 'approver_designation', 'source_of_fund', 'charge_to'], 'safe'],
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
        $query = PrPr::find();

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
            'id' => $this->id,
            'date_requested' => $this->date_requested,
        ]);

        $query->andFilterWhere(['like', 'dts_no', $this->dts_no])
            ->andFilterWhere(['like', 'entity_name', $this->entity_name])
            ->andFilterWhere(['like', 'rc_code', $this->rc_code])
            ->andFilterWhere(['like', 'fund_cluster', $this->fund_cluster])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'requester', $this->requester])
            ->andFilterWhere(['like', 'requester_designation', $this->requester_designation])
            ->andFilterWhere(['like', 'approver', $this->approver])
            ->andFilterWhere(['like', 'approver_designation', $this->approver_designation])
            ->andFilterWhere(['like', 'source_of_fund', $this->source_of_fund])
            ->andFilterWhere(['like', 'charge_to', $this->charge_to]);

        return $dataProvider;
    }
}
