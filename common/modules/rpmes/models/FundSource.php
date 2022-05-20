<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "fund_source".
 *
 * @property int $id
 * @property string|null $fund_type
 * @property string|null $code
 * @property string|null $title
 * @property string|null $description
 * @property string|null $allow_typhoon
 */
class FundSource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fund_source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fund_type', 'code', 'title', 'allow_typhoon'], 'required'],
            [['fund_type', 'description', 'allow_typhoon'], 'string'],
            [['code'], 'unique', 'message' => 'The abbreviation has been used already'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
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
            'fund_type' => 'Fund Type',
            'code' => 'Code',
            'title' => 'Title',
            'description' => 'Description',
            'allow_typhoon' => 'Allow Typhoon Field',
        ];
    }
}
