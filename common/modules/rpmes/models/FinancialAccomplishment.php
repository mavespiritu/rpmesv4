<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "financial_accomplishment".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $quarter
 * @property float|null $allocation
 * @property float|null $releases
 * @property float|null $obligation
 * @property float|null $disbursement
 * @property string|null $remarks
 *
 * @property Project $project
 */
class FinancialAccomplishment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'financial_accomplishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['releases', 'obligation', 'expenditures'], 'required'],
            [['project_id', 'year'], 'integer'],
            [['quarter', 'remarks'], 'string'],
            [['releases', 'obligation', 'expenditures'], 'safe'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'year' => 'Year',
            'quarter' => 'Quarter',
            'allocation' => 'Allocation',
            'releases' => 'Releases',
            'obligation' => 'Obligation',
            'expenditures' => 'Expenditures',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
