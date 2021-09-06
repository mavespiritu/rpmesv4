<h4>Add Objects</h4>
<?= $this->render('_obj_form', [
    'model' => $model,
    'objModel' => $objModel,
    'objs' => $objs,
]) ?>
<hr style="opacity: 0.3">
<h4>Included Objects for GAA <?= $model->year ?></h4>
<p><i class="fa fa-exclamation-circle"></i> Drag and drop the items to save arrangement</p>