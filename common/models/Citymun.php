<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tblcitymun".
 *
 * @property string $region_c
 * @property string $province_c
 * @property string $district_c
 * @property string $citymun_c
 * @property string $citymun_m
 * @property string $lgu_type
 */
class Citymun extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblcitymun';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_c', 'province_c', 'district_c', 'citymun_c', 'citymun_m', 'lgu_type'], 'required'],
            [['region_c', 'province_c', 'citymun_c'], 'string', 'max' => 2],
            [['district_c', 'lgu_type'], 'string', 'max' => 3],
            [['citymun_m'], 'string', 'max' => 200],
        ];
    }

    /**
       * @inheritdoc$primaryKey
       */
      public static function primaryKey()
      {
          return ["citymun_c"];
      }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'region_c' => 'Region C',
            'province_c' => 'Province C',
            'district_c' => 'District C',
            'citymun_c' => 'Citymun C',
            'citymun_m' => 'Citymun M',
            'lgu_type' => 'Lgu Type',
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

    public function getCitymunId()
    {
        return $this->region_c.'-'.$this->province_c.'-'.$this->citymun_c;
    }

    public function getCitymunTitle()
    {
        return $this->province ? $this->province->province_m.': '.$this->citymun_m : $this->citymun_m;
    }
}
