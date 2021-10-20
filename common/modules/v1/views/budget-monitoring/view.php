<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;   
use yii\bootstrap\Modal;
$total = 0;

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

function getChildren($elements, $padding = 0, $fontSize = 20, $fundSources, $offices, $stage, $year, $activity){
    $data = '';
    if(!empty($elements)){
        foreach ($elements as $element) {
            if(isset($element["children"])){
                $nroAppropriation = 0;
                $rdcAppropriation = 0;
                $nroTotal = 0;
                $rdcTotal = 0;
                $data.='<tr>';
                    $data.='<td style="text-indent: '.$padding.'px; width: 20%; font-size: '.$fontSize.'px;"><b>'.$element['title'].'</b></td>';

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $data.= $element['source'.$fundSource->code] > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right><b>'.number_format($element['source'.$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>';
                        }
                    }

                    $nroAppropriation += isset($element['sourceNRO']) ? $element['sourceNRO'] : 0;
                    $rdcAppropriation += isset($element['sourceRDC']) ? $element['sourceRDC'] : 0;

                    if($offices)
                    {
                        foreach($offices as $office)
                        {
                            if($fundSources)
                            {
                                foreach($fundSources as $fundSource)
                                {
                                    $data.= $element['ppmp'.$office->abbreviation.$fundSource->code] > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right><b>'.number_format($element['ppmp'.$office->abbreviation.$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>';
                                    if($fundSource->code == 'NRO')
                                    {
                                        $nroTotal += $element['ppmp'.$office->abbreviation.$fundSource->code];
                                    }else{
                                        $rdcTotal += $element['ppmp'.$office->abbreviation.$fundSource->code];
                                    }
                                }
                            }
                        }
                    }

                    $data.= $nroTotal > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right><b>'.number_format($nroTotal, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= $rdcTotal > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right><b>'.number_format($rdcTotal, 2).'</b></td>' : '<td>&nbsp;</td>';
                    $data.= ($nroAppropriation - $nroTotal) >= 0 ? ($nroAppropriation - $nroTotal) == 0 ? '<td>&nbsp;</td>' : '<td style="font-size: '.$fontSize.'px;" align=right><b>'.number_format($nroAppropriation - $nroTotal, 2).'</b></td>' : '<td style="font-size: '.$fontSize.'px; color: red" align=right><b>('.number_format(abs($nroAppropriation - $nroTotal), 2).')</b></td>';
                    $data.= ($rdcAppropriation - $rdcTotal) >= 0 ? ($rdcAppropriation - $rdcTotal) == 0 ? '<td>&nbsp;</td>' : '<td style="font-size: '.$fontSize.'px;" align=right><b>'.number_format($rdcAppropriation - $rdcTotal, 2).'</b></td>' : '<td style="font-size: '.$fontSize.'px; color: red" align=right><b>('.number_format(abs($rdcAppropriation - $rdcTotal), 2).')</b></td>';
                $data.='</tr>';

                $data.= getChildren($element['children'], intval($padding) + 20, intval($fontSize) - 2, $fundSources, $offices, $stage, $year, $activity);
            }else{
                $nroAppropriation = 0;
                $rdcAppropriation = 0;
                $nroTotal = 0;
                $rdcTotal = 0;
                $data.='<tr>';
                    $data.='<td style="text-indent: '.$padding.'px; width: 20%; font-size: '.$fontSize.'px;">'.$element['title'].'</td>';

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $data.= $element['source'.$fundSource->code] > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right>'.number_format($element['source'.$fundSource->code], 2).'</td>' : '<td>&nbsp;</td>';
                        }
                    }

                    $nroAppropriation += isset($element['sourceNRO']) ? $element['sourceNRO'] : 0;
                    $rdcAppropriation += isset($element['sourceRDC']) ? $element['sourceRDC'] : 0;

                    if($offices)
                    {
                        foreach($offices as $office)
                        {
                            if($fundSources)
                            {
                                foreach($fundSources as $fundSource)
                                {
                                    $data.= $element['ppmp'.$office->abbreviation.$fundSource->code] > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right><a style="cursor: pointer;" href="javascript:void(0)" onclick="seeItems('.$office->id.','.$fundSource->id.','.$activity->id.',\''.$stage.'\','.$year.','.$element['id'].');" >'.number_format($element['ppmp'.$office->abbreviation.$fundSource->code], 2).'</a></td>' : '<td>&nbsp;</td>';
                                    if($fundSource->code == 'NRO')
                                    {
                                        $nroTotal += $element['ppmp'.$office->abbreviation.$fundSource->code];
                                    }else{
                                        $rdcTotal += $element['ppmp'.$office->abbreviation.$fundSource->code];
                                    }
                                }
                            }
                        }
                    }

                    $data.= $nroTotal > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right>'.number_format($nroTotal, 2).'</td>' : '<td>&nbsp;</td>';
                    $data.= $rdcTotal > 0 ? '<td style="font-size: '.$fontSize.'px;" align=right>'.number_format($rdcTotal, 2).'</td>' : '<td>&nbsp;</td>';
                    $data.= ($nroAppropriation - $nroTotal) >= 0 ? ($nroAppropriation - $nroTotal) == 0 ? '<td>&nbsp;</td>' : '<td style="font-size: '.$fontSize.'px;" align=right>'.number_format($nroAppropriation - $nroTotal, 2).'</td>' : '<td style="color: red" align=right>('.number_format(abs($nroAppropriation - $nroTotal), 2).')</td>';
                    $data.= ($rdcAppropriation - $rdcTotal) >= 0 ? ($rdcAppropriation - $rdcTotal) == 0 ? '<td>&nbsp;</td>' : '<td style="font-size: '.$fontSize.'px;" align=right>'.number_format($rdcAppropriation - $rdcTotal, 2).'</td>' : '<td style="color: red" align=right>('.number_format(abs($rdcAppropriation - $rdcTotal), 2).')</td>';
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
                ['label' => 'Excel', 'url' => Url::to(['/v1/budget-monitoring/download', 'type' => 'excel', 'post' => json_encode($postData)])],
                ['label' => 'PDF', 'url' => Url::to(['/v1/budget-monitoring/download', 'type' => 'pdf', 'post' => json_encode($postData)])],
            ],
        ],
    ]); ?>
    <?= Html::a('<i class="fa fa-print"></i> Print', ['#'],['class' => 'btn btn-danger']) ?>
</div>
<div class="clearfix"></div>
<br>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="freeze-table">
        <table class="table table-responsive table-bordered table-hover">
            <thead>
                <tr>
                    <th rowspan=3>Objects</th>
                    <th colspan=2><?= $activity->pap->short_code ?></th>
                    <?php if($offices){ ?>
                        <?php foreach($offices as $office){ ?>
                            <th colspan=2 rowspan=2><?= $office->abbreviation ?></th>
                        <?php } ?>
                    <?php } ?>
                    <th colspan=2 rowspan=2>Total Utilization</th>
                    <th colspan=2 rowspan=2>(Over) Under</th>
                </tr>
                <tr>
                    <th colspan=2><?= $activity->pap->codeTitle ?></th>
                </tr>
                <tr>
                    <?php for($i = 0; $i < 9; $i++){ ?>
                        <?php if($fundSources){ ?>
                            <?php foreach($fundSources as $fundSource){ ?>
                                <th><?= $fundSource->code ?></th>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?= getChildren(getAreaTree($data, null), "", "", $fundSources, $offices, $stage, $year, $activity) ?>
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
        $("#modal").modal("show").find("#modal-content").load("'.Url::to(['/v1/budget-monitoring']).'" + "/view-items?office_id=" + office_id + "&fund_source_id=" + fund_source_id + "&activity_id=" + activity_id + "&stage=" + stage + "&year=" + year + "&obj_id=" + obj_id);
    }

    $(document).ready(function() {
        $(".freeze-table").freezeTable({
            "scrollable": true,
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>