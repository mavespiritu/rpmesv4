<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tblbarangay".
 *
 * @property string $region_c
 * @property string $province_c
 * @property string $citymun_c
 * @property string $barangay_c
 * @property resource $district_c
 * @property string $barangay_m
 */
class Barangay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblbarangay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_c', 'province_c', 'citymun_c', 'barangay_c', 'district_c', 'barangay_m'], 'required'],
            [['region_c', 'province_c', 'citymun_c'], 'string', 'max' => 2],
            [['barangay_c', 'district_c'], 'string', 'max' => 3],
            [['barangay_m'], 'string', 'max' => 200],
        ];
    }

    /**
       * @inheritdoc$primaryKey
       */
      public static function primaryKey()
      {
          return ["barangay_c"];
      }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'region_c' => 'Region C',
            'province_c' => 'Province C',
            'citymun_c' => 'Citymun C',
            'barangay_c' => 'Barangay C',
            'district_c' => 'District C',
            'barangay_m' => 'Barangay M',
        ];
    }

    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['region_c' => 'region_c']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['region_c' => 'region_c', 'province_c' => 'province_c']);
    }

    public function getCitymun()
    {
        return $this->region_c == '13' && $this->province_c == '39' ? 
        $this->hasOne(Citymun::className(), ['region_c' => 'region_c', 'province_c' => 'province_c', 'citymun_c' => '00']) :
        $this->hasOne(Citymun::className(), ['region_c' => 'region_c', 'province_c' => 'province_c', 'citymun_c' => 'citymun_c']);
    }

    public function getBarangayId()
    {
        return $this->region_c.'-'.$this->province_c.'-'.$this->citymun_c.'-'.$this->barangay_c;
    }

    public function getBarangayTitle()
    {
        return $this->province->province_m.': '.$this->citymun->citymun_m.': '.$this->barangay_m;
    }
}
