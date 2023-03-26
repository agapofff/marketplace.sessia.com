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
 * @property int|null $sum
 * @property string|null $created_at
 * @property string|null $updated_at
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
            [['marketplace_id', 'sum'], 'integer'],
            [['request', 'response', 'status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['marketplace_order_id', 'sessia_order_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back', 'ID'),
            'marketplace_id' => Yii::t('back', 'Маркетплейс'),
            'marketplace_order_id' => Yii::t('back', 'Заказ в маркетплейсе'),
            'sessia_order_id' => Yii::t('back', 'Заказ в CRM'),
            'request' => Yii::t('back', 'Запрос'),
            'response' => Yii::t('back', 'Ответ'),
            'sum' => Yii::t('back', 'Сумма'),
            'created_at' => Yii::t('back', 'Дата создания'),
            'updated_at' => Yii::t('back', 'Дата изменения'),
            'status' => Yii::t('back', 'Статус заказа'),
        ];
    }
}
