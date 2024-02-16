<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

$this->title = 'Add Project';
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-create">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Project Information</h3></div>
                <div class="box-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'regionModel' => $regionModel,
                        'provinceModel' => $provinceModel,
                        'citymunModel' => $citymunModel,
                        'barangayModel' => $barangayModel,
                        'categoryModel' => $categoryModel,
                        'sdgModel' => $sdgModel,
                        'rdpChapterModel' => $rdpChapterModel,
                        'rdpChapterOutcomeModel' => $rdpChapterOutcomeModel,
                        'rdpSubChapterOutcomeModel' => $rdpSubChapterOutcomeModel,
                        'expectedOutputModels' => $expectedOutputModels,
                        'outcomeModels' => $outcomeModels,
                        'revisedScheduleModels' => $revisedScheduleModels,
                        'fundSourceModels' => $fundSourceModels,
                        'agencies' => $agencies,
                        'projects' => $projects,
                        'programs' => $programs,
                        'sectors' => $sectors,
                        'subSectors' => $subSectors,
                        'modes' => $modes,
                        'fundSources' => $fundSources,
                        'scopes' => $scopes,
                        'regions' => $regions,
                        'provinces' => $provinces,
                        'citymuns' => $citymuns,
                        'barangays' => $barangays,
                        'categories' => $categories,
                        'chapters' => $chapters,
                        'goals' => $goals,
                        'chapterOutcomes' => $chapterOutcomes,
                        'subChapterOutcomes' => $subChapterOutcomes,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
