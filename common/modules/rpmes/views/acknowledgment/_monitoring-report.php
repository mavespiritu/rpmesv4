<style>
    *{ font-family: 'Arial'; }
    table{ width: 100%; border-collapse: collapse; }
    td{ border: 1px solid black; padding: 10px;}
</style>
<p style="text-align: center;">
    <b>CORRESPONDING ACKNOWLEDGMENT</b> <br>
    <b>ONLINE REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM</b> <br>
    <b><?= $officeTitle ? $officeTitle->value : '' ?></b> <br>
    <?= $officeAddress ? $officeAddress->value : '' ?>
</p>
<table cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td style="width: 50%;">
                <b>For/To: <?= $acknowledgment->recipient_name ?></b> <br>
                <span style="margin-left: 6%;"><?= $acknowledgment->recipient_designation ?> <br>
                <span style="margin-left: 6%;"><?= $acknowledgment->recipient_office ?></span> <br>
                <span style="margin-left: 6%;"><?= $acknowledgment->recipient_address ?></span>
            </td>
            <td style="width: 50%;">
                <b>Control No:</b> <?= $acknowledgment->control_no ?> <br>
                <b>Date & Time Received:</b> <?= date("F j, Y H:i:s", strtotime($submission->date_submitted))?><br>
                <b>Report Submitted by:</b> <?= $submission->submitter ?> <br>
                <span style="margin-left: 24%;">
                    <?= $submission->submitterPosition ?>
                </span>
            </td>
        </tr>
        <tr><td colspan=2><b>Subject: Submission of CY <?= $submission->year ?> <?= $submission->quarter ?> Regional Project Monitoring and Evaluation System (RPMES) Form 2 (Project Monitoring Report)</b></td></tr>
        <tr>
            <td colspan=2>
                <b>Findings:</b>
                <?= $acknowledgment->findings ?>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <b>Action Taken:</b>
                <?= $acknowledgment->action_taken ?>
            </td>
        </tr>
        <tr>
            <td>
                <b>CA Prepared by:</b> <br><br>
                <?= $acknowledgment->acknowledger ?> <br>
                <?= $acknowledgment->acknowledgerPosition ?>
            </td>
            <td>
                <b>Division:</b> <br><br>
                Monitoring and Evaluation Division
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <p style="text-align: center;">
                    <u><?= date("F j, Y", strtotime($acknowledgment->date_acknowledged)) ?></u> <br>
                    <b>Date</b>
                </p>
            </td>
            <td>
                <br>
                <p style="text-align: center;">
                    <u><b><?= $officeHead ? $officeHead->value : 'No set agency head' ?></b></u> <br>
                    <?= $officeTitleShort ? $officeTitleShort->value : 'No set agency title short' ?> Regional Director
                </p>
            </td>
        </tr>
    </tbody>
</table>