<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "agency_type".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property Agency[] $agencies
 */
class AgencyType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'title'], 'required'],
            [['code'], 'unique', 'message' => 'The abbreviation has been used already'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['description'], 'string'],
            [['code'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Agencies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgencies()
    {
        return $this->hasMany(Agency::className(), ['agency_type_id' => 'id']);
    }
}
