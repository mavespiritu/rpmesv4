<div class="row">
        <div class="col-md-12 col-xs-12">
            <!-- <h4>Target Setting</h4>
            <hr> -->
            <!-- <div class="form-group">
                <label class="control-label col-sm-3" for="persons_employed_target">Target Employment Generated</label>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <?= $form->field($targets[2], "[2]annual")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'autocomplete' => 'off',
                                    'value' => $targets[2]['annual'] != '' ? $targets[2]['annual'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label('Male') ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <?= $form->field($targets[3], "[3]annual")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'autocomplete' => 'off',
                                    'value' => $targets[3]['annual'] != '' ? $targets[3]['annual'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label('Female') ?>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- <div class="row">
                <div class="col-md-6 col-xs-12">
                    <?= $form->field($model, 'data_type')->widget(Select2::classname(), [
                        'data' => ['Default' => 'Default', 'Maintained' => 'Maintained', 'Cumulative' => 'Cumulative'],
                        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'data-type-select'],
                        'pluginOptions' => [
                            'allowClear' =>  true,
                        ],
                        ])->label('Data Type *');
                    ?>
                </div>
            </div> -->
            <!-- <div class="form-group">
                <label class="control-label col-sm-3" for="monthly_target">Monthly Targets</label>
                <div class="col-sm-9">
                    <table class="table table-bordered table-condensed table-responsive">
                        <thead>
                            <tr>
                                <td>&nbsp;</td>
                                <td align=center style="width: 40%;">Physical</td>
                                <td align=center style="width: 40%;">Financial</td>
                            </tr>
                            <tr>
                                <td>Baseline Accomplishment</td>
                                <td align=center><?= $form->field($targets[0], "[0]baseline")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'id' => 'baseline-physical-input',
                                        'autocomplete' => 'off',
                                        'value' => $targets[0]['baseline'] != '' ? $targets[0]['baseline'] : 0,
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <td align=center><?= $form->field($targets[1], "[1]baseline")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'id' => 'baseline-financial-input',
                                        'autocomplete' => 'off',
                                        'value' => $targets[1]['baseline'] != '' ? $targets[1]['baseline'] : 0,
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td align=center style="width: 20%;">&nbsp;</td>
                            <td align=center colspan=2 style="width: 80%;">Monthly Targets</td>
                        </tr>
                        <?php foreach($months as $month => $monthName){ ?>
                            <tr>
                                <td align=center><?= $monthName ?></td>
                                <td align=center><?= $form->field($targets[0], "[0]{$month}")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'id' => $month.'-physical-input',
                                        'autocomplete' => 'off',
                                        'value' => $targets[0][$month] != '' ? $targets[0][$month] : 0,
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <td align=center><?= $form->field($targets[1], "[1]{$month}")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'id' => $month.'-financial-input',
                                        'autocomplete' => 'off',
                                        'value' => $targets[1][$month] != '' ? $targets[1][$month] : 0,
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div> -->

            <?= $form->field($model, 'id')->hiddenInput(['value' => $model->id])->label(false) ?>
            <?php // $form->field($targets[0], "[0]indicator")->textInput(['type' => 'text'])->label(false) ?>
        </div>
    </div>