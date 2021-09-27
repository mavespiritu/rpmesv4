<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_item".
 *
 * @property int $id
 * @property int|null $procurement_mode_id
 * @property string|null $title
 * @property string|null $unit_of_measure
 * @property float|null $cost_per_unit
 * @property string|null $cse
 * @property string|null $classification
 *
 * @property PpmpProcurementMode $procurementMode
 * @property PpmpPpmpItem[] $ppmpPpmpItems
 */
class Item extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['procurement_mode_id', 'title', 'unit_of_measure', 'cost_per_unit', 'cse', 'classification'], 'required'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['procurement_mode_id'], 'integer'],
            [['code', 'title', 'cse', 'classification', 'category'], 'string'],
            [['cost_per_unit'], 'safe'],
            [['unit_of_measure'], 'string', 'max' => 100],
            [['procurement_mode_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcurementMode::className(), 'targetAttribute' => ['procurement_mode_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'procurement_mode_id' => 'Mode of Procurement',
            'code' => 'DBM Code',
            'title' => 'Title',
            'unit_of_measure' => 'Unit Of Measure',
            'cost_per_unit' => 'Cost Per Unit',
            'cse' => 'CSE',
            'classification' => 'Classification',
            'category' => 'DBM Category'
        ];
    }

    /**
     * Gets query for [[ProcurementMode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcurementMode()
    {
        return $this->hasOne(ProcurementMode::className(), ['id' => 'procurement_mode_id']);
    }

    /**
     * Gets query for [[PpmpPpmpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpmpItems()
    {
        return $this->hasMany(PpmpItem::className(), ['item_id' => 'id']);
    }

    /**
     * Gets query for [[PpmpPpmpItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemCosts()
    {
        return $this->hasMany(ItemCost::className(), ['item_id' => 'id']);
    }

    public function getCurrentCost()
    {
        $cost = $this->getItemCosts()->orderBy(['id' => SORT_DESC])->one();

        return $cost ? $cost->cost : $this->cost_per_unit;
    }
}
