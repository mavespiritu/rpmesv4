<table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <td align=right><b>No. of Male Beneficiaries</b></td>
            <td align=right><b>No. of Female Beneficiaries</b></td>
            <td align=right><b>Total</b></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align=right><b>Total</b></td>
            <td align=right><b><?= number_format($total['maleTotal'], 0) ?></b></td>
            <td align=right><b><?= number_format($total['femaleTotal'], 0) ?></b></td>
            <td align=right><b><?= number_format($total['maleTotal'] + $total['femaleTotal'], 0) ?></b></td>
        </tr>
    </tbody>
</table>