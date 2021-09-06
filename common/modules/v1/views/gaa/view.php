<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\View;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */

$this->title = 'GAA '.$model->year;
$this->params['breadcrumbs'][] = ['label' => 'GAAs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gaa-view">
    <?= $this->render('_menu', ['model' => $model]) ?>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div id="object-form"></div>
                            <div id="objects" style="max-height: 45vh; overflow-y: scroll; overflow-x: hidden;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <h4>Add Programs</h4>
                            <?= $this->render('_pap_form', [
                                'model' => $model,
                                'papModel' => $papModel,
                                'paps' => $paps,
                                'fundSources' => $fundSources,
                            ]) ?>
                            <hr style="opacity: 0.3">
                            <h4>Included Programs for GAA <?= $model->year ?></h4>
                            <p><i class="fa fa-exclamation-circle"></i> Drag and drop the items to save arrangement</p>
                            <div id="programs" style="max-height: 45vh; overflow-y: scroll; overflow-x: hidden;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
  $script = '
    $(document).ready(function() {
        programs();
        objects();
        objectForm();
    });

    function programs()
    {
        $.ajax({
            url: "'.Url::to(['/v1/gaa/programs']).'?id=" + '.$model->id.',
            beforeSend: function(){
                $("#programs").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#programs").empty();
                $("#programs").hide();
                $("#programs").fadeIn("slow");
                $("#programs").html(data);
            }
        }); 
    }

    function objectForm()
    {
        $.ajax({
            url: "'.Url::to(['/v1/gaa/object-form']).'?id=" + '.$model->id.',
            beforeSend: function(){
                $("#object-form").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#object-form").empty();
                $("#object-form").hide();
                $("#object-form").fadeIn("slow");
                $("#object-form").html(data);
            }
        }); 
    }

    function objects()
    {
        $.ajax({
            url: "'.Url::to(['/v1/gaa/objects']).'?id=" + '.$model->id.',
            beforeSend: function(){
                $("#objects").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#objects").empty();
                $("#objects").hide();
                $("#objects").fadeIn("slow");
                $("#objects").html(data);
            }
        }); 
    }
  ';
  $this->registerJs($script, View::POS_END);
?>
