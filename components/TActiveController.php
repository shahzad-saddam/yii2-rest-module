<?php
/**
 * Created by PhpStorm.
 * User: Saddam Shahzad
 * Date: 6/24/14
 * Time: 10:10 AM
 */

namespace frontend\components;


use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\Controller;
use Yii;

class TActiveController extends ActiveController
{
    public function behaviors()
    {
//        if(Yii::$app->requestedAction->actionMethod != 'actionDownloadApp') {
            $behaviors = parent::behaviors();
            $behaviors['authenticator'] = [
                'class' =>  TParamAuth::className(),
            ];
            return $behaviors;
//        } else {
//            $behaviors = parent::behaviors();
//            return $behaviors;
//        }
//    }

}