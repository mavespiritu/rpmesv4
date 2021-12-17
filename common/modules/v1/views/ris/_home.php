<div id="alert-container"></div>
<div class="row">
    <div class="col-md-2 col-xs-12">
        <p class="panel-title"><i class="fa fa-search"></i> Filter By</p>
        <hr style="opacity: 0.3;">
        <?= $this->render('_load-items', [
            'model' => $model,
            'appropriationItemModel' => $appropriationItemModel,
            'activities' => $activities,
            'subActivities' => $subActivities,
            'fundSources' => $fundSources,
        ]) ?>
    </div>
    <div class="col-md-10 col-xs-12">
        <p class="panel-title"><i class="fa fa-list"></i> Available PPMP Items</p><br>
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div id="ris-item-list"></div>
            </div>
        </div>
    </div>
</div>
