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

    $this->title = 'Acknowledgment of RPMES Form 4 Submissions';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-results-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">RPMES Form 4 Submissions</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search-project-results', [
                        'model' => $model,
                        'years' => $years,
                        'agencies' => $agencies,
                    ]) ?>  
                    <br>
                    <?php if(!empty($getData)){ ?>
                        <?= $this->render('project-results-submissions', [
                            'submissions' => $submissions,
                            'getData' => $getData,
                        ]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>