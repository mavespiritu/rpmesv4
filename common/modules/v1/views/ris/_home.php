<div id="alert-container"></div>
<?= $this->render('_load-items', [
    'model' => $model,
    'appropriationItemModel' => $appropriationItemModel,
    'activities' => $activities,
    'subActivities' => $subActivities,
    'fundSources' => $fundSources,
]) ?>
<br>
<br>
<hr style="opacity: 0.3;">
<div class="row">
    <div class="col-md-12 col-xs-12" id="ris-item-list">
            <p class="panel-title"><i class="fa fa-list"></i> Available Items</p><br>
    </div>
</div>