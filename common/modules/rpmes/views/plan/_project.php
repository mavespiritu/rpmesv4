<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>

<td><?= $i ?></td>
<td><?= $project->project_no ?></td>
<td><?= $project->title ?></td>
<td><?= $project->sector->title ?></td>
<td><?= $project->modeOfImplementation->title ?></td>
<td>
    <?php if($project->files){ ?>
        <?php foreach($project->files as $file){ ?>
            <?= Html::a($file->name.'.'.$file->type, ['/file/file/download', 'id' => $file->id]).'<br><br>'; ?>
        <?php } ?>
    <?php } ?>
</td>
<td align=center>
    <?= $model->draft == 'Yes' ? $form->field($projects[$project->id], "[$project->id]id")->checkbox([
        'value' => $project->id, 
        'class' => 'check-included-project', 
        'label' => ''
    ]) : '' ?>
</td>