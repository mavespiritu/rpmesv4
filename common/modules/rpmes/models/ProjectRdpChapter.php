<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_rdp_chapter".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property int|null $rdp_chapter_id
 *
 * @property RdpChapter $rdpChapter
 * @property Project $project
 */
class ProjectRdpChapter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_rdp_chapter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rdp_chapter_id'], 'required'],
            [['project_id', 'year', 'rdp_chapter_id'], 'integer'],
            [['rdp_chapter_id'], 'exist', 'skipOnError' => true, 'targetClass' => RdpChapter::className(), 'targetAttribute' => ['rdp_chapter_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
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
            'rdp_chapter_id' => 'RDP Chapter',
        ];
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

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
