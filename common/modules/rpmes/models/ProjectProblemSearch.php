<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\ProjectProblem;

/**
 * ProjectProblemSearch represents the model behind the search form of `common\modules\rpmes\models\ProjectProblem`.
 */
class ProjectProblemSearch extends ProjectProblem
{
    public $projectTitle;
    public $sectorTitle;
    public $subSectorTitle;
    public $location;
    public $allocationTotal;
    public $agency;
    public $projectBarangays;
    public $projectCitymuns;
    public $projectProvinces;
    public $projectRegions;
    public $globalSearch;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'submitted_by','year'], 'integer'],
            [['year', 'nature', 'detail', 'strategy', 'responsible_entity', 'lesson_learned', 'globalSearch'], 'safe'],
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
        $query = ProjectProblem::find()
                ->joinWith('project')
                ->joinWith('project.sector')
                ->joinWith('project.modeOfImplementation');
                ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /* $dataProvider->setSort([
            'attributes' => [
                'id',
                'year',
                'quarter',
                'projectTitle' => [
                    'asc' => ['concat(project.title)' => SORT_ASC],
                    'desc' => ['concat(project.title)' => SORT_DESC],
                ],
                'sectorTitle' => [
                    'asc' => ['concat(sector.title)' => SORT_ASC],
                    'desc' => ['concat(sector.title)' => SORT_DESC],
                ],
                'subSectorTitle' => [
                    'asc' => ['concat(sub_sector.title)' => SORT_ASC],
                    'desc' => ['concat(sub_sector.title)' => SORT_DESC],
                ],
                'projectBarangays' => [
                    'asc' => ['concat(tblbarangay.barangay_m)' => SORT_ASC],
                    'desc' => ['concat(tblbarangay.barangay_m)' => SORT_DESC],
                ],
                'projectCitymuns' => [
                    'asc' => ['concat(tblcitymun.citymun_m)' => SORT_ASC],
                    'desc' => ['concat(tblcitymun.citymun_m)' => SORT_DESC],
                ],
                'projectProvinces' => [
                    'asc' => ['concat(tblprovince.province_m)' => SORT_ASC],
                    'desc' => ['concat(tblprovince.province_m)' => SORT_DESC],
                ],
                'projectRegions' => [
                    'asc' => ['concat(tblregion.region_m)' => SORT_ASC],
                    'desc' => ['concat(tblregion.region_m)' => SORT_DESC],
                ],
                'agency' => [
                    'asc' => ['concat(agency.code)' => SORT_ASC],
                    'desc' => ['concat(agency.code)' => SORT_DESC],
                ],
                'nature',
                'detail',
                'strategy',
                'salutation',
                'responsible_entity',
                'lesson_learned',
            ]
        ]); */

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
        ->orFilterWhere(['like', 'project_problem.year', $this->globalSearch])
        ->orFilterWhere(['like', 'nature', $this->globalSearch])
        ->orFilterWhere(['like', 'detail', $this->globalSearch])
        ->orFilterWhere(['like', 'strategy', $this->globalSearch])
        ->orFilterWhere(['like', 'responsible_entity', $this->globalSearch])
        ->orFilterWhere(['like', 'lesson_learned', $this->globalSearch]);

        $query = Yii::$app->user->can('AgencyUser') ? $query->andWhere(['project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $query;
        $query = $query->orderBy(['project_problem.id' => SORT_DESC]);

        return $dataProvider;
    }
}
