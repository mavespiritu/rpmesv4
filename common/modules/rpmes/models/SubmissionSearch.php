<?php

namespace common\modules\rpmes\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\Submission;

/**
 * SubmissionSearch represents the model behind the search form of `common\modules\rpmes\models\Submission`.
 */
class SubmissionSearch extends Submission
{
    public $globalSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'agency_id', 'submitted_by'], 'integer'],
            [['report', 'year', 'quarter', 'semester', 'globalSearch'], 'safe'],
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
        $query = Submission::find()
                ->joinWith('agency');

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
            ->orFilterWhere(['year' => $this->globalSearch])
            ->orFilterWhere(['like', 'agency.code', $this->globalSearch])
            ->orFilterWhere(['like', 'quarter', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(year," ",agency.code)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(year," ",quarter)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(agency.code," ",year)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(agency.code," ",quarter)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(quarter," ",agency.code)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(quarter," ",year)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(year," ",agency.code," ",quarter)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(year," ",quarter," ",agency.code)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(agency.code," ",year," ",quarter)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(agency.code," ",quarter," ",year)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(quarter," ",agency.code," ",year)', $this->globalSearch])
            ->orFilterWhere(['like', 'CONCAT(quarter," ",year," ",agency.code)', $this->globalSearch]);

        if(Yii::$app->user->can('AgencyUser'))
        {
            $query->andFilterWhere([
                'report' => $this->report,
                'agency_id' => $this->agency_id,
            ]);

        }else
        {
            $query->andFilterWhere([
                'report' => $this->report,
            ]);
        }

        $query = $query->orderBy(['year' => SORT_DESC, 'quarter' => SORT_DESC]);

        return $dataProvider;
    }
}
