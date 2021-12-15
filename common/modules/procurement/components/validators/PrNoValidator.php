<?php 

namespace common\modules\procurement\components\validators;

use yii\validators\Validator;
use common\modules\procurement\models\PrProcVerification;

class PrNoValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = 'PR No. has been used already';
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (PrProcVerification::find()->where(['pr_no' => $value])->exists()) {
            $model->addError($attribute, $this->message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $prNos = json_encode(PrProcVerification::find()->select('pr_no')->asArray()->column());
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
if ($.inArray(value, $prNos) === -1) {
    messages.push($message);
}
JS;
    }
}
