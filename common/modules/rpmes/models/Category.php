<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 *
 * @property ProjectCategory[] $projectCategories
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'title'], 'required'],
            [['code'], 'unique', 'message' => 'The code has been used already'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['description'], 'string'],
            [['code'], 'string', 'max' => 20],
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
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ProjectCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCategories()
    {
        return $this->hasMany(ProjectCategory::className(), ['category_id' => 'id']);
    }
}
