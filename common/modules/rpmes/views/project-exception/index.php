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

    $this->title = 'Form 3: Project Exception Report';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-exception-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Exception Report Form</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'years' => $years,
                        'agencies' => $agencies,
                        'quarters' => $quarters,
                        'statuses' => $statuses,
                    ]) ?>
                    <br>
                    <?php if(!empty($getData)){ ?>
                        <?= $this->render('_form', [
                            'years' => $years,
                            'agencies' => $agencies,
                            'quarters' => $quarters,
                            'exceptions' => $exceptions,
                            'projectsModels' => $projectsModels,
                            'projectsPages' => $projectsPages,
                            'getData' => $getData,
                            'dueDate' => $dueDate,
                            'submissionModel' => $submissionModel,
                            'agency_id' => $agency_id
                        ]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>