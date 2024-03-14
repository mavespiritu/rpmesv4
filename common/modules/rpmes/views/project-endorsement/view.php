<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<table class="table table-bordered table-striped table-hover table-responsive">
    <thead>
        <tr style='background-color: #002060; color: white; font-weight: normal;'>
            <td>#</td>
            <td style="width: 10%;">Year</td>
            <td style="width: 10%;">Quarter</td>
            <td style="width: 30%;">(a) Project No. <br>
                                    (b) Program/Project Title <br>
                                    (c) Province <br>
                                    (d) City/Municipality <br>
                                    (e) Barangay</td>
            <td style="width: 10%;">Slippage</td>
            <td style="width: 40%;">(a) Typology <br>
                                    (b) Issue Status <br>
                                    (c) Findings <br>
                                    (d) Reasons <br>
                                    (e) Actions Taken <br>
                                    (f) Actions to be taken</td>
        </tr>
    </thead>
    <tbody>
    <?php if($exceptions){ ?>
        <?php foreach($exceptions as $i => $model){ ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= $model->year ?></td>
                <td><?= $model->quarter ?></td>
                <td><?= '(a) '.$model->project->project_no.'<br>'.
                        '(b) '.$model->project->title.'<br>'.
                        '(c) '.$model->project->provinceTitle.'<br>'.
                        '(d) '.$model->project->citymunTitle.'<br>'.
                        '(e) '.$model->project->barangayTitle ?></td>
                <td><?= $model->project->getSlippage($model->year)[$model->quarter] > -10 && $model->project->getSlippage($model->year)[$model->quarter] < 10 ? number_format($model->project->getSlippage($model->year)[$model->quarter], 2) : '<font style="color:red">'.number_format($model->project->getSlippage($model->year)[$model->quarter], 2).'</font>' ?></td>
                <td><?= '(a) '.($model->typology ? $model->typology->title : '').'<br>'.
                        '(b) '.$model->issue_status.'<br>'.
                        '(c) '.strip_tags($model->findings).'<br>'.
                        '(d) '.strip_tags($model->causes).'<br>'.
                        '(e) '.strip_tags($model->action_taken).'<br>'.
                        '(f) '.strip_tags($model->recommendations) ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>