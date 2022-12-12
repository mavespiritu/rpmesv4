<?php $total = 0; ?>

<table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Category</th>
            <td align=right><b>Total</b></td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($categories)){ ?>
        <?php foreach($categories as $category){ ?>
            <tr>
                <td><?= $category['code'] ?></td>
                <td align=right><?= number_format($category['total'], 2) ?></td>
            </tr>
            <?php $total += $category['total']; ?> 
        <?php } ?>
    <?php } ?>
    <tr>
        <td align=right><b>Total</b></td>
        <td align=right><b><?= number_format($total, 2) ?></b></td>
    </tr>
    </tbody>
</table>