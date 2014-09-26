#REST API Module in Yii2
================

This is a REST API written in Yii2 with Access token based authentication.

## INSTALLATION
To install this module in your yii2 installation put  *api* in modules directory inside your site. Now go to your config file and add this module like this in modules

`
'modules' => [
		...
        'api' => [
            'class' => 'frontend\modules\api\Api',
        ],
        ...
    ] 
`