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
<p class="panel-title"><i class="fa fa-list"></i> Available Items</p><br>
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div id="ris-items">
            <p class="text-center">Please select filter provided above.</p>
        </div>
    </div>
</div>