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
        $dateFrom = $date ? date('Y-m-d 00:00:00', strtotime($date)) : date('Y-m-d', strtotime('-1 month'));
        
        $marketplaceList = $from ? [strtolower($from)] : array_keys(Yii::$app->params['marketplace']);

        foreach ($marketplaceList as $marketplaceKey) {
            $params = Yii::$app->params['marketplace'][$marketplaceKey];
            $marketplaceID = array_search($marketplaceKey, array_keys(Yii::$app->params['marketplace']));

            if (!$params['active']) continue;
        
            $marketplaceName = ucfirst($marketplaceKey);
            $marketplace = new $params['class']();
            
            $marketplaceOrders = $marketplace::getOrders($dateFrom);
// echo VarDumper::dump($marketplaceOrders, 99, true); exit;
            if ($marketplaceOrders) {
                
                if ($marketplace::getOrdersErrors($marketplaceOrders)) {
                    $out[] = Yii::t('app', 'Ошибка получения списка заказов из {0}', $marketplaceName);
                } else {
                    foreach ($marketplaceOrders as $k => $marketplaceOrder) {
                        if ($loaded == Yii::$app->params['marketplaceImportLimit']) continue;
                        
                        $orderProducts = [];
                        $storeID = null;
                        $sessiaOrderSum = $marketplaceOrderSum = 0;
                        $marketplaceOrderID = $marketplace::getOrderID($marketplaceOrder);
                        
                        $orderParams = [
                            'delivery_method' => 72216,
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
                            if ($marketplace::isOrderCancelled($marketplaceOrder) && $sessiaOrder->status !== 'cancel') {
                                $sessiaOrder->status = 'cancel';
                                $sessiaOrder->updated_at = date('Y-m-d H:i:s');
                                $sessiaOrder->save();
                                // todo: метод смены статуса заказа в CRM
                            }
                        } else {
                            $marketplaceProducts = $marketplace::getOrderProducts($order);
                            
                            foreach ($marketplaceProducts as $marketplaceProduct) {
                                $product = Products::findOne([
                                    'marketplace_id' => $marketplaceID,
                                    'marketplace_product_id' => $marketplaceProduct['id'],
                                ]);

                                if ($product) {
                                    $sessiaProduct = Sessia::getProduct($product->sessia_product_id);
echo VarDumper::dump($sessiaProduct, 99, true); exit;

                                    if ($sessiaProduct && isset($sessiaProduct['id'])) {
                                        $productCount = (int)$marketplaceProduct['count'];
                                        
                                        $storeID = $storeID != $sessiaProduct['store']['id'] ? $sessiaProduct['store']['id'] : $storeID;
                                        
                                        $sessiaOrderSum += (float)$sessiaProduct['price'] * $productCount;
                                        
                                        $marketplaceOrderSum += (float)$marketplaceProduct['price'] * $productCount;
                                        
                                        $orderProducts[] =                                     [
                                            'goods' => $marketplaceProduct->sessia_product_id,
                                            'quantity' => $productCount,
                                        ];
                                    } else {
                                        $out[] = Yii::t('app', 'Ошибка сопоставления товара {0} с CRM при загрузке заказа {1} из {2}', [
                                            $marketplaceProduct['id'],
                                            $marketplaceOrderID,
                                            $marketplaceName,
                                        ]);
                                    }
                                }
                            }
                            
                            $orderParams['products'] = $orderProducts;
                            $discount = $sessiaOrderSum - $marketplaceOrderSum;
                            $orderParams['ext_discount'] = $discount > 0 ? $discount : 0;
                            
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
                                    $order->sum = $orderSum;
                                    $order->created_at = date('Y-m-d H:i:s');
                                    $order->updated_at = date('Y-m-d H:i:s');
                                    $order->status = 'new';
                                    
                                    if ($order->save()) {
                                        $out[] = Yii::t('app', 'Заказ {0} из {1} успешно загружен: {2}', [
                                            $marketplaceOrderID,
                                            $marketplaceName,
                                            Html::a($newOrder['id'], 'https://crm.sessia.com/shop/orders/edit/' . $newOrder['id'])
                                        ]);
                                        $loaded++;
                                    } else {
                                        $out[] = Yii::t('app', 'Ошибка загрузки заказа {0} из {1}', [
                                            $marketplaceOrderID,
                                            $marketplaceName,
                                        ]) . ': ' . print_r($order->getErrors(), true);
                                    }
                                } else {
                                    $out[] = Yii::t('app', 'Ошибка загрузки заказа {0} из {1}', [
                                        $marketplaceOrderID,
                                        $marketplaceName,
                                    ]) . ': ' . print_r($newOrder, true);
                                }
                            } else {
                                $out[] = Yii::t('app', 'Ошибка создания заказа {0} из {1} в Sessia', [
                                    $marketplaceOrderID,
                                    $marketplaceName,
                                ]);
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
    
    public function actionGetSupplies($next = 0, $data = [])
    {
        $response = Yii::$app->runAction('curl', [
            'url' => Yii::$app->params['marketplace']['wildberries']['url'] . '/api/v3/supplies',
            'headers' => json_encode([
                'Authorization' => Yii::$app->params['marketplace']['wildberries']['token'],
            ]),
            'params' => json_encode([
                'limit' => 1000,
                'next' => $next,
            ]),
        ]);
        
echo $response; exit;
        
        if ($response) {
            $orders = json_decode($response);
            if ($orders->supplies) {
                foreach ($orders->supplies as $order) {
                    $data[] = $order;
                }
            }
            
            if ($orders->next) {
                $data = $this->actionGetSupplies($orders->next, $data);
            }
        } else {
            throw new Exception(print_r($response));
        }
        
        // return $data;
        echo VarDumper::dump($data, 99, true);
        exit;
    }
    
    public function actionGetSupply($id)
    {
        $response = Yii::$app->runAction('curl', [
            'url' => Yii::$app->params['wildberries']['url'] . '/api/v3/supplies/' . $id,
            'headers' => json_encode([
                'Authorization' => Yii::$app->params['wildberries']['token'],
            ]),
        ]);
        
        if ($response) {
            return $response;
        } else {
            throw new Exception(print_r($response));
        }
    }
    
    public function actionGetSupplyOrders($id)
    {
        $response = Yii::$app->runAction('curl', [
            'url' => Yii::$app->params['wildberries']['url'] . '/api/v3/supplies/' . $id . '/orders',
            'headers' => json_encode([
                'Authorization' => Yii::$app->params['wildberries']['token'],
            ]),
        ]);
        
        if ($response) {
            return $response;
        } else {
            throw new Exception(print_r($response));
        }
    }
    
    public function actionGetNewOrders()
    {
        $orders = Yii::$app->runAction('curl', [
            'url' => Yii::$app->params['wildberries']['url'] . '/api/v3/orders/new',
            'headers' => json_encode([
                'Authorization' => Yii::$app->params['wildberries']['token'],
            ]),
        ]);
        
        // return $data;
        echo VarDumper::dump(json_decode($orders), 99, true);
        exit;
    }
    
    public function actionGetSales($dateFrom = null)
    {
        if (!$dateFrom) $dateFrom = date('Y-m-d');
        
        $response = Yii::$app->runAction('curl', [
            'url' => Yii::$app->params['wildberries']['url'] . '/api/v1/supplier/sales',
            'headers' => json_encode([
                'Authorization' => Yii::$app->params['wildberries']['token'],
            ]),
            'params' => json_encode([
                'dateFrom' => $dateFrom,
            ]),
        ]);
        
        // return $data;
        echo VarDumper::dump(json_decode($orders), 99, true);
        exit;
    }
}