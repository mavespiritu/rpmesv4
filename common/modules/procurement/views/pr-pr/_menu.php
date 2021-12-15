<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
?>
<br>
<ul class="list-inline">
    <li><a href="javascript:void(0)" id="basic-information" onClick="viewBasicInformation(<?= $model->id ?>)">Basic Information</a></li>
    <li><a href="javascript:void(0)" id="rfq" onClick="viewRfq(<?= $model->id ?>)">RFQs/APRs</a></li>
    <li><a href="javascript:void(0)" id="aoq" onClick="viewAoq(<?= $model->id ?>)">AOQs</a></li>
    <li><a href="javascript:void(0)" id="po" onClick="viewPo(<?= $model->id ?>)">Purchase Orders</a></li>
    <li><a href="javascript:void(0)" id="timeline" onClick="viewTimeline(<?= $model->id ?>)">Timeline</a></li>
</ul>
<style>
    .list-inline li{
        padding-right: 20px;
    }
</style>
<?php
$script = '
    $( document ).ready(function() {
        viewBasicInformation('.$model->id.');
    });

    function toggleMenu(id)
    {
        var ids = ["basic-information", "rfq", "aoq", "po", "timeline"];

        $.each(ids, function(idx, value)
        {
            id == value ? $("#"+value).css("font-weight", "bolder") : $("#"+value).css("font-weight", "normal");
        });
    }

    function viewItemForm(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/view-item-form']).'?id=" + id,
            beforeSend: function(){
                $("#item-form").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#item-form").empty();
                $("#item-form").hide();
                $("#item-form").fadeIn();
                $("#item-form").html(data);
            }
        }); 
    }

    function viewItems(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/view-items']).'?id=" + id,
            beforeSend: function(){
                $("#item-list").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#item-list").empty();
                $("#item-list").hide();
                $("#item-list").fadeIn();
                $("#item-list").html(data);
            }
        }); 
    }

    function viewBasicInformation(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/view-basic-information']).'?id=" + id,
            beforeSend: function(){
                $("#main-content").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#main-content").empty();
                $("#main-content").hide();
                $("#main-content").fadeIn();
                $("#main-content").html(data);
                viewItemForm(id);
                viewItems(id);
                toggleMenu("basic-information");
            }
        }); 
    }

    function viewAoq(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/view-aoq']).'?id=" + id,
            beforeSend: function(){
                $("#main-content").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#main-content").empty();
                $("#main-content").hide();
                $("#main-content").fadeIn();
                $("#main-content").html(data);
            }
        }); 
    }

    function viewPo(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/view-po']).'?id=" + id,
            beforeSend: function(){
                $("#main-content").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#main-content").empty();
                $("#main-content").hide();
                $("#main-content").fadeIn();
                $("#main-content").html(data);
            }
        }); 
    }

    function viewTimeline(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/view-timeline']).'?id=" + id,
            beforeSend: function(){
                $("#main-content").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#main-content").empty();
                $("#main-content").hide();
                $("#main-content").fadeIn();
                $("#main-content").html(data);
            }
        }); 
    }
';
$this->registerJs($script, View::POS_END);
?>
