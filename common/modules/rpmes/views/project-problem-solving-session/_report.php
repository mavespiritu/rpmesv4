<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="project-problem-solving-session-table" style="height: 600px;">
    </h5>
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <td align=center><b>#</td>
                <td align=center><b>Name of Project / Total PRoject Cost</td>
                <td align=center><b>Sector / Subsector </td>
                <td align=center><b>Issue Details</td>
                <td align=center><b>Location</td>
                <td align=center><b>Implementing Agency</td>
                <td align=center><b>Date of PSS / Facilitation Meeting</td>
                <td align=center><b>Concerned Agencies</td>
                <td align=center><b>Agreements Reached</td>
                <td align=center><b>Next Steps</td>
                <td align=center><b>Actions</td>
            </tr>   
        </thead>
        <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
                <?php foreach($projects as $project){ ?>
                    <tr>
                        <td align=center><?= $idx ?></td>
                        <td align=center><?= $project['projectTitle'].' / '. number_format($project['totalCost'], 2) ?></td>
                        <td align=center><?= $project['sectorTitle']. ' / '.$project['subSectorTitle'] ?></td>
                        <td align=center><?= $project['cause'] ?></td>
                        <td align=center><?= $project['locationTitle'] ?></td>
                        <td align=center><?= $project['agencyTitle'] ?></td>
                        <td align=center><?= date('F j, Y', strtotime($project['pssDate'])) ?></td>
                        <td align=center><?= $project['agencyTitle'] ?></td>
                        <td align=center><?= $project['agreementReached'] ?></td>
                        <td align=center><?= $project['nextStep'] ?></td>
                        <td align=center></td>
                    </tr>
                    <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
    $script = '
        $(document).ready(function(){
            $(".project-problem-solving-session-table").freezeTable({
                "scrollable": true,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>

