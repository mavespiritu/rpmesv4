<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Project;

/**
 * ProjectSearch represents the model behind the search form of `common\modules\rpmes\models\Project`.
 */
class ProjectSearch extends Project
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year', 'agency_id', 'program_id', 'sector_id', 'sub_sector_id', 'location_scope_id', 'mode_of_implementation_id', 'fund_source_id'], 'integer'],
            [['project_no', 'title', 'description', 'typhoon', 'data_type', 'period', 'start_date', 'completion_date'], 'safe'],
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
        $query = Project::find();

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
            'agency_id' => $this->agency_id,
            'program_id' => $this->program_id,
            'sector_id' => $this->sector_id,
            'sub_sector_id' => $this->sub_sector_id,
            'location_scope_id' => $this->location_scope_id,
            'mode_of_implementation_id' => $this->mode_of_implementation_id,
            'fund_source_id' => $this->fund_source_id,
            'start_date' => $this->start_date,
            'completion_date' => $this->completion_date,
        ]);

        $query->andFilterWhere(['like', 'project_no', $this->project_no])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'typhoon', $this->typhoon])
            ->andFilterWhere(['like', 'data_type', $this->data_type])
            ->andFilterWhere(['like', 'period', $this->period]);

        return $dataProvider;
    }
}
