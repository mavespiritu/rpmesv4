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
            [['resolution_number', 'resolution', 'date_approved','rpmc_action','quarter','year'], 'required'],
            [['resolution_number'], 'unique', 'message' => 'The resolution number has been used already'],
            [['submitted_by'], 'integer'],
            [['resolution_number','resolution', 'rpmc_action'], 'string'],
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
            'resolution' => 'Resolutions Passed',
            'date_approved' => 'Date Approved',
            'rpmc_action' => 'Rpmc Action/Remarks',
            'quarter' => 'Quarter',
            'year' => 'Year',
            'date_submitted' => 'Date Submitted',
            'submitted_by' => 'Submitted By'
        ];
    }

    public function getYearsList() 
    {
        $currentYear = 2099;
        $yearFrom = 1900;
        $yearsRange = range($yearFrom, $currentYear);
        return array_combine($yearsRange, $yearsRange);
    }

    public function getTotalParticipant($id)
    {
        $data = Project::findOne($this->id);

        $total_participant = $data->male_participant + $data->female_participant;

        return $total_participant;
    }
}
