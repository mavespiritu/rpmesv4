<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<tr>
    <td><?= $i ?></td>
    <td align=center><?= $item['stockNo'] ?></td>
    <td><?= $item['unitOfMeasure'] ?></td>
    <td><?= $item['itemTitle'] ?></td>
    <td align=center><?= $item['total'] ?></td>
    <td align=right><?= number_format($item['total'] * $item['cost'], 2) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>