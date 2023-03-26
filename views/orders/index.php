<?php

use yii\helpers\Html;
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
                    'heading' => '<h5 class="panel-title mb-0">' . Yii::t('app', 'Товары') . '</h5>',
                    'type' => 'default',
                    'before' => Html::a(Html::tag('i', '&nbsp;', ['class' => 'fas fa-plus']) . Yii::t('app', 'Добавить'), ['create'], [
                        'class' => 'btn btn-success',
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
                        'attribute' => 'created_at',
                        'format' => 'raw', 
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->created_at);
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
