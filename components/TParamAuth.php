<?php
/**
 * Created by PhpStorm.
 * User: saddam.shahzad
 * Date: 9/12/14
 * Time: 6:06 PM
 */


namespace frontend\components;

use common\models\Company;
use frontend\models\LoginForm;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

/**
 * QueryParamAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TParamAuth extends QueryParamAuth
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        header('Content-Type: application/json');
        //original
        //$accessToken = $request->get($this->tokenParam);
        //mine
        $accessToken = $request->getHeaders()->get('accesstoken');
        $username = $request->post('username');
        $password = $request->post('password');

        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken);
            if ($identity !== null) {
                $user = \frontend\models\User::findOne(\Yii::$app->user->id)->toArray();
                $user_data = [
                    'id'            =>  $user['id'],
                    'username'      =>  $user['username'],
                    'fullname'      =>  $user['first_name'] .' '. $user['last_name'],
                    'email'         =>  $user['email'],
                    'access_token'  =>  $user['access_token'],
                ];
                $data = [
                    "status"    =>  1,
                    "message"   =>  "Successful login",
                    "user"      =>  $user_data
                ];
                //echo json_encode($data);
                return $identity;
            }
        }
        //Login with username/Password
        else if ($username !== null) {
            \Yii::$app->user->logout();
            if(!empty($username) && !empty($password)){
                $model = new LoginForm();
                $model->username = $username;
                $model->password = $password;

                $model_for_up = \frontend\models\User::findByUsername($username, Company::getCompanyBySubdomain()->attributes['id']);
                if($model_for_up){
                    $model_for_up->access_token = time().'_'.$model->username;
                    $model_for_up->access_created_at = time();
                    if($model_for_up->save()){

                        if ( $model->login() ) {

                            //$user = Yii::$app->user->identity;
                            $user = \frontend\models\User::find(['id' => \Yii::$app->user->id])->asArray()->one();
                            $user_data = [
                                'id'            =>  $user['id'],
                                'username'      =>  $user['username'],
                                'fullname'      =>  $user['first_name'] .' '. $user['last_name'],
                                'email'         =>  $user['email'],
                                'access_token'  =>  $user['access_token'],
                            ];
                            $data = [
                                "status"    =>  1,
                                "message"   =>  "Successful login",
                                "user"      =>  $user_data
                            ];
                            return true;
                        } else {
                            $this->handleFailure($response);
                            return null;
                        }

                    }
                } else {
//                    $this->handleFailure($response);
//                    return null;
                }

            } else {
                $this->handleFailure($response);
                return null;
            }

        }
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        $data = [
            "status" => 0,
            "message" 	=> "You are not Authorized and or Access with invalid Credentials",
            "user"		=> [],
        ];

        echo json_encode($data);
    }
}
