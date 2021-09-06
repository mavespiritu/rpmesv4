<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_fund_source".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $description
 */
class FundSource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_fund_source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'unique', 'message' => 'The title has been used already'],
            [['description'], 'string'],
            [['code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Title',
            'description' => 'Description',
        ];
    }
}
