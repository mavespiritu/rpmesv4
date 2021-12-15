<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Appropriation;

/**
 * AppropriationSearch represents the model behind the search form of `common\modules\v1\models\Appropriation`.
 */
class AppropriationSearch extends Appropriation
{
    public $creatorName;
    public $updaterName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['type', 'year', 'date_created', 'date_updated', 'creatorName', 'updaterName'], 'safe'],
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
        $query = Appropriation::find()
                ->joinWith('creator c')
                ->joinWith('updater u')
                ->orderBy(['year' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'year',
                'creatorName' => [
                    'asc' => ['concat(c.FIRST_M," ",c.LAST_M)' => SORT_ASC],
                    'desc' => ['concat(c.FIRST_M," ",c.LAST_M)' => SORT_DESC],
                ],
                'date_created',
                'updaterName' => [
                    'asc' => ['concat(u.FIRST_M," ",u.LAST_M)' => SORT_ASC],
                    'desc' => ['concat(u.FIRST_M," ",u.LAST_M)' => SORT_DESC],
                ],
                'date_updated',
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

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'concat(c.FIRST_M," ",c.LAST_M)', $this->creatorName])
            ->andFilterWhere(['like', 'concat(u.FIRST_M," ",u.LAST_M)', $this->updaterName])
            ->andFilterWhere(['like', 'date_created', $this->date_created])
            ->andFilterWhere(['like', 'date_updated', $this->date_updated]);

        return $dataProvider;
    }
}
