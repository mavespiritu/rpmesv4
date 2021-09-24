<?php 
use yii\web\View;

$columnTotals = [];
?>
<h3 class="panel-title"><?= $model->title ?></h3><br>
<div class="freeze-table" style="width:98%; max-height: 800px; overflow: auto;">
<table class="table table-responsive table-striped table-condensed table-hover table-bordered content">
    <thead>
        <tr>
            <th>Objects</th>
            <?php if($model->appropriationPaps): ?>
                <?php foreach($model->getAppropriationPaps()->orderBy(['arrangement'=> SORT_ASC])->all() as $program): ?>
                    <th>
                        <?= $program->fundSource->code ?>
                        <hr style="opacity: 0.3">
                        <p><span style="font-size: 12px;"><?= $program->pap->title ?></span><br>
                        <?= $program->pap->codeTitle ?>
                        </p>
                    </th>
                <?php endforeach ?>
            <?php endif ?>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
    <?php if($model->appropriationObjs): ?>
        <?php foreach($model->getAppropriationObjs()->orderBy(['arrangement'=> SORT_ASC])->all() as $object): ?>
            <?php $rowTotal = 0; ?>
            <tr>
                <td><?= $object->obj->objectTitle ?></td>
                <?php if(!empty($items)): ?>
                    <?php $id = 0; ?>
                    <?php foreach($items[$object->obj_id] as $key => $objectItem): ?>
                        <?php $columnTotals[$id] = isset($columnTotals[$id]) ? $columnTotals[$id] : 0 ?>
                            <td align=right><?= number_format($objectItem->amount, 2) ?></td>
                            <?php $rowTotal += $objectItem->amount; ?>
                            <?php $columnTotals[$id] += $objectItem->amount; ?>
                            <?php $id++ ?>
                    <?php endforeach ?> 
                <?php endif ?>
                <td align=right><b><?= number_format($rowTotal, 2) ?></b></td>
            </tr>
        <?php endforeach ?>
            <?php $grandTotal = 0; ?>
            <tr>
                <td><b>Total</b></td>
                <?php if(!empty($columnTotals)){ ?>
                    <?php foreach($columnTotals as $columnTotal){ ?>
                        <td align=right><b><?= number_format($columnTotal, 2) ?></b></td>
                        <?php $grandTotal += $columnTotal ?>
                    <?php } ?>
                <?php } ?>
                <td align=right><b><?= number_format($grandTotal, 2) ?></b></td>
            </tr>
    <?php endif ?>
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