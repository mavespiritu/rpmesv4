<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Ppmp;

/**
 * PpmpSearch represents the model behind the search form of `common\modules\v1\models\Ppmp`.
 */
class PpmpSearch extends Ppmp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'year', 'created_by', 'updated_by'], 'integer'],
            [['stage', 'date_created', 'date_updated'], 'safe'],
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
        $query = Ppmp::find();

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
            'office_id' => $this->office_id,
            'year' => $this->year,
            'created_by' => $this->created_by,
            'date_created' => $this->date_created,
            'updated_by' => $this->updated_by,
            'date_updated' => $this->date_updated,
        ]);

        $query->andFilterWhere(['like', 'stage', $this->stage]);

        return $dataProvider;
    }
}
