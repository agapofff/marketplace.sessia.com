<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Orders;
use yii\helpers\VarDumper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'login' => [
                'class' => 'yii2mod\user\actions\LoginAction'
            ],
            'logout' => [
                'class' => 'yii2mod\user\actions\LogoutAction'
            ],
            'signup' => [
                'class' => 'yii2mod\user\actions\SignupAction'
            ],
            'request-password-reset' => [
                'class' => 'yii2mod\user\actions\RequestPasswordResetAction'
            ],
            'password-reset' => [
                'class' => 'yii2mod\user\actions\PasswordResetAction'
            ],            
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }
        
        $reports = [];
        
        $stores = Orders::find()
            ->select('store_id')
            ->groupBy('store_id')
            ->column();
            
        $marketplaces = array_keys(Yii::$app->params['marketplace']);
            
        // orders by stores
        $query = ['DATE_FORMAT(order_date, "%Y.%m") as date'];
        foreach ($stores as $store_id) {
            $store = Yii::$app->params['stores'][$store_id];
            $query[] = "SUM(CASE WHEN store_id = $store_id THEN sum ELSE 0 END) AS '$store'";
        }
        $orders = Orders::find()
            ->select($query)
            ->groupBy('date')
            ->asArray();
        
        $reports[] = [
            'name' => Yii::t('app', 'Все маркетплейсы'),
            'data' => $orders->all()
        ];
        
        foreach ($marketplaces as $mpKey => $mpName) {
            $reports[] = [
                'name' => ucfirst($mpName),
                'data' => $orders
                    ->where([
                        'marketplace_id' => $mpKey
                    ])
                    ->all()
            ];
        }
        
        // orders by marketplaces
        $query = ['DATE_FORMAT(order_date, "%Y.%m") as date'];
        foreach ($marketplaces as $mpKey => $mpName) {
            $mpName = ucfirst($mpName);
            $query[] = "SUM(CASE WHEN marketplace_id = $mpKey THEN sum ELSE 0 END) AS '$mpName'";
        }
        $orders = Orders::find()
            ->select($query)
            ->groupBy('date')
            ->asArray();
        
        $reports[] = [
            'name' => Yii::t('app', 'Все магазины'),
            'data' => $orders->all()
        ];
        
        foreach ($stores as $store_id) {
            $reports[] = [
                'name' => Yii::$app->params['stores'][$store_id],
                'data' => $orders
                    ->where([
                        'store_id' => $store_id
                    ])
                    ->all()
            ];
        }
        
        foreach ($reports as $r => $report) {
            $rows = [array_keys($report['data'][0])];
            foreach ($report['data'] as $d => $data) {
                // $trans[] = array_values($data);
                $row = [];
                foreach ($data as $v => $val) {
                    $row[] = $v == 'date' ? $val : (int)$val;
                }
                $rows[] = $row;
            }
            $reports[$r]['data'] = $rows;
        }
        
        foreach ($reports as $r => $report) {
            $reports[$r] = [
                'name' => $report['name'],
                'data' => json_encode($report['data'])
            ];
        }
            
// echo VarDumper::dump($reports, 99, true); exit;

        return $this->render('index', [
            'reports' => $reports,
        ]);
    }
    

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
