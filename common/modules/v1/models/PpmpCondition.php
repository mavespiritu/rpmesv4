<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_ppmp_condition".
 *
 * @property int $id
 * @property int|null $ppmp_id
 * @property string|null $con
 * @property string|null $value
 */
class PpmpCondition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_ppmp_condition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ppmp_id'], 'integer'],
            [['value', 'counter'], 'string'],
            [['con'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ppmp_id' => 'Ppmp ID',
            'con' => 'Con',
            'value' => 'Value',
            'counter' => 'Counter',
        ];
    }
}
