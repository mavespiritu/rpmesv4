
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;   
use yii\bootstrap\Modal;

$sourceTotal = 0;
$ppmpTotal = 0;
?>
<div class="pull-left">
    <?= ButtonDropdown::widget([
        'label' => 'Export',
        'options' => ['class' => 'btn btn-success'],
        'dropdown' => [
            'items' => [
                ['label' => 'Excel', 'url' => Url::to(['/v1/ppmp-monitoring/download', 'type' => 'excel', 'post' => json_encode($postData)])],
                //['label' => 'PDF', 'url' => Url::to(['/v1/ppmp-monitoring/download', 'type' => 'pdf', 'post' => json_encode($postData)])],
            ],
        ],
    ]); ?>
    <?php // Html::a('<i class="fa fa-print"></i> Print', ['#'],['class' => 'btn btn-danger']) ?>
</div>
</h5>
<div class="clearfix"></div>
<br>
<div class="freeze-table" style="height: 800px;">
    <table class="table table-condensed table-hover table-bordered table-responsive">
        <thead>
            <tr>
                <?php if(!empty($groups)){ ?>
                    <?php foreach($groups as $group){ ?>
                        <?php if($group == 'prexc'){ ?>
                            <th rowspan=2>Program Code</th>
                        <?php }else if($group == 'activity'){ ?>
                            <th rowspan=2 colspan=2>PPAs</th>
                        <?php }else if($group == 'objectTitle'){ ?>
                            <th rowspan=2>Object</th>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <th rowspan=2>Item</th>
                <th>Quantity</th>
                <th rowspan=2>Estimated Budget</th>
                <?php if($quarters){ ?>
                    <?php foreach($quarters as $quarter){ ?>
                        <?php if($months){ ?>
                            <?php foreach($months as $month){ ?>
                                <?php if($quarter->quarter == $month->quarter){ ?>
                                    <th colspan=2><?= $month->abbreviation ?></th>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <th colspan=2><?= $quarter->quarter ?></th>
                    <?php } ?>
                <?php } ?>
            </tr>
            <tr>
                <th>Size</th>
                <?php if($quarters){ ?>
                    <?php foreach($quarters as $quarter){ ?>
                        <?php if($months){ ?>
                            <?php foreach($months as $month){ ?>
                                <?php if($quarter->quarter == $month->quarter){ ?>
                                    <th>Q</th>
                                    <th>C</th>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <th>Q</th>
                        <th>C</th>
                    <?php } ?>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($data)){ ?>
            <?php foreach($data as $firstFilter => $firstLevel){ ?>
                <tr>
                    <td colspan=<?= (count($quarters) + count($months))*2 + 7 ?>><?= $firstFilter ?></td>
                </tr>
                <?php if(!empty($firstLevel)){ ?>
                    <?php foreach($firstLevel as $secondFilter => $secondLevel){ ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan=<?= (count($quarters) + count($months))*2 + 6 ?>><?= $secondFilter ?></td>
                        </tr>
                        <?php if(!empty($secondLevel)){ ?>
                            <?php foreach($secondLevel as $thirdFilter => $thirdLevel){ ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td colspan=<?= (count($quarters) + count($months))*2 + 5 ?>><?= $thirdFilter ?></td>
                                </tr>
                                <?php if(!empty($thirdLevel)){ ?>
                                    <?php foreach($thirdLevel as $fourthFilter => $items){ ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td colspan=<?= (count($quarters) + count($months))*2 + 4 ?>><?= $fourthFilter ?></td>
                                        </tr>
                                        <?php if(!empty($items)){ ?>
                                            <?php foreach($items as $item){ ?>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td><?= $item['itemTitle'] ?></td>
                                                    <td><?= number_format($item['totalQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['totalQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['janQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['janQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['febQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['febQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['marQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['marQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format(($item['janQty'] + $item['febQty'] + $item['marQty']), 0) ?></td>
                                                    <td align=right><?= number_format(($item['janQty'] + $item['febQty'] + $item['marQty']) * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['aprQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['aprQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['mayQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['mayQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['junQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['junQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format(($item['aprQty'] + $item['mayQty'] + $item['junQty']), 0) ?></td>
                                                    <td align=right><?= number_format(($item['aprQty'] + $item['mayQty'] + $item['junQty']) * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['julQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['julQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['augQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['augQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['sepQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['sepQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format(($item['julQty'] + $item['augQty'] + $item['sepQty']), 0) ?></td>
                                                    <td align=right><?= number_format(($item['julQty'] + $item['augQty'] + $item['sepQty']) * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['octQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['octQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['novQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['novQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format($item['decQty'], 0) ?></td>
                                                    <td align=right><?= number_format($item['decQty'] * $item['costPerUnit'], 2) ?></td>
                                                    <td align=right><?= number_format(($item['octQty'] + $item['novQty'] + $item['decQty']), 0) ?></td>
                                                    <td align=right><?= number_format(($item['octQty'] + $item['novQty'] + $item['decQty']) * $item['costPerUnit'], 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
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
            "columnNum" : 4
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>