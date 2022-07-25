<?php

namespace common\modules\rpmes\models;

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
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'submitted_by','year'], 'integer'],
            [['nature', 'detail', 'strategy', 'responsible_entity', 'lesson_learned', 'date_submitted','projectTitle','sectorTitle','quarter','subSectorTitle','allocationTotal','projectBarangays','projectCitymuns','projectProvinces','projectRegions','agency'], 'safe'],
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
                ->joinWith('project.subSector')
                ->joinWith('project.agency')
                ->joinWith('project.projectBarangays')
                ->joinWith('project.projectCitymuns')
                ->joinWith('project.projectProvinces')
                ->joinWith('project.projectRegions')
                ->joinWith('project.projectBarangays.barangay')
                ->joinWith('project.projectCitymuns.citymun')
                ->joinWith('project.projectProvinces.province')
                ->joinWith('project.projectRegions.region')
                ;

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
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'project_problem.id' => $this->id,
            'project.id' => $this->project_id,
            'submitted_by' => $this->submitted_by,
            'date_submitted' => $this->date_submitted,
        ]);

        $query->andFilterWhere(['like', 'project_problem.id', $this->id])
            ->andFilterWhere(['like', 'sector.title', $this->sectorTitle])
            ->andFilterWhere(['like', 'project_problem.year', $this->year])
            ->andFilterWhere(['like', 'project_problem.quarter', $this->quarter])
            ->andFilterWhere(['like', 'sub_sector.title', $this->subSectorTitle])
            ->andFilterWhere(['like', 'agency.code', $this->agency])
            ->andFilterWhere(['like', 'nature', $this->nature])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'strategy', $this->strategy])
            ->andFilterWhere(['like', 'responsible_entity', $this->responsible_entity])
            ->andFilterWhere(['like', 'lesson_learned', $this->lesson_learned])
            ->andFilterWhere(['like', 'project.title', $this->projectTitle])
            ->andFilterWhere(['like', 'tblbarangay.barangay_m', $this->projectBarangays])
            ->andFilterWhere(['like', 'tblcitymun.citymun_m', $this->projectCitymuns])
            ->andFilterWhere(['like', 'tblprovince.province_m', $this->projectProvinces])
            ->andFilterWhere(['like', 'tblregion.abbreviation', $this->projectRegions])
            ;

        return $dataProvider;
    }
}
