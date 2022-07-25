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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'male_participant', 'female_participant', 'submitted_by'], 'integer'],
            [['title', 'objective', 'office', 'organization', 'start_date', 'end_date', 'date_submitted','quarter','year'], 'safe'],
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
        $query = Training::find()->joinWith('submitter');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $dataProvider->setSort([
            'attributes' => [
                'submitterName' => [
                    'asc' => ['concat(user_info.FIRST_M)' => SORT_ASC],
                    'desc' => ['concat(user_info.FIRST_M)' => SORT_DESC],
                ],
                'title',
                'objective',
                'office',
                'organization',
                'start_date',
                'end_date',
                'male_participant',
                'female_participant',
                'total_participant',
                'date_submitted',
                'quarter',
                'year',
            ]
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'submitterName' => $this->submitted_by,
            'quarter'=> $this->quarter
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'objective', $this->objective])
            ->andFilterWhere(['like', 'quarter', $this->quarter])
            ->andFilterWhere(['like', 'office', $this->office])
            ->andFilterWhere(['like', 'organization', $this->organization])
            ->andFilterWhere(['like', 'quarter', $this->quarter]);

        return $dataProvider;
    }
    public function getYearsList() 
    {
        $currentYear = 2099;
        $yearFrom = 1900;
        $yearsRange = range($yearFrom, $currentYear);
        return array_combine($yearsRange, $yearsRange);
    }
}
