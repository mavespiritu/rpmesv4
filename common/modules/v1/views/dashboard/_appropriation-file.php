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

$sourceTotal = 0;
$ppmpTotal = 0;

?>

<h5><?= $appropriation ? $appropriation->type.' '.$appropriation->year.' SUMMARY' : '' ?>
<table class="table table-condensed table-hover table-bordered table-responsive">
    <thead>
        <tr>
            <th colspan=2>PREXC</th>
            <th><?= $appropriation ? $appropriation->type.' '.$appropriation->year.' TOTAL' : '' ?></th>
            <th><?= 'PPMP TOTAL' ?></th>
            <th>Over (Deficit)</th>
        </tr>
    </thead>
    <tbody>
    <?php if($paps){ ?>
        <?php foreach($paps as $pap){ ?>
            <tr>
                <th rowspan=2><?= $pap->short_code != '' ? $pap->short_code : '-' ?></th>
                <?php if($fundSources){ ?>
                    <?php foreach($fundSources as $fundSource){ ?>
                        <th><?= $fundSource->code ?></th>
                        <td align=right><b><?= isset($data['source'][$pap->id][$fundSource->id]['total']) ? number_format($data['source'][$pap->id][$fundSource->id]['total'], 2) : '' ?></b></td>
                        <td align=right><b><?= isset($data['ppmp'][$pap->id][$fundSource->id]['total']) ? number_format($data['ppmp'][$pap->id][$fundSource->id]['total'], 2) : '' ?></b></td>

                        <?php $source = isset($data['source'][$pap->id][$fundSource->id]['total']) ? $data['source'][$pap->id][$fundSource->id]['total'] : 0 ?>
                        <?php $ppmp = isset($data['ppmp'][$pap->id][$fundSource->id]['total']) ? $data['ppmp'][$pap->id][$fundSource->id]['total'] : 0 ?>

                        <?php $sourceTotal += $source; ?>
                        <?php $ppmpTotal += $ppmp; ?>

                        <?= $source - $ppmp != 0 ? $source - $ppmp > 0 ? '<td align=right><b>'.number_format($source - $ppmp, 2).'</b></td>' : '<td align=right style="color: red;"><b>('.number_format(abs($source - $ppmp), 2).')</b></td>' : '<td>&nbsp;</td>' ?>
                        </tr><tr>
                    <?php } ?>
                <?php } ?>
        <?php } ?>
    <?php } ?>
    <tr>
        <td colspan=2 align=right><b>TOTAL</b></td>
        <td align=right><b><?= number_format($sourceTotal, 2) ?></b></td>
        <td align=right><b><?= number_format($ppmpTotal, 2) ?></b></td>
        <?= $sourceTotal - $ppmpTotal != 0 ? $sourceTotal - $ppmpTotal > 0 ? '<td align=right><b>'.number_format($sourceTotal - $ppmpTotal, 2).'</b></td>' : '<td align=right style="color: red;"><b>('.number_format(abs($sourceTotal - $ppmpTotal), 2).')</b></td>' : '<td>&nbsp;</td>' ?>
    </tr>
    </tbody>
</table>