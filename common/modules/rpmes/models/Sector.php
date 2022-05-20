<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "sector".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 *
 * @property SubSectorPerSector[] $subSectorPerSectors
 */
class Sector extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
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
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[SubSectorPerSectors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubSectorPerSectors()
    {
        return $this->hasMany(SubSectorPerSector::className(), ['sector_id' => 'id']);
    }
}
