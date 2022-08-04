<?php if($type != 'pdf'){ ?>
    <style>
    table{
        font-family: "Arial";
        border-collapse: collapse;
        width: 100%;
    }
    thead{
        font-size: 12px;
        text-align: center;
    }

    td{
        font-size: 10px;
        border: 1px solid black;
        padding: 5px;
    }

    th{
        text-align: center;
        border: 1px solid black;
        padding: 5px;
    }
</style>
<?php } ?>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive">
<thead>
            <tr>
                <td colspan=5 rowspan=3 align=center><b>Project Details</b><br>a. Project Title<br>b. Sector/Subsector<br>c. Fund Source<br>d. Project Schedule<br>e. Category</td>
                <td rowspan=3 align=center><b>Output Indicator</b></td>
                <td colspan=6 align=center><b>Financial Status of Reporting Period</b></td>
                <td colspan=4 align=center><b>Physical Status as of Reporting Period</b></td>
                <td colspan=6 align=center><b>Number of Persons Employed as of Reporting Period</b></td>
                <td colspan=5 align=center><b>Number of Beneficiaries as of Reporting Period</b></td>
                <td colspan=7 align=center><b>Project Implementation Status</b></td>
                <td rowspan=3 align=center><b>Remarks</b></td>
            </tr>
            <tr>
                <td rowspan=2 align=center><b>Allocations</b></td>
                <td rowspan=2 align=center><b>Releases</b></td>
                <td rowspan=2 align=center><b>Obligation</b></td>
                <td rowspan=2 align=center><b>Disbursement</b></td>
                <td rowspan=2 align=center><b>Funding Support (%)</b></td>
                <td rowspan=2 align=center><b>Fund Utilization (%)</b></td>
                <td rowspan=2 align=center><b>Target as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Actual Accomplishment as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Slippage as of Reporting Period (%)</b></td>
                <td rowspan=2 align=center><b>Performance as of Reporting Period</b></td>
                <td colspan=3 align=center><b>Target</b></td>
                <td colspan=3 align=center><b>Actual</b></td>
                <td colspan=2 align=center><b>Target</b></td>
                <td colspan=3 align=center><b>Actual</b></td>
                <td rowspan=2 align=center><b>Completed</b></td>
                <td colspan=3 align=center><b>Ongoing</b></td>
                <td colspan=2 align=center><b>Not Yet Started</b></td>
                <td rowspan=2 align=center><b>Total</b></td>
            </tr>
            <tr>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b><?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Total</b></td>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b><?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Total</b></td>
                <td align=center><b>Individual</b></td>
                <td align=center><b>Group</b></td>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b>Indiviual - <?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Group</b></td>
                <td align=center><b>Behind Schedule</b></td>
                <td align=center><b>On-time</b></td>
                <td align=center><b>Ahead of Schedule</b></td>
                <td align=center><b>With Target</b></td>
                <td align=center><b>No Target</b></td>
            </tr>
        </thead>
        <tbody>
        <tr style="font-weight: bolder;">
            <td colspan=5>Grand Total</td>
            <td align=right><?= number_format($total['allocations'], 2) ?></td>
            <td align=right><?= number_format($total['releases'], 2) ?></td>
            <td align=right><?= number_format($total['obligations'], 2) ?></td>
            <td align=right><?= number_format($total['expenditures'], 2) ?></td>
            <td align=right><?= $total['allocations'] > 0 ? number_format(($total['releases'] / $total['allocations']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= $total['releases'] > 0 ? number_format(($total['expenditures'] / $total['releases']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= number_format($total['physicalTarget'], 2) ?></td>
            <td align=right><?= number_format($total['physicalActual'], 2) ?></td>
            <td align=right><?= count($total) > 0 ? number_format(($total['slippage']/count($total)), 2) : number_format(0, 2) ?></td>
            <td align=right><?= $total['physicalTarget'] > 0 ? number_format(($total['physicalActual'] / $total['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'] + $total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'] + $total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['beneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['maleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['femaleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['completed'], 0) ?></td>
            <td align=right><?= number_format($total['behindSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['onSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['aheadOnSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithTarget'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithNoTarget'], 0) ?></td>
            <td align=right><?= number_format($total['completed'] + $total['behindSchedule'] + $total['onSchedule'] + $total['aheadOnSchedule'] + $total['notYetStartedWithTarget'] + $total['notYetStartedWithNoTarget'], 0) ?></td>
            <td>&nbsp;</td>
        </tr>
        <?php if(!empty($data)){ ?>
            <?php $i = 1; ?>
            <?php foreach($data as $firstLevel => $firstLevels){ ?>
                    <tr style="font-weight: bolder;">
                        <td colspan=5><?= $i ?>. <?= str_replace("*","<br>", $firstLevel) ?></td>
                        <td align=right>&nbsp;</td>
                        <td align=right><?= number_format($firstLevels['content']['allocations'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['releases'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['obligations'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['expenditures'], 2) ?></td>
                        <td align=right><?= $firstLevels['content']['allocations'] > 0 ? number_format(($firstLevels['content']['releases'] / $firstLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= $firstLevels['content']['releases'] > 0 ? number_format(($firstLevels['content']['expenditures'] / $firstLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['physicalTarget'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['physicalActual'], 2) ?></td>
                        <td align=right><?= count($firstLevels) > 0 ? number_format(($firstLevels['content']['slippage']/count($firstLevels['content'])), 2) : number_format(0, 2) ?></td>
                        <td align=right><?= $firstLevels['content']['physicalTarget'] > 0 ? number_format(($firstLevels['content']['physicalActual'] / $firstLevels['content']['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['malesEmployedTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['femalesEmployedTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['malesEmployedTarget'] + $firstLevels['content']['femalesEmployedTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['malesEmployedActual'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['femalesEmployedActual'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['malesEmployedActual'] + $firstLevels['content']['femalesEmployedActual'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['beneficiariesTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['completed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['behindSchedule'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['onSchedule'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['aheadOnSchedule'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['completed'] + $firstLevels['content']['behindSchedule'] + $firstLevels['content']['onSchedule'] + $firstLevels['content']['aheadOnSchedule'] + $firstLevels['content']['notYetStartedWithTarget'] + $firstLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                        <td>&nbsp;</td>
                    </tr>
                <?php if(!empty($firstLevels['firstLevels'])){ ?>
                    <?php $j = 1; ?>
                    <?php foreach($firstLevels['firstLevels'] as $secondLevel => $secondLevels){ ?>
                        <tr style="font-weight: bolder;">
                            <td align=right>&nbsp;</td>
                            <td colspan=4><?= $i.'.'.$j ?>.<?= str_replace("*","<br>", $secondLevel) ?></td>
                            <td align=right>&nbsp;</td>
                            <td align=right><?= number_format($secondLevels['content']['allocations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['releases'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['obligations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['expenditures'], 2) ?></td>
                            <td align=right><?= $secondLevels['content']['allocations'] > 0 ? number_format(($secondLevels['content']['releases'] / $secondLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                            <td align=right><?= $secondLevels['content']['releases'] > 0 ? number_format(($secondLevels['content']['expenditures'] / $secondLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['physicalTarget'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['physicalActual'], 2) ?></td>
                            <td align=right><?= count($secondLevels) > 0 ? number_format(($secondLevels['content']['slippage']/count($secondLevels['content'])), 2) : number_format(0, 2) ?></td>
                            <td align=right><?= $secondLevels['content']['physicalTarget'] > 0 ? number_format(($secondLevels['content']['physicalActual'] / $secondLevels['content']['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femalesEmployedTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedTarget'] + $secondLevels['content']['femalesEmployedTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femalesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedActual'] + $secondLevels['content']['femalesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['beneficiariesTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['completed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['behindSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['onSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['aheadOnSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['completed'] + $secondLevels['content']['behindSchedule'] + $secondLevels['content']['onSchedule'] + $secondLevels['content']['aheadOnSchedule'] + $secondLevels['content']['notYetStartedWithTarget'] + $secondLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php if(!empty($secondLevels['secondLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['secondLevels'] as $thirdLevel => $thirdLevels){ ?>
                                <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                <?= '<tr style="font-weight: bolder;">' ?>
                                <?php }else{ ?>
                                <?= '<tr>' ?>
                                <?php } ?>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td colspan=3><?= $i.'.'.$j.'.'.$k ?>.<?= str_replace("*","<br>", $thirdLevel) ?></td>
                                    <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                    <?= '<td align=right>&nbsp;</td>' ?>
                                    <?php }else{ ?>
                                    <?= '<td align=right>'. $thirdLevels['content']['indicator'].'</td>' ?>
                                    <?php } ?>
                                    <td align=right><?= number_format($thirdLevels['content']['allocations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['releases'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['expenditures'], 2) ?></td>
                                    <td align=right><?= $thirdLevels['content']['allocations'] > 0 ? number_format(($thirdLevels['content']['releases'] / $thirdLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                                    <td align=right><?= $thirdLevels['content']['releases'] > 0 ? number_format(($thirdLevels['content']['expenditures'] / $thirdLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                                    <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                    <?= '<td align=right>'. number_format($thirdLevels['content']['physicalTarget'], 2).'</td>' ?>
                                    <?= '<td align=right>'. number_format($thirdLevels['content']['physicalActual'], 2).'</td>' ?>
                                    <?php }else{ ?>
                                    <?= '<td align=right>'. number_format($thirdLevels['content']['projectPhysicalTarget'], 2).'</td>' ?>
                                    <?= '<td align=right>'. number_format($thirdLevels['content']['projectPhysicalAccomp'], 2).'</td>' ?>
                                    <?php } ?>
                                    <td align=right><?= count($thirdLevels) > 0 ? number_format(($thirdLevels['content']['slippage']/count($thirdLevels['content'])), 2) : number_format(0, 2) ?></td>
                                    <td align=right><?= $thirdLevels['content']['physicalTarget'] > 0 ? number_format(($thirdLevels['content']['physicalActual'] / $thirdLevels['content']['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedTarget'] + $thirdLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedActual'] + $thirdLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['beneficiariesTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['completed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['behindSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['onSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['aheadOnSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['completed'] + $thirdLevels['content']['behindSchedule'] + $thirdLevels['content']['onSchedule'] + $thirdLevels['content']['aheadOnSchedule'] + $thirdLevels['content']['notYetStartedWithTarget'] + $thirdLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                    <?php $l = 1; ?>
                                    <?php foreach($thirdLevels['thirdLevels'] as $fourthLevel => $fourthLevels){ ?>
                                        <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                        <?= '<tr style="font-weight: bolder;">'; ?>
                                        <?php }else{ ?>
                                        <?= '<tr>'; ?>
                                        <?php } ?>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td colspan=2><?= $i.'.'.$j.'.'.$k.'.'.$l ?>.<?= str_replace("*","<br>", $fourthLevel) ?></td>
                                            <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <?= '<td align=right>&nbsp;</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td align=right>'. $fourthLevels['content']['indicator'].'</td>' ?>
                                            <?php } ?>
                                            <td align=right><?= number_format($fourthLevels['content']['allocations'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['releases'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['obligations'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['expenditures'], 2) ?></td>
                                            <td align=right><?= $fourthLevels['content']['allocations'] > 0 ? number_format(($fourthLevels['content']['releases'] / $fourthLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                                            <td align=right><?= $fourthLevels['content']['releases'] > 0 ? number_format(($fourthLevels['content']['expenditures'] / $fourthLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                                            <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                            <?= '<td align=right>'. number_format($fourthLevels['content']['physicalTarget'], 2).'</td>' ?>
                                            <?= '<td align=right>'. number_format($fourthLevels['content']['physicalActual'], 2).'</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td align=right>'. number_format($fourthLevels['content']['projectPhysicalTarget'], 2).'</td>' ?>
                                            <?= '<td align=right>'. number_format($fourthLevels['content']['projectPhysicalAccomp'], 2).'</td>' ?>
                                            <?php } ?>
                                            <td align=right><?= count($fourthLevels['content']) > 0 ? number_format(($fourthLevels['content']['slippage']/count($fourthLevels['content'])), 2) : number_format(0, 2) ?></td>
                                            <td align=right><?= $fourthLevels['content']['physicalTarget'] > 0 ? number_format(($fourthLevels['content']['physicalActual'] / $fourthLevels['content']['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedTarget'] + $fourthLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['femalesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedActual'] + $fourthLevels['content']['femalesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['beneficiariesTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['completed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['behindSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['onSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['aheadOnSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['completed'] + $fourthLevels['content']['behindSchedule'] + $fourthLevels['content']['onSchedule'] + $fourthLevels['content']['aheadOnSchedule'] + $fourthLevels['content']['notYetStartedWithTarget'] + $fourthLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <?php $m = 1; ?>
                                            <?php foreach($fourthLevels['fourthLevels'] as $fifthLevel => $fifthLevels){ ?>
                                                <tr>
                                                    <td align=right>&nbsp;</td>
                                                    <td align=right>&nbsp;</td>
                                                    <td align=right>&nbsp;</td>
                                                    <td align=right>&nbsp;</td>
                                                    <td><?= $i.'.'.$j.'.'.$k.'.'.$l.'.'.$m ?>. <?= str_replace("*","<br>", $fifthLevel) ?></td>
                                                    <td align=right><?= $fifthLevels['content']['indicator'] ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['allocations'], 2) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['releases'], 2) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['obligations'], 2) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['expenditures'], 2) ?></td>
                                                    <td align=right><?= $fifthLevels['content']['allocations'] > 0 ? number_format(($fifthLevels['content']['releases'] / $fifthLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <td align=right><?= $fifthLevels['content']['releases'] > 0 ? number_format(($fifthLevels['content']['expenditures'] / $fifthLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <td align=right><?= number_format($fourthLevels['content']['projectPhysicalTarget'], 2) ?></td>
                                                    <td align=right><?= number_format($fourthLevels['content']['projectPhysicalAccomp'], 2) ?></td>
                                                    <td align=right><?= count($fifthLevels['content']) > 0 ? number_format(($fifthLevels['content']['slippage']/count($fifthLevels['content'])), 2) : number_format(0, 2) ?></td>
                                                    <td align=right><?= $fifthLevels['content']['physicalTarget'] > 0 ? number_format(($fifthLevels['content']['physicalActual'] / $fifthLevels['content']['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['malesEmployedTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['malesEmployedTarget'] + $fifthLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['malesEmployedActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['femalesEmployedActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['malesEmployedActual'] + $fifthLevels['content']['femalesEmployedActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['beneficiariesTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['completed'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['behindSchedule'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['onSchedule'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['aheadOnSchedule'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthLevels['content']['completed'] + $fifthLevels['content']['behindSchedule'] + $fifthLevels['content']['onSchedule'] + $fifthLevels['content']['aheadOnSchedule'] + $fifthLevels['content']['notYetStartedWithTarget'] + $fifthLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            <?php $m++ ?>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php $l++ ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php $k++ ?>
                            <?php } ?>
                        <?php } ?>
                        <?php $j++ ?>
                    <?php } ?>
                <?php } ?>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        <tr style="font-weight: bolder;">
            <td colspan=5>Grand Total</td>
            <td align=right><?= number_format($total['allocations'], 2) ?></td>
            <td align=right><?= number_format($total['releases'], 2) ?></td>
            <td align=right><?= number_format($total['obligations'], 2) ?></td>
            <td align=right><?= number_format($total['expenditures'], 2) ?></td>
            <td align=right><?= $total['allocations'] > 0 ? number_format(($total['releases'] / $total['allocations']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= $total['releases'] > 0 ? number_format(($total['expenditures'] / $total['releases']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= number_format($total['physicalTarget'], 2) ?></td>
            <td align=right><?= number_format($total['physicalActual'], 2) ?></td>
            <td align=right><?= count($total) > 0 ? number_format(($total['slippage']/count($total)), 2) : number_format(0, 2) ?></td>
            <td align=right><?= $total['physicalTarget'] > 0 ? number_format(($total['physicalActual'] / $total['physicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'] + $total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'] + $total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['beneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['maleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['femaleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['completed'], 0) ?></td>
            <td align=right><?= number_format($total['behindSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['onSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['aheadOnSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithTarget'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithNoTarget'], 0) ?></td>
            <td align=right><?= number_format($total['completed'] + $total['behindSchedule'] + $total['onSchedule'] + $total['aheadOnSchedule'] + $total['notYetStartedWithTarget'] + $total['notYetStartedWithNoTarget'], 0) ?></td>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>

