<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_mode".
 *
 * @property int $id
 * @property string|null $title
 *
 * @property PrProcVerification[] $prProcVerifications
 */
class PrMode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_mode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
        ];
    }

    /**
     * Gets query for [[PrProcVerifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrProcVerifications()
    {
        return $this->hasMany(PrProcVerification::className(), ['mode_id' => 'id']);
    }
}
