<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\DueDate;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use common\modules\rpmes\models\ProjectCategory;
use common\modules\rpmes\models\ProjectSdgGoal;
use common\modules\rpmes\models\ProjectRdpChapter;
use common\modules\rpmes\models\ProjectRdpChapterOutcome;
use common\modules\rpmes\models\ProjectRdpSubChapterOutcome;
use common\modules\rpmes\models\ProjectExpectedOutput;
use common\modules\rpmes\models\ProjectOutcome;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Program;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\SubSector;
use common\modules\rpmes\models\SubSectorPerSector;
use common\modules\rpmes\models\Category;
use common\modules\rpmes\models\KeyResultArea;
use common\modules\rpmes\models\ModeOfImplementation;
use common\modules\rpmes\models\FundSource;
use common\modules\rpmes\models\LocationScope;
use common\modules\rpmes\models\SdgGoal;
use common\modules\rpmes\models\RdpChapter;
use common\modules\rpmes\models\RdpChapterOutcome;
use common\modules\rpmes\models\RdpSubChapterOutcome;
use common\modules\rpmes\models\ProjectSearch;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\MultipleModel;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use kartik\mpdf\Pdf;

class ReportController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['form-one'],
                'rules' => [
                    [
                        'actions' => ['form-one'],
                        'allow' => true,
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }
    public function actionFormOne()
    {
        $model = new Submission();
        $model->year = date("Y");
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;

        $data = [];

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');
        
        $provinces = [];
        $citymuns = [];

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = [];

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        if($model->load(Yii::$app->request->post()))
        {
            $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial'])->createCommand()->getRawSql();
            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical'])->createCommand()->getRawSql();
            $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed'])->createCommand()->getRawSql();
            $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed'])->createCommand()->getRawSql();
            $beneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Beneficiaries'])->createCommand()->getRawSql();
            $regionTitles = ProjectRegion::find()
                        ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblregion.abbreviation ORDER BY tblregion.abbreviation ASC SEPARATOR ", ") as title'])
                        ->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id')
                        ->leftJoin('project', 'project.id = project_region.project_id')
                        ->where(['project.draft' => 'No'])
                        ->groupBy(['project_region.project_id'])
                        ->createCommand()->getRawSql();

            $provinceTitles = ProjectProvince::find()
                        ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ", ") as title'])
                        ->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id')
                        ->leftJoin('project', 'project.id = project_province.project_id')
                        ->where(['project.draft' => 'No'])
                        ->groupBy(['project_province.project_id'])
                        ->createCommand()->getRawSql();

            $citymunTitles = ProjectCitymun::find()
                        ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
                        ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                        ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                        ->leftJoin('project', 'project.id = project_citymun.project_id')
                        ->where(['project.draft' => 'No'])
                        ->groupBy(['project_citymun.project_id'])
                        ->createCommand()->getRawSql();
            
            $barangayTitles = ProjectBarangay::find()
                        ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,",",tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
                        ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
                        ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
                        ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                        ->leftJoin('project', 'project.id = project_barangay.project_id')
                        ->where(['project.draft' => 'No'])
                        ->groupBy(['project_barangay.project_id'])
                        ->createCommand()->getRawSql();
    
            $projects = Project::find()
                        ->select([
                            'mode_of_implementation.title as modeOfImplementationTitle',
                            'project.title as projectTitle',
                            'sector.title as sectorTitle',
                            'sub_sector.title as subSectorTitle',
                            'fund_source.title as fundSourceTitle',
                            'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                            'project.start_date as startDate',
                            'project.completion_date as completionDate',
                            'IF(project.data_type <> "", concat(physicalTargets.indicator, " (",project.data_type,")"), concat(physicalTargets.indicator, " (No Data Type)")) as unitOfMeasure',
                            'financialTargets.q1 as financialQ1',
                            'financialTargets.q2 as financialQ2',
                            'financialTargets.q3 as financialQ3',
                            'financialTargets.q4 as financialQ4',
                            'physicalTargets.q1 as physicalQ1',
                            'physicalTargets.q2 as physicalQ2',
                            'physicalTargets.q3 as physicalQ3',
                            'physicalTargets.q4 as physicalQ4',
                            'maleEmployedTargets.q1 as maleEmployedQ1',
                            'maleEmployedTargets.q2 as maleEmployedQ2',
                            'maleEmployedTargets.q3 as maleEmployedQ3',
                            'maleEmployedTargets.q4 as maleEmployedQ4',
                            'femaleEmployedTargets.q1 as femaleEmployedQ1',
                            'femaleEmployedTargets.q2 as femaleEmployedQ2',
                            'femaleEmployedTargets.q3 as femaleEmployedQ3',
                            'femaleEmployedTargets.q4 as femaleEmployedQ4',
                            'beneficiariesTargets.q1 as beneficiaryQ1',
                            'beneficiariesTargets.q2 as beneficiaryQ2',
                            'beneficiariesTargets.q3 as beneficiaryQ3',
                            'beneficiariesTargets.q4 as beneficiaryQ4',
                        ]);
            $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
            $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
            $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
            $projects = $projects->leftJoin(['beneficiariesTargets' => '('.$beneficiariesTargets.')'], 'beneficiariesTargets.project_id = project.id');
            $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
            $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
            $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
            $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
            $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
            $projects = $projects->leftJoin('mode_of_implementation', 'mode_of_implementation.id = project.mode_of_implementation_id');
            $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
            $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
            $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
            $projects = $projects->andWhere(['project.draft' => 'No']);

            if(Yii::$app->user->can('AgencyUser'))
            {
                $projects = $projects->andWhere(['agency.id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
            }

            if($model->year != '')
            {
                $projects = $projects->andWhere(['project.year' => $model->year]);
            }

            if($model->agency_id != '')
            {
                $projects = $projects->andWhere(['agency.id' => $model->agency_id]);
            }

            if($model->fund_source_id != '')
            {
                $projects = $projects->andWhere(['fund_source.id' => $model->fund_source_id]);
            }

            if($model->region_id != '')
            {
                $projects = $projects->andWhere(['tblregion.region_c' => $model->region_id]);
            }

            if($model->province_id != '')
            {
                $projects = $projects->andWhere(['tblprovince.province_c' => $model->province_id]);
            }

            if($model->citymun_id != '')
            {
                $projects = $projects->andWhere(['tblcitymun.province_c' => $model->province_id, 'tblcitymun.citymun_c' => $model->citymun_id]);
            }

            if($model->sector_id != '')
            {
                $projects = $projects->andWhere(['sector.id' => $model->sector_id]);
            }

            if($model->sub_sector_id != '')
            {
                $projects = $projects->andWhere(['sub_sector.id' => $model->sub_sector_id]);
            }

            $projects = $projects->asArray()->all();

            return $this->renderAjax('_form-one', [
                'model' => $model,
                'projects' => $projects,
                'quarters' => $quarters,
                'genders' => $genders
            ]);
        }

        return $this->render('form-one', [
            'model' => $model,
            'years' => $years,
            'fundSources' => $fundSources,
            'regions' => $regions,
            'provinces' => $provinces,
            'citymuns' => $citymuns,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'agencies' => $agencies,
        ]);
    }

    public function actionPrintFormOne($year, $fund_source_id, $sector_id, $sub_sector_id, $region_id, $province_id, $citymun_id, $agency_id)
    {
        $model = [];
        $model['year'] = $year;
        $model['fund_source_id'] = $fund_source_id;
        $model['sector_id'] = $sector_id;
        $model['sub_sector_id'] = $sub_sector_id;
        $model['region_id'] = $region_id;
        $model['province_id'] = $province_id;
        $model['citymun_id'] = $citymun_id;
        $model['agency_id'] = $agency_id;

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial'])->createCommand()->getRawSql();
        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical'])->createCommand()->getRawSql();
        $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed'])->createCommand()->getRawSql();
        $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed'])->createCommand()->getRawSql();
        $beneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Beneficiaries'])->createCommand()->getRawSql();

        $regionTitles = ProjectRegion::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblregion.abbreviation ORDER BY tblregion.abbreviation ASC SEPARATOR ", ") as title'])
                    ->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id')
                    ->leftJoin('project', 'project.id = project_region.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_region.project_id'])
                    ->createCommand()->getRawSql();

        $provinceTitles = ProjectProvince::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ", ") as title'])
                    ->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id')
                    ->leftJoin('project', 'project.id = project_province.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_province.project_id'])
                    ->createCommand()->getRawSql();

        $citymunTitles = ProjectCitymun::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('project', 'project.id = project_citymun.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_citymun.project_id'])
                    ->createCommand()->getRawSql();
        
        $barangayTitles = ProjectBarangay::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,",",tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
                    ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('project', 'project.id = project_barangay.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_barangay.project_id'])
                    ->createCommand()->getRawSql();

        $projects = Project::find()
                    ->select([
                        'mode_of_implementation.title as modeOfImplementationTitle',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'fund_source.title as fundSourceTitle',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'project.start_date as startDate',
                        'project.completion_date as completionDate',
                        'IF(project.data_type <> "", concat(physicalTargets.indicator, " (",project.data_type,")"), concat(physicalTargets.indicator, " (No Data Type)")) as unitOfMeasure',
                        'financialTargets.q1 as financialQ1',
                        'financialTargets.q2 as financialQ2',
                        'financialTargets.q3 as financialQ3',
                        'financialTargets.q4 as financialQ4',
                        'physicalTargets.q1 as physicalQ1',
                        'physicalTargets.q2 as physicalQ2',
                        'physicalTargets.q3 as physicalQ3',
                        'physicalTargets.q4 as physicalQ4',
                        'maleEmployedTargets.q1 as maleEmployedQ1',
                        'maleEmployedTargets.q2 as maleEmployedQ2',
                        'maleEmployedTargets.q3 as maleEmployedQ3',
                        'maleEmployedTargets.q4 as maleEmployedQ4',
                        'femaleEmployedTargets.q1 as femaleEmployedQ1',
                        'femaleEmployedTargets.q2 as femaleEmployedQ2',
                        'femaleEmployedTargets.q3 as femaleEmployedQ3',
                        'femaleEmployedTargets.q4 as femaleEmployedQ4',
                        'beneficiariesTargets.q1 as beneficiaryQ1',
                        'beneficiariesTargets.q2 as beneficiaryQ2',
                        'beneficiariesTargets.q3 as beneficiaryQ3',
                        'beneficiariesTargets.q4 as beneficiaryQ4',
                    ]);
        $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['beneficiariesTargets' => '('.$beneficiariesTargets.')'], 'beneficiariesTargets.project_id = project.id');
        $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->leftJoin('mode_of_implementation', 'mode_of_implementation.id = project.mode_of_implementation_id');
        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
        $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
        $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        

        if(Yii::$app->user->can('AgencyUser'))
        {
            $projects = $projects->andWhere(['agency.id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        }

        if($model['year'] != '')
        {
            $projects = $projects->andWhere(['project.year' => $model['year']]);
        }

        if($model['agency_id'] != '')
        {
            $projects = $projects->andWhere(['agency.id' => $model['agency_id']]);
        }

        if($model['fund_source_id'] != '')
        {
            $projects = $projects->andWhere(['fund_source.id' => $model['fund_source_id']]);
        }

        if($model['region_id'] != '')
        {
            $projects = $projects->andWhere(['tblregion.region_c' => $model['region_id']]);
        }

        if($model['province_id'] != '')
        {
            $projects = $projects->andWhere(['tblprovince.province_c' => $model['province_id']]);
        }

        if($model['citymun_id'] != '')
        {
            $projects = $projects->andWhere(['tblcitymun.province_c' => $model['province_id'], 'tblcitymun.citymun_c' => $model['citymun_id']]);
        }

        if($model['sector_id'] != '')
        {
            $projects = $projects->andWhere(['sector.id' => $model['sector_id']]);
        }

        if($model['sub_sector_id'] != '')
        {
            $projects = $projects->andWhere(['sub_sector.id' => $model['sub_sector_id']]);
        }

        $projects = $projects->asArray()->all();

        return $this->renderAjax('reports/form-one', [
            'model' => $model,
            'type' => 'print',
            'projects' => $projects,
            'quarters' => $quarters,
            'genders' => $genders
        ]);
    }
}
