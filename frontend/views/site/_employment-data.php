<table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Sector</th>
            <td align=right><b>No. of Male</b></td>
            <td align=right><b>No. of Female</b></td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($sectors)){ ?>
        <?php foreach($sectors as $sector){ ?>
            <tr>
                <td><?= $sector['title'] ?></td>
                <td align=right><?= number_format($sector['maleTotal'], 0) ?></td>
                <td align=right><?= number_format($sector['femaleTotal'], 0) ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    <tr>
        <td align=right><b>Total</b></td>
        <td align=right><b><?= number_format($sectorsTotal['maleTotal'], 0) ?></b></td>
        <td align=right><b><?= number_format($sectorsTotal['femaleTotal'], 0) ?></b></td>
    </tr>
    </tbody>
</table>