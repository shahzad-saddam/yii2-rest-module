<?php

namespace frontend\modules\api\controllers;

use frontend\components\TActiveController;
use frontend\models\AppUpdate;
use frontend\models\User;
use Yii;

class UserController extends TActiveController
{
    public $modelClass = 'frontend\models\User';

    /**
     * Index
     * Not in actual use currently
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            throw new \HttpHeaderException();
        }
        return \Yii::$app->user->id;
    }

    /**
     * Login Action
     * Use to return user object as login is handled by TParamAuth class
     * Return User object or error message
     * @return json Object
     */
    public function actionLogin()
    {

        if (Yii::$app->user->identity) {
            $user = Yii::$app->user->identity;
            $sql = 'SELECT * FROM app_update';
            $update_data = AppUpdate::findBySql($sql)->one()->toArray();
            $user_data = [
                'id' => $user->getId(),
                'username' => $user['username'],
                'fullname' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'access_token' => $user['access_token'],
                'aap_version' => $update_data['version'],
                'force_update' => (bool)$update_data['forec_update'],
                'download_url' => Yii::$app->getUrlManager()->createAbsoluteUrl('api/user/download-app/?app_version=' . $update_data['version']),
            ];
            $data = [
                "status" => 1,
                "message" => "Successful login",
                "user" => $user_data
            ];
        } else {
            //            $data = [
            //                "status" => 0,
            //                "message" 	=> "You are not Authorized and or Access with invalid Credentials",
            //                "user"		=> [],
            //            ];
        }
        return $data;

    }

    /**
     * Download android App
     * Send Android app .apk file to device
     */
    public function actionDownloadApp($app_version = NULL)
    {
        if ($app_version) {
            $sql = 'SELECT * FROM app_update WHERE `version`=' . $app_version;
            $update_data = AppUpdate::findBySql($sql)->one()->toArray();
            Yii::$app->response->SendFile(str_replace('frontend', '', Yii::$app->basePath) . '/uploads/companies/' . $update_data['file_name']);
            Yii::$app->end();
        }
    }


    /**
     * Logout Action
     * Logout user and remove access token for that user.
     * Return User object or error message
     * @return json Object
     */
    public function actionLogout()
    {
        $model = User::findOne(Yii::$app->user->id);
        $model->access_token = '';
        $model->access_created_at = '';
        if ($model->save()) {

        }
        if (Yii::$app->user->logout()) {
            $data = [
                "status" => 1,
                "message" => "Successful logout",
            ];
        } else {
            $data = [
                "status" => 0,
                "message" => "Logout not Successful",
            ];
        }
        return $data;
    }
}
