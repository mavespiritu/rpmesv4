<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\SubSectorPerSector;

/**
 * SubSectorPerSectorSearch represents the model behind the search form of `common\modules\rpmes\models\SubSectorPerSector`.
 */
class SubSectorPerSectorSearch extends SubSectorPerSector
{
    public $sectorTitle;
    public $subSectorTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sector_id', 'sub_sector_id'], 'integer'],
            [['sectorTitle', 'subSectorTitle'], 'safe'],
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
        $query = SubSectorPerSector::find()
            ->joinWith('sector')
            ->joinWith('subSector');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'sectorTitle' => [
                    'asc' => ['concat(sector.title)' => SORT_ASC],
                    'desc' => ['concat(sector.title)' => SORT_DESC],
                ],
                'subSectorTitle' => [
                    'asc' => ['concat(sub_sector.title)' => SORT_ASC],
                    'desc' => ['concat(sub_sector.title)' => SORT_DESC],
                ],
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
            'sector_id' => $this->sector_id,
            'sub_sector_id' => $this->sub_sector_id,
        ]);

        $query->andFilterWhere(['like', 'sector.title', $this->sectorTitle])
              ->andFilterWhere(['like', 'sub_sector.title', $this->subSectorTitle]);

        return $dataProvider;
    }
}
