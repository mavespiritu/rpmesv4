<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
$total = 0;
?>
<div class="budget-monitoring-items">
    <table class="table table-striped table-responsive table-condensed table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Unit of Measure</th>
                <th>Quantity</th>
                <th>Cost Per Unit</th>
                <th>Total</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($items)){ ?>
            <?php foreach($items as $item){ ?>
                <tr>
                    <td><?= $item['title'] ?></td>
                    <td><?= $item['unit_of_measure'] ?></td>
                    <td><?= number_format($item['quantity'], 0) ?></td>
                    <td><?= number_format($item['cost_per_unit'], 2) ?></td>
                    <td align=right><?= number_format($item['quantity'] * $item['cost_per_unit'], 2) ?></td>
                    <td><?= $item['remarks'] ?></td>
                </tr>
                <?php $total += $item['quantity'] * $item['cost_per_unit']; ?>
            <?php } ?>
        <?php } ?>
        <tr>
            <td colspan=4 align=right><b>Total:</b></td>
            <td align=right><b><?= number_format($total, 2) ?></b></td>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>
</div>
