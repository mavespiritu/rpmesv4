<?php

namespace common\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Obj;

/**
 * ObjSearch represents the model behind the search form of `common\modules\v1\models\Obj`.
 */
class ObjSearch extends Obj
{
    public $objTitle;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'obj_id'], 'integer'],
            [['code', 'title', 'description', 'active', 'objTitle'], 'safe'],
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
        $query = Obj::find()
        ->joinWith('obj objParent');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'objTitle' => [
                    'asc' => ['concat(objParent.code," - ",objParent.title)' => SORT_ASC],
                    'desc' => ['concat(objParent.code," - ",objParent.title)' => SORT_DESC],
                ],
                'code',
                'title',
                'description',
                'active',
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
            'obj_id' => $this->obj_id,
        ]);

        $query->andFilterWhere(['like', 'ppmp_obj.code', $this->code])
            ->andFilterWhere(['like', 'concat(objParent.code," - ",objParent.title)', $this->objTitle])
            ->andFilterWhere(['like', 'ppmp_obj.title', $this->title])
            ->andFilterWhere(['like', 'ppmp_obj.active', $this->active])
            ->andFilterWhere(['like', 'ppmp_obj.description', $this->description]);

        return $dataProvider;
    }
}
