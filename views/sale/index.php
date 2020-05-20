<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sales';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sale', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'start',
                'value' => function ($model) {
                    return date('d.m.Y h:i', $model->start);
                },
                'filter' => DateTimePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'start',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy hh:ii'
                    ]
                ])
            ],
            [
                'attribute' => 'end',
                'value' => function ($model) {
                    return date('d.m.Y h:i', $model->end);
                },
                'filter' => DateTimePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'end',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy hh:ii'
                    ]
                ])
            ],
            [
                'attribute' => 'status',
                'filter' => [1=>'Active', 0=>'Passive'],
                'value' => function($model){
                    if ($model->status == 1){
                        return 'Active';
                    }
                    else{
                        return 'Passive';
                    }
                }
            ],
            [
                'attribute' => 'img',
                'format' => 'raw',
                'value' => function($model){
                    return "<img style='max-height: 100px' src='$model->img'>";
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
