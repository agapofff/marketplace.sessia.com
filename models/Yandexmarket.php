<?php
namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use linslin\yii2\curl;
use yii\base\Exception;

class Yandexmarket
{
    public function getSupplies($data = [], $next = 0, $limit = 1000)
    {
        $curl = new curl\Curl();
        
        $response = $curl
            ->setHeaders([
                'Authorization' => Yii::$app->params['marketplace']['wildberries']['token'],
            ])
            ->setGetParams([
                'limit' => $limit,
                'next' => $next,
            ])
            ->get(Yii::$app->params['marketplace']['wildberries']['url'] . '/api/v3/supplies');

        if ($curl->errorCode !== null) {
            throw new HttpException($curl->errorCode, VarDumper::dump($curl->responseHeaders, 99, true));
        }

        $response = json_decode($response);
        
        if ($response->supplies) {
            foreach ($response->supplies as $supply) {
                $data[] = $supply;
            }
        }
        
        if ($response->next) {
            $data = $this->getSupplies($data, $response->next, $limit);
        }
        
        return $data;
    }
    
    public function getSupplyOrders($id)
    {
        $curl = new curl\Curl();
        
        $response = $curl
            ->setHeaders([
                'Authorization' => Yii::$app->params['marketplace']['wildberries']['token'],
            ])
            ->get(Yii::$app->params['marketplace']['wildberries']['url'] . '/api/v3/supplies/' . $id . '/orders');

        if ($curl->errorCode !== null) {
            throw new HttpException($curl->errorCode, VarDumper::dump($curl->responseHeaders, 99, true));
        }

        return json_decode($response);
    }
}