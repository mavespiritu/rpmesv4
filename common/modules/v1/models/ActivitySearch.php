<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Activity;

/**
 * ActivitySearch represents the model behind the search form of `common\modules\v1\models\Activity`.
 */
class ActivitySearch extends Activity
{   
    public $papTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pap_id'], 'integer'],
            [['code', 'title', 'description', 'papTitle'], 'safe'],
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
        $query = Activity::find()
        ->joinWith('pap')
        ->joinWith('pap.identifier')
        ->joinWith('pap.identifier.subProgram')
        ->joinWith('pap.identifier.subProgram.program')
        ->joinWith('pap.identifier.subProgram.program.organizationalOutcome')
        ->joinWith('pap.identifier.subProgram.program.organizationalOutcome.costStructure');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'papTitle' => [
                    'asc' => ['concat(
                        ppmp_cost_structure.code,"",
                        ppmp_organizational_outcome.code,"",
                        ppmp_program.code,"",
                        ppmp_sub_program.code,"",
                        ppmp_identifier.code,"",
                        ppmp_pap.code
                        ," - ",
                        ppmp_pap.title)' => SORT_ASC],
                    'desc' => ['concat(
                        ppmp_cost_structure.code,"",
                        ppmp_organizational_outcome.code,"",
                        ppmp_program.code,"",
                        ppmp_sub_program.code,"",
                        ppmp_identifier.code,"",
                        ppmp_pap.code
                        ," - ",
                        ppmp_pap.title)' => SORT_DESC],
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
        ]);

        $query->andFilterWhere(['like', 'ppmp_activity.code', $this->code])
            ->andFilterWhere(['like', 'concat(
                        ppmp_cost_structure.code,"",
                        ppmp_organizational_outcome.code,"",
                        ppmp_program.code,"",
                        ppmp_sub_program.code,"",
                        ppmp_identifier.code,"",
                        ppmp_pap.code
                        ," - ",
                        ppmp_pap.title)', $this->papTitle])
            ->andFilterWhere(['like', 'ppmp_activity.title', $this->title])
            ->andFilterWhere(['like', 'ppmp_activity.description', $this->description]);

        return $dataProvider;
    }
}
