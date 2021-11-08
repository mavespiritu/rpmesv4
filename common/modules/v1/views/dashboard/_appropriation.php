
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;   
use yii\bootstrap\Modal;

?>

<div class="freeze-table" style="height: 800px;">
    <h5><?= $appropriation ? $appropriation->type.' '.$appropriation->year.' SUMMARY' : '' ?></h5>
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

                            <?= $source - $ppmp != 0 ? $source - $ppmp > 0 ? '<td align=right><b>'.number_format($source - $ppmp, 2).'</b></td>' : '<td align=right style="color: red;"><b>('.number_format(abs($source - $ppmp), 2).')</b></td>' : '<td>&nbsp;</td>' ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
  $script = '
    $(document).ready(function() {
        $(".freeze-table").freezeTable({
            "scrollable": true,
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>