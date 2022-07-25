<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveField;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use yii\web\View;
use yii\widgets\MaskedInput;
use kartik\daterange\DateRangePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;
use \file\components\AttachmentsInput;
use yii\web\JsExpression;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use dosamigos\switchery\Switchery;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */   
/* @var $form yii\widgets\ActiveForm */
$HtmlHelper = new HtmlHelper();
?>
<div class="agreement-table">
    <table id="agreement-table" class="table table-bordered table-hover table-striped" cellspacing="0" style="min-width: 4000px;">
        <thead>
            <tr>
                <td>ID</td>
                <td>Project Name/Total Project Cost</td>
                <td>Sector/Sub Sector</td>
                <td>Issue Details</td>
                <td>Location</td>
                <td>Implementing Agency</td>
                <td>Date of PSS/Facilitation Meeting</td>
                <td>Concerned Agencies</td>
                <td>Agreements Reached</td>
                <td>Next Steps</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($agreements)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($agreements as $agreement){ ?>
                <tr>
                    <td align=center><?= $agreement['id'] ?></td>
                    <td align=center><?= $agreement['projectTitle'] ?></td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>