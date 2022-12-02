<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<div id="heatmap-table">
    <?php if(!empty($projects)){ ?>
        <?php foreach($projects as $project){ ?>
                <?= $project['provinceTitle'] ?><br>
            <?php }?>
        <?php } ?>
</div>
