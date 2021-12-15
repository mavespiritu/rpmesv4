<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_organizational_outcome".
 *
 * @property int $id
 * @property int|null $cost_structure_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property PpmpCostStructure $costStructure
 * @property PpmpProgram[] $ppmpPrograms
 */
class OrganizationalOutcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_organizational_outcome';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_structure_id', 'code', 'title'], 'required'],
            [['cost_structure_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['code'], 'string', 'max' => 5],
            [['cost_structure_id'], 'exist', 'skipOnError' => true, 'targetClass' => CostStructure::className(), 'targetAttribute' => ['cost_structure_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cost_structure_id' => 'Cost Structure',
            'costStructureTitle' => 'Cost Structure',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[CostStructure]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCostStructure()
    {
        return $this->hasOne(CostStructure::className(), ['id' => 'cost_structure_id']);
    }

    public function getCostStructureTitle()
    {
        return $this->costStructure ? $this->costStructure->code.' - '.$this->costStructure->title : '';
    }

    /**
     * Gets query for [[PpmpPrograms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Program::className(), ['organizational_outcome_id' => 'id']);
    }
}
