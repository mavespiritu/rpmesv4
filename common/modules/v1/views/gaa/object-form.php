<?= $this->render('_obj_form', [
    'model' => $model,
    'objModel' => $objModel,
    'objs' => $objs,
]) ?>
<hr style="opacity: 0.3">
<p class="panel-title"><i class="fa fa-list"></i> Included Objects for GAA <?= $model->year ?></p><br>
<p><i class="fa fa-exclamation-circle"></i> Drag and drop the items to save arrangement</p>