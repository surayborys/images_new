<?php

namespace frontend\controllers;

use yii\web\Controller;
use frontend\models\forms\SearchForm;
use Yii;

/**
 * controller for search procedure
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class SearchController extends Controller{
    
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
