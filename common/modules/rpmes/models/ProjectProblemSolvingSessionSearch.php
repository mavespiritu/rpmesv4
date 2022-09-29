<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\ProjectProblemSolvingSession;

/**
 * ProjectProblemSolvingSessionSearch represents the model behind the search form of `common\modules\rpmes\models\ProjectProblemSolvingSession`.
 */
class ProjectProblemSolvingSessionSearch extends ProjectProblemSolvingSession
{
    public $projectTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'quarter'], 'required'],
            [['id', 'year', 'project_id', 'submitted_by'], 'integer'],
            [['quarter', 'pss_date', 'agreement_reached', 'next_step', 'projectTitle', 'date_submitted'], 'safe'],
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
        $query = ProjectProblemSolvingSession::find()
                    ->joinWith('project');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'year',
                'quarter',
                'projectTitle' => [
                    'asc' => ['concat(project.title)' => SORT_ASC],
                    'desc' => ['concat(project.title)' => SORT_DESC],
                ],
                'pss_date'
            ]
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
            'pss_date' => $this->pss_date,
            'submitted_by' => $this->submitted_by,
            'date_submitted' => $this->date_submitted,
        ]);

        $query->andFilterWhere(['like', 'quarter', $this->quarter])
            ->andFilterWhere(['like', 'agreement_reached', $this->agreement_reached])
            ->andFilterWhere(['like', 'next_step', $this->next_step]);

        return $dataProvider;
    }
}
