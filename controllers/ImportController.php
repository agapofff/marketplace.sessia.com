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
    public function actionIndex($from = null)
    {
        $out = [];
        $loaded = 0;
        $dateFrom = $from ? date('Y-m-d 00:00:00', strtotime($from)) : date('Y-m-d', strtotime('-1 month'));

        $marketplace = new Wildberries();
        $marketplaceOrders = $marketplace::getOrders($dateFrom);
// echo VarDumper::dump($marketplaceOrders, 99, true); exit;
        if ($marketplaceOrders) {
            if (property_exists($marketplaceOrders, 'errors')) {
                $out[] = Yii::t('front', 'Ошибка получения списка заказов из {0}', [
                    'Wildberries',
                ]) . ': ' . print_r($marketplaceOrders->errors);
            } else {
                foreach ($marketplaceOrders as $k => $marketplaceOrder) {
if ($loaded > 0) continue;
                    $sessiaOrder = Orders::findOne([
                        'marketplace_id' => 1,
                        'marketplace_order_id' => $marketplaceOrder->odid
                    ]);
                    
                    if ($sessiaOrder) {
                        if ($marketplaceOrder->isCancel && $sessiaOrder->status !== 'cancel') {
                            $sessiaOrder->status = 'cancel';
                            $sessiaOrder->updated_at = date('Y-m-d H:i:s');
                            $sessiaOrder->save();
                            // todo: метод смены статуса заказа в CRM
                        }
                    } else {
                        $marketplaceProduct = Products::findOne([
                            'marketplace_id' => 1,
                            'marketplace_product_id' => $marketplaceOrder->nmId . $marketplaceOrder->techSize,
                        ]);

                        if ($marketplaceProduct) {
                            $sessiaProduct = Sessia::getProduct($marketplaceProduct->sessia_product_id);
echo $sessiaProduct['store']['id'];
echo '<hr>';
echo VarDumper::dump($sessiaProduct, 99, true); exit;
                            if ($sessiaProduct && isset($sessiaProduct['id'])) {
                                $priceWithDiscount = round($marketplaceOrder->totalPrice * (1 - $marketplaceOrder->discountPercent/100));
                                $ext_discount = (float)$sessiaProduct['price'] - $priceWithDiscount;
                                
                                $orderParams = [
                                    'delivery_method' => 72216,
                                    'delivery_address' => [
                                        'country' => 1,
                                        'city' => 3,
                                        'city_name' => 'Москва',
                                        'post_code' => '101000',
                                        'address1' => 'Москва',
                                        'full_name' => Yii::$app->params['marketplace']['wildberries']['user']['name'],
                                    ],
                                    'products' => [
                                        [
                                            'goods' => $marketplaceProduct->sessia_product_id,
                                            'quantity' => 1,
                                        ]
                                    ],
                                    'payment_service' => 5,
                                    'name' => Yii::$app->params['marketplace']['wildberries']['user']['name'],
                                    'email' => Yii::$app->params['marketplace']['wildberries']['user']['email'],
                                    'country_code' => '+7',
                                    'phone' => Yii::$app->params['marketplace']['wildberries']['user']['phone'],
                                    'lang_id' => 1,
                                    'response_lang_id' => 1,
                                    // 'ext_discount' => $ext_discount,
                                    'comment' => $marketplaceOrder->odid,
                                ];
                                
                                $newOrder = Sessia::createOrder($sessiaProduct['store']['id'], $orderParams);
                                
                                if ($newOrder) {
// echo VarDumper::dump($newOrder, 99, true); exit; 
                                    if (isset($newOrder['id'])) {
                                        $order = new MarketplaceOrders();
                                        $order->marketplace_id = 1;
                                        $order->marketplace_order_id = (string)$marketplaceOrder->odid;
                                        $order->sessia_order_id = (string)$newOrder['id'];
                                        $order->request = Json::encode($orderParams);
                                        $order->response = Json::encode($newOrder);
                                        $order->sum = $priceWithDiscount;
                                        $order->created_at = date('Y-m-d H:i:s');
                                        $order->updated_at = date('Y-m-d H:i:s');
                                        $order->status = 'new';
                                        
                                        if ($order->save()) {
                                            $out[] = Yii::t('front', 'Заказ {0} из {1} успешно загружен: {2}', [
                                                $marketplaceOrder->odid,
                                                'Wildberries',
                                                Html::a($newOrder['id'], 'https://crm.sessia.com/shop/orders/edit/' . $newOrder['id'])
                                            ]);
                                            $loaded++;
                                        } else {
                                            $out[] = Yii::t('front', 'Ошибка загрузки заказа {0} из {1}', [
                                                $marketplaceOrder->odid,
                                                'Wildberries',
                                            ]) . ': ' . print_r($order->getErrors());
                                        }
                                    } else {
                                        $out[] = Yii::t('front', 'Ошибка загрузки заказа {0} из {1}', [
                                            $marketplaceOrder->odid,
                                            'Wildberries',
                                        ]) . ': ' . print_r($newOrder);
                                    }
                                } else {
                                    $out[] = Yii::t('front', 'Ошибка создания заказа {0} из {1} в Sessia', [
                                        $marketplaceOrder->odid,
                                        'Wildberries'
                                    ]);
                                }
                            } else {
                                $out[] = Yii::t('front', 'Ошибка сопоставления товара {0} с CRM при загрузке заказа {1} из {2}', [
                                    $marketplaceOrder->nmId,
                                    $marketplaceOrder->odid,
                                    'Wildberries',
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            $out[] = Yii::t('front', 'Ошибка загрузки списка заказов из {0}', [
                'Wildberries'
            ]);
        }
        
        // if ($out) {
            echo VarDumper::dump($out, 99, true);
            
            // $mail = Yii::$app->mailer
                // ->compose('default', [
                    // 'content' => $out
                // ])
                // ->setFrom([
                    // Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']
                // ])
                // ->setTo('agapofff@gmail.com')
                // ->setReplyTo(Yii::$app->params['senderEmail'])
                // ->setSubject(Yii::t('front', 'Импорт заказов из маркетплейсов'));
            // $mail->send();
        // }
        
// echo VarDumper::dump($marketplaceOrders, 99, true); exit;
        
        
        // if ($supplies = $marketplace->getSupplies()) {
            // foreach ($supplies as $s => $supply) {
                // if ($supplyOrders = $marketplace->getSupplyOrders($supply)) {
                    // foreach ($supplyOrders as $supplyOrder) {
                        // $orders[] = $supplyOrder;
                    // }
                // }
            // }
// echo VarDumper::dump($orders, 99, true); exit;
            // if ($orders) {
                // foreach ($orders as $order) {
                    // $carts[] = $order->orderUid;
                // }
                // $carts = array_unique($carts);
            // }
        // }
        // echo VarDumper::dump($carts, 99, true);
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