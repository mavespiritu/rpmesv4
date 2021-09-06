<?php

namespace common\modules\procurement\models;

use Yii;
use markavespiritu\user\models\UserInfo;

/**
 * This is the model class for table "pr_transaction_history".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property string|null $group
 * @property string|null $status
 * @property string|null $action_type
 * @property int|null $action_taken_by
 * @property string|null $date_of_action
 * @property string|null $remarks
 *
 * @property PrPr $pr
 */
class PrTransactionHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_transaction_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_id', 'action_taken_by'], 'integer'],
            [['group', 'status', 'remarks'], 'string'],
            [['date_of_action'], 'safe'],
            [['action_type'], 'string', 'max' => 100],
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
            'group' => 'Group',
            'status' => 'Status',
            'action_type' => 'Action Type',
            'action_taken_by' => 'Action Taken By',
            'actionTakenByName' => 'Action Taken By',
            'date_of_action' => 'Date/Time Of Action',
            'remarks' => 'Remarks',
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

    public function getActionTakenByName()
    {
        $user = UserInfo::findOne(['user_id' => $this->action_taken_by]);

        return $user->fullName;
    }
}
