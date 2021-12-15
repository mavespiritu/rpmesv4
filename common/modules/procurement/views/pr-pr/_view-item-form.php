<?php if($status){ ?>
    <?php if(($status->status == 'PENDING' && $status->action_type == 'FOR ADDITION OF ITEM') && Yii::$app->user->can('EndUser') || $itemModel->approvalStatus == 'FOR REVISION'){ ?>
        <h4>Add/Edit Item Form</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $this->render('_form-item', [
                    'model' => $model,
                    'itemModel' => $itemModel,
                    'stockName' => $stockName
                ]); ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>