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

<h5>FINANCIAL PLAN</h5>
<table class="table table-condensed table-hover table-bordered table-responsive">
    <thead>
    <?php if(!empty($headers)){ ?>
    <tr>
        <th rowspan=3>PROGRAMS/PROJECTS/ACTIVITIES</th>
        <?php foreach($headers as $shortCode => $header){ ?>
        <th colspan=<?= (count($offices) * count($fundSources)) ?>><?= $shortCode ?></th>
        <th rowspan=3><?= $shortCode ?> TOTAL</th>
        <?php } ?>
        <th rowspan=3>GRAND TOTAL</th>
    </tr>
    <tr>
        <?php foreach($headers as $shortCode => $header){ ?>
            <?php foreach($header as $division => $fundSrcs){ ?>
                <th colspan=2><?= $division ?></th>
            <?php } ?>
        <?php } ?>
    </tr>
    <tr>
        <?php foreach($headers as $shortCode => $header){ ?>
            <?php foreach($header as $division => $fundSrcs){ ?>
                <?php foreach($fundSrcs as $fundSrc){ ?>
                    <th><?= $fundSrc ?></th>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </tr>
    <?php } ?>
    </thead>
    <tbody>
    <?php if($paps){ ?>
        <?php foreach($paps as $pap){ ?>
            <?php $papTot = 0; ?>
            <tr style="background: yellow;">
                <th><?= $pap->short_code != '' ? $pap->short_code : '-' ?></th>
                <?php if(!empty($headers)){ ?>
                    <?php foreach($headers as $shortCode => $divisions){ ?>
                        <?php $tempTot[$shortCode] = 0; ?>
                        <?php if(!empty($divisions)){ ?>
                            <?php foreach($divisions as $division => $fundSrcs){ ?>
                                <?php if(!empty($fundSrcs)){ ?>
                                    <?php foreach($fundSrcs as $fundSrc){ ?>
                                        <?= isset($total[$pap->short_code][$shortCode][$division][$fundSrc]) ? $total[$pap->short_code][$shortCode][$division][$fundSrc] > 0 ? '<td align=right><b>'.number_format($total[$pap->short_code][$shortCode][$division][$fundSrc], 2).'</b></td>' : '<td>&nbsp;</td>' : '<td>&nbsp;</td>' ?>
                                        <?php $tempTot[$shortCode] += isset($total[$pap->short_code][$shortCode][$division][$fundSrc]) ? $total[$pap->short_code][$shortCode][$division][$fundSrc] : 0; ?>
                                        <?php $papTot += isset($total[$pap->short_code][$shortCode][$division][$fundSrc]) ? $total[$pap->short_code][$shortCode][$division][$fundSrc] : 0; ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <td align=right><b><?= $tempTot[$shortCode] > 0 ? number_format($tempTot[$shortCode], 2) : '' ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=right><b><?= $papTot > 0 ? number_format($papTot, 2) : '' ?></b></td>
            </tr>

            <?php foreach($pap->getActivities()->orderBy(['code' => SORT_ASC])->asArray()->all() as $activity){ ?>
                <?php $tot = 0; ?>
                <tr>
                    <td style="text-indent: 20px;"><?= $activity['title'] ?></td>
                    <?php foreach($headers as $shortCode => $header){ ?>
                        <?php $temp[$shortCode] = 0; ?>
                        <?php foreach($header as $division => $fundSrcs){ ?>
                            <?php foreach($fundSrcs as $fundSrc){ ?>
                                <td align=right><?= isset($data[$activity['title']][$shortCode][$division][$fundSrc]) ? number_format($data[$activity['title']][$shortCode][$division][$fundSrc], 2) : '' ?></td>
                                <?php $temp[$shortCode] += isset($data[$activity['title']][$shortCode][$division][$fundSrc]) ? $data[$activity['title']][$shortCode][$division][$fundSrc] : 0; ?>
                                <?php $tot += isset($data[$activity['title']][$shortCode][$division][$fundSrc]) ? $data[$activity['title']][$shortCode][$division][$fundSrc] : 0; ?>
                            <?php } ?>
                        <?php } ?>
                        <td align=right><b><?= $temp[$shortCode] > 0 ? number_format($temp[$shortCode], 2) : '' ?></b></td>
                    <?php } ?>
                    <td align=right><b><?= $tot > 0 ? number_format($tot, 2) : '' ?></b></td>
                </tr>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
