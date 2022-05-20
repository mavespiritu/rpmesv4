<?php

namespace common\modules\rpmes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\rpmes\models\RdpChapterOutcome;

/**
 * RdpChapterOutcomeSearch represents the model behind the search form of `common\modules\rpmes\models\RdpChapterOutcome`.
 */
class RdpChapterOutcomeSearch extends RdpChapterOutcome
{
    public $rdpChapterTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'rdp_chapter_id'], 'integer'],
            [['level', 'rdpChapterTitle', 'title', 'description'], 'safe'],
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
        $query = RdpChapterOutcome::find()
            ->joinWith('rdpChapter');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'rdpChapterTitle' => [
                    'asc' => ['concat("Chapter ",rdp_chapter.chapter_no,": ",rdp_chapter.title)' => SORT_ASC],
                    'desc' => ['concat("Chapter ",rdp_chapter.chapter_no,": ",rdp_chapter.title)' => SORT_DESC],
                ],
                'level',
                'title',
                'description',
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
            'rdp_chapter_id' => $this->rdp_chapter_id,
        ]);

        $query->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'concat("Chapter ",rdp_chapter.chapter_no,": ",rdp_chapter.title)', $this->rdpChapterTitle])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
