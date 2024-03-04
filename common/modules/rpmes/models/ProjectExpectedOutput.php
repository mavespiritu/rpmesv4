<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_expected_output".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $indicator
 * @property string|null $target
 *
 * @property Project $project
 */
class ProjectExpectedOutput extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_expected_output';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'indicator', 
            ], 'required'],
            [['project_id', 'year'], 'integer'],
            [[
                'target',
                'jan',
                'feb',
                'mar',
                'apr',
                'may',
                'jun',
                'jul',
                'aug',
                'sep',
                'oct',
                'nov',
                'dec',
            ], 'string'],
            [['indicator'], 'string', 'max' => 200],
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
            'indicator' => 'Indicator',
            'target' => 'Target',
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
            'type' => 'Type',
            'baseline' => 'Baseline Accomplishment',
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

    public function getPhysicalTargetPerQuarter($year)
    {
        $quarters = [
            'Q1' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
            ],
            'Q2' => [
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
            ],
            'Q3' => [
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
            ],
            'Q4' => [
                'oct' => 'Oct',
                'nov' => 'Nov',
                'dec' => 'Dec',
            ]
        ];

        $months = [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
        ];

        $total = 0;

        foreach($months as $mo => $month){
            $total += floatval($this->$mo);
        }

        $targets = [];

        foreach($quarters as $quarter => $months){
            $targets[$quarter] = 0;
            foreach($months as $mo => $month){
                $targets[$quarter] += floatval($this->$mo);
            }
        }

        $targets['Q1'] += floatval($this->baseline);

        return [
            'Q1' => $targets['Q1'],
            'Q2' => $targets['Q1'] + $targets['Q2'],
            'Q3' => $targets['Q1'] + $targets['Q2'] + $targets['Q3'],
            'Q4' => $targets['Q1'] + $targets['Q2'] + $targets['Q3'] + $targets['Q4'],
        ];
    }

    public function getEndOfProjectTarget($year)
    {
        $months = [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
        ];

        $total = 0;

        foreach($months as $mo => $month){
            $total += floatval($this->$mo);
        }

        $total += floatval($this->baseline);

        return $total;
    }

    public function getAccomplishmentPerQuarter($year){
        $q1 = ExpectedOutputAccomplishment::findOne([
            'project_id' => $this->project_id,
            'expected_output_id' => $this->id,
            'year' => $this->year,
            'quarter' => 'Q1'
        ]);

        $q2 = ExpectedOutputAccomplishment::findOne([
            'project_id' => $this->project_id,
            'expected_output_id' => $this->id,
            'year' => $this->year,
            'quarter' => 'Q2'
        ]);

        $q3 = ExpectedOutputAccomplishment::findOne([
            'project_id' => $this->project_id,
            'expected_output_id' => $this->id,
            'year' => $this->year,
            'quarter' => 'Q3'
        ]);

        $q4 = ExpectedOutputAccomplishment::findOne([
            'project_id' => $this->project_id,
            'expected_output_id' => $this->id,
            'year' => $this->year,
            'quarter' => 'Q4'
        ]);

        return [
            'Q1' => $q1 ? $this->indicator == 'number of individual beneficiaries served' ? floatval($q1->male) + floatval($q1->female) : floatval(floatval($q1->value)) : 0,
            'Q2' => $q2 ? $this->indicator == 'number of individual beneficiaries served' ? floatval($q2->male) + floatval($q2->female) : floatval(floatval($q2->value)) : 0,
            'Q3' => $q3 ? $this->indicator == 'number of individual beneficiaries served' ? floatval($q3->male) + floatval($q3->female) : floatval(floatval($q3->value)) : 0,
            'Q4' => $q4 ? $this->indicator == 'number of individual beneficiaries served' ? floatval($q4->male) + floatval($q4->female) : floatval(floatval($q4->value)) : 0,
        ];
    }
}
