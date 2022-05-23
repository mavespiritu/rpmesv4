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

    $this->title = 'Accomplishment';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomplishment-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Accomplishments</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'years' => $years,
                        'agencies' => $agencies,
                        'quarters' => $quarters,
                    ]) ?>
                    <br>
                    <?php if(!empty($getData)){ ?>
                        <?= $this->render('_form', [
                            'years' => $years,
                            'agencies' => $agencies,
                            'quarters' => $quarters,
                            'genders' => $genders,
                            'physical' => $physical,
                            'financial' => $financial,
                            'personEmployed' => $personEmployed,
                            'accomplishment' => $accomplishment,
                            'beneficiaries' => $beneficiaries,
                            'groups' => $groups,
                            'projectsModels' => $projectsModels,
                            'projectsPages' => $projectsPages,
                            'projects' => $projects,
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