
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;   
use yii\bootstrap\Modal;

?>

<div class="freeze-table" style="height: 800px;">
    <h5><?= $appropriation ? $appropriation->type.' '.$appropriation->year : '' ?></h5>
    <table class="table table-condensed table-hover table-bordered table-responsive">
        <thead>
            <tr>
            <th>PREXC</th>
            <?php if($fundSources){ ?>
                <?php foreach($fundSources as $fundSource){ ?>
                    <th><?= $fundSource->code ?></th>
                <?php } ?>
            <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php if($paps){ ?>
            <?php foreach($paps as $pap){ ?>
                <tr>
                    <th><?= $pap->short_code != '' ? $pap->short_code : '-' ?></th>
                <?php if($fundSources){ ?>
                    <?php foreach($fundSources as $fundSource){ ?>
                        <td align=right><b><?= isset($data[$pap->id][$fundSource->code]['total']) ? number_format($data[$pap->id][$fundSource->code]['total'], 2) : '' ?></b></td>
                    <?php } ?>
                <?php } ?>
                </tr>
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