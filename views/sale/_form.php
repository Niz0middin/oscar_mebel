<?php

use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use zhuravljov\yii\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            echo $form->field($model, 'img')->widget(InputFile::className(), [
                'language'      => 'ru',
                'controller'    => 'elfinder',
                'filter'        => 'image',
                'template'      => '<div class="input-group"><span class="input-group-btn">{button}</span>{input}</div>',
                'options'       => ['class' => 'form-control','readonly' => true, 'style' => 'background-color:white!important'],
                'buttonOptions' => ['class' => 'btn btn-default'],
                'multiple'      => false
            ]);
            ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'start')->widget(DateTimePicker::class, [
                        'clientOptions' => [
                            'format' => 'dd.mm.yyyy hh:ii',
                            'language' => 'ru',
                            'autoclose' => true,
                        ],
                        'clientEvents' => [],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'end')->widget(DateTimePicker::class, [
                        'clientOptions' => [
                            'format' => 'dd.mm.yyyy hh:ii',
                            'language' => 'ru',
                            'autoclose' => true,
                        ],
                        'clientEvents' => [],
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
