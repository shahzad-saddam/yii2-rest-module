<?php

namespace frontend\modules\api\controllers;

use backend\components\TController;

class DefaultController extends TController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
