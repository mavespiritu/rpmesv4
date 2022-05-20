<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-create">
    <br>
    <div id="project-alert"></div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'regionModel' => $regionModel,
                'provinceModel' => $provinceModel,
                'citymunModel' => $citymunModel,
                'barangayModel' => $barangayModel,
                'categoryModel' => $categoryModel,
                'kraModel' => $kraModel,
                'sdgModel' => $sdgModel,
                'rdpChapterModel' => $rdpChapterModel,
                'rdpChapterOutcomeModel' => $rdpChapterOutcomeModel,
                'rdpSubChapterOutcomeModel' => $rdpSubChapterOutcomeModel,
                'targets' => $targets,
                'expectedOutputModels' => $expectedOutputModels,
                'outcomeModels' => $outcomeModels,
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
                'kras' => $kras,
                'chapters' => $chapters,
                'goals' => $goals,
                'chapterOutcomes' => $chapterOutcomes,
                'subChapterOutcomes' => $subChapterOutcomes,
                'quarters' => $quarters,
                'genders' => $genders,
                'dueDate' => $dueDate
            ]) ?>
        </div>
    </div>
</div>
