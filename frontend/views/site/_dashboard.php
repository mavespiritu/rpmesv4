<?php
use yii\helpers\Url;
?>
<div>
<a href="<?= Url::to('/user/login', ['title' => 'Go to Login Page']) ?>">Go to Login Page</a>
<br>
<br>
<h2 class="text-center">Welcome to the NRO1 RPMES Dashboard<br>
<small>as of <?= date("F j, Y") ?></small>
</h2>
<div>
<iframe title="eRPMES dashboard_sample3" width="100%" height="1012" src="https://app.powerbi.com/view?r=eyJrIjoiNWZkOTNhY2UtODg2NC00NmU1LWJmZTEtOTNjMmU0NmFhMmQ4IiwidCI6IjUyYzA4NGNjLWNkMTUtNDY3MS04YTU3LWMxOTU2NWJjZGZjMiIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>
</div>
</div>