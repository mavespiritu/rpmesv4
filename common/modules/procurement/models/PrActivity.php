<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_activity".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 *
 * @property PrActivityTimeline[] $prActivityTimelines
 */
class PrActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[PrActivityTimelines]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrActivityTimelines()
    {
        return $this->hasMany(PrActivityTimeline::className(), ['activity_id' => 'id']);
    }
}
