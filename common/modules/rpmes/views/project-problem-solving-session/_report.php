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
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" cellspacing="0" style="min-width: 2000px;">
        <thead>
            <tr>
                <td align=center><b>#</td>
                <td colspan=2 align=center><b>Name of Project / Total Project Cost</td>
                <td colspan=2 align=center><b>Sector / Subsector </td>
                <td colspan=2 align=center><b>Issue Details</td>
                <td colspan=2 align=center><b>Location</td>
                <td align=center><b>Implementing Agency</td>
                <td align=center><b>Date of PSS / Facilitation Meeting</td>
                <td align=center><b>Concerned Agencies</td>
                <td colspan=2 align=center><b>Agreements Reached</td>
                <td colspan=2 align=center><b>Next Steps</td>
                <td align=center><b>Actions</td>
            </tr>   
        </thead>
        <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
                <?php foreach($projects as $project){ ?>
                    <tr>
                        <td align=center><?= $idx ?></td>
                        <td colspan=2 align=center><?= $project['projectTitle'].' / '. number_format($project['totalCost'], 2) ?></td>
                        <td colspan=2 align=center><?= $project['sectorTitle']. ' / '.$project['subSectorTitle'] ?></td>
                        <td colspan=2 align=center><?= $project['cause'] ?></td>
                        <td colspan=2 align=center><?= $project['locationTitle'] ?></td>
                        <td align=center><?= $project['agencyTitle'] ?></td>
                        <td align=center><?= date('F j, Y', strtotime($project['pssDate'])) ?></td>
                        <td align=center><?= $project['agencyTitle'] ?></td>
                        <td colspan=2 align=center><?= $project['agreementReached'] ?></td>
                        <td colspan=2 align=center><?= $project['nextStep'] ?></td>
                        <td align=center>
                            <?php echo Html::a('<i class="fa fa-edit"></i>Edit', array('project-problem-solving-session/update', 'id'=>$project['pssId']), array('class'=>'btn btn-success btn-xs btn-block')); ?>
				            <?= Html::a('<i class="fa fa-trash"></i>Delete', ['delete', 'id' => $project['pssId']], [
                                'class' => 'btn btn-danger btn-xs btn-block',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
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

