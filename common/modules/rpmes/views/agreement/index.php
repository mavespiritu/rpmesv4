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

    $this->title = 'Agreements';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="agreement-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Agreement</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'quarters' => $quarters,
                        'years' => $years,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'regions' => $regions,
                        'provinces' => $provinces,
                    ]) ?>
                    <hr>
                    <div id="agreement-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>