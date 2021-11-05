
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;   
use yii\bootstrap\Modal;

?>

<div class="freeze-table" style="height: 800px;">
    <table class="table table-condensed table-hover table-bordered table-responsive">
        <thead>
        <?php if(!empty($headers)){ ?>
        <tr>
            <th rowspan=3>PROGRAMS/PROJECTS/ACTIVITIES</th>
            <?php foreach($headers as $shortCode => $header){ ?>
            <th colspan=12><?= $shortCode ?></th>
            <th rowspan=3>TOTAL</th>
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
                <?php $papTotal = 0; ?>
                <tr style="background: yellow;">
                    <th colspan=<?= count($paps)*13 + 1?>><?= $pap->short_code != '' ? $pap->short_code : '-' ?></th>
                </tr>

                <?php foreach($pap->getActivities()->orderBy(['code' => SORT_ASC])->asArray()->all() as $activity){ ?>
                    <?php $tot = 0; ?>
                    <tr>
                        <td style="text-indent: 20px;"><?= $activity['title'] ?></td>
                        <?php foreach($headers as $shortCode => $header){ ?>
                            <?php $temp[$shortCode] = 0; ?>
                            <?php foreach($header as $division => $fundSrcs){ ?>
                                <?php foreach($fundSrcs as $fundSrc){ ?>
                                    <td align=right><?= isset($data[$shortCode][$activity['title']][$division][$fundSrc]) ? number_format($data[$shortCode][$activity['title']][$division][$fundSrc], 2) : '' ?></td>
                                    <?php $temp[$shortCode] += isset($data[$shortCode][$activity['title']][$division][$fundSrc]) ? $data[$shortCode][$activity['title']][$division][$fundSrc] : 0; ?>
                                    <?php $tot += isset($data[$shortCode][$activity['title']][$division][$fundSrc]) ? $data[$shortCode][$activity['title']][$division][$fundSrc] : 0; ?>
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