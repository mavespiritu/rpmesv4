<?php
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'eRPMES';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome to eRPMES!</h1>

        <p class="lead">You have successfully landed to our project monitoring and evaluation system.</p>

        <p><?= Html::a('I want to login', ['/user/login'],['class' => 'btn btn-lg btn-success'])?> <?= Html::a('Let me register', ['/user/register'],['class' => 'btn btn-lg btn-primary'])?></p>
    </div>

    <div class="body-content container">
        <h2 class="text-center">Features of eRPMES</h2>
        <div class="row">
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;"><i class="fa fa-folder-open"></i> Project Management</h3>

                <p>Enroll your projects to keep track of progress physically and financially using the RPMES Forms.</p>
            </div>
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;"><i class="fa fa-map-marker"></i> Geo-tagging</h3>

                <p>Soon enough, our development team will integrate open-source mapping tools to easily tag your projects on Philippine Map.</p>
            </div>
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;"><i class="fa fa-area-chart"></i> Dashboard</h3>

                <p>Your data and statistics will be showcased through graphs and charts.</p>
            </div>
        </div>
        <br>
        <h2 class="text-center">Our Technical Team</h2>
        <div class="row">
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;">Rey Ferreria</h3>

                <p>Email: rbferreria@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;">Jeremiah Chor Miranda</h3>

                <p>Email: jdmiranda@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;">Mary Ann Virtudes</h3>

                <p>Email: mdvirtudes@neda.gov.ph</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;">John Chester Erestingcol</h3>

                <p>Email: jeerestingcol@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;">Arleah Joice Banaga</h3>

                <p>Email: acbanaga@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h3 style="color: #3C8DBC;">Andrew Valmores</h3>

                <p>Email: TBA</p>
            </div>
        </div>
    </div>
</div>
