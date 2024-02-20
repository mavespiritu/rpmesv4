<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "typology".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 *
 * @property ProjectException[] $projectExceptions
 */
class Typology extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'typology';
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
     * Gets query for [[ProjectExceptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExceptions()
    {
        return $this->hasMany(ProjectException::className(), ['typology_id' => 'id']);
    }
}
