<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_service_type".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 *
 * @property PrSupplierList[] $prSupplierLists
 */
class PrServiceType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_service_type';
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

    /**
     * Gets query for [[PrSupplierLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrSupplierLists()
    {
        return $this->hasMany(PrSupplierList::className(), ['service_type_id' => 'id']);
    }
}
