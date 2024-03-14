<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\ProjectException;

/**
 * ProjectFindingSearch represents the model behind the search form of `common\modules\rpmes\models\ProjectFinding`.
 */
class ProjectExceptionSearch extends ProjectException
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year', 'project_id'], 'integer'],
            [['quarter', 'issue_status', 'findings', 'causes', 'action_taken', 'recommendations', 'for_npmc_action', 'requested_action', 'globalSearch'], 'safe'],
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
        $query = ProjectException::find()
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
        ->orFilterWhere(['like', 'project_exception.year', $this->globalSearch])
        ->orFilterWhere(['like', 'project_exception.quarter', $this->globalSearch])
        ->orFilterWhere(['like', 'issue_status', $this->globalSearch])
        ->orFilterWhere(['like', 'findings', $this->globalSearch])
        ->orFilterWhere(['like', 'causes', $this->globalSearch])
        ->orFilterWhere(['like', 'action_taken', $this->globalSearch])
        ->orFilterWhere(['like', 'recommendations', $this->globalSearch])
        ->orFilterWhere(['like', 'for_npmc_action', $this->globalSearch])
        ->orFilterWhere(['like', 'requested_action', $this->globalSearch]);

        $query = Yii::$app->user->can('AgencyUser') ? $query->andWhere(['project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $query;
        $query = $query->orderBy(['project_exception.quarter' => SORT_DESC, 'project_exception.year' => SORT_DESC, 'project.project_no' => SORT_DESC]);

        return $dataProvider;
    }
}
