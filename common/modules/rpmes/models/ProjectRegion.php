<?php

namespace common\modules\rpmes\models;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
/**
 * This is the model class for table "project_region".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $region_id
 *
 * @property Project $project
 */
class ProjectRegion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id'], 'required'],
            [['project_id', 'year'], 'integer'],
            [['region_id'], 'safe'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'year' => 'Year',
            'region_id' => 'Region',
        ];
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['region_c' => 'region_id']);
    }

    public function getRegionName()
    {
        return $this->region ? $this->region->abbreviation : '';
    }
}
