<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "event_image".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $uploaded_by
 * @property string|null $date_uploaded
 * @property string|null $image
 */
class EventImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string'],
            [['uploaded_by'], 'integer'],
            [['date_uploaded'], 'safe'],
            [['image'], 'string', 'max' => 200],
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
            'uploaded_by' => 'Uploaded By',
            'date_uploaded' => 'Date Uploaded',
            'image' => 'Image',
        ];
    }
}
