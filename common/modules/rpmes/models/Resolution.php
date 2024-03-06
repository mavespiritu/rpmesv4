<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "resolution".
 *
 * @property int $id
 * @property int|null $resolution_number
 * @property string|null $resolution
 * @property string|null $date_approved
 * @property string|null $rpmc_action
 * @property string|null $scanned_file
 */
class Resolution extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resolution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resolution_number', 'resolution_title', 'date_approved', 'year'], 'required'],
            [['submitted_by'], 'integer'],
            [['resolution_number','resolution_title', 'resolution', 'rpmc_action', 'resolution_url'], 'string'],
            [['date_approved','date_submitted'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            'fileBehavior' => [
                'class' => \file\behaviors\FileBehavior::className()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resolution_number' => 'Resolution Number',
            'resolution_title' => 'Resolution Title',
            'resolution_url' => 'Link to the Resolution',
            'resolution' => 'Resolution',
            'date_approved' => 'Date Approved',
            'rpmc_action' => 'Rpmc Action/Remarks',
            'quarter' => 'Quarter',
            'year' => 'Year',
            'date_submitted' => 'Date Submitted',
            'submitted_by' => 'Submitted By'
        ];
    }
}
