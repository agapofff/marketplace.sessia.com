<?php
namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use linslin\yii2\curl;
use yii\base\Exception;

class Ozon
{
    public static function getOrders($dateFrom, $orders = [], $offset = 0, $limit = 100)
    {
        $params = Yii::$app->params['marketplace']['ozon'];
        
        $curl = new curl\Curl();

        $response = $curl
            ->setHeaders([
                'Client-Id' => $params['clientID'],
                'Api-Key' => $params['apiKey'],
            ])
            ->setRequestBody(Json::encode([
                'dir' => 'ASC',
                'filter' => [
                    'since' => date('Y-m-d', strtotime($dateFrom)) . 'T' . date('H:i:s', strtotime($dateFrom)) . '.000Z',
                    'to' => date('Y-m-d') . 'T' . date('H:i:s') . '.000Z',
                ],
                'limit' => $limit,
                'offset' => $offset,
                'translit' => true,
            ]))
            ->post($params['url'] . '/v3/posting/fbs/list');

        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        $response = Json::decode($response);
        
        if (isset($response['message'])) {
            return $response;
        }
        
        if (isset($response['result'])) {
            $result = $response['result'];
            if ($result['postings']) {
                foreach ($result['postings'] as $posting) {
                    $orders[] = $posting;
                }
            }
            if ($result['has_next']) {
                $offset = $offset + $limit;
                $orders = self::getOrders($dateFrom, $orders, $offset, $limit);
            }
        }

        return $orders;
    }
    
    public static function getOrdersErrors($orders)
    {
        return isset($orders['message']);
    }
    
    public static function getOrderID($order)
    {
        return $order['order_id'];
    }
    
    public static function getOrderSum($order)
    {
        $sum = 0;
        foreach ($order['products'] as $product) {
            $sum += (float)$product['price'] * $product['quantity'];
        }
        return $sum;
    }
    
    public static function getOrderProducts($order)
    {
        $data = [];
        foreach ($order['products'] as $product) {
            $data[] = [
                'id' => $product['sku'],
                'count' => $product['quantity'],
                'price' => (float)$product['price'],
            ];
        }
        return $data;
    }
    
    public static function getProductPrice($order, $productID)
    {
        foreach ($order['products'] as $product) {
            if ($product['sku'] == $productID) {
                return (float)$product['price'];
            }
        }
        return false;
    }
    
    public static function isOrderCancelled($order)
    {
        return $order['status'] == 'cancelled';
    }
}
