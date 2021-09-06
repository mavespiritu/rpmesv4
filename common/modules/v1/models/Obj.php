<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_obj".
 *
 * @property int $id
 * @property int|null $obj_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 * @property string|null $active
 *
 * @property Obj $obj
 * @property Obj[] $objs
 */
class Obj extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_obj';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'title'], 'required'],
            [['obj_id'], 'integer'],
            [['code'], 'unique', 'message' => 'The title has been used already'],
            [['title', 'description', 'active'], 'string'],
            [['code'], 'string', 'max' => 10],
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
            'obj_id' => 'Obj ID',
            'objectTitle' => 'Parent Object',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
            'active' => 'Active',
        ];
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

    public function getObjectTitle()
    {
        return $this->code.' - '.$this->title;
    }

    public function getObjTitle()
    {
        return $this->obj ? $this->obj->code.' - '.$this->obj->title : '';
    }

    /**
     * Gets query for [[Objs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjs()
    {
        return $this->hasMany(Obj::className(), ['obj_id' => 'id']);
    }
}
