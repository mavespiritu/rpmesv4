<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Program;

/**
 * ProgramSearch represents the model behind the search form of `common\modules\v1\models\Program`.
 */
class ProgramSearch extends Program
{
    public $codeTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cost_structure_id', 'organizational_outcome_id'], 'integer'],
            [['code', 'title', 'description', 'codeTitle'], 'safe'],
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
        $query = Program::find()
        ->joinWith('costStructure')
        ->joinWith('organizationalOutcome');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'codeTitle' => [
                    'asc' => ['concat(ppmp_cost_structure.code,"",ppmp_organizational_outcome.code,"",ppmp_program.code)' => SORT_ASC],
                    'desc' => ['concat(ppmp_cost_structure.code,"",ppmp_organizational_outcome.code,"",ppmp_program.code)' => SORT_DESC],
                ],
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
            'ppmp_program.cost_structure_id' => $this->cost_structure_id,
            'ppmp_program.organizational_outcome_id' => $this->organizational_outcome_id,
        ]);

        $query->andFilterWhere(['like', 'concat(ppmp_cost_structure.code,"",ppmp_organizational_outcome.code,"",ppmp_program.code)', $this->codeTitle])
            ->andFilterWhere(['like', 'ppmp_program.title', $this->title])
            ->andFilterWhere(['like', 'ppmp_program.description', $this->description]);

        return $dataProvider;
    }
}
