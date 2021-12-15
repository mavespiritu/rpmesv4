<?php

namespace common\modules\v1\models;

use Yii;
use markavespiritu\user\models\UserInfo;
use yii\helpers\ArrayHelper;    
/**
 * This is the model class for table "ppmp_appropriation".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $year
 * @property int|null $created_by
 * @property string|null $date_created
 * @property int|null $updated_by
 * @property string|null $date_updated
 */
class Appropriation extends \yii\db\ActiveRecord
{
    public $copy;
    public $data;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_appropriation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year'], 'validateYear'],
            [['year'], 'required'],
            [['year', 'copy', 'data'], 'required', 'on' => 'copy'],
            [['type'], 'string'],
            [['created_by', 'updated_by', 'copy'], 'integer'],
            [['date_created', 'date_updated', 'data'], 'safe'],
            [['year'], 'string', 'max' => 5],
        ];
    }

    public function validateYear($attribute, $params, $validator)
    {
        $years = Appropriation::find()->select(['year'])->where(['type' => $this->type])->asArray()->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        if (in_array($this->$attribute, $years)) {
            $this->addError($attribute, 'The year has been used already');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'year' => 'Year',
            'created_by' => 'Created By',
            'creatorName' => 'Created By',
            'date_created' => 'Date Created',
            'updated_by' => 'Updated By',
            'updaterName' => 'Updated By',
            'date_updated' => 'Date Updated',
            'copy' => 'Copy From',
            'data' => 'Data',
        ];
    }

    /**
     * Gets query for [[PpmpAppropriationPap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationPaps()
    {
        return $this->hasMany(AppropriationPap::className(), ['appropriation_id' => 'id']);
    }

    /**
     * Gets query for [[PpmpAppropriationPap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationObjs()
    {
        return $this->hasMany(AppropriationObj::className(), ['appropriation_id' => 'id']);
    }

    /**
     * Gets query for [[PpmpAppropriationItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationItems()
    {
        return $this->hasMany(AppropriationItem::className(), ['appropriation_id' => 'id']);
    }

    public function getCreator()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'created_by']); 
    }

    public function getCreatorName()
    {
        return $this->creator ? ucwords(strtolower($this->creator->FIRST_M.' '.$this->creator->LAST_M)) : '';
    }

    public function getUpdater()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'updated_by']); 
    }

    public function getUpdaterName()
    {
        return $this->updater ? ucwords(strtolower($this->updater->FIRST_M.' '.$this->updater->LAST_M)) : '';
    }

    public function getTitle()
    {
        return $this->type.' '.$this->year;
    }

    public function getTotal()
    {
        $total = AppropriationItem::find()->select('sum(amount) as amount')->where(['appropriation_id' => $this->id])->one();
        
        return $total['amount'];
    }

    public function afterSave($insert, $changedAttributes){
        if($insert){
            if($this->copy == '')
            {
                $paps = DefaultPap::find()->where(['type' => $this->type])->all();
                if($paps)
                {
                    foreach($paps as $pap)
                    {
                        $model = new AppropriationPap();
                        $model->appropriation_id = $this->id;
                        $model->pap_id = $pap->pap_id;
                        $model->fund_source_id = $pap->fund_source_id;
                        $model->arrangement = $pap->arrangement;
                        $model->save();
                    }
                }

                $objs = DefaultObj::find()->where(['type' => $this->type])->all();
                if($objs)
                {
                    foreach($objs as $obj)
                    {
                        $model = new AppropriationObj();
                        $model->appropriation_id = $this->id;
                        $model->obj_id = $obj->obj_id;
                        $model->arrangement = $obj->arrangement;
                        $model->save();
                    }
                }
            }else
            {
                $model = Appropriation::findOne(['id' => $this->copy]);
                if($model)
                {
                    $connection = \Yii::$app->db;

                    $objects = AppropriationObj::find()->select([
                        'concat("'.$this->id.'")',
                        'obj_id',
                        'arrangement'
                    ])
                    ->where(['appropriation_id' => $model->id])
                    ->createCommand()
                    ->getRawSql();

                    $programs = AppropriationPap::find()->select([
                        'concat("'.$this->id.'")',
                        'pap_id',
                        'fund_source_id',
                        'arrangement'
                    ])
                    ->where(['appropriation_id' => $model->id])
                    ->createCommand()
                    ->getRawSql();

                    $connection->createCommand('INSERT into ppmp_appropriation_obj (appropriation_id, obj_id, arrangement) '.$objects)->execute();
                    $connection->createCommand('INSERT into ppmp_appropriation_pap (appropriation_id, pap_id, fund_source_id, arrangement) '.$programs)->execute();

                    if($this->data == 2)
                    {
                        $items = AppropriationItem::find()->select([
                            'concat("'.$this->id.'")',
                            'obj_id',
                            'pap_id',
                            'fund_source_id',
                            'amount'
                        ])
                        ->where(['appropriation_id' => $model->id])
                        ->createCommand()
                        ->getRawSql();

                        $connection->createCommand('INSERT into ppmp_appropriation_item (appropriation_id, obj_id, pap_id, fund_source_id, amount) '.$items)->execute();
                    }
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public static function pageQuantityTotal($provider, $fieldName)
    {
        $total = 0;
        foreach($provider as $item){
            $total+=$item[$fieldName];
        }
        return '<b>'.number_format($total, 2).'</b>';
    }
}
