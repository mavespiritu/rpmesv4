<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "sub_sector_per_sector".
 *
 * @property int $id
 * @property int|null $sector_id
 * @property int|null $sub_sector_id
 *
 * @property Sector $sector
 * @property SubSector $subSector
 */
class SubSectorPerSector extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub_sector_per_sector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sector_id', 'sub_sector_id'], 'required'],
            [['sector_id', 'sub_sector_id'], 'integer'],
            ['sub_sector_id', 'unique', 'targetAttribute' => 'sector_id', 'message' => 'The sub-sector has been included in the sector already'],
            [['sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sector::className(), 'targetAttribute' => ['sector_id' => 'id']],
            [['sub_sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubSector::className(), 'targetAttribute' => ['sub_sector_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sector_id' => 'Sector',
            'sectorTitle' => 'Sector',
            'sub_sector_id' => 'Sub Sector',
            'subSectorTitle' => 'Sub Sector',
        ];
    }

    /**
     * Gets query for [[Sector]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    public function getSectorTitle()
    {
        return $this->sector ? $this->sector->title : '';
    }

    /**
     * Gets query for [[SubSector]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubSector()
    {
        return $this->hasOne(SubSector::className(), ['id' => 'sub_sector_id']);
    }

    public function getSubSectorTitle()
    {
        return $this->subSector ? $this->subSector->title : '';
    }
}
