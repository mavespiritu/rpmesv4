<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\PrItem;

/**
 * PrItemSearch represents the model behind the search form of `common\modules\v1\models\PrItem`.
 */
class PrItemSearch extends PrItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pr_id', 'ris_id', 'ris_item_id', 'ppmp_item_id', 'month_id', 'quantity'], 'integer'],
            [['cost'], 'number'],
            [['type'], 'safe'],
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
        $query = PrItem::find();

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
            'pr_id' => $this->pr_id,
            'ris_id' => $this->ris_id,
            'ris_item_id' => $this->ris_item_id,
            'ppmp_item_id' => $this->ppmp_item_id,
            'month_id' => $this->month_id,
            'cost' => $this->cost,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
