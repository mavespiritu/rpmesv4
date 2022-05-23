<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>

<tr>
    <td align=center>
        <?= $form->field($projectIds[$model->id], "[$model->id]id")->checkbox(['value' => $model->id, 'class' => 'check-draft-project', 'label' => '', 'id' => 'check-draft-project-'.$model->id, 'checked' => 'checked']) ?>
    </td>
    <td align=center>
        <?= Html::a('<i class="fa fa-edit"></i> Edit', ['/rpmes/project/update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs btn-block', 'id' => 'update-draft-project'.$model->id.'-button']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Delete', ['/rpmes/project/delete', 'id' => $model->id], [
        'class' => 'btn btn-xs btn-danger btn-block',
        'data' => [
            'confirm' => 'Are you sure you want to remove this item?',
            'method' => 'post',
        ],
    ]) ?>
    </td>
    <td><?= $idx + 1 ?></td>
    <td>
        (a) <?= $model->title ?><br>
        <table>
            <tr>
                <td style="vertical-align: top;">(b)&nbsp;</td>
                <td><?= $model->location ?></td>
            </tr>
        </table>
        (c) <?= $model->sectorTitle.'/'.$model->subSectorTitle ?><br>
        (d) <?= $model->fundSourceTitle ?><br>
        (e) <?= $model->modeOfImplementationTitle ?><br>
        (f) <?= $model->startDate ?> to <?= $model->completionDate ?><br>
    </td>
    <td><?= $model->data_type != "" ? $model->unitOfMeasure.' ('.$model->data_type.')' : $model->unitOfMeasure ?></td>
    <td align=right><?= $model->financialTarget ? number_format($model->financialTarget->q1, 2) : '0.00' ?></td>
    <td align=right><?= $model->financialTarget ? number_format($model->financialTarget->q2, 2) : '0.00' ?></td>
    <td align=right><?= $model->financialTarget ? number_format($model->financialTarget->q3, 2) : '0.00' ?></td>
    <td align=right><?= $model->financialTarget ? number_format($model->financialTarget->q4, 2) : '0.00' ?></td>
    <td align=right><b><?= number_format($model->getAllocationTotal(), 2) ?></b></td>
    <td align=right><?= $model->physicalTarget ? number_format($model->physicalTarget->q1, 0) : '0' ?></td>
    <td align=right><?= $model->physicalTarget ? number_format($model->physicalTarget->q2, 0) : '0' ?></td>
    <td align=right><?= $model->physicalTarget ? number_format($model->physicalTarget->q3, 0) : '0' ?></td>
    <td align=right><?= $model->physicalTarget ? number_format($model->physicalTarget->q4, 0) : '0' ?></td>
    <td align=right><b><?= number_format($model->getPhysicalTotal(), 2) ?></b></td>
    <td align=right><?= $model->maleEmployedTarget ? number_format($model->maleEmployedTarget->q1, 0) : '0' ?></td>
    <td align=right><?= $model->femaleEmployedTarget ? number_format($model->femaleEmployedTarget->q1, 0) : '0' ?></td>
    <td align=right><?= $model->maleEmployedTarget ? number_format($model->maleEmployedTarget->q2, 0) : '0' ?></td>
    <td align=right><?= $model->femaleEmployedTarget ? number_format($model->femaleEmployedTarget->q2, 0) : '0' ?></td>
    <td align=right><?= $model->maleEmployedTarget ? number_format($model->maleEmployedTarget->q3, 0) : '0' ?></td>
    <td align=right><?= $model->femaleEmployedTarget ? number_format($model->femaleEmployedTarget->q3, 0) : '0' ?></td>
    <td align=right><?= $model->maleEmployedTarget ? number_format($model->maleEmployedTarget->q4, 0) : '0' ?></td>
    <td align=right><?= $model->femaleEmployedTarget ? number_format($model->femaleEmployedTarget->q4, 0) : '0' ?></td>
    <td align=right><b><?= $model->maleEmployedTarget ? number_format(
        $model->maleEmployedTarget->q1 +
        $model->maleEmployedTarget->q2 +
        $model->maleEmployedTarget->q3 +
        $model->maleEmployedTarget->q4 
    , 0) : '0' ?></b></td>
    <td align=right><b><?= $model->femaleEmployedTarget ? number_format(
        $model->femaleEmployedTarget->q1 +
        $model->femaleEmployedTarget->q2 +
        $model->femaleEmployedTarget->q3 +
        $model->femaleEmployedTarget->q4 
    , 0) : '0' ?></b></td>
    <td align=right><?= $model->beneficiaryTarget ? number_format($model->beneficiaryTarget->q1, 0) : '0' ?></td>
    <td align=right><?= $model->groupTarget ? number_format($model->groupTarget->q1, 0) : '0' ?></td>
    <td align=right><?= $model->beneficiaryTarget ? number_format($model->beneficiaryTarget->q2, 0) : '0' ?></td>
    <td align=right><?= $model->groupTarget ? number_format($model->groupTarget->q2, 0) : '0' ?></td>
    <td align=right><?= $model->beneficiaryTarget ? number_format($model->beneficiaryTarget->q3, 0) : '0' ?></td>
    <td align=right><?= $model->groupTarget ? number_format($model->groupTarget->q3, 0) : '0' ?></td>
    <td align=right><?= $model->beneficiaryTarget ? number_format($model->beneficiaryTarget->q4, 0) : '0' ?></td>
    <td align=right><?= $model->groupTarget ? number_format($model->groupTarget->q4, 0) : '0' ?></td>
    <td align=right><b><?= $model->beneficiaryTarget ? number_format(
        $model->beneficiaryTarget->q1 +
        $model->beneficiaryTarget->q2 +
        $model->beneficiaryTarget->q3 +
        $model->beneficiaryTarget->q4 
    , 0) : '0' ?></b></td>
    <td align=right><b><?= $model->groupTarget ? number_format(
        $model->groupTarget->q1 +
        $model->groupTarget->q2 +
        $model->groupTarget->q3 +
        $model->groupTarget->q4 
    , 0) : '0' ?></b></td>
</tr>