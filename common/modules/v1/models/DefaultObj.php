<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_default_obj".
 *
 * @property int $id
 * @property int|null $obj_id
 * @property int|null $arrangement
 */
class DefaultObj extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_default_obj';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obj_id', 'arrangement'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obj_id' => 'Obj ID',
            'arrangement' => 'Arrangement',
        ];
    }
}
