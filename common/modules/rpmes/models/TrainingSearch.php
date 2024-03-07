<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Training;

/**
 * TrainingSearch represents the model behind the search form of `common\modules\rpmes\models\Training`.
 */
class TrainingSearch extends Training
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'male_participant', 'female_participant', 'submitted_by'], 'integer'],
            [['title', 'objective', 'office', 'organization', 'start_date', 'end_date', 'date_submitted','quarter','year', 'action', 'feedback', 'globalSearch'], 'safe'],
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
        $query = Training::find();

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
            ->orFilterWhere(['like', 'title', $this->globalSearch])
            ->orFilterWhere(['like', 'objective', $this->globalSearch])
            ->orFilterWhere(['like', 'office', $this->globalSearch])
            ->orFilterWhere(['like', 'action', $this->globalSearch])
            ->orFilterWhere(['like', 'office', $this->globalSearch])
            ->orFilterWhere(['like', 'organization', $this->globalSearch])
            ->orFilterWhere(['like', 'feedback', $this->globalSearch])
            ->orFilterWhere(['like', 'year', $this->globalSearch]);

        $query = $query->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
   
}
