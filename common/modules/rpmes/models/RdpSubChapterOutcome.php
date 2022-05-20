<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "rdp_sub_chapter_outcome".
 *
 * @property int $id
 * @property int|null $rdp_chapter_id
 * @property int|null $rdp_chapter_outcome_id
 * @property string|null $level
 * @property string|null $title
 * @property string|null $description
 *
 * @property ProjectRdpSubChapterOutcome[] $projectRdpSubChapterOutcomes
 * @property RdpChapterOutcome $rdpChapterOutcome
 */
class RdpSubChapterOutcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rdp_sub_chapter_outcome';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rdp_chapter_id', 'level', 'title'], 'required'],
            [['rdp_chapter_id', 'rdp_chapter_outcome_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['level'], 'string', 'max' => 10],
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
            'rdp_chapter_id' => 'RDP Chapter',
            'rdpChapterTitle' => 'RDP Chapter',
            'rdp_chapter_outcome_id' => 'RDP Chapter Outcome',
            'rdpChapterOutcomeTitle' => 'RDP Chapter Outcome',
            'level' => 'Level',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ProjectRdpSubChapterOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRdpSubChapterOutcomes()
    {
        return $this->hasMany(ProjectRdpSubChapterOutcome::className(), ['rdp_sub_chapter_outcome_id' => 'id']);
    }

    /**
     * Gets query for [[RdpChapterOutcome]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRdpChapter()
    {
        return $this->hasOne(RdpChapter::className(), ['id' => 'rdp_chapter_id']);
    }

    public function getRdpChapterTitle()
    {
        return $this->rdpChapter ? 'Chapter '.$this->rdpChapter->chapter_no.': '.$this->rdpChapter->title : '';
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

    public function getRdpChapterOutcomeTitle()
    {
        return $this->rdpChapterOutcome ? 'Outcome '.$this->rdpChapterOutcome->level.': '.$this->rdpChapterOutcome->title : '';
    }
}
