<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "rdp_chapter".
 *
 * @property int $id
 * @property int|null $chapter_no
 * @property string|null $title
 * @property string|null $description
 *
 * @property ProjectRdpChapter[] $projectRdpChapters
 * @property RdpChapterOutcome[] $rdpChapterOutcomes
 */
class RdpChapter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rdp_chapter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chapter_no', 'title', 'year'], 'required'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
            'chapter_no' => 'Chapter No.',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ProjectRdpChapters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRdpChapters()
    {
        return $this->hasMany(ProjectRdpChapter::className(), ['rdp_chapter_id' => 'id']);
    }

    /**
     * Gets query for [[RdpChapterOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRdpChapterOutcomes()
    {
        return $this->hasMany(RdpChapterOutcome::className(), ['rdp_chapter_id' => 'id']);
    }
}
