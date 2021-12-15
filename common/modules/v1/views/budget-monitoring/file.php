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

function getChildren($elements, $padding = 0, $fundSources, $offices, $activity, $stage, $year){
    $data = '';
    if(!empty($elements)){
        foreach ($elements as $element) {
            if(isset($element["children"])){
                $utilizationTotal = [];
                $data.='<tr style="background: #F9F9F9;">';
                
                    $data.='<td style="text-indent: '.$padding.'px; width: 20%;"><b>'.$element['title'].'</b></td>';

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $data.= $element['source'][$fundSource->code] > 0 ? '<td align=right><b>'.number_format($element['source'][$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>';

                            $utilizationTotal[$fundSource->code] = 0;
                        }
                    }

                    if($offices)
                    {
                        foreach($offices as $office)
                        {
                            if($fundSources)
                            {
                                foreach($fundSources as $fundSource)
                                {
                                    $data.= $element['ppmp'][$office->id][$fundSource->code] > 0 ? '<td align=right><b>'.number_format($element['ppmp'][$office->id][$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>';

                                    $utilizationTotal[$fundSource->code] += $element['ppmp'][$office->id][$fundSource->code];
                                }
                            }
                        }
                    }

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $data.= $utilizationTotal[$fundSource->code] > 0 ? '<td align=right><b>'.number_format($utilizationTotal[$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>';
                        }
                    }

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $overUnder = $element['source'][$fundSource->code] - $utilizationTotal[$fundSource->code];
                            $data.= $overUnder != 0 ? $overUnder > 0 ? '<td align=right><b>'.number_format($overUnder, 2).'</b></td>' : '<td align=right style="color: red;"><b>('.number_format(abs($overUnder), 2).')</b></td>' : '<td>&nbsp;</td>';
                        }
                    }

                $data.='</tr>';

                $data.= getChildren($element['children'], intval($padding) + 20, $fundSources, $offices, $activity, $stage, $year);
            }else{
                $utilizationTotal = [];

                $data.='<tr>';
                
                    $data.='<td style="text-indent: '.$padding.'px; width: 20%;">'.$element['title'].'</td>';

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $data.= $element['source'][$fundSource->code] > 0 ? '<td align=right>'.number_format($element['source'][$fundSource->code], 2).'</td>' : '<td>&nbsp;</td>';

                            $utilizationTotal[$fundSource->code] = 0;
                        }
                    }

                    if($offices)
                    {
                        foreach($offices as $office)
                        {
                            if($fundSources)
                            {
                                foreach($fundSources as $fundSource)
                                {
                                    $data.= $element['ppmp'][$office->id][$fundSource->code] > 0 ? '<td align=right>'.number_format($element['ppmp'][$office->id][$fundSource->code], 2).'</td>' : '<td>&nbsp;</td>';

                                    $utilizationTotal[$fundSource->code] += $element['ppmp'][$office->id][$fundSource->code];
                                }
                            }
                        }
                    }

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $data.= $utilizationTotal[$fundSource->code] > 0 ? '<td align=right>'.number_format($utilizationTotal[$fundSource->code], 2).'</td>' : '<td>&nbsp;</td>';
                        }
                    }

                    if($fundSources)
                    {
                        foreach($fundSources as $fundSource)
                        {
                            $overUnder = $element['source'][$fundSource->code] - $utilizationTotal[$fundSource->code];
                            $data.= $overUnder != 0 ? $overUnder > 0 ? '<td align=right>'.number_format($overUnder, 2).'</td>' : '<td align=right style="color: red;">('.number_format(abs($overUnder), 2).')</td>' : '<td>&nbsp;</td>';
                        }
                    }
                    
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
            <?php for($i = 0; $i < (count($offices) + 3); $i++){ ?>
                <?php if($fundSources){ ?>
                    <?php foreach($fundSources as $fundSource){ ?>
                        <th><?= $fundSource->code ?></th>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr style="background: #F9F9F9;">
            <?php $grandUtilizationTotal = []; ?>
            <td><b>Total</b></td>
            <?php if(!empty($total)){ ?>
                <?php if($fundSources){ ?>
                    <?php foreach($fundSources as $fundSource){ ?>
                        <td align=right><b><?= $total['source'][$fundSource->code] > 0 ? number_format($total['source'][$fundSource->code], 2) : '' ?></td>
                        <?php $grandUtilizationTotal[$fundSource->code] = 0; ?>
                    <?php } ?>
                <?php } ?>

                <?php if($offices){ ?>
                    <?php foreach($offices as $office){ ?>
                        <?php if($fundSources){ ?>
                            <?php foreach($fundSources as $fundSource){ ?>
                                <td align=right><b><?= $total['ppmp'][$office->id][$fundSource->code] > 0 ? number_format($total['ppmp'][$office->id][$fundSource->code], 2) : '' ?></td>
                                <?php $grandUtilizationTotal[$fundSource->code] += $total['ppmp'][$office->id][$fundSource->code]; ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                
                <?php if($fundSources){ ?>
                    <?php foreach($fundSources as $fundSource){ ?>
                        <?= $grandUtilizationTotal[$fundSource->code] > 0 ? '<td align=right><b>'.number_format($grandUtilizationTotal[$fundSource->code], 2).'</b></td>' : '<td>&nbsp;</td>'; ?>
                    <?php } ?>
                <?php } ?>

                <?php if($fundSources){ ?>
                    <?php foreach($fundSources as $fundSource){ ?>
                        <?php $overUnder = $total['source'][$fundSource->code] - $grandUtilizationTotal[$fundSource->code]; ?>
                        <?= $overUnder != 0 ? $overUnder > 0 ? '<td align=right><b>'.number_format($overUnder, 2).'</b></td>' : '<td align=right style="color: red;"><b>('.number_format(abs($overUnder), 2).')</b></td>' : '<td>&nbsp;</td>'; ?>
                    <?php } ?>
                <?php } ?>    
            <?php } ?>
        </tr>
        <?= getChildren(getAreaTree($data, null), "", $fundSources, $offices, $activity, $stage, $year) ?>
    </tbody>
    </table>
