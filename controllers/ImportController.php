<?php
namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\base\Exception;
use app\models\Sessia;
use app\models\Orders;
use app\models\Products;
use app\models\Wildberries;

class ImportController extends \yii\web\Controller
{
    public function actionIndex($from = null, $date = null)
    {
        $out = [];
        $loaded = 0;
        $dateFrom = $date ? date('Y-m-d 00:00:00', strtotime($date)) : date('Y-m-d', strtotime('-6 months'));
        
        $marketplaceList = $from ? [strtolower($from)] : array_keys(Yii::$app->params['marketplace']);
// echo VarDumper::dump($marketplaceList, 99, true); exit;
        foreach ($marketplaceList as $marketplaceKey) {
            $params = Yii::$app->params['marketplace'][$marketplaceKey];
            $marketplaceID = array_search($marketplaceKey, array_keys(Yii::$app->params['marketplace']));

            if (!$params['active']) continue;
        
            $marketplaceName = ucfirst($marketplaceKey);
            $marketplace = new $params['class']();
            
            $marketplaceOrders = $marketplace::getOrders($dateFrom);
echo VarDumper::dump($marketplaceOrders, 99, true); exit;
            if ($marketplaceOrders) {
                if ($marketplace::getOrdersErrors($marketplaceOrders)) {
                    $out[] = Yii::t('app', 'Ошибка получения списка заказов из {0}', $marketplaceName);
                } else {
                    foreach ($marketplaceOrders as $k => $marketplaceOrder) {
                        if ($loaded == Yii::$app->params['marketplaceImportLimit']) continue;
// echo VarDumper::dump($marketplaceOrder, 99, true); exit;
                        $orderProducts = [];
                        $storeID = null;
                        $sessiaOrderSum = $marketplaceOrderSum = 0;
                        $marketplaceOrderID = $marketplace::getOrderID($marketplaceOrder);
                        $marketplaceOrderDate = $marketplace::getOrderDate($marketplaceOrder);
                        
                        $orderParams = [
                            'delivery_method' => 74175,
                            'delivery_address' => [
                                'country' => 1,
                                'city' => 3,
                                'city_name' => 'Москва',
                                'post_code' => '101000',
                                'address1' => 'Москва',
                                'full_name' => $params['user']['name'],
                            ],
                            'payment_service' => 5,
                            'name' => $params['user']['name'],
                            'email' => $params['user']['email'],
                            'country_code' => '+7',
                            'phone' => $params['user']['phone'],
                            'lang_id' => 1,
                            'response_lang_id' => 1,
                            'comment' => $marketplaceOrderID,
                        ];
                        
                        $sessiaOrder = Orders::findOne([
                            'marketplace_id' => $marketplaceID,
                            'marketplace_order_id' => $marketplaceOrderID,
                        ]);
                        
                        if ($sessiaOrder) {
                            if (!$sessiaOrder->order_date) {
                                $sessiaOrder->order_date = $marketplaceOrderDate;
                                $sessiaOrder->save();
                            }
                            
                            if ($marketplace::isOrderCancelled($marketplaceOrder) && $sessiaOrder->status !== 'cancel') {
                                $sessiaOrder->status = 'cancel';
                                $sessiaOrder->updated_at = date('Y-m-d H:i:s');
                                $sessiaOrder->save();
                                // todo: метод смены статуса заказа в CRM
                            }
                        } else {
                            $marketplaceProducts = $marketplace::getOrderProducts($marketplaceOrder);
// echo VarDumper::dump($marketplaceProducts, 99, true); exit;
                            foreach ($marketplaceProducts as $marketplaceProduct) {
                                $product = Products::findOne([
                                    'marketplace_id' => $marketplaceID,
                                    'marketplace_product_id' => $marketplaceProduct['id'],
                                ]);

                                if ($product) {
                                    $sessiaProduct = Sessia::getProduct($product->sessia_product_id);
// echo VarDumper::dump($sessiaProduct, 99, true); exit;

                                    if ($sessiaProduct && isset($sessiaProduct['id'])) {
                                        $storeID = $storeID != $sessiaProduct['store']['id'] ? $sessiaProduct['store']['id'] : $storeID;
                                        
                                        $sessiaOrderSum += $sessiaProduct['price'] * $marketplaceProduct['count'];
                                        
                                        $marketplaceOrderSum += $marketplaceProduct['price'] * $marketplaceProduct['count'];
                                        
                                        $orderProducts[] = [
                                            'goods' => $product->sessia_product_id,
                                            'quantity' => $marketplaceProduct['count'],
                                        ];
                                    } else {
                                        $out[] = Yii::t('app', 'Ошибка сопоставления товара {0} с CRM при загрузке заказа {1} от {2} из {3}', [
                                            $marketplaceProduct['id'],
                                            $marketplaceOrderID,
                                            $marketplaceOrderDate,
                                            $marketplaceName,
                                        ]);
                                    }
                                }
                            }
                                
                            if (!empty($orderProducts)) {
                                $orderParams['products'] = $orderProducts;
                                $discount = $sessiaOrderSum - $marketplaceOrderSum;
                                $orderParams['ext_discount'] = $discount > 0 ? $discount : 0;
                                // $orderParams['ext_discount'] = $sessiaOrderSum - $marketplaceOrderSum;
                                
                                $newOrder = Sessia::createOrder($storeID, $orderParams);
                                
                                if ($newOrder) {
// echo VarDumper::dump($newOrder, 99, true); exit; 
                                    if (isset($newOrder['id'])) {
                                        $order = new Orders();
                                        $order->marketplace_id = $marketplaceID;
                                        $order->marketplace_order_id = (string)$marketplaceOrderID;
                                        $order->sessia_order_id = (string)$newOrder['id'];
                                        $order->request = Json::encode($orderParams);
                                        $order->response = Json::encode($newOrder);
                                        $order->sum = $sessiaOrderSum;
                                        $order->created_at = date('Y-m-d H:i:s');
                                        $order->updated_at = date('Y-m-d H:i:s');
                                        $order->status = 'new';
                                        $order->order_date = $marketplaceOrderDate;
                                        
                                        if ($order->save()) {
                                            $out[] = Yii::t('app', 'Заказ {0} от {1} из {2} успешно загружен: {3}', [
                                                $marketplaceOrderID,
                                                $marketplaceOrderDate,
                                                $marketplaceName,
                                                Html::a($newOrder['id'], 'https://crm.sessia.com/shop/orders/edit/' . $newOrder['id'])
                                            ]);
                                            $loaded++;
                                        } else {
                                            $out[] = Yii::t('app', 'Ошибка сохранения заказа {0} от {1} из {2}', [
                                                $marketplaceOrderID,
                                                $marketplaceOrderDate,
                                                $marketplaceName,
                                            ]) . ': ' . print_r($order->getErrors(), true);
                                        }
                                    } else {
                                        $out[] = Yii::t('app', 'Ошибка загрузки заказа {0} от {1} из {2}', [
                                            $marketplaceOrderID,
                                            $marketplaceOrderDate,
                                            $marketplaceName,
                                        ]) . ': ' . print_r($newOrder, true); // . ' \ ' . print_r($orderParams, true) . ' \ ' . print_r($marketplaceOrder, true);
                                    }
                                } else {
                                    $out[] = Yii::t('app', 'Ошибка создания заказа {0} от {1} из {2} в Sessia', [
                                        $marketplaceOrderID,
                                        $marketplaceOrderDate,
                                        $marketplaceName,
                                    ]);
                                }
                            }
                        }
                    }
                }
            } else {
                $out[] = Yii::t('app', 'Ошибка загрузки списка заказов из {0}', $marketplaceName);
            }
            
            if ($out) {
                // echo VarDumper::dump($out, 99, true);
                $mail = Yii::$app->mailer
                    ->compose('default', [
                        'content' => '<p>'. join('</p><p>', $out) . '</p>'
                    ])
                    ->setFrom([
                        Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']
                    ])
                    ->setTo($params['mailto'])
                    ->setReplyTo(Yii::$app->params['senderEmail'])
                    ->setSubject(Yii::t('app', 'Импорт заказов из маркетплейсов'));
                $mail->send();
            }
        }
        
        return VarDumper::dump($out, 99, true);
    }
}
