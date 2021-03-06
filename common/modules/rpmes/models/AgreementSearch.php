<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Agreement;

/**
 * AgreementSearch represents the model behind the search form of `common\modules\rpmes\models\Agreement`.
 */
class AgreementSearch extends Agreement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year', 'project_id', 'submitted_by'], 'integer'],
            [['quarter', 'date_of_pss', 'agreement_reached', 'next_step', 'date_submitted'], 'safe'],
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
        $query = Agreement::find();

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
            'year' => $this->year,
            'project_id' => $this->project_id,
            'date_of_pss' => $this->date_of_pss,
            'submitted_by' => $this->submitted_by,
            'date_submitted' => $this->date_submitted,
        ]);

        $query->andFilterWhere(['like', 'quarter', $this->quarter])
            ->andFilterWhere(['like', 'agreement_reached', $this->agreement_reached])
            ->andFilterWhere(['like', 'next_step', $this->next_step]);

        return $dataProvider;
    }
}
