<h4>Budget Verification</h4>
<?php if($model->prBudgetVerification){ ?>
    <?= DetailView::widget([
        'model' => $model->prBudgetVerification,
        'attributes' => [
            'allotment',
            'fund_cluster',
            'rc_code',
            'source_of_fund',
            'charge_to',
            'remarks',
        ],
    ]) ?>
<?php }else{ ?>
    <p>Needs budget verification</p>
<?php } ?>

<h4>Procurement Verification</h4>
<?php if($model->prProcVerification){ ?>
    <?= DetailView::widget([
        'model' => $model->prProcVerification,
        'attributes' => [
            'pr_no',
            [
                'format' => 'raw',
                'attribute' => 'modeTitle',
                'value' => function($model){ return $model->mode->title; }
            ],
             [
                'format' => 'raw',
                'attribute' => 'procurementTypeTitle',
                'value' => function($model){ return $model->procurementType->title; }
            ],
            'remarks',
        ],
    ]) ?>
<?php }else{ ?>
    <p>Needs procurement verification</p>
<?php } ?>