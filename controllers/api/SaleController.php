<?php
namespace app\controllers\api;
use yii\rest\ActiveController;

class SaleController extends ActiveController{
    public $modelClass = 'app\models\Sale';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
//        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

}
