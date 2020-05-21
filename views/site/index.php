<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<style>
    .btn-success{
        width: 200px;
        margin-top: 50px;
    }
</style>
<div class="site-index">

    <div class="jumbotron">
        <h1>Oscar Mebel</h1>
        <div class="row">
            <div class="col-md-3 col-md-offset-2">
                <p><a class="btn btn-lg btn-success" href="/category">Категория</a></p>
            </div>
            <div class="col-md-3">
                <p><a class="btn btn-lg btn-success" href="/product">Продукты</a></p>
            </div>
            <div class="col-md-2">
                <p><a class="btn btn-lg btn-success" href="/sale">Акции</a></p>
            </div>
        </div>
    </div>
</div>
