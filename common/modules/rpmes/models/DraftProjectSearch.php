<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Project;

/**
 * ProjectSearch represents the model behind the search form of `common\modules\rpmes\models\Project`.
 */
class DraftProjectSearch extends Project
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year', 'agency_id', 'program_id', 'sector_id', 'sub_sector_id', 'location_scope_id', 'mode_of_implementation_id', 'fund_source_id'], 'integer'],
            [['project_no', 'title', 'description', 'typhoon', 'data_type', 'period', 'start_date', 'completion_date'], 'safe'],
            [['globalSearch'], 'string'],
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
        $query = Project::find()
                ->joinWith('agency')
                ->joinWith('sector')
                ->joinWith('modeOfImplementation');

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
            ->orFilterWhere(['like', 'project_no', $this->globalSearch])
            ->orFilterWhere(['like', 'project.title', $this->globalSearch])
            ->orFilterWhere(['like', 'sector.title', $this->globalSearch])
            ->orFilterWhere(['like', 'mode_of_implementation.title', $this->globalSearch]);

        if(Yii::$app->user->can('AgencyUser'))
        {
            $query = $query->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        }

        if(Yii::$app->user->can('Administrator'))
        {
            $query
            ->orFilterWhere(['like', 'agency.code', $this->globalSearch]);
        }
                
        $query = $query->andWhere(['draft' => 'Yes']);
        $query = $query->orderBy(['id' => SORT_DESC]);


        return $dataProvider;
    }
}
