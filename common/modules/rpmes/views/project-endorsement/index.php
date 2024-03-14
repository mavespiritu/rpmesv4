<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPMES Form 6: Reports on the Status of Projects Encountering Implementation Problems';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>
<?php foreach ($dataProvider->models as $model): ?>
    <?php
    Modal::begin([
        'id' => 'endorse-modal-'.$model->id,
        'size' => "modal-lg",
        'header' => '<div id="endorse-modal-'.$model->id.'-header"><h4>Endorse to NPMC</h4></div>',
        'options' => ['tabindex' => false],
    ]);
    echo '<div id="endorse-modal-'.$model->id.'-content"></div>';
    Modal::end();

    ?>
<?php endforeach; ?>
<div class="project-status-index">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">List of Reports on the Status of Projects Encountering Implementation Problems</h3>
        </div>
        <div class="box-body">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
            <?= GridView::widget([
                'options' => [
                    'class' => 'table-responsive',
                ],
                'tableOptions' => [
                    'class' => 'table table-bordered table-striped table-hover',
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                    ],
                    [
                        'attribute' => 'year',
                        'header' => 'Year',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'quarter',
                        'header' => 'Quarter',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'header' => '
                                    (a) Project No. <br>
                                    (b) Program/Project Title <br>
                                    (c) Province <br>
                                    (d) City/Municipality <br>
                                    (e) Barangay
                                    ',
                        'headerOptions' => [
                            'style' => 'width: 30%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return 
                                '(a) '.$model->project->project_no.'<br>'.
                                '(b) '.$model->project->title.'<br>'.
                                '(c) '.$model->project->provinceTitle.'<br>'.
                                '(d) '.$model->project->citymunTitle.'<br>'.
                                '(e) '.$model->project->barangayTitle
                            ;
                        }
                    ],
                    [
                        'header' => 'Slippage <br> (%)',
                        'headerOptions' => [
                            'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->project->getSlippage($model->year)[$model->quarter] > -10 && $model->project->getSlippage($model->year)[$model->quarter] < 10 ? number_format($model->project->getSlippage($model->year)[$model->quarter], 2) : '<font style="color:red">'.number_format($model->project->getSlippage($model->year)[$model->quarter], 2).'</font>';
                        }
                    ],
                    [
                        'header' => '
                                    (a) Typology <br>
                                    (b) Issue Status <br>
                                    (c) Findings <br>
                                    (d) Reasons <br>
                                    (e) Actions Taken <br>
                                    (f) Actions to be taken
                                    ',
                        'headerOptions' => [
                            'style' => 'width: 30%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return 
                                '(a) '.($model->projectException ? $model->projectException->typology ? $model->projectException->typology->title : '' : '').'<br>'.
                                '(b) '.($model->projectException ? $model->projectException->issue_status : '').'<br>'.
                                '(c) '.strip_tags($model->projectException ? $model->projectException->findings: '').'<br>'.
                                '(d) '.strip_tags($model->projectException ? $model->projectException->causes : '').'<br>'.
                                '(e) '.strip_tags($model->projectException ? $model->projectException->action_taken : '').'<br>'.
                                '(f) '.strip_tags($model->projectException ? $model->projectException->recommendations : '')
                            ;
                        }
                    ],
                    [
                        'attribute' => 'requested_action',
                        'header' => 'Requested Action from the NPMC',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return strip_tags($model->npmc_action);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'template' => '<center>{update} {delete}</center>',
                        'buttons' => [
                            'update' => function($url, $model, $key){
                                $modalID = $model->id;
                                return Html::a('Update', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-warning btn-block btn-xs'
                                ]);
                            },
                            'delete' => function($url, $model, $key){
                                return Html::a('Delete', ['delete', 'id' => $model->id], [
                                                    'class' => 'btn btn-danger btn-block btn-xs',
                                                    'data' => [
                                                        'confirm' => 'Are you sure want to delete this item?',
                                                        'method' => 'post',
                                                    ],
                                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<?php foreach ($dataProvider->models as $model): ?>
    <?php
    $this->registerJs('
        $("#endorse-'.$model->id.'-button").click(function(e){
            e.preventDefault();

            var modalId = $(this).data("target");
            $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
            
            return false;
        });');
    ?>
<?php endforeach; ?>

<?php
if ($successMessage) {
    $this->registerJs("
        $(document).ready(function() {
            // Display the flash message
            $('.alert-success').fadeIn();

            // Hide the flash message after 5 seconds
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        });
    ");
}
?>