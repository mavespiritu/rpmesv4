<?php

namespace common\modules\rpmes\models;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
/**
 * This is the model class for table "project_barangay".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $region_id
 * @property string|null $province_id
 * @property string|null $citymun_id
 * @property string|null $barangay_id
 *
 * @property Project $project
 */
class ProjectBarangay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_barangay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'year'], 'integer'],
            [['region_id'], 'string', 'max' => 4],
            [['province_id', 'citymun_id'], 'string', 'max' => 3],
            [['barangay_id'], 'safe'],
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
            'region_id' => 'Region ID',
            'province_id' => 'Province ID',
            'citymun_id' => 'Citymun ID',
            'barangay_id' => 'Barangay',
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

    public function getBarangay()
    {
        return $this->region_id == '13' && $this->province_id == '39' ? 
        $this->hasOne(Barangay::className(), ['region_c' => 'region_id', 'province_c' => 'province_id', 'barangay_c' => 'barangay_id']) : 
        $this->hasOne(Barangay::className(), ['region_c' => 'region_id', 'province_c' => 'province_id', 'citymun_c' => 'citymun_id', 'barangay_c' => 'barangay_id']);
    }
    
    public function getBarangayName()
    {
        return $this->barangay ? $this->barangay->barangay_m : '';
    }

    public function getCitymun()
    {
        return $this->region_id == '13' && $this->province_id == '39' ?  $this->hasOne(Citymun::className(), ['region_c' => 'region_id', 'province_c' => 'province_id'])->where(['citymun_c' => '00']) : $this->hasOne(Citymun::className(), ['region_c' => 'region_id', 'province_c' => 'province_id', 'citymun_c' => 'citymun_id']);
    }

    public function getCitymunName()
    {
        return $this->citymun ? $this->citymun->citymun_m : '';
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['region_c' => 'region_id', 'province_c' => 'province_id']);
    }

    public function getProvinceName()
    {
        return $this->province ? $this->province->province_m : '';
    }

    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['region_c' => 'region_id']);
    }

    public function getRegionName()
    {
        return $this->region ? $this->region->abbreviation : '';
    }

    public function getBarangayId()
    {
        return $this->region_id.'-'.$this->province_id.'-'.$this->citymun_id.'-'.$this->barangay_id;
    }
}