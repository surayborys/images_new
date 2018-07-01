<?php

namespace frontend\controllers;

use yii\web\Controller;
use frontend\models\forms\SearchForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use Yii;

/**
 * controller for search procedure
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class SearchController extends Controller{
    
    public function behaviors() {
        //set access rules
        //'@' - logged user
        return[
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function($rule, $action) {
                            Yii::$app->session->setFlash('danger', Yii::t('login','Please, login to continue...'));
                            return $this->redirect(Url::to(['/user/default/login']));
                        },
                'only' => ['search'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['search'],
                        'roles' => ['@'],
                        
                    ],
                ],
            ],
        ];       
    }

    public function actionSearch()
    {
        $model = new SearchForm();
        $results = null;
        
        if($model->load(Yii::$app->request->get())) {
            $results = $model->search();
        }
        
        return $this->render('_form', [
            'model' => $model,
            'results' => $results,
        ]);
    }
}
