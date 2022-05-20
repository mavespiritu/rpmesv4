<?php

namespace common\modules\rpmes\models;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
/**
 * This is the model class for table "project_province".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $region_id
 * @property string|null $province_id
 *
 * @property Project $project
 */
class ProjectProvince extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_province';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'year'], 'integer'],
            [['province_id'], 'safe'],
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
            'province_id' => 'Province',
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

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['province_c' => 'province_id']);
    }

    public function getProvinceName()
    {
        return $this->province ? $this->province->province_m : '';
    }

    public function getRegion()
    {
        return $this->province->region;
    }

    public function getRegionName()
    {
        return $this->province->region ? $this->province->region->abbreviation : '';
    }
}
