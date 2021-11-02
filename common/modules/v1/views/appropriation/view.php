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

function getChildren($elements, $padding = 0, $stage, $year){
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

                    if(!empty($element['source']))
                    {
                        foreach($element['source'] as $idx => $source)
                        {
                            if(!empty($source))
                            {
                                foreach($source as $fundSource => $value)
                                {
                                    $data.= $value > 0 ? '<td align=right><b>'.number_format($value, 2).'</b></td>' : '<td>&nbsp;</td>';
                                    $data.= isset($element['ppmp'][$idx][$fundSource]) ? $element['ppmp'][$idx][$fundSource] > 0 ? '<td align=right><b>'.number_format($element['ppmp'][$idx][$fundSource], 2).'</b></td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                    $totalAppPerObject += $value;
                                    $totalPpmpPerObject += isset($element['ppmp'][$idx][$fundSource]) ? $element['ppmp'][$idx][$fundSource] : 0;
                                }
                            }
                        }
                    }

                    // NEED TO FIXED HERE TO REFLECT TOTAL NOT BASING FROM THE SOURCE

                    $totalPrevSourcePerObject += isset($element['prevSource']) ? $element['prevSource'] : 0;
                    $totalAppDifference = $totalAppPerObject - $totalPrevSourcePerObject;

                    $data.= $totalPpmpPerObject > 0 ? '<td align=right><b>'.number_format($totalPpmpPerObject, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $totalAppPerObject > 0 ? '<td align=right><b>'.number_format($totalAppPerObject, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $totalPrevSourcePerObject > 0 ? '<td align=right><b>'.number_format($totalPrevSourcePerObject, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $totalAppDifference >= 0 ? $totalAppDifference == 0 ? '<td>&nbsp;</td>' : '<td align=right><b>'.number_format($totalAppDifference, 2).'</b></td>' : '<td align=right><b>('.number_format(abs($totalAppDifference), 2).')</b></td>';
                    $data.= $totalPrevSourcePerObject != 0 ? ($totalAppDifference/$totalPrevSourcePerObject)*100 >= 0 ? '<td align=right><b>'.number_format(($totalAppDifference/$totalPrevSourcePerObject)*100, 2).'</b></td>' : '<td align=right><b>('.number_format(abs(($totalAppDifference/$totalPrevSourcePerObject)*100), 2).')</b></td>' : '<td>&nbsp;</td>';
                $data.='</tr>';

                $data.= getChildren($element['children'], intval($padding) + 20, $stage, $year);
            }else{
                $data.='<tr>';
                    $data.='<td style="text-indent: '.$padding.'px;">'.$element['title'].'</td>';

                    if(!empty($element['source']))
                    {
                        foreach($element['source'] as $idx => $source)
                        {
                            if(!empty($source))
                            {
                                foreach($source as $fundSource => $value)
                                {
                                    $data.= $value > 0 ? '<td align=right>'.number_format($value, 2).'</td>' : '<td>&nbsp;</td>';
                                    $data.= isset($element['ppmp'][$idx][$fundSource]) ? $element['ppmp'][$idx][$fundSource] > 0 ? '<td align=right>'.number_format($element['ppmp'][$idx][$fundSource], 2).'</td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                    $totalAppPerObject += $value;
                                    $totalPpmpPerObject += isset($element['ppmp'][$idx][$fundSource]) ? $element['ppmp'][$idx][$fundSource] : 0;
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
                    <th rowspan=3>Objects</th>
                    <?php if(!empty($headers)){ ?>
                        <?php foreach($headers as $costStructure => $header){ ?>
                            <th colspan=<?= array_sum(array_map("count", $header));?>><?= $costStructure ?></th>
                        <?php } ?>
                    <?php } ?>
                    <th rowspan=3>PPMP Total</th>
                    <th rowspan=3><?= $appropriation ? $appropriation->type.' '.$appropriation->year : 'No encoded appropriation' ?></th>
                    <th rowspan=3><?= $prevAppropriation ? $prevAppropriation->type.' '.$prevAppropriation->year : 'No encoded appropriation' ?></th>
                    <th rowspan=2 colspan=2><?= $appropriation ? $prevAppropriation ? $appropriation->type.' '.$appropriation->year. ' vs. ' .$prevAppropriation->type.' '.$prevAppropriation->year : $appropriation->type.' '.$appropriation->year. ' vs. No encoded appropriation' : 'No encoded appropriation' ?></th>
                </tr>
                <tr>
                    <?php if(!empty($headers)){ ?>
                        <?php foreach($headers as $header){ ?>
                            <?php if(!empty($header)){ ?>
                                <?php foreach($header as $pap => $contents){ ?>
                                    <th colspan=<?= count($contents) ?>><?= $pap ?></th>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <tr>
                    <?php if(!empty($headers)){ ?>
                        <?php foreach($headers as $header){ ?>
                            <?php if(!empty($header)){ ?>
                                <?php foreach($header as $contents){ ?>
                                    <?php if(!empty($contents)){ ?>
                                        <?php foreach($contents as $content){ ?>
                                            <th><?= $content ?></th>
                                        <?php } ?>  
                                    <?php } ?>
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
                    <?php if(!empty($total)){ ?>
                        <?php foreach($total['source'] as $idx => $source){ ?>
                            <?php foreach($source as $fundSource => $value){ ?>
                                <td align="right"><b><?= $value > 0 ? number_format($value, 2) : '&nbsp;' ?></b></td>
                                <td align="right"><b><?= isset($total['ppmp'][$idx][$fundSource]) ? $total['ppmp'][$idx][$fundSource] > 0 ? number_format($total['ppmp'][$idx][$fundSource], 2) : '&nbsp;' : '&nbsp;' ?></b></td>
                                <?php $grandTotalAppPerObject += $value ?>
                                <?php $grandTotalPpmpPerObject += isset($total['ppmp'][$idx][$fundSource]) ? $total['ppmp'][$idx][$fundSource]: 0 ?>
                            <?php } ?>
                        <?php } ?>
                        <?php $grandTotalPrevSourcePerObject += $total['prevSource'] ?>
                    <?php } ?>
                    <?php $grandTotalAppDifference = $grandTotalAppPerObject - $grandTotalPrevSourcePerObject; ?>
                    <td align=right><b><?= $grandTotalPpmpPerObject > 0 ? number_format($grandTotalPpmpPerObject, 2) : '&nbsp;' ?></b></td>
                    <td align=right><b><?= $grandTotalAppPerObject > 0 ? number_format($grandTotalAppPerObject, 2) : '&nbsp;' ?></b></td>
                    <td align=right><b><?= $grandTotalPrevSourcePerObject > 0 ? number_format($grandTotalPrevSourcePerObject, 2) : '&nbsp;' ?></b></td>
                    <td align=right><b><?= $grandTotalAppDifference >= 0 ? $grandTotalAppDifference == 0 ? '' : number_format($grandTotalAppDifference, 2) : '('.number_format(abs($grandTotalAppDifference), 2).')' ?></b></td>
                    <td align=right><b><?= $grandTotalPrevSourcePerObject != 0 ? ($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100 >= 0 ? number_format(($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100, 2) : '('.number_format(abs(($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100), 2).')' : '' ?></b></td>
                </tr>
                <?= getChildren(getAreaTree($data, null), "", "", $stage, $year) ?>
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