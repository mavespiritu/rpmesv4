<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use yii\grid\GridView;
    use yii\widgets\LinkPager;
    use common\components\helpers\HtmlHelper;
    use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
    DisableButtonAsset::register($this);
    use yii\web\View;

    $this->title = 'Acknowledgment of Form 1 Submissions';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="monitoring-plan-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Form 1 Submissions</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search-monitoring-plan', [
                        'model' => $model,
                        'years' => $years,
                        'agencies' => $agencies,
                    ]) ?>  
                    <br>
                    <?php if(!empty($getData)){ ?>
                        <?= $this->render('monitoring-plan-submissions', [
                            'submissions' => $submissions,
                            'getData' => $getData,
                        ]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>