<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_program".
 *
 * @property int $id
 * @property int|null $cost_structure_id
 * @property int|null $organizational_outcome_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property PpmpOrganizationalOutcome $organizationalOutcome
 * @property PpmpSubProgram[] $ppmpSubPrograms
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_structure_id', 'organizational_outcome_id', 'code', 'title'], 'required'],
            [['cost_structure_id', 'organizational_outcome_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['code'], 'string', 'max' => 5],
            [['organizational_outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrganizationalOutcome::className(), 'targetAttribute' => ['organizational_outcome_id' => 'id']],
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
     * Gets query for [[PpmpSubPrograms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubPrograms()
    {
        return $this->hasMany(SubProgram::className(), ['program_id' => 'id']);
    }

    public function getCodeTitle()
    {
        return $this->organizationalOutcome ? $this->organizationalOutcome->costStructure->code.''.$this->organizationalOutcome->code.''.$this->code : '';
    }
}
