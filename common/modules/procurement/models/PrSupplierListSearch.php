<?php

namespace common\modules\procurement\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\procurement\models\PrSupplierList;

/**
 * PrSupplierListSearch represents the model behind the search form of `common\modules\procurement\models\PrSupplierList`.
 */
class PrSupplierListSearch extends PrSupplierList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'service_type_id'], 'integer'],
            [['type', 'business_name', 'business_address', 'contact_person', 'landline', 'mobile', 'email_address', 'philgeps_no', 'bir_registration', 'tin_no'], 'safe'],
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
        $query = PrSupplierList::find();

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
            'service_type_id' => $this->service_type_id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'business_name', $this->business_name])
            ->andFilterWhere(['like', 'business_address', $this->business_address])
            ->andFilterWhere(['like', 'contact_person', $this->contact_person])
            ->andFilterWhere(['like', 'landline', $this->landline])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'email_address', $this->email_address])
            ->andFilterWhere(['like', 'philgeps_no', $this->philgeps_no])
            ->andFilterWhere(['like', 'bir_registration', $this->bir_registration])
            ->andFilterWhere(['like', 'tin_no', $this->tin_no]);

        return $dataProvider;
    }
}
