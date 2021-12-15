<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_supplier_list".
 *
 * @property int $id
 * @property int|null $service_type_id
 * @property string|null $type
 * @property string|null $business_name
 * @property string|null $business_address
 * @property string|null $contact_person
 * @property string|null $landline
 * @property string|null $mobile
 * @property string|null $email_address
 * @property string|null $philgeps_no
 * @property string|null $bir_registration
 * @property string|null $tin_no
 *
 * @property PrServiceType $serviceType
 * @property PrSupplierQuote[] $prSupplierQuotes
 */
class PrSupplierList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_supplier_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_type_id'], 'integer'],
            [['type', 'business_name', 'business_address'], 'string'],
            [['contact_person', 'philgeps_no', 'bir_registration'], 'string', 'max' => 100],
            [['landline'], 'string', 'max' => 20],
            [['mobile'], 'string', 'max' => 11],
            [['email_address'], 'string', 'max' => 50],
            [['tin_no'], 'string', 'max' => 40],
            [['service_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrServiceType::className(), 'targetAttribute' => ['service_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_type_id' => 'Service Type ID',
            'type' => 'Type',
            'business_name' => 'Business Name',
            'business_address' => 'Business Address',
            'contact_person' => 'Contact Person',
            'landline' => 'Landline',
            'mobile' => 'Mobile',
            'email_address' => 'Email Address',
            'philgeps_no' => 'Philgeps No',
            'bir_registration' => 'Bir Registration',
            'tin_no' => 'Tin No',
        ];
    }

    /**
     * Gets query for [[ServiceType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceType()
    {
        return $this->hasOne(PrServiceType::className(), ['id' => 'service_type_id']);
    }

    /**
     * Gets query for [[PrSupplierQuotes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrSupplierQuotes()
    {
        return $this->hasMany(PrSupplierQuote::className(), ['supplier_id' => 'id']);
    }
}
