<?php

namespace common\modules\procurement\models;

use Yii;
use markavespiritu\user\models\UserInfo;

/**
 * This is the model class for table "pr_item_approval".
 *
 * @property int $id
 * @property int|null $item_id
 * @property string|null $status
 * @property int|null $action_taken_by
 * @property string|null $date_of_action
 * @property string|null $remarks
 *
 * @property PrItem $item
 */
class PrItemApproval extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_item_approval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'remarks'], 'required'],
            [['item_id', 'action_taken_by'], 'integer'],
            [['status', 'remarks'], 'string'],
            [['date_of_action'], 'safe'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrItem::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'status' => 'Status',
            'action_taken_by' => 'Action Taken By',
            'date_of_action' => 'Date Of Action',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(PrItem::className(), ['id' => 'item_id']);
    }

    public function getActionTakenByName()
    {
        $user = UserInfo::findOne(['user_id' => $this->action_taken_by]);

        return $user->fullName;
    }
}
