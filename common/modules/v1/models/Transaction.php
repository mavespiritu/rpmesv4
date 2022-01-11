<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_transaction".
 *
 * @property int $id
 * @property string|null $model
 * @property int|null $model_id
 * @property string|null $status
 * @property string|null $datetime
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remarks'], 'required', 'on' => 'RIS For Revision'],
            [['model_id'], 'integer'],
            [['actor', 'datetime'], 'safe'],
            [['model', 'status'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'actor' => 'Actor',
            'model' => 'Model',
            'model_id' => 'Model ID',
            'status' => 'Status',
            'datetime' => 'Datetime',
            'remarks' => 'Remarks'
        ];
    }
}
