<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "mode_of_implementation".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 */
class ModeOfImplementation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mode_of_implementation';
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
}
