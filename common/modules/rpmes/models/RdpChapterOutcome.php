<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "rdp_chapter_outcome".
 *
 * @property int $id
 * @property int|null $rdp_chapter_id
 * @property string|null $level
 * @property string|null $title
 * @property string|null $description
 *
 * @property ProjectRdpChapterOutcome[] $projectRdpChapterOutcomes
 * @property RdpChapter $rdpChapter
 * @property RdpSubChapterOutcome[] $rdpSubChapterOutcomes
 */
class RdpChapterOutcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rdp_chapter_outcome';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rdp_chapter_id', 'level', 'title'], 'required'],
            [['rdp_chapter_id'], 'integer'],
            [['title', 'description'], 'string'],
            ['title', 'unique', 'targetAttribute' => 'rdp_chapter_id', 'message' => 'The title has been used already'],
            [['level'], 'string', 'max' => 10],
            [['rdp_chapter_id'], 'exist', 'skipOnError' => true, 'targetClass' => RdpChapter::className(), 'targetAttribute' => ['rdp_chapter_id' => 'id']],
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
            'level' => 'Level',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ProjectRdpChapterOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRdpChapterOutcomes()
    {
        return $this->hasMany(ProjectRdpChapterOutcome::className(), ['rdp_chapter_outcome_id' => 'id']);
    }

    /**
     * Gets query for [[RdpChapter]].
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

    public function getRdpChapterOutcomeTitle()
    {
        return 'Chapter Outcome '.$this->level.': '.$this->title;
    }

    /**
     * Gets query for [[RdpSubChapterOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRdpSubChapterOutcomes()
    {
        return $this->hasMany(RdpSubChapterOutcome::className(), ['rdp_chapter_outcome_id' => 'id']);
    }
}
