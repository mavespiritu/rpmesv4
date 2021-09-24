<?php

namespace common\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\v1\models\Ppmp;

/**
 * PpmpSearch represents the model behind the search form of `common\modules\v1\models\Ppmp`.
 */
class PpmpSearch extends Ppmp
{
    public $officeName;
    public $creatorName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'year', 'created_by', 'updated_by'], 'integer'],
            [['stage', 'date_created', 'date_updated', 'officeName', 'creatorName', 'updaterName'], 'safe'],
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
        $query = Yii::$app->user->can('Administrator') ? Ppmp::find()
                ->joinWith('creator c')
                ->joinWith('office')
                ->orderBy(['year' => SORT_DESC])
                 : Ppmp::find()
                ->joinWith('creator c')
                ->joinWith('office')
                ->andWhere(['office_id' => Yii::$app->user->identity->userinfo->OFFICE_C])
                ->orderBy(['year' => SORT_DESC])
                ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'officeName' => [
                    'asc' => ['tbloffice.abbreviation' => SORT_ASC],
                    'desc' => ['tbloffice.abbreviation' => SORT_DESC],
                ],
                'year',
                'stage',
                'creatorName' => [
                    'asc' => ['concat(c.FIRST_M," ",c.LAST_M)' => SORT_ASC],
                    'desc' => ['concat(c.FIRST_M," ",c.LAST_M)' => SORT_DESC],
                ],
                'date_created',
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
            'year' => $this->year,
            'office_id' => $this->office_id,
            'stage' => $this->stage,
        ]);


        return $dataProvider;
    }
}
