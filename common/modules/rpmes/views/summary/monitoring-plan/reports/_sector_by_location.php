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
            <td rowspan=3 colspan=4 align=center><b>Project Category</b></td>
            <td colspan=5 align=center><b>Financial Requirements</b></td>
            <td colspan=5 align=center><b>Number of Projects</b></td>
            <td colspan=10 align=center><b>Number of Persons Employed</b></td>
            <td colspan=4 align=center><b>Number of Beneficiaries</b></td>
        </tr>
        <tr>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center rowspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center rowspan=2><b>Total</b></td>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center rowspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center rowspan=2><b>Total</b></td>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center colspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center colspan=2><b>Total</b></td>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center rowspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
        </tr>
        <tr>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $quarter){ ?>
                    <?php if($genders){ ?>
                        <?php foreach($genders as $g => $gender){ ?>
                            <td align=center><b><?= $g ?></b></td>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <?php if($genders){ ?>
                <?php foreach($genders as $g => $gender){ ?>
                    <td align=center><b><?= $g ?></b></td>
                <?php } ?>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($data)){ ?>
        <?php $i = 0; ?>
        <?php foreach($data as $location => $locations){ ?>
                <tr style="font-weight: bolder;">
                    <td colspan=4><?= $bigCaps[$i] ?>. <?= $location ?></td>
                    <td align=right><?= number_format($locations['content']['q1financial'], 2) ?></td>
                    <td align=right><?= number_format($locations['content']['q2financial'], 2) ?></td>
                    <td align=right><?= number_format($locations['content']['q3financial'], 2) ?></td>
                    <td align=right><?= number_format($locations['content']['q4financial'], 2) ?></td>
                    <td align=right><?= number_format(
                        $locations['content']['q1financial'] +
                        $locations['content']['q2financial'] +
                        $locations['content']['q3financial'] +
                        $locations['content']['q4financial']
                        , 2) ?>
                    </td>
                    <td align=right><?= number_format($locations['content']['q1physical'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q2physical'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q3physical'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q4physical'], 0) ?></td>
                    <td align=right><?= number_format(
                        $locations['content']['q1physical'] +
                        $locations['content']['q2physical'] +
                        $locations['content']['q3physical'] +
                        $locations['content']['q4physical']
                        , 0) ?>
                    </td>
                    <td align=right><?= number_format($locations['content']['q1maleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q1femaleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q2maleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q2femaleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q3maleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q3femaleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q4maleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q4femaleEmployed'], 0) ?></td>
                    <td align=right><?= number_format(
                        $locations['content']['q1maleEmployed'] +
                        $locations['content']['q2maleEmployed'] +
                        $locations['content']['q3maleEmployed'] +
                        $locations['content']['q4maleEmployed']
                        , 0) ?>
                    </td>
                    <td align=right><?= number_format(
                        $locations['content']['q1femaleEmployed'] +
                        $locations['content']['q2femaleEmployed'] +
                        $locations['content']['q3femaleEmployed'] +
                        $locations['content']['q4femaleEmployed']
                        , 0) ?>
                    </td>
                    <td align=right><?= number_format($locations['content']['q1beneficiary'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q2beneficiary'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q3beneficiary'], 0) ?></td>
                    <td align=right><?= number_format($locations['content']['q4beneficiary'], 0) ?></td>
                </tr>
            <?php if(!empty($locations['sectors'])){ ?>
                <?php $j = 0; ?>
                <?php foreach($locations['sectors'] as $sector => $sectors){ ?>
                    <tr>
                        <td align=right>&nbsp;</td>
                        <td colspan=3><?= $smallCaps[$j] ?>. <?= $sector ?></td>
                        <td align=right><?= number_format($sectors['content']['q1financial'], 2) ?></td>
                        <td align=right><?= number_format($sectors['content']['q2financial'], 2) ?></td>
                        <td align=right><?= number_format($sectors['content']['q3financial'], 2) ?></td>
                        <td align=right><?= number_format($sectors['content']['q4financial'], 2) ?></td>
                        <td align=right><?= number_format(
                            $sectors['content']['q1financial'] +
                            $sectors['content']['q2financial'] +
                            $sectors['content']['q3financial'] +
                            $sectors['content']['q4financial']
                            , 2) ?>
                        </td>
                        <td align=right><?= number_format($sectors['content']['q1physical'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q2physical'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q3physical'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q4physical'], 0) ?></td>
                        <td align=right><?= number_format(
                            $sectors['content']['q1physical'] +
                            $sectors['content']['q2physical'] +
                            $sectors['content']['q3physical'] +
                            $sectors['content']['q4physical']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format($sectors['content']['q1maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q1femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q2maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q2femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q3maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q3femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q4maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q4femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format(
                            $sectors['content']['q1maleEmployed'] +
                            $sectors['content']['q2maleEmployed'] +
                            $sectors['content']['q3maleEmployed'] +
                            $sectors['content']['q4maleEmployed']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format(
                            $sectors['content']['q1femaleEmployed'] +
                            $sectors['content']['q2femaleEmployed'] +
                            $sectors['content']['q3femaleEmployed'] +
                            $sectors['content']['q4femaleEmployed']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format($sectors['content']['q1beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q2beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q3beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($sectors['content']['q4beneficiary'], 0) ?></td>
                    </tr>
                    <?php $j++ ?>
                <?php } ?>
            <?php } ?>
            <?php $i++ ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>