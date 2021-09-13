<?php

namespace common\modules\v1\models;

use Yii;
use markavespiritu\user\models\Office;
use markavespiritu\user\models\UserInfo;
/**
 * This is the model class for table "ppmp_ppmp".
 *
 * @property int $id
 * @property int|null $division_id
 * @property string|null $stage
 * @property int|null $year
 * @property int|null $created_by
 * @property string|null $date_created
 * @property int|null $updated_by
 * @property string|null $date_updated
 *
 * @property PpmpPpmpItem[] $ppmpPpmpItems
 */
class Ppmp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_ppmp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year','stage'], 'validatePpmp'],
            [['year','stage'], 'required'],
            [['year','stage'], 'required', 'on' => 'isUser'],
            [['year','stage', 'office_id'], 'required', 'on' =>  'isAdmin'],
            [['office_id', 'year', 'created_by', 'updated_by'], 'integer'],
            [['stage'], 'string'],
            [['date_created', 'date_updated'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'office_id' => 'Division',
            'officeName' => 'Division',
            'stage' => 'Stage',
            'year' => 'Year',
            'copy' => 'Copy From',
            'data' => 'Data',
            'created_by' => 'Created By',
            'creatorName' => 'Created By',
            'date_created' => 'Date Created',
            'updated_by' => 'Updated By',
            'updaterName' => 'Updated By',
            'date_updated' => 'Date Updated',
        ];
    }

    public function validatePpmp($attribute, $params, $validator)
    {
        $model = Ppmp::findOne(['office_id' => $this->office_id, 'year' => $this->year, 'stage' => $this->stage]);

        if($model)
        {
            $this->addError($attribute, 'This PPMP already exists');
        }
    }

    /**
     * Gets query for [[PpmpPpmpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpmpPpmpItems()
    {
        return $this->hasMany(PpmpPpmpItem::className(), ['ppmp_id' => 'id']);
    }

    public function getOffice()
    {
        return $this->hasOne(Office::className(), ['id' => 'office_id']);
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->abbreviation : '';
    }

    public function getCreator()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'created_by']); 
    }

    public function getCreatorName()
    {
        return $this->creator ? $this->creator->FIRST_M.' '.$this->creator->LAST_M : '';
    }

    public function getUpdater()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'updated_by']); 
    }

    public function getUpdaterName()
    {
        return $this->updater ? $this->updater->FIRST_M.' '.$this->updater->LAST_M : '';
    }

    public function getTitle()
    {
        return 'PPMP-'.$this->office->abbreviation.'-'.strtoupper($this->stage).'-'.$this->year;
    }

    public function getReference()
    {
        if($this->stage == 'Indicative')
        {
            return Appropriation::findOne(['type' => 'GAA', 'year' => $this->year -1]);
        }
        else if($this->stage == 'Adjusted')
        {
            return Appropriation::findOne(['type' => 'NEP', 'year' => $this->year]);
        }
        else if($this->stage == 'Final')
        {
            return Appropriation::findOne(['type' => 'GAA', 'year' => $this->year]);
        }
    }

    public function getTotal()
    {
        $quantity = ItemBreakdown::find()
                   ->select([
                       'ppmp_item_id',
                       'sum(quantity) as total'
                   ])
                    ->groupBy(['ppmp_item_id'])
                    ->createCommand()
                    ->getRawSql();

        $total = PpmpItem::find()
                ->select([
                    'sum(quantity.total * cost) as total'
                ])
                ->leftJoin(['quantity' => '('.$quantity.')'], 'quantity.ppmp_item_id = ppmp_ppmp_item.id')
                ->andWhere([
                    'ppmp_id' => $this->id,
                ])
                ->asArray()
                ->one();
        
        return $total['total'];
    }
}
