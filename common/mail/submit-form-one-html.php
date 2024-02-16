<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<b>Dear NRO1 Admin,</b>

<p>Please be informed that the <?= $model->agency->title ?> has submitted its Agency Form 1 for FY <?= $model->year ?>. You can login to eRPMES to review the submission.</p>

<p style="text-align: center;"><a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/rpmes/plan/view', 'id' => $model->id]) ?>" style="box-sizing:border-box; border-radius:4px; color:#fff; display:inline-block; overflow:hidden; text-decoration:none; background-color:#2d3748; border-bottom:8px solid #2d3748; border-left:18px solid #2d3748; border-right:18px solid #2d3748; border-top:8px solid #2d3748">Review Submission</a></p>

<p>Regards,</p>
<p>NEDA Regional Office 1 eRPMES </p>
<br>
<hr>
<small>If you're having trouble clicking the "Review Submission" button, copy and paste the URL below into your web browser: <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/rpmes/plan/view', 'id' => $model->id]) ?>"><?= Yii::$app->urlManager->createAbsoluteUrl(['/rpmes/plan/view', 'id' => $model->id]) ?></a><br>
<i>This email is generated automatically by our information system. Please note that this is not a monitored inbox, and replies to this email will not be attended to. If you have any inquiries or concerns, kindly contact our support team through the designated channels. Thank you.</i></small>
