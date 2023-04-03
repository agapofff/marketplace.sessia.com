<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MarketplaceOrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('back', 'Заказы');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="marketplace-orders-index">

    <?php Pjax::begin(); ?>
    
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax' => true,
                'toolbar'=>[
                    '{export}',
                    '&nbsp;',
                    '{toggleData}',
                    '&nbsp;',
                    Html::a(Html::tag('i', '', ['class' => 'fas fa-redo']), ['/orders'], ['class' => 'btn btn-outline-secondary']),
                ],
                'panel' => [
                    'heading' => '<h5 class="panel-title mb-0">' . Yii::t('app', 'Заказы') . '</h5>',
                    'type' => 'default',
                    'before' => Html::a(Html::tag('i', '&nbsp;', ['class' => 'fas fa-chart-line']) . Yii::t('app', 'Графики'), ['/'], [
                        'class' => 'btn btn-info',
                        'data-pjax' => 0,
                    ]),
                    'after' => false,
                ],
                'columns' => [  
                    // ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'id',
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],

                    [
                        'attribute' => 'marketplace_id',
                        'format' => 'html',
                        'filter' => Html::activeDropDownList($searchModel, 'marketplace_id', array_keys(Yii::$app->params['marketplace']), [
                                'class' => 'form-control',
                                'prompt' => Yii::t('back', 'Все'),
                            ]
                        ),
                        'value' => function ($model) {
                            return array_keys(Yii::$app->params['marketplace'])[$model->marketplace_id];
                        },
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],
                    
                    [
                        'attribute' => 'marketplace_order_id',
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],
                    
                    [
                        'attribute' => 'sessia_order_id',
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],
                    
                    [
                        'attribute' => 'sum',
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],
                    
                    [
                        'attribute' => 'order_date',
                        'format' => 'raw', 
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->order_date);
                        },
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],
                    
                    [
                        'attribute' => 'store_id',
                        'filter' => Html::activeDropDownList(
                            $searchModel, 
                            'store_id', 
                            ArrayHelper::map(Yii::$app->params['stores'], 'id', 'name'), 
                            [
                                'class' => 'form-control',
                                'prompt' => Yii::t('back', 'Все'),
                            ]
                        ),
                        'value' => function ($model) {
                            foreach (Yii::$app->params['stores'] as $store) {
                                if ($store['id'] == $model->store_id) {
                                    return $store['name'];
                                }
                            }
                        },
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],                    
                    
                    [
                        'attribute' => 'status',
                        'filterInputOptions' => [
                            'class' => 'form-control text-center',
                            'placeholder' => 'Поиск...'
                        ],
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'contentOptions' => [
                            'class' => 'text-center'
                        ],
                    ],
                ],
            ]); 
        ?>

    <?php Pjax::end(); ?>

</div>
