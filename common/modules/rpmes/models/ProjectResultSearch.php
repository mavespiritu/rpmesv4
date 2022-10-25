<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\ProjectResult;

/**
 * ProjectResultSearch represents the model behind the search form of `common\modules\rpmes\models\ProjectResult`.
 */
class ProjectResultSearch extends ProjectResult
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'submitted_by'], 'integer'],
            [['objective', 'results_indicator', 'observed_results', 'deadline', 'date_submitted'], 'safe'],
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
        $query = ProjectResult::find();

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
            'project_id' => $this->project_id,
            'deadline' => $this->deadline,
            'submitted_by' => $this->submitted_by,
            'date_submitted' => $this->date_submitted,
        ]);

        $query->andFilterWhere(['like', 'objective', $this->objective])
            ->andFilterWhere(['like', 'results_indicator', $this->results_indicator])
            ->andFilterWhere(['like', 'observed_results', $this->observed_results]);

        return $dataProvider;
    }
}
