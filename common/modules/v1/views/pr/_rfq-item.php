
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>

<tr>
    <td><?= $i ?></td>
    <td><?= $item['unit'] ?></td>
    <td style="width: 20%;"><?= $item['item'] ?><br><?= !empty($specifications[$item['id']]) ? \file\components\AttachmentsTable::widget(['model' => $specifications[$item['id']]]) : '' ?></td>
    <td align=center><?= number_format($item['total'], 0) ?></td>
    <td align=right><?= number_format($item['cost'], 2) ?></td>
    <td align=right><?= number_format($item['total'] * $item['cost'], 2) ?></td>
    <td align=center>
        <?= $form->field($rfqItems[$item['id']], "[$id]id")->checkbox(['value' => $item['id'], 'class' => 'check-rfq-item', 'label' => '', 'id' => 'check-rfq-item-'.$item['id'], 'checked' => 'checked']) ?>
    </td>
</tr>