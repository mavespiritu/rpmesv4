<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;   
use yii\bootstrap\Modal;

$grandTotalAppPerObject = 0;
$grandTotalPpmpPerObject = 0;
$grandTotalPrevSourcePerObject = 0;
$grandTotalAppDifference = 0;

function getAreaTree(array $elements, $parentId = null) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['obj_id'] == $parentId) {
            $children = getAreaTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
    return empty($branch) ? null : $branch;
}

function getChildren($elements, $padding = 0, $stage, $year, $paps, $fundSources){
    $data = '';
    if(!empty($elements)){
        foreach ($elements as $element) {
            $totalAppPerObject = 0;
            $totalPpmpPerObject = 0;
            $totalPrevSourcePerObject = 0;
            $totalAppDifference = 0;
            if(isset($element["children"])){
                $data.='<tr style="background: #F9F9F9;">';
                    $data.='<td style="text-indent: '.$padding.'px;"><b>'.$element['title'].'</b></td>';

                    if($paps)
                    {
                        foreach($paps as $pap)
                        {
                            if($fundSources)
                            {
                                foreach($fundSources as $fundSource)
                                {
                                    $data.= isset($element['source'][$pap->id][$fundSource->code]) ? $element['source'][$pap->id][$fundSource->code] > 0 ? '<td align=right><b>'.number_format($element['source'][$pap->id][$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                    $data.= isset($element['ppmp'][$pap->id][$fundSource->code]) ? $element['ppmp'][$pap->id][$fundSource->code] > 0 ? '<td align=right><b>'.number_format($element['ppmp'][$pap->id][$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                
                                    $totalAppPerObject += isset($element['source'][$pap->id][$fundSource->code]) ? $element['source'][$pap->id][$fundSource->code] : 0;
                                    $totalPpmpPerObject += isset($element['ppmp'][$pap->id][$fundSource->code]) ? $element['ppmp'][$pap->id][$fundSource->code] : 0;
                                }
                            }
                        }
                    }

                    $totalPrevSourcePerObject += isset($element['prevSource']) ? $element['prevSource'] : 0;
                    $totalAppDifference = $totalAppPerObject - $totalPrevSourcePerObject;

                    $data.= $totalPpmpPerObject > 0 ? '<td align=right><b>'.number_format($totalPpmpPerObject, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $totalAppPerObject > 0 ? '<td align=right><b>'.number_format($totalAppPerObject, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $totalPrevSourcePerObject > 0 ? '<td align=right><b>'.number_format($totalPrevSourcePerObject, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $totalAppDifference >= 0 ? $totalAppDifference == 0 ? '<td>&nbsp;</td>' : '<td align=right><b>'.number_format($totalAppDifference, 2).'</b></td>' : '<td align=right><b>('.number_format(abs($totalAppDifference), 2).')</b></td>';
                    $data.= $totalPrevSourcePerObject != 0 ? ($totalAppDifference/$totalPrevSourcePerObject)*100 >= 0 ? '<td align=right><b>'.number_format(($totalAppDifference/$totalPrevSourcePerObject)*100, 2).'</b></td>' : '<td align=right><b>('.number_format(abs(($totalAppDifference/$totalPrevSourcePerObject)*100), 2).')</b></td>' : '<td>&nbsp;</td>';
                $data.='</tr>';

                $data.= getChildren($element['children'], intval($padding) + 20, $stage, $year, $paps, $fundSources);
            }else{
                $data.='<tr>';
                    $data.='<td style="text-indent: '.$padding.'px;">'.$element['title'].'</td>';

                    if($paps)
                    {
                        foreach($paps as $pap)
                        {
                            if($fundSources)
                            {
                                foreach($fundSources as $fundSource)
                                {
                                    $data.= isset($element['source'][$pap->id][$fundSource->code]) ? $element['source'][$pap->id][$fundSource->code] > 0 ? '<td align=right>'.number_format($element['source'][$pap->id][$fundSource->code], 2).'</td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                    $data.= isset($element['ppmp'][$pap->id][$fundSource->code]) ? $element['ppmp'][$pap->id][$fundSource->code] > 0 ? '<td align=right>'.number_format($element['ppmp'][$pap->id][$fundSource->code], 2).'</td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                
                                    $totalAppPerObject += isset($element['source'][$pap->id][$fundSource->code]) ? $element['source'][$pap->id][$fundSource->code] : 0;
                                    $totalPpmpPerObject += isset($element['ppmp'][$pap->id][$fundSource->code]) ? $element['ppmp'][$pap->id][$fundSource->code] : 0;
                                }
                            }
                        }
                    }

                    $totalPrevSourcePerObject += isset($element['prevSource']) ? $element['prevSource'] : 0;
                    $totalAppDifference = $totalAppPerObject - $totalPrevSourcePerObject;

                    $data.= $totalPpmpPerObject > 0 ? '<td align=right>'.number_format($totalPpmpPerObject, 2).'</td>' : '<td>&nbsp;</td>';
                    $data.= $totalAppPerObject > 0 ? '<td align=right>'.number_format($totalAppPerObject, 2).'</td>' : '<td>&nbsp;</td>';
                    $data.= $totalPrevSourcePerObject > 0 ? '<td align=right>'.number_format($totalPrevSourcePerObject, 2).'</td>' : '<td>&nbsp;</td>';
                    $data.= $totalAppDifference >= 0 ? $totalAppDifference == 0 ? '<td>&nbsp;</td>' : '<td align=right>'.number_format($totalAppDifference, 2).'</td>' : '<td align=right>('.number_format(abs($totalAppDifference), 2).')</td>';
                    $data.= $totalPrevSourcePerObject != 0 ? ($totalAppDifference/$totalPrevSourcePerObject)*100 >= 0 ? '<td align=right>'.number_format(($totalAppDifference/$totalPrevSourcePerObject)*100, 2).'</td>' : '<td align=right>('.number_format(abs(($totalAppDifference/$totalPrevSourcePerObject)*100), 2).')</td>' : '<td>&nbsp;</td>';
                $data.='</tr>';
            }
        }
    }

    return $data;
}

?>

<div class="pull-left">
    <?= ButtonDropdown::widget([
        'label' => 'Export',
        'options' => ['class' => 'btn btn-success'],
        'dropdown' => [
            'items' => [
                ['label' => 'Excel', 'url' => Url::to(['/v1/appropriation/download', 'type' => 'excel', 'post' => json_encode($postData)])],
                ['label' => 'PDF', 'url' => Url::to(['/v1/appropriation/download', 'type' => 'pdf', 'post' => json_encode($postData)])],
            ],
        ],
    ]); ?>
    <?php // Html::a('<i class="fa fa-print"></i> Print', ['#'],['class' => 'btn btn-danger']) ?>
</div>
<div class="clearfix"></div>
<br>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="freeze-table" style="height: 800px;">
        <table class="table table-responsive table-bordered table-hover">
            <thead>
                <tr>
                    <th rowspan=2>Objects</th>
                    <?php if($paps){ ?>
                        <?php foreach($paps as $pap){ ?>
                            <th colspan=4><?= $pap->codeAndTitle ?></th>
                        <?php } ?>
                    <?php } ?>
                    <th rowspan=2>PPMP Total</th>
                    <th rowspan=2><?= $appropriation ? $appropriation->type.' '.$appropriation->year : 'No encoded appropriation' ?></th>
                    <th rowspan=2><?= $prevAppropriation ? $prevAppropriation->type.' '.$prevAppropriation->year : 'No encoded appropriation' ?></th>
                    <th colspan=2><?= $appropriation ? $prevAppropriation ? $appropriation->type.' '.$appropriation->year. ' vs. ' .$prevAppropriation->type.' '.$prevAppropriation->year : $appropriation->type.' '.$appropriation->year. ' vs. No encoded appropriation' : 'No encoded appropriation' ?></th>
                </tr>
                <tr>
                    <?php if($paps){ ?>
                        <?php foreach($paps as $pap){ ?>
                            <?php if($fundSources){ ?>
                                <?php foreach($fundSources as $fundSource){ ?>
                                    <th><?= $fundSource->code ?></th>
                                    <th>PPMP</th>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <th>Amount</th>
                    <th>(%)</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background: #F9F9F9;">
                    <td><b>Total</b></td>
                    <?php if($paps){ ?>
                        <?php foreach($paps as $pap){ ?>
                            <?php if($fundSources){ ?>
                                <?php foreach($fundSources as $fundSource){ ?>
                                    <td align="right"><b><?= isset($total['source'][$pap->id][$fundSource->code]) ? $total['source'][$pap->id][$fundSource->code] > 0 ? number_format($total['source'][$pap->id][$fundSource->code], 2) : '&nbsp;' : '&nbsp;' ?></b></td>
                                    <td align="right"><b><?= isset($total['ppmp'][$pap->id][$fundSource->code]) ? $total['ppmp'][$pap->id][$fundSource->code] > 0 ? number_format($total['ppmp'][$pap->id][$fundSource->code], 2) : '&nbsp;' : '&nbsp;' ?></b></td>
                                    
                                    <?php $grandTotalAppPerObject += isset($total['source'][$pap->id][$fundSource->code]) ? $total['source'][$pap->id][$fundSource->code] : 0 ?>
                                    <?php $grandTotalPpmpPerObject += isset($total['ppmp'][$pap->id][$fundSource->code]) ? $total['ppmp'][$pap->id][$fundSource->code] : 0 ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>

                    <?php if(!empty($total)){ ?>
                        <?php $grandTotalPrevSourcePerObject += isset($total['prevSource']) ? $total['prevSource'] : 0 ?>
                    <?php } ?>

                    <?php $grandTotalAppDifference = $grandTotalAppPerObject - $grandTotalPrevSourcePerObject; ?>
                    <td align=right><b><?= $grandTotalPpmpPerObject > 0 ? number_format($grandTotalPpmpPerObject, 2) : '&nbsp;' ?></b></td>
                    <td align=right><b><?= $grandTotalAppPerObject > 0 ? number_format($grandTotalAppPerObject, 2) : '&nbsp;' ?></b></td>
                    <td align=right><b><?= $grandTotalPrevSourcePerObject > 0 ? number_format($grandTotalPrevSourcePerObject, 2) : '&nbsp;' ?></b></td>
                    <?= $grandTotalAppDifference >= 0 ? $grandTotalAppDifference == 0 ? '<td>&nbsp;</td>' : '<td align=right><b>'.number_format($grandTotalAppDifference, 2).'</b></td>' : '<td style="color: red;" align=right><b>('.number_format(abs($grandTotalAppDifference), 2).')</b></td>' ?></b></td>
                    <?= $grandTotalPrevSourcePerObject != 0 ? ($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100 >= 0 ? '<td align=right><b>'.number_format(($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100, 2).'</b></td>' : '<td style="color: red;" align=right><b>('.number_format(abs(($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100), 2).')</b></td>' : '<td>&nbsp;</td>' ?></b></td>
                </tr>
                <?= getChildren(getAreaTree($data, null), "", $stage, $year, $paps, $fundSources) ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php
  Modal::begin([
    'id' => 'modal',
    'size' => "modal-lg",
    'header' => '<div id="modal-header"><h4>View Items</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="modal-content"></div>';
  Modal::end();
?>
<?php
  $script = '
    function seeItems(office_id, fund_source_id, activity_id, stage, year, obj_id)
    {
        $("#modal").modal("show").find("#modal-content").load("'.Url::to(['/v1/appropriation']).'" + "/view-items?office_id=" + office_id + "&fund_source_id=" + fund_source_id + "&activity_id=" + activity_id + "&stage=" + stage + "&year=" + year + "&obj_id=" + obj_id);
    }

    $(document).ready(function() {
        $(".freeze-table").freezeTable({
            "scrollable": true,
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>