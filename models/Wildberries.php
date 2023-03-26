<?php
namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use linslin\yii2\curl;
use yii\base\Exception;

class Wildberries
{
    public static function getSupplies($data = [], $next = 0, $limit = 1000)
    {
        $params = Yii::$app->params['marketplace']['wildberries']['suppliers'];
        
        $curl = new curl\Curl();
        
        $response = $curl
            ->setHeaders([
                'Authorization' => $params['token'],
            ])
            ->setGetParams([
                'limit' => $limit,
                'next' => $next,
            ])
            ->get($params['url'] . '/api/v3/supplies');

        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }

        $response = Json::decode($response, false);

        if ($response->supplies) {
            foreach ($response->supplies as $supply) {
                $data[] = $supply;
            }
        }
        
        if ($response->next) {
            $data = self::getSupplies($data, $response->next, $limit);
        }
        
        return $data;
    }
    
    public static function getSupplyOrders($supply)
    {
        $params = Yii::$app->params['marketplace']['wildberries']['suppliers'];
        
        $curl = new curl\Curl();

        $response = $curl
            ->setHeaders([
                'Authorization' => $params['token'],
            ])
            ->get($params['url'] . '/api/v3/supplies/' . $supply->id . '/orders');

        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }

        return Json::decode($response)['orders'];
    }
    
    public static function getOrders($dateFrom)
    {
        $params = Yii::$app->params['marketplace']['wildberries']['statistics'];
        
        $curl = new curl\Curl();

        $response = $curl
            ->setHeaders([
                'Authorization' => $params['token'],
            ])
            ->setGetParams([
                'dateFrom' => $dateFrom,
            ])
            ->get($params['url'] . '/api/v1/supplier/orders');

        if ($curl->errorCode !== null) {
            throw new Exception('Error ' . $curl->errorCode . ': ' . print_r($curl->responseHeaders));
        }

        return Json::decode($response, false);
    }
}
