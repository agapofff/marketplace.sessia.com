<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property int $marketplace_id
 * @property string|null $marketplace_order_id
 * @property string|null $sessia_order_id
 * @property string|null $request
 * @property string|null $response
 * @property string|null $store_id
 * @property int|null $sum
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $order_date
 * @property int $status
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['marketplace_id', 'status'], 'required'],
            [['marketplace_id', 'sum', 'store_id'], 'integer'],
            [['request', 'response', 'status'], 'string'],
            [['created_at', 'updated_at', 'order_date'], 'safe'],
            [['marketplace_order_id', 'sessia_order_id'], 'string', 'max' => 100],
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
            'marketplace_order_id' => Yii::t('app', 'Заказ в маркетплейсе'),
            'sessia_order_id' => Yii::t('app', 'Заказ в CRM'),
            'request' => Yii::t('app', 'Запрос'),
            'response' => Yii::t('app', 'Ответ'),
            'sum' => Yii::t('app', 'Сумма'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата изменения'),
            'order_date' => Yii::t('app', 'Дата заказа'),
            'store_id' => Yii::t('app', 'Магазин'),
            'status' => Yii::t('app', 'Статус заказа'),
        ];
    }
}
