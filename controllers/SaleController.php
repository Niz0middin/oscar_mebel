<?php

namespace app\controllers;

use Yii;
use app\models\Sale;
use app\models\search\SaleSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SaleController implements the CRUD actions for Sale model.
 */
class SaleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SaleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sale model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $token='1185997109:AAFqTaqEhTobFjrR9_wYWA70gGvyBcDYfzI';
        $chatId= 2975459;
        //$chatId=-1001335263520;

        $text='aksiya';
        $parametres=[
            'chat_id'=>$chatId,
            'text'=>$text,
        ];
        $url = 'https://api.telegram.org/bot'.$token.'/sendMessage?'.http_build_query($parametres);

        @file_get_contents($url);
        $model = new Sale();

        if ($model->load(Yii::$app->request->post())) {
            $model->start = strtotime($model->start);
            $model->end = strtotime($model->end);
            $model->save();
            $token='1185997109:AAFqTaqEhTobFjrR9_wYWA70gGvyBcDYfzI';
            $chatId= 2975459;
            //$chatId=-1001335263520;

            $text='aksiya';
            $parametres=[
                'chat_id'=>$chatId,
                'text'=>$text,
            ];
            $url = 'https://api.telegram.org/bot'.$token.'/sendMessage?'.http_build_query($parametres);

            @file_get_contents($url);
//            echo "<pre>";
//            print_r($model->getErrors());
//            die;
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionAll(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Sale::find()->where(['status' => 1])->all();
    }

    public function actionLast(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $rate = Sale::find()->where(['status' => 1])->orderBy(['id' => SORT_DESC])->one();
        return $rate;
    }

    public function actionOne($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $rate = $this->findModel($id);
        return $rate;
    }
}
