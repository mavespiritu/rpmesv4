<?php

namespace common\modules\procurement\models;

use Yii;
//use common\modules\procurement\components\validators\PrNoValidator;

/**
 * This is the model class for table "pr_proc_verification".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property int|null $program_id
 * @property int|null $mode_id
 * @property int|null $procurement_type_id
 * @property int|null $procurement_subtype_id
 * @property string|null $pr_no
 *
 * @property PrMode $mode
 * @property PrPr $pr
 * @property PrProcurementSubtype $procurementSubtype
 * @property PrProcurementType $procurementType
 * @property PrProgram $program
 */
class PrProcVerification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_proc_verification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_no', 'mode_id', 'procurement_type_id'], 'required'],
            ['pr_no', 'unique', 'message' => 'PR No. has been used already'],
            [['pr_id', 'mode_id', 'procurement_type_id', 'procurement_subtype_id'], 'integer'],
            [['pr_no'], 'string', 'max' => 100],
            [['mode_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrMode::className(), 'targetAttribute' => ['mode_id' => 'id']],
            [['pr_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrPr::className(), 'targetAttribute' => ['pr_id' => 'id']],
            [['procurement_subtype_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrProcurementSubtype::className(), 'targetAttribute' => ['procurement_subtype_id' => 'id']],
            [['procurement_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrProcurementType::className(), 'targetAttribute' => ['procurement_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_id' => 'Pr ID',
            'mode_id' => 'Mode of Procurement',
            'procurement_type_id' => 'Procurement Type',
            'procurement_subtype_id' => 'Procurement Subtype ID',
            'pr_no' => 'PR No.',
            'remarks' => 'Remarks',
            'modeTitle' => 'Mode of Procurement',
            'procurementTypeTitle' => 'Procurement Type'
        ];
    }

    /**
     * Gets query for [[Mode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(PrMode::className(), ['id' => 'mode_id']);
    }

    public function getModeTitle()
    {
        return $this->mode ? $this->mode->title : '';
    }

    /**
     * Gets query for [[Pr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPr()
    {
        return $this->hasOne(PrPr::className(), ['id' => 'pr_id']);
    }

    /**
     * Gets query for [[ProcurementSubtype]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcurementSubtype()
    {
        return $this->hasOne(PrProcurementSubtype::className(), ['id' => 'procurement_subtype_id']);
    }

    /**
     * Gets query for [[ProcurementType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcurementType()
    {
        return $this->hasOne(PrProcurementType::className(), ['id' => 'procurement_type_id']);
    }

    public function getProcurementTypeTitle()
    {
        return $this->procurementType ? $this->procurementType->title : '';
    }
}
