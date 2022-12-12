<table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Category</th>
            <td align=right><b>No. of Male</b></td>
            <td align=right><b>%</b></td>
            <td align=right><b>No. of Female</b></td>
            <td align=right><b>%</b></td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($categories)){ ?>
        <?php foreach($categories as $category){ ?>
            <tr>
                <td><?= $category['code'] ?></td>
                <td align=right><?= number_format($category['maleTotal'], 0) ?></td>
                <td align=right><?= $categoriesTotal['maleTotal'] > 0 ? number_format((($category['maleTotal']/$categoriesTotal['maleTotal']))*100, 2) : number_format(0, 2) ?></td>
                <td align=right><?= number_format($category['femaleTotal'], 0) ?></td>
                <td align=right><?= $categoriesTotal['femaleTotal'] > 0 ? number_format((($category['femaleTotal']/$categoriesTotal['femaleTotal']))*100, 2) : number_format(0, 2) ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    <tr>
        <td align=right><b>Total</b></td>
        <td align=right><b><?= number_format($categoriesTotal['maleTotal'], 0) ?></b></td>
        <td align=right><b>100%</b></td>
        <td align=right><b><?= number_format($categoriesTotal['femaleTotal'], 0) ?></b></td>
        <td align=right><b>100%</b></td>
    </tr>
    </tbody>
</table>