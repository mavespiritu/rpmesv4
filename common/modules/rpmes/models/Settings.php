<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property string $title
 * @property string|null $value
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['value'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['title'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'value' => 'Value',
        ];
    }
}
