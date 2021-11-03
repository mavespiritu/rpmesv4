<?php if($type == 'excel'){ ?>
    <style>
    table{
        border-collapse: collapse;
    }
    thead{
        font-size: 12px;
        text-align: center;
    }

    td{
        font-size: 12px;
        border: 1px solid black;
    }

    th{
        text-align: center;
        border: 1px solid black;
    }
</style>

<?php } ?>

<?php 
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

function getChildren($elements, $padding = 0, $stage, $year, $appropriation){
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

                    if($appropriation)
                    {
                        if($appropriation->appropriationPaps)
                        {
                            foreach($appropriation->getAppropriationPaps()->orderBy(['arrangement' => SORT_ASC])->all() as $pap)
                            {
                                $data.= isset($element['source'][$pap->pap->id][$pap->fundSource->code]) ? $element['source'][$pap->pap->id][$pap->fundSource->code] > 0 ? '<td align=right><b>'.number_format($element['source'][$pap->pap->id][$pap->fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                $data.= isset($element['ppmp'][$pap->pap->id][$pap->fundSource->code]) ? $element['ppmp'][$pap->pap->id][$pap->fundSource->code] > 0 ? '<td align=right><b>'.number_format($element['ppmp'][$pap->pap->id][$pap->fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                
                                $totalAppPerObject += isset($element['source'][$pap->pap->id][$pap->fundSource->code]) ? $element['source'][$pap->pap->id][$pap->fundSource->code] : 0;
                                $totalPpmpPerObject += isset($element['ppmp'][$pap->pap->id][$pap->fundSource->code]) ? $element['ppmp'][$pap->pap->id][$pap->fundSource->code] : 0;
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

                $data.= getChildren($element['children'], intval($padding) + 20, $stage, $year, $appropriation);
            }else{
                $data.='<tr>';
                    $data.='<td style="text-indent: '.$padding.'px;">'.$element['title'].'</td>';

                    if($appropriation)
                    {
                        if($appropriation->appropriationPaps)
                        {
                            foreach($appropriation->getAppropriationPaps()->orderBy(['arrangement' => SORT_ASC])->all() as $pap)
                            {
                                $data.= isset($element['source'][$pap->pap->id][$pap->fundSource->code]) ? $element['source'][$pap->pap->id][$pap->fundSource->code] > 0 ? '<td align=right>'.number_format($element['source'][$pap->pap->id][$pap->fundSource->code], 2).'</td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                $data.= isset($element['ppmp'][$pap->pap->id][$pap->fundSource->code]) ? $element['ppmp'][$pap->pap->id][$pap->fundSource->code] > 0 ? '<td align=right>'.number_format($element['ppmp'][$pap->pap->id][$pap->fundSource->code], 2).'</td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>';
                                
                                $totalAppPerObject += isset($element['source'][$pap->pap->id][$pap->fundSource->code]) ? $element['source'][$pap->pap->id][$pap->fundSource->code] : 0;
                                $totalPpmpPerObject += isset($element['ppmp'][$pap->pap->id][$pap->fundSource->code]) ? $element['ppmp'][$pap->pap->id][$pap->fundSource->code] : 0;
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

<table class="table table-responsive table-bordered table-hover">
    <thead>
        <tr>
            <th rowspan=2>Objects</th>
            <?php if(!empty($headers)){ ?>
                <?php foreach($headers as $pap => $header){ ?>
                    <th colspan=<?= count($header) ?>><?= $pap ?></th>
                <?php } ?>
            <?php } ?>
            <th rowspan=2>PPMP Total</th>
            <th rowspan=2><?= $appropriation ? $appropriation->type.' '.$appropriation->year : 'No encoded appropriation' ?></th>
            <th rowspan=2><?= $prevAppropriation ? $prevAppropriation->type.' '.$prevAppropriation->year : 'No encoded appropriation' ?></th>
            <th colspan=2><?= $appropriation ? $prevAppropriation ? $appropriation->type.' '.$appropriation->year. ' vs. ' .$prevAppropriation->type.' '.$prevAppropriation->year : $appropriation->type.' '.$appropriation->year. ' vs. No encoded appropriation' : 'No encoded appropriation' ?></th>
        </tr>
        <tr>
            <?php if(!empty($headers)){ ?>
                <?php foreach($headers as $header){ ?>
                    <?php if(!empty($header)){ ?>
                        <?php foreach($header as $head){ ?>
                            <th><?= $head ?></th>
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
            <?php if($appropriation){ ?>
                <?php if($appropriation->appropriationPaps){ ?>
                    <?php foreach($appropriation->getAppropriationPaps()->orderBy(['arrangement' => SORT_ASC])->all() as $pap){ ?>
                        <td align="right"><b><?= isset($total['source'][$pap->pap->id][$pap->fundSource->code]) ? $total['source'][$pap->pap->id][$pap->fundSource->code] > 0 ? number_format($total['source'][$pap->pap->id][$pap->fundSource->code], 2) : '&nbsp;' : '&nbsp;' ?></b></td>
                        <td align="right"><b><?= isset($total['ppmp'][$pap->pap->id][$pap->fundSource->code]) ? $total['ppmp'][$pap->pap->id][$pap->fundSource->code] > 0 ? number_format($total['ppmp'][$pap->pap->id][$pap->fundSource->code], 2) : '&nbsp;' : '&nbsp;' ?></b></td>
                        
                        <?php $grandTotalAppPerObject += isset($total['source'][$pap->pap->id][$pap->fundSource->code]) ? $total['source'][$pap->pap->id][$pap->fundSource->code] : 0 ?>
                        <?php $grandTotalPpmpPerObject += isset($total['ppmp'][$pap->pap->id][$pap->fundSource->code]) ? $total['ppmp'][$pap->pap->id][$pap->fundSource->code] : 0 ?>
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
            <td align=right><b><?= $grandTotalAppDifference >= 0 ? $grandTotalAppDifference == 0 ? '' : number_format($grandTotalAppDifference, 2) : '('.number_format(abs($grandTotalAppDifference), 2).')' ?></b></td>
            <td align=right><b><?= $grandTotalPrevSourcePerObject != 0 ? ($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100 >= 0 ? number_format(($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100, 2) : '('.number_format(abs(($grandTotalAppDifference/$grandTotalPrevSourcePerObject)*100), 2).')' : '' ?></b></td>
        </tr>
        <?= getChildren(getAreaTree($data, null), "", $stage, $year, $appropriation) ?>
    </tbody>
</table>