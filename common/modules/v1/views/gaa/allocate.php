<?php

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\Collapse;
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */

$this->title = 'GAA '.$model->year;
$this->params['breadcrumbs'][] = ['label' => 'GAAs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="nep-view">
    <?= $this->render('_menu', ['model' => $model]) ?>
    <div class="row">
        <div class="col-md-4">
            <h4>Objects</h4>
            <p><i class="fa fa-exclamation-circle"></i> Select an item to allocate amounts in every division.</p>
            <?= !empty($items) ? Collapse::widget(['items' => $items]) : '<p>No objects allocated with amount. Try putting instead. '.Html::a('Click Here', ['/v1/gaa/form', 'id' => $model->id]).'</p>' ?>
        </div>
        <div class="col-md-6">
            <div id="allocation-form"></div>
        </div>
    </div>
</div>
<?php
  $script = '
    function allocate(id)
    {
        $.ajax({
            url: "'.Url::to(['/v1/gaa/allocation-form']).'?id=" + id,
            beforeSend: function(){
                $("#allocation-form").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#allocation-form").empty();
                $("#allocation-form").hide();
                $("#allocation-form").fadeIn("slow");
                $("#allocation-form").html(data);
            }
        }); 
    }
  ';
  $this->registerJs($script, View::POS_END);
?>
