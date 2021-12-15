<?php

namespace common\modules\procurement\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\procurement\models\PrStockInventory;

/**
 * PrStockInventorySearch represents the model behind the search form of `common\modules\procurement\models\PrStockInventory`.
 */
class PrStockInventorySearch extends PrStockInventory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['stock_code', 'article', 'description', 'unit'], 'safe'],
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
        $query = PrStockInventory::find();

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
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'stock_code', $this->stock_code])
            ->andFilterWhere(['like', 'article', $this->article])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'unit', $this->unit]);

        return $dataProvider;
    }
}
