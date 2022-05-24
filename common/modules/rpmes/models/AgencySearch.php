<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Agency;

/**
 * AgencySearch represents the model behind the search form of `common\modules\rpmes\models\Agency`.
 */
class AgencySearch extends Agency
{
    public $agencyTypeTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'agency_type_id'], 'integer'],
            [['code', 'agencyTypeTitle', 'title', 'head', 'salutation', 'head_designation', 'address'], 'safe'],
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
        $query = Agency::find()
                ->joinWith('agencyType');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'agencyTypeTitle' => [
                    'asc' => ['concat(agency_type.title)' => SORT_ASC],
                    'desc' => ['concat(agency_type.title)' => SORT_DESC],
                ],
                'code',
                'title',
                'head',
                'salutation',
                'head_designation',
                'address',
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
            'agency_type_id' => $this->agency_type_id,
        ]);

        $query->andFilterWhere(['like', 'agency.code', $this->code])
            ->andFilterWhere(['like', 'agency_type.title', $this->agencyTypeTitle])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'head', $this->head])
            ->andFilterWhere(['like', 'salutation', $this->salutation])
            ->andFilterWhere(['like', 'head_designation', $this->head_designation])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
