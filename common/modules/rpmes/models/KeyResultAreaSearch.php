<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\KeyResultArea;

/**
 * KeyResultAreaSearch represents the model behind the search form of `common\modules\rpmes\models\KeyResultArea`.
 */
class KeyResultAreaSearch extends KeyResultArea
{
    public $categoryTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'kra_no'], 'integer'],
            [['title', 'categoryTitle', 'description'], 'safe'],
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
        $query = KeyResultArea::find()
            ->joinWith('category');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'categoryTitle' => [
                    'asc' => ['category.title' => SORT_ASC],
                    'desc' => ['category.title' => SORT_DESC],
                ],
                'kra_no',
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
            'category_id' => $this->category_id,
            'kra_no' => $this->kra_no,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'category.title', $this->categoryTitle])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
