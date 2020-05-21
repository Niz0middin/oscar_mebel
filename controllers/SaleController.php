<?php

namespace app\controllers;

use app\models\Client;
use CURLFile;
use Yii;
use app\models\Sale;
use app\models\search\SaleSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'delete', 'view'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function beforeAction($action)
    {
        $checks = Sale::find()->all();
        foreach ($checks as $check){
            if (time()<$check->start || time()>$check->end ){
                $check->status = 0;
            }else{
                $check->status = 1;
            }
            $check->save();
        }

        return parent::beforeAction($action);
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
        $model = new Sale();

        if ($model->load(Yii::$app->request->post())) {
            $model->start = strtotime($model->start);
            $model->end = strtotime($model->end);
            $model->save();
            $clients = Client::find()->all();
            $token = '1185997109:AAFqTaqEhTobFjrR9_wYWA70gGvyBcDYfzI';
            foreach ($clients as $client) {
                $chat_id = $client->chat_id;
                $url = "https://api.telegram.org/bot$token/sendPhoto?chat_id=$chat_id";
                $post_fields = [
                    'chat_id'   => $chat_id,
                    'photo' => new CURLFile(Url::to('@webroot').$model->img)
                ];
                $this->curlBot($url, $post_fields);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function curlBot($url, $post_fields){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
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

        if ($model->load(Yii::$app->request->post())) {
            $model->start = strtotime($model->start);
            $model->end = strtotime($model->end);
            $model->save();
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
