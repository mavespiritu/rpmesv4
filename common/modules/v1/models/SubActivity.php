<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_sub_activity".
 *
 * @property int $id
 * @property int|null $pap_id
 * @property int|null $activity_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property PpmpActivity $activity
 */
class SubActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_sub_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity_id', 'code', 'title'], 'required'],
            [['activity_id'], 'integer'],
            [['title', 'description'], 'string'],
            [['code'], 'string', 'max' => 10],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activity::className(), 'targetAttribute' => ['activity_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activity_id' => 'Activity',
            'activityTitle' => 'Activity',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Activity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

    public function getActivityTitle()
    {
        return $this->activity ? $this->activity->codeAndTitle : '';
    }
}
