<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\ProjectProblemSolvingSession;

/**
 * ProjectProblemSolvingSessionSearch represents the model behind the search form of `common\modules\rpmes\models\ProjectProblemSolvingSession`.
 */
class ProjectProblemSolvingSessionSearch extends ProjectProblemSolvingSession
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year', 'project_id', 'submitted_by'], 'integer'],
            [['quarter', 'pss_date', 'agreement_reached', 'next_step', 'date_submitted', 'issue_details', 'issue_typology', 'agencies','globalSearch'], 'safe'],
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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query
        ->orFilterWhere(['like', 'project.project_no', $this->globalSearch])
        ->orFilterWhere(['like', 'project.title', $this->globalSearch])
        ->orFilterWhere(['like', 'project_problem_solving_session.year', $this->globalSearch])
        ->orFilterWhere(['like', 'project_problem_solving_session.quarter', $this->globalSearch])
        ->orFilterWhere(['like', 'quarter', $this->globalSearch])
        ->orFilterWhere(['like', 'issue_details', $this->globalSearch])
        ->orFilterWhere(['like', 'issue_typology', $this->globalSearch])
        ->orFilterWhere(['like', 'agencies', $this->globalSearch])
        ->orFilterWhere(['like', 'agreement_reached', $this->globalSearch]);

        $query = Yii::$app->user->can('AgencyUser') ? $query->andWhere(['project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $query;
        $query = $query->orderBy(['project_problem_solving_session.id' => SORT_DESC]);

        return $dataProvider;
    }
}
