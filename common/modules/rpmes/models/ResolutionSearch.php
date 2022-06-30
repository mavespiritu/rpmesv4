<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Resolution;

/**
 * ResolutionSearch represents the model behind the search form of `common\modules\rpmes\models\Resolution`.
 */
class ResolutionSearch extends Resolution
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'resolution_number'], 'integer'],
            [['resolution', 'date_approved', 'rpmc_action','quarter','year'], 'safe'],
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
        $query = Resolution::find();

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
            'resolution_number' => $this->resolution_number,
            'resolution' => $this->resolution,
            'quarter' => $this->quarter,
            'year' => $this->year,
        ]);

        $query->andFilterWhere(['like', 'resolution', $this->resolution])
            ->andFilterWhere(['like', 'resolution_number', $this->resolution_number])
            ->andFilterWhere(['like', 'date_approved', $this->date_approved])
            ->andFilterWhere(['like', 'rpmc_action', $this->rpmc_action])
            ->andFilterWhere(['like', 'quarter', $this->quarter])
            ->andFilterWhere(['like', 'year', $this->year]);

        return $dataProvider;
    }
}
