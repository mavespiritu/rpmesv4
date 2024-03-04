<?php

namespace common\modules\rpmes\controllers;

class GuidelineController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUpdates()
    {
        return $this->render('updates');
    }

}
