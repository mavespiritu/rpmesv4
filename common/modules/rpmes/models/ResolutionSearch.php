<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Resolution;

/**
 * ResolutionSearch represents the model behind the search form of `common\modules\rpmes\models\Resolution`.
 */
class ResolutionSearch extends Resolution
{
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'resolution_number'], 'integer'],
            [['resolution', 'date_approved', 'rpmc_action','quarter','year','globalSearch'], 'safe'],
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
        $query = Resolution::find();

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
            ->orFilterWhere(['like', 'resolution', $this->globalSearch])
            ->orFilterWhere(['like', 'resolution_number', $this->globalSearch])
            ->orFilterWhere(['like', 'date_approved', $this->globalSearch])
            ->orFilterWhere(['like', 'resolution_title', $this->globalSearch])
            ->orFilterWhere(['like', 'resolution_url', $this->globalSearch])
            ->orFilterWhere(['like', 'year', $this->globalSearch]);

        $query = $query->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
}
