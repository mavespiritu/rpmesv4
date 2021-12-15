<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_cost_structure".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 * @property string|null $abbreviation
 *
 * @property PpmpOrganizationalOutcome[] $ppmpOrganizationalOutcomes
 */
class CostStructure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_cost_structure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'title'], 'required'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['title', 'description'], 'string'],
            [['code', 'abbreviation'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
            'abbreviation' => 'Abbreviation',
        ];
    }

    /**
     * Gets query for [[PpmpOrganizationalOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizationalOutcomes()
    {
        return $this->hasMany(OrganizationalOutcome::className(), ['cost_structure_id' => 'id']);
    }
}
