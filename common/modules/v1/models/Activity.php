<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_activity".
 *
 * @property int $id
 * @property int|null $pap_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property PpmpPap $pap
 * @property PpmpSubactivity[] $ppmpSubactivities
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pap_id', 'code', 'title'], 'required'],
            [['pap_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['code'], 'string', 'max' => 10],
            [['pap_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pap::className(), 'targetAttribute' => ['pap_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pap_id' => 'PAP',
            'papTitle' => 'PAP',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    public function getCodeAndTitle()
    {
        return $this->pap ? $this->pap->codeTitle.'-'.$this->code.' - '.$this->title : '';
    }

    /**
     * Gets query for [[Pap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPap()
    {
        return $this->hasOne(Pap::className(), ['id' => 'pap_id']);
    }

    public function getPapTitle()
    {
        return $this->pap? $this->pap->codeAndTitle : '';
    }

    /**
     * Gets query for [[PpmpSubactivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubactivities()
    {
        return $this->hasMany(Subactivity::className(), ['activity_id' => 'id']);
    }

    /**
     * Gets query for [[PpmpSubactivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpas()
    {
        return $this->hasMany(Ppa::className(), ['activity_id' => 'id']);
    }
}
