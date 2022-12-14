<?php $total = 0; ?>

<table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Category</th>
            <td align=right><b>Total</b></td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($sectors)){ ?>
        <?php foreach($sectors as $sector){ ?>
            <tr>
                <td><?= $sector['title'] ?></td>
                <td align=right><?= number_format($sector['total'], 2) ?></td>
            </tr>
            <?php $total += $sector['total']; ?> 
        <?php } ?>
    <?php } ?>
    </tbody>
</table>