<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_rdp_chapter_outcome".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property int|null $rdp_chapter_outcome_id
 *
 * @property Project $project
 * @property RdpChapterOutcome $rdpChapterOutcome
 */
class ProjectRdpChapterOutcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_rdp_chapter_outcome';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['rdp_chapter_outcome_id'], 'required'],
            [['project_id', 'year', 'rdp_chapter_outcome_id'], 'integer'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['rdp_chapter_outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => RdpChapterOutcome::className(), 'targetAttribute' => ['rdp_chapter_outcome_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'year' => 'Year',
            'rdp_chapter_outcome_id' => 'RDP Chapter Outcome',
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
     * Gets query for [[RdpChapterOutcome]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRdpChapterOutcome()
    {
        return $this->hasOne(RdpChapterOutcome::className(), ['id' => 'rdp_chapter_outcome_id']);
    }
}
