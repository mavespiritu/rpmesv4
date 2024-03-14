<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\ProjectEndorsement;

/**
 * ProjectFindingSearch represents the model behind the search form of `common\modules\rpmes\models\ProjectFinding`.
 */
class ProjectEndorsementSearch extends ProjectEndorsement
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year', 'project_id'], 'integer'],
            [['quarter', 'npmc_action', 'globalSearch'], 'safe'],
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
        $query = ProjectEndorsement::find()
                ->joinWith('project')
                ;

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
        ->orFilterWhere(['like', 'project_endorsement.year', $this->globalSearch])
        ->orFilterWhere(['like', 'project_endorsement.quarter', $this->globalSearch])
        ->orFilterWhere(['like', 'npmc_action', $this->globalSearch]);

        $query = Yii::$app->user->can('AgencyUser') ? $query->andWhere(['project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $query;
        $query = $query->orderBy(['project_endorsement.quarter' => SORT_DESC, 'project_endorsement.year' => SORT_DESC, 'project.project_no' => SORT_DESC]);

        return $dataProvider;
    }
}
