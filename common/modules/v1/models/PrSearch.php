<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Pr;

/**
 * PrSearch represents the model behind the search form of `common\modules\v1\models\Pr`.
 */
class PrSearch extends Pr
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fund_source_id', 'fund_cluster_id'], 'integer'],
            [['pr_no', 'office_id', 'section_id', 'unit_id', 'purpose', 'requested_by', 'date_requested', 'approved_by', 'date_approved', 'type'], 'safe'],
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
        $query = Pr::find();

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
            'fund_source_id' => $this->fund_source_id,
            'fund_cluster_id' => $this->fund_cluster_id,
            'date_requested' => $this->date_requested,
            'date_approved' => $this->date_approved,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'office_id', $this->office_id])
            ->andFilterWhere(['like', 'section_id', $this->section_id])
            ->andFilterWhere(['like', 'unit_id', $this->unit_id])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'requested_by', $this->requested_by])
            ->andFilterWhere(['like', 'approved_by', $this->approved_by])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
