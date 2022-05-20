<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_rdp_sub_chapter_outcome".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property int|null $rdp_sub_chapter_outcome_id
 *
 * @property Project $project
 * @property RdpSubChapterOutcome $rdpSubChapterOutcome
 */
class ProjectRdpSubChapterOutcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_rdp_sub_chapter_outcome';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['rdp_sub_chapter_outcome_id'], 'required'],
            [['project_id', 'year', 'rdp_sub_chapter_outcome_id'], 'integer'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['rdp_sub_chapter_outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => RdpSubChapterOutcome::className(), 'targetAttribute' => ['rdp_sub_chapter_outcome_id' => 'id']],
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
            'rdp_sub_chapter_outcome_id' => 'RDP Sub-Chapter Outcome',
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
     * Gets query for [[RdpSubChapterOutcome]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRdpSubChapterOutcome()
    {
        return $this->hasOne(RdpSubChapterOutcome::className(), ['id' => 'rdp_sub_chapter_outcome_id']);
    }
}
