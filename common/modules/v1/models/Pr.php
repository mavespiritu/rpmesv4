<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_pr".
 *
 * @property int $id
 * @property string|null $pr_no
 * @property string|null $office_id
 * @property string|null $section_id
 * @property string|null $unit_id
 * @property int|null $fund_source_id
 * @property int|null $fund_cluster_id
 * @property string|null $purpose
 * @property string|null $requested_by
 * @property string|null $date_requested
 * @property string|null $approved_by
 * @property string|null $date_approved
 * @property string|null $type
 */
class Pr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_pr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fund_source_id', 'fund_cluster_id'], 'required'],
            [['fund_source_id', 'fund_cluster_id'], 'integer'],
            [['purpose', 'type'], 'string'],
            [['year'], 'integer'],
            [['date_requested', 'date_approved', 'date_created'], 'safe'],
            [['pr_no', 'office_id', 'section_id', 'unit_id', 'requested_by', 'approved_by', 'created_by'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_no' => 'PR No.',
            'office_id' => 'Division',
            'section_id' => 'Section',
            'unit_id' => 'Unit',
            'fund_source_id' => 'Fund Source',
            'fund_cluster_id' => 'Fund Cluster',
            'purpose' => 'Purpose',
            'requested_by' => 'Requested By',
            'date_requested' => 'Date Requested',
            'approved_by' => 'Approved By',
            'date_approved' => 'Date Approved',
            'created_by' => 'Created By',
            'date_created' => 'Date Created',
            'year' => 'Year',
            'type' => 'Type',
        ];
    }

    public function getStatus()
    {
        $status = Transaction::find()->where(['model' => 'Pr', 'model_id' => $this->id])->orderBy(['datetime' => SORT_DESC])->one();

        return $status;
    }
}
