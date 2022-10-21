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
<div class="project-result-table" style="height: 600px;">
    </h5>
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" cellspacing="0" style="min-width: 2000px;">
        <thead>
            <tr>
                <td align=center><b>Actions</td>
                <td align=center><b>#</td>
                <td colspan=2 align=center><b>Project Title</td>
                <td colspan=2 align=center><b>Project Objective</td>
                <td colspan=2 align=center><b>Results Indicator/Target</td>
                <td colspan=2 align=center><b>Observed Results</td>
                <td colspan=2 align=center><b>Deadline</td>
                <td align=center><b>Status</td>
            </tr>   
        </thead>
        <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
                <?php foreach($projects as $project){ ?>
                    <tr>
                        <td align=center>
                            <?php echo Html::a('<i class="fa fa-edit"></i>Edit', array('project-result/update', 'id'=>$project['resultId']), array('class'=>'btn btn-success btn-xs btn-block')); ?>
				            <?= Html::a('<i class="fa fa-trash"></i>Delete', ['delete', 'id' => $project['resultId']], [
                                'class' => 'btn btn-danger btn-xs btn-block',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                        <td align=center><?= $idx ?></td>
                        <td colspan=2 align=center><?= $project['projectTitle']?></td>
                        <td colspan=2 align=center><?= $project['objective']?></td>
                        <td colspan=2 align=center><?= $project['resultsIndicator'] ?></td>
                        <td colspan=2 align=center><?= $project['observedResults'] ?></td>
                        <td colspan=2 align=center><?= date('F j, Y', strtotime($project['deadline'])) ?></td>
                        <td align=center>
                            <?= $form->field($project, 'action')->widget(Switchery::className(), [
                                'options' => [
                                    'label' => false,
                                    'title' => 'Toggle if project is completed',
                                ],
                                'clientOptions' => [
                                    'color' => '#5fbeaa',
                                    'size' => 'small'
                                ],
                            'clientEvents' => [
                                    'change' => new JsExpression('function() {
                                        this.checked == true ? this.value = 1 : this.value = 0;
                                        updateAccomplishmentTable();
                                        enableInputFields(this.value, '.$model->id.');
                                    }'),
                                ]
                            ])->label('Project is completed?') ?>
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
            $(".project-result-table").freezeTable({
                "scrollable": true,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>

