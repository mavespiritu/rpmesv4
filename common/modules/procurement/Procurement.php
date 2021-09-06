<?php

namespace common\modules\procurement;

/**
 * procurement module definition class
 */
class Procurement extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'common\modules\procurement\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        /*$this->layoutPath = \Yii::getAlias('@common/modules/procurement/views/layouts/');
        $this->layout = 'main';*/
    }
}
