<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('back', 'Товары');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pages-index">

    <?php Pjax::begin([
            'scrollTo' => 0
        ]); 
    ?>
    
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
                    Html::a(Html::tag('i', '', ['class' => 'fas fa-redo']), ['/products'], ['class' => 'btn btn-outline-secondary']),
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
                        'attribute' => 'sessia_product_id',
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
                        'attribute' => 'marketplace_product_id',
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}&nbsp;{delete}',
                        'contentOptions' => [
                            'class' => 'text-center d-flex h-100'
                        ],
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(Html::tag('i', '', [
                                    'class' => 'fas fa-pen'
                                ]), $url, [
                                    'class' => 'btn btn-primary btn-sm',
                                    'title' => Yii::t('back', 'Изменить'),
                                    'data-pjax' => 0,
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(Html::tag('i', '', [
                                    'class' => 'fas fa-trash'
                                ]), $url, [
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => Yii::t('back', 'Удалить'),
                                    'data' => [
                                        'pjax' => 0,
                                        'confirm' => Yii::t('back', 'Вы уверены, что хотите удалить этот элемент?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            },
                        ]
                    ],
                ],
            ]); 
        ?>

    <?php Pjax::end(); ?>

</div>
