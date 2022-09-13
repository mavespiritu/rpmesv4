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
                        'categories' => $categories,
                        'sectors' => $sectors,
                    ]) ?>
                    <br>
                    <?php if(!empty($getData)){ ?>
                        <?= $this->render('_form', [
                            'years' => $years,
                            'model' => $model,
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
                            'agency_id' => $agency_id,
                        ]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function printSummary(model, year, quarter, agency_id, category_id, sector_id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/accomplishment/download-accomplishment']).'?type=print&model=" + model + "&year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&category_id=" + category_id + "&sector_id=" + sector_id, 
                "Print",
                "left=200", 
                "top=200", 
                "width=650", 
                "height=500", 
                "toolbar=0", 
                "resizable=0"
                );
                printWindow.addEventListener("load", function() {
                    printWindow.print();
                    setTimeout(function() {
                    printWindow.close();
                }, 1);
                }, true);
        }
    ';

    $this->registerJs($script, View::POS_END);
?>