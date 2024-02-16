<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Plan;

/**
 * PlanSearch represents the model behind the search form of `common\modules\rpmes\models\Plan`.
 */
class PlanSearch extends Plan
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'submission_id', 'project_id', 'submitted_by'], 'integer'],
            [['year', 'date_submitted', 'globalSearch'], 'safe'],
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
        $query = Plan::find()
            ->joinWith('submission')
            ->joinWith('project')
            ->joinWith('project.sector')
            ->joinWith('project.modeOfImplementation');

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

        $submission = Submission::findOne($params['id']);

        $planSubmission = Submission::findOne([
            'year' => $submission->year,
            'agency_id' => $submission->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $query
            ->orFilterWhere(['like', 'project.project_no', $this->globalSearch])
            ->orFilterWhere(['like', 'project.title', $this->globalSearch])
            ->orFilterWhere(['like', 'sector.title', $this->globalSearch])
            ->orFilterWhere(['like', 'mode_of_implementation.title', $this->globalSearch]);

        $query = $planSubmission ? $query->andWhere(['submission_id' => $planSubmission->id]) : $query->andWhere(['submission_id' => $params['id']]);
        $query = $query->orderBy(['project.id' => SORT_DESC]);

        return $dataProvider;
    }
}