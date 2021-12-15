<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_sub_program".
 *
 * @property int $id
 * @property int|null $cost_structure_id
 * @property int|null $organizational_outcome_id
 * @property int|null $program_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property PpmpIdentifier[] $ppmpIdentifiers
 * @property PpmpProgram $program
 */
class SubProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_sub_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_structure_id', 'organizational_outcome_id', 'program_id', 'code', 'title'], 'required'],
            [['cost_structure_id', 'organizational_outcome_id', 'program_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['code'], 'string', 'max' => 5],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
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
     * Gets query for [[PpmpIdentifiers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdentifiers()
    {
        return $this->hasMany(Identifier::className(), ['sub_program_id' => 'id']);
    }

    public function getCodeTitle()
    {
        return $this->program ? 
            $this->program->organizationalOutcome->costStructure->code.''.
            $this->program->organizationalOutcome->code.''.
            $this->program->code.''.
            $this->code 
            : '';
    }
}
