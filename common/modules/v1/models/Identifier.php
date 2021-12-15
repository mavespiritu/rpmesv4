<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_identifier".
 *
 * @property int $id
 * @property int|null $cost_structure_id
 * @property int|null $organizational_outcome_id
 * @property int|null $program_id
 * @property int|null $sub_program_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property PpmpSubProgram $subProgram
 * @property PpmpPap[] $ppmpPaps
 */
class Identifier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_identifier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_structure_id', 'organizational_outcome_id', 'program_id', 'sub_program_id', 'code', 'title'], 'required'],
            [['cost_structure_id', 'organizational_outcome_id', 'program_id', 'sub_program_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['code'], 'string', 'max' => 5],
            [['sub_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubProgram::className(), 'targetAttribute' => ['sub_program_id' => 'id']],
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
            'organizational_outcome_id' => 'Organizational Outcome',
            'program_id' => 'Program',
            'sub_program_id' => 'Sub Program',
            'code' => 'Code',
            'codeTitle' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[OrganizationalOutcome]].
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
     * Gets query for [[OrganizationalOutcome]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizationalOutcome()
    {
        return $this->hasOne(OrganizationalOutcome::className(), ['id' => 'organizational_outcome_id']);
    }

    public function getOrganizationalOutcomeTitle()
    {
        return $this->organizationalOutcome ? $this->organizationalOutcome->code.' - '.$this->organizationalOutcome->title : '';
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    public function getProgramTitle()
    {
        return $this->program ? $this->program->code.' - '.$this->program->title : '';
    }

    /**
     * Gets query for [[SubProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubProgram()
    {
        return $this->hasOne(SubProgram::className(), ['id' => 'sub_program_id']);
    }

    public function getSubProgramTitle()
    {
        return $this->subProgram ? $this->subProgram->code.' - '.$this->subProgram->title : '';
    }

    /**
     * Gets query for [[PpmpPaps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaps()
    {
        return $this->hasMany(Pap::className(), ['identifier_id' => 'id']);
    }

    public function getCodeTitle()
    {
        return $this->subProgram ? 
            $this->subProgram->organizationalOutcome->costStructure->code.''.
            $this->subProgram->organizationalOutcome->code.''.
            $this->subProgram->program->code.''.
            $this->subProgram->code.''.
            $this->code 
            : '';
    }
}
