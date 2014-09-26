#REST API Module in Yii2
================

This is a REST API written in Yii2 with Access token based authentication.

## SETUP
To Setup this mopdule you have to do two things first is module config then API routing usign URL Manager.

### MODULE CONFIG
 To configure this module in your yii2 installation put  **api** in modules directory inside your site. Now go to your config file and add this module like this in modules.

```
'modules' => [
		...
        'api' => [
            'class' => 'frontend\modules\api\Api',
        ],
        ...
    ] 
```

### URL MANAGER
 In url Manager put these lines to configure the conrollters sample controllers used in this API are **User** and **Jobs**.
```
/*
             * REST api rules
             */
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/user',
                    'controller' => 'api/jobs',
                    'except' => [
                        'delete',
                    ],
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'POST logout' => 'logout',
                        'POST forgot' => 'forgot',
                    ],
                ],
            ],
```            


Module is configure you can access controllers with `your-base-url/api/controller`.

## AUTHENTICATION
For authentication I have overidden the `QueryParamAuth` Class with `TParamAuth` (in components Directory) and used it for Access Token based Authentication which is added in the `Components` directory and you will notice that `Controllers` are extending a Class `TActiveController` reason is Authetication behaviour. I have added code to use `TParamAuth` as authentication class.

```
 public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' =>  TParamAuth::className(),
        ];
        
        return $behaviors;
    }
```

Finally bingo you are good to go using Classes in components for wrtting a multi tenant site.