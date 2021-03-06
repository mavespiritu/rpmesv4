<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;

$appAsset = frontend\assets\AppAsset::register($this);
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
                <h4 style="color: #3C8DBC;"><?php /* Html::img($appAsset->baseUrl.'/images/_MG_9221.jpg', ['class' => 'profile-image']) */ ?>Rey Ferreria</h4>

                <p>Email: rbferreria@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h4 style="color: #3C8DBC;">Jeremiah Chor Miranda</h4>

                <p>Email: jdmiranda@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h4 style="color: #3C8DBC;">Mary Ann Virtudes</h4>

                <p>Email: mdvirtudes@neda.gov.ph</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <h4 style="color: #3C8DBC;">Yehlen Yangat</h4>

                <p>Email: yiyangat@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h4 style="color: #3C8DBC;">John Chester Erestingcol</h4>

                <p>Email: jeerestingcol@neda.gov.ph</p>
            </div>
            <div class="col-lg-4">
                <h4 style="color: #3C8DBC;">Arleah Joice Banaga</h4>

                <p>Email: acbanaga@neda.gov.ph</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <h4 style="color: #3C8DBC;">Andrew Valmores</h4>

                <p>Email: TBA</p>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-image{
        border-radius: 50%;
        height: 20%;
        width: 20%;
    }
</style>