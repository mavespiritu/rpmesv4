<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<b>Dear <?= $model->submission->agency->title ?>,</b>

<p>Please be informed that your submission of eRPMES Form 1: Initial Project Report for CY <?= $model->submission->year ?> has been acknowledged last <?= date("F j, Y") ?> by <?= $model->acknowledger ?>, <?= $model->acknowledgerPosition ?> of the NEDA Regional Office 1. Please see details below for your reference and appropriate action: </p>

<p><b>Findings:</b><br>
<?= $model->findings ?>
</p>
<p><b>Action Taken:</b><br>
<?= $model->action_taken ?>
</p>

<p>Regards,</p>
<p>NEDA Regional Office 1 eRPMES </p>
<br>
<hr>
<small>
<i>This email is generated automatically by our information system. Please note that this is not a monitored inbox, and replies to this email will not be attended to. If you have any inquiries or concerns, kindly contact our support team through the designated channels. Thank you.</i></small>
