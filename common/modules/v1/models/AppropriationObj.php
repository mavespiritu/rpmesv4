<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_appropriation_obj".
 *
 * @property int $id
 * @property int|null $appropriation_id
 * @property int|null $obj_id
 *
 * @property PpmpAppropriationItem[] $ppmpAppropriationItems
 * @property PpmpAppropriation $appropriation
 * @property PpmpObj $obj
 */
class AppropriationObj extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_appropriation_obj';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obj_id'], 'required'],
            [['appropriation_id', 'obj_id'], 'integer'],
            [['appropriation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Appropriation::className(), 'targetAttribute' => ['appropriation_id' => 'id']],
            [['obj_id'], 'exist', 'skipOnError' => true, 'targetClass' => Obj::className(), 'targetAttribute' => ['obj_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appropriation_id' => 'Appropriation ID',
            'obj_id' => 'Object',
            'arrangement' => 'Arrangement',
        ];
    }

    public function validateObject($attribute, $params, $validator)
    {
        $objs = AppropriationObj::find()->select(['obj_id'])->where(['id' => $this->id])->asArray()->all();
        $objs = ArrayHelper::map($objs, 'obj_id', 'obj_id');

        if (in_array($this->$attribute, $objs)) {
            $this->addError($attribute, 'The object has been added already');
        }
    }

    /**
     * Gets query for [[PpmpAppropriationItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationItems()
    {
        return $this->hasMany(AppropriationItem::className(), ['appropriation_id' => 'appropriation_id', 'obj_id' => 'obj_id']);
    }

    /**
     * Gets query for [[Appropriation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriation()
    {
        return $this->hasOne(Appropriation::className(), ['id' => 'appropriation_id']);
    }

    /**
     * Gets query for [[Obj]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObj()
    {
        return $this->hasOne(Obj::className(), ['id' => 'obj_id']);
    }
}
