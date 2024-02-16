<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_has_fund_sources".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property int|null $fund_source_id
 *
 * @property Project $project
 * @property FundSource $fundSource
 */
class ProjectHasFundSources extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_has_fund_sources';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fund_source_id'], 'required'],
            [['project_id', 'year', 'fund_source_id'], 'integer'],
            [['other_fund_source', 'type', 'agency'], 'string'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['fund_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundSource::className(), 'targetAttribute' => ['fund_source_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project',
            'year' => 'Year',
            'fund_source_id' => 'Funding Source',
            'type' => 'Type',
            'agency' => 'Agency'
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

    /**
     * Gets query for [[FundSource]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFundSource()
    {
        return $this->hasOne(FundSource::className(), ['id' => 'fund_source_id']);
    }
}
