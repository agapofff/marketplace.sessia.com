<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use linslin\yii2\curl;
use yii\base\Exception;

class Sessia
{
    public static function getLangs()
    {
        $curl = new curl\Curl();
        $response = $curl->get(Yii::$app->params['sessia']['url'] . '/language');
        
        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        return Json::decode($response, false);
    }
    
    public static function getCountries($store_id)
    {
        $curl = new curl\Curl();
        $response = $curl->get(Yii::$app->params['sessia']['url'] . '/market/delivery-countries/' . $store_id);
        
        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        return Json::decode($response, false);
    }
    
    public static function getCities($country_id, $lang = null, $q = null, $limit = 10, $offset = 0)
    {
        $curl = new curl\Curl();
        $response = $curl
            ->setGetParams([
                '_format' => 'json',
                'limit' => $limit,
                'offset' => $offset,
                'lang' => $lang ?: Yii::$app->language,
                'q' => $q
            ])
            ->get(Yii::$app->params['sessia']['url'] . '/directory/cities/' . $country_id);
            
        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        return Json::decode($response, false);
    }
    
    public static function getDeliveryCost($products, $country_id, $city_id)
    {
        $curl = new curl\Curl();
        $response = $curl
            ->setPostParams([
                'country' => $country_id,
                'city' => $city_id,
                'products' => $products,
            ])
            ->post(Yii::$app->params['sessia']['url'] . '/market/delivery-cost');
            
        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        return Json::decode($response, false);
    }
    
    public static function getProductData($store_id, $product_id)
    {
        $curl = new curl\Curl();
        $response = $curl->get(Yii::$app->params['sessia']['url'] . '/market/' . $store_id . '/showcase-tree');

        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        $response = Json::decode($response, false);

        foreach ($response as $val) {
            if (isset($val->goods_list)) {
                foreach ($val->goods_list as $good) {
                    if ($good->id == (int)$product_id) {
                        return $good;
                    }
                }
            }
        }
        
        return false;
    }
    
    public static function getProduct($product_id)
    {
        $curl = new curl\Curl();
        $response = $curl->get(Yii::$app->params['sessia']['url'] . '/market/goods/' . $product_id);
        
        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }
        
        return Json::decode($response)[0];
    }
    
    public static function createOrder($store_id, $params)
    {
        $curl = new curl\Curl();
        $response = $curl
            ->setPostParams($params)
            ->post(Yii::$app->params['sessia']['url'] . '/market/' . $store_id . '/ordersAnonymous');
            
        if ($curl->errorCode !== null) {
            throw new HttpException($curl->errorCode, VarDumper::dump($curl->responseHeaders, 99, true));
        }
        
        return Json::decode($response);
    }
}
