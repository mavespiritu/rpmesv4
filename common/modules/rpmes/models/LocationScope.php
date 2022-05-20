<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "location_scope".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $title
 * @property string|null $description
 */
class LocationScope extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location_scope';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'description'], 'string'],
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
            'type' => 'Type',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }
}
