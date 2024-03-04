<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

$asset = AppAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'System Updates';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="plan-index">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">System Updates</h3></div>
            <div class="box-body" style="height: calc(100vh - 200px); width: 100%;">
                <ul>
                    <li>04 March, 2024
                        <ul>
                            <li>Creating, updating and deleting an output indicator is enabled in Form 1 if the submission status is "Draft" or "For further validation", to provide more output indicators specific for an implementation year.</li>
                        </ul>
                    </li>
                    <li>01 March, 2024
                        <ul>
                            <li>Deleting attached project profile is available in projects table.</li>
                            <li>Fixed issues on deleting multiple fields in project creation form (funding sources, revised schedules, output indicators, outcome indicators).</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
