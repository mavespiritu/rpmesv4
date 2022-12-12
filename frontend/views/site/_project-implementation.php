<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<h4 class="text-center">Project Implementation By Status</h4>
<div style="height: 90%; width: 100%">
<table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" style="width: 100%; height: 200px;" >
    <thead>
        <tr>
            <td rowspan=2 align=center style="width: 40%"><b>Sector</b></td>
            <td colspan=3 align=center><b>Status</b></td>
            <td rowspan=2 align=center><b>Total</b></td>
        </tr>
        <tr>
            <td align=center><b>Completed</b></td>
            <td align=center><b>Ongoing</b></td>
            <td align=center><b>Not Yet Started</b></td>
        </tr>
    </thead>
    <tbody>
        <?php $completedSectors = 0; ?>
        <?php $ongoingSectors = 0; ?>
        <?php $nysSectors = 0; ?>
        <?php $grandSectorsTotal = 0; ?>
        
        <?php if(!empty($sectors)){ ?>
            <?php foreach($sectors as $project){ ?>
                <tr style="font-weight: bolder;">
                    <td><?= $project['sectorTitle'] ?></td>
                    <td align=center><?= number_format(intval($project['completed']), 0) ?></td>
                    <td align=center><?= number_format(intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']), 0) ?></td>
                    <td align=center><?= number_format(intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget']), 0) ?></td>
                    <td align=right><?= number_format(intval($project['completed']) + intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']) + intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget']), 0) ?></td>
                </tr>
                <?php $completedSectors += intval($project['completed']) ?>
                <?php $ongoingSectors += (intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule'])) ?>
                <?php $nysSectors += (intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget'])) ?>
                <?php $grandSectorsTotal += (intval($project['completed']) + intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']) + intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget'])) ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<br>
<table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" style="width: 100%; height: 200px;" >
    <thead>
        <tr>
            <td rowspan=2 align=center style="width: 40%"><b>Categories</b></td>
            <td colspan=3 align=center><b>Status</b></td>
            <td rowspan=2 align=center><b>Total</b></td>
        </tr>
        <tr>
            <td align=center><b>Completed</b></td>
            <td align=center><b>Ongoing</b></td>
            <td align=center><b>Not Yet Started</b></td>
        </tr>
    </thead>
    <tbody>
        <?php $completedCategories = 0; ?>
        <?php $ongoingCategories = 0; ?>
        <?php $nysCategories = 0; ?>
        <?php $grandCategoriesTotal = 0; ?>
        
        <?php if(!empty($categories)){ ?>
            <?php foreach($categories as $project){ ?>
                <tr style="font-weight: bolder;">
                    <td><?= $project['categoryTitle'] ?></td>
                    <td align=center><?= number_format(intval($project['completed']), 0) ?></td>
                    <td align=center><?= number_format(intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']), 0) ?></td>
                    <td align=center><?= number_format(intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget']), 0) ?></td>
                    <td align=right><?= number_format(intval($project['completed']) + intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']) + intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget']), 0) ?></td>
                </tr>
                <?php $completedCategories += intval($project['completed']) ?>
                <?php $ongoingCategories += (intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule'])) ?>
                <?php $nysCategories += (intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget'])) ?>
                <?php $grandCategoriesTotal += (intval($project['completed']) + intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']) + intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget'])) ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
</div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('disbursement-by-category', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-backward"></i> Previous Graph</button></div>
    <div class="col-md-6 col-xs-12">
      <button class="btn btn-block btn-default" id="implementation-button" value="<?= Url::to(['/site/project-implementation-data', 
                'year' => $year,
                'quarter' => $quarter, 
                'agency_id' => $agency_id, 
                'category_id' => $category_id, 
                'sector_id' => $sector_id, 
                'sub_sector_id' => $sub_sector_id, 
                'province_id' => $province_id, 
                'fund_source_id' => $fund_source_id
      ]) ?>">View Tabular Data</button>
    </div>
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('employment', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-forward"></i> Next Graph</button></div>
  </div>
</div>

<?php
  Modal::begin([
    'id' => 'implementation-modal',
    'size' => "modal-md",
    'header' => '<div id="implementation-modal-header"><h4>Project Implementation By Status</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="implementation-modal-content"></div>';
  Modal::end();
?>

<?php
    $script = '
        $(document).ready(function(){
            $("#implementation-button").click(function(){
              $("#implementation-modal").modal("show").find("#implementation-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>