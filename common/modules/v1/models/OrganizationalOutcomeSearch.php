<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\OrganizationalOutcome;

/**
 * OrganizationalOutcomeSearch represents the model behind the search form of `common\modules\v1\models\OrganizationalOutcome`.
 */
class OrganizationalOutcomeSearch extends OrganizationalOutcome
{
    public $costStructureTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cost_structure_id'], 'integer'],
            [['code', 'title', 'description', 'costStructureTitle'], 'safe'],
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
        $query = OrganizationalOutcome::find()
        ->joinWith('costStructure');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'costStructureTitle' => [
                    'asc' => ['concat(ppmp_cost_structure.code," - ",ppmp_cost_structure.title)' => SORT_ASC],
                    'desc' => ['concat(ppmp_cost_structure.code," - ",ppmp_cost_structure.title)' => SORT_DESC],
                ],
                'code',
                'title',
                'description',
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
            'cost_structure_id' => $this->cost_structure_id,
        ]);

        $query->andFilterWhere(['like', 'ppmp_organizational_outcome.code', $this->code])
            ->andFilterWhere(['like', 'ppmp_organizational_outcome.title', $this->title])
            ->andFilterWhere(['like', 'ppmp_organizational_outcome.description', $this->description]);

        return $dataProvider;
    }
}
