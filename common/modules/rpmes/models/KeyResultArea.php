<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "key_result_area".
 *
 * @property int $id
 * @property int|null $category_id
 * @property int|null $kra_no
 * @property string|null $title
 * @property string|null $description
 *
 * @property Category $category
 */
class KeyResultArea extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'key_result_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'kra_no', 'title'], 'required'],
            [['category_id', 'kra_no'], 'integer'],
            ['kra_no', 'unique', 'targetAttribute' => 'category_id', 'message' => 'The number has been included in the category already'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 100],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
            'categoryTitle' => 'Category',
            'kra_no' => 'KRA/Cluster No.',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getCategoryTitle()
    {
        return $this->category ? $this->category->title : '';
    }

    public function getKraId()
    {
        return $this->category_id.'-'.$this->id;
    }

    public function getKraTitle()
    {
        return 'KRA/Cluster #'.$this->kra_no.': '.$this->title;
    }
}
