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
            
        $marketplaces = array_keys(Yii::$app->params['marketplace']);
            
        // orders by stores
        for ($i = 0; $i < 2; $i++) {
            $query = ['DATE_FORMAT(order_date, "%Y.%m") as date'];
            foreach (Yii::$app->params['stores'] as $store) {
                $query[] = ($i ? 'COUNT' : 'SUM') . "(CASE WHEN store_id = " . $store['id'] . " THEN sum ELSE 0 END) AS '" . $store['name'] . "'";
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
                $query[] = ($i ? 'COUNT' : 'SUM') . "(CASE WHEN marketplace_id = $mpKey THEN sum ELSE 0 END) AS '$mpName'";
            }
            $orders = Orders::find()
                ->select($query)
                ->groupBy('date')
                ->asArray();
            
            $reports[] = [
                'name' => Yii::t('app', 'Все магазины'),
                'data' => $orders->all()
            ];
            
            foreach (Yii::$app->params['stores'] as $store) {
                $reports[] = [
                    'name' => $store['name'],
                    'data' => $orders
                        ->where([
                            'store_id' => $store['id']
                        ])
                        ->all()
                ];
            }
        }
        
        foreach ($reports as $r => $report) {
            $rows = [array_keys($report['data'][0])];
            foreach ($report['data'] as $d => $data) {
                $row = [];
                foreach ($data as $v => $val) {
                    $row[] = $v == 'date' ? $val : (int)$val;
                }
                $rows[] = $row;
            }
            $reports[$r]['data'] = json_encode($rows);
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
