<div id="graphs-table">
    <hr>
    <h3 align=center>Project Breakdown per Sector</h3>
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" style="width: 100%; height: 200px;" >
        <thead>
            <tr>
                <td rowspan=2 align=center><b>Sectors</b></td>
                <td colspan=3 align=center><b>Project Status</b></td>
                <td rowspan=2 align=center><b>Total</b></td>
            </tr>
            <tr>
                <td align=center><b>Completed</b></td>
                <td align=center><b>Ongoing</b></td>
                <td align=center><b>Not Yet Started</b></td>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($projectStatus)){ ?>
                <?php foreach($projectStatus as $project){ ?>
                    <tr style="font-weight: bolder;">
                        <td><?= $project['sectorTitle'] ?></td>
                        <td align=center><?= intval($project['completed']) ?></td>
                        <td align=center><?= intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']) ?></td>
                        <td align=center><?= intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget']) ?></td>
                        <td align=center><?= intval($project['completed']) + intval($project['behindSchedule']) + intval($project['onSchedule']) + intval($project['aheadOnSchedule']) + intval($project['notYetStartedWithTarget']) +  intval($project['notYetStartedWithNoTarget']) ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>