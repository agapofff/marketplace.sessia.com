<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property int $id
 * @property int $marketplace_id
 * @property string|null $sessia_product_id
 * @property string|null $marketplace_product_id
 */
class Products extends \yii\db\ActiveRecord
{    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['marketplace_id'], 'required'],
            [['marketplace_id'], 'integer'],
            [['sessia_product_id', 'marketplace_product_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'marketplace_id' => Yii::t('app', 'Маркетплейс'),
            'sessia_product_id' => Yii::t('app', 'ID в CRM'),
            'marketplace_product_id' => Yii::t('app', 'ID в маркетплейсе'),
        ];
    }
}
