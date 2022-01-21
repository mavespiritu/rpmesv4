<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<table class="table table-responsive table-condensed">
    <tr>
        <th>Item</th>
        <td><?= $model->item->title ?></td>
    </tr>
    <tr>
        <th>Unit of Measure</th>
        <td><?= $model->item->unit_of_measure ?></td>
    </tr>
    <tr>
        <th>Cost</th>
        <td><?= number_format($model->cost, 2) ?></td>
    </tr>
</table>

<table class="table table-responsive table-condensed table-bordered">
    <thead>
        <tr>
            <th>Month</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
    <?php if($model->itemBreakdowns){ ?>
        <?php foreach($model->itemBreakdowns as $item){ ?>
            <tr>
                <td><?= $item->month->abbreviation ?></td>
                <td><?= $item->quantity ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>