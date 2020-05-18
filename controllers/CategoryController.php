<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\search\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ( ! empty(Yii::$app->request->post('Category')))
        {
            $post            = Yii::$app->request->post('Category');
            $model->name     = $post['name'];
            $model->position = $post['position'];
            $parent_id       = $post['parentId'];

            if (empty($parent_id))
                $model->makeRoot();
            else
            {
                $parent = Category::findOne($parent_id);
                $model->appendTo($parent);
            }

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ( ! empty(Yii::$app->request->post('Category')))
        {
            $post            = Yii::$app->request->post('Category');

            $model->name     = $post['name'];
            $model->position = $post['position'];
            $parent_id       = $post['parentId'];

            if ($model->save())
            {
                if (empty($parent_id))
                {
                    if ( ! $model->isRoot())
                        $model->makeRoot();
                }
                else // move node to other root
                {
                    if ($model->id != $parent_id)
                    {
                        $parent = Category::findOne($parent_id);
                        $model->appendTo($parent);
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->isRoot())
            $model->deleteWithChildren();
        else
            $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionList(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $roots = Category::find()->roots()->all();
//        $leaves = Category::find()->leaves()->all();
//        $one_leaves = Category::findOne(['name' => 'Telefon'])->leaves()->all();
//        $array['root'] = $roots;
//        foreach ($roots as $k => $root) {
        $root = $roots[0];
            $array['root'][0] = [
                'text' => $root->name,
                'callback_data' => 'sc'.$root->tree
            ];
            for ($i=0;$i<3;$i++) {
                $callback = $array['root'][0]['callback_data'];
                $j=0;
                if ($i == 0) {
                    $response = Category::findOne(['name' => $root->name])->children(1)->all();
                }else{
                    if ($i>1) $j++;
                    $response = Category::findOne(['name' => $array[$callback][$j]['text']])->children(1)->all();
                }
                if ($i != 0) $callback = $callback.$i;
                foreach ($response as $key => $r) {
                    $array[$callback][$key] = [
                        'text' => $r->name,
                        'callback_data' => $callback . ($key + 1),
                    ];
                }
            }
//        }
        return $array;
    }

    public function actionGet($name=null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($name == 'root' || $name == null) {
            $response = Category::find()->roots()->all();
        }elseif ($name == 'all'){
            $response = Category::find()->all();
        }
        else{
            $response = Category::findOne(['name' => $name])->children(1)->all();
        }
        foreach ($response as $r) {
            $array[] = [
                'text' => $r->name,
                'callback_data' => $r->id
            ];
        }
        return $array;
    }
}
