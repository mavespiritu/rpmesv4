<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\Modal;
?>

<h5>
<b>Monitoring Plan <?= $model->year ?></b><br><br>
Project No. <?= $plan->project->project_no.': '.$plan->project->title ?>
</h5>

<table class="table table-bordered table-responsive table-striped table-hover">
    <thead>
        <tr style="background-color: #002060; color: white; font-weight: normal;">
            <td rowspan=2>#</td>
            <td rowspan=2 style="width: 10%;">Output Indicator</td>
            <td rowspan=2 style="width: 10%;">Baseline <br>Accomplishment</td>
            <td colspan=12 style="text-align: center;">Monthly Target</td>
            <td rowspan=2>&nbsp;</td>
        </tr>
        <tr style="background-color: #002060; color: white; font-weight: normal;">
            <?php foreach($months as $month){ ?>
                <td style="text-align: center;"><?= $month ?></td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php if($expectedOutputs){ ?>
            <?php foreach($expectedOutputs as $i => $expectedOutput){ ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $expectedOutput->indicator ?></td>
                    <td align=center><?= $expectedOutput->type == 'Percentage' ? number_format($expectedOutput->baseline, 2) : number_format($expectedOutput->baseline, 0) ?></td>
                    <?php foreach($months as $mo => $month){ ?>
                        <td align=center><?= $expectedOutput->type == 'Percentage' ? number_format($expectedOutput->$mo, 2) : number_format($expectedOutput->$mo, 0) ?></td>
                    <?php } ?>
                    <td align=center style="width: 5%;">
                    <?= !Yii::$app->user->can('Administrator') ? 
                            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                $expectedOutput->indicator != 'number of individual beneficiaries served' ?
                                    Html::a('Edit', '#', [
                                        'class' => 'btn btn-warning btn-xs btn-block update-button',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#update-oi-modal-'.$expectedOutput->id,
                                        'data-url' => Url::to(['update-output-indicator', 'id' => $expectedOutput->id, 'submission_id' => $model->id]),
                                    ]) :
                                '' :
                            '' :
                        Html::a('Edit', '#', [
                            'class' => 'btn btn-warning btn-xs btn-block update-button',
                            'data-toggle' => 'modal',
                            'data-target' => '#update-oi-modal-'.$expectedOutput->id,
                            'data-url' => Url::to(['update-output-indicator', 'id' => $expectedOutput->id, 'submission_id' => $model->id]),
                        ])  ?>
                    <?= !Yii::$app->user->can('Administrator') ? 
                            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                $expectedOutput->indicator != 'number of individual beneficiaries served' ?
                                    Html::a('Remove', ['/rpmes/plan/delete-output-indicator', 'id' => $expectedOutput->id, 'submission_id' => $model->id],[
                                        'class' => 'btn btn-xs btn-danger btn-block',
                                        'data' => [
                                            'confirm' => 'Are you sure want to remove this output indicator?',
                                            'method' => 'post',
                                        ],
                                    ]) :
                                '' :
                            '' :
                        Html::a('Remove', ['/rpmes/plan/delete-output-indicator', 'id' => $expectedOutput->id, 'submission_id' => $model->id],[
                            'class' => 'btn btn-xs btn-danger btn-block',
                            'data' => [
                                'confirm' => 'Are you sure want to remove this output indicator?',
                                'method' => 'post',
                            ],
                        ])
                    ?></td>
                </tr>
                <?php
                    Modal::begin([
                        'id' => 'update-oi-modal-'.$expectedOutput->id,
                        'size' => "modal-md",
                        'header' => '<div id="update-oi-modal-'.$expectedOutput->id.'-header"><h4>Edit Output Indicator</h4></div>',
                        'options' => ['tabindex' => false],
                    ]);
                    echo '<div id="update-oi-modal-'.$expectedOutput->id.'-content"></div>';
                    Modal::end();
                ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<?php
$this->registerJs('
    $(".update-button").click(function(e){
        e.preventDefault();

        var modalId = $(this).data("target");
        $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
        
        return false;
    });
');
?>