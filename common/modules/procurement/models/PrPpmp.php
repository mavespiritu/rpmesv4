<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_ppmp".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property string|null $description
 * @property string|null $source
 * @property string|null $source_version
 * @property string|null $version_no
 * @property string|null $item_no
 *
 * @property PrPr $pr
 */
class PrPpmp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_ppmp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'source', 'source_version', 'version_no', 'item_no'], 'required'],
            [['pr_id'], 'integer'],
            [['description', 'source'], 'string'],
            [['source_version', 'version_no'], 'string', 'max' => 10],
            [['item_no'], 'string', 'max' => 20],
            [['pr_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrPr::className(), 'targetAttribute' => ['pr_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_id' => 'Pr ID',
            'description' => 'Description',
            'source' => 'Source',
            'source_version' => 'Source Version',
            'version_no' => 'Version No',
            'item_no' => 'Item No',
        ];
    }

    /**
     * Gets query for [[Pr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPr()
    {
        return $this->hasOne(PrPr::className(), ['id' => 'pr_id']);
    }
}
