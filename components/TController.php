<?php
/**
 * Created by PhpStorm.
 * User: nazar2
 * Date: 6/24/14
 * Time: 10:10 AM
 */

namespace frontend\components;


use yii\web\Controller;

class TController extends \common\components\TController
{
    public $company = null;

    public function behaviors()
    {
        return [
            'domainFilter' => [
                'class' => DomainFilter::className(),
                'except' => ['company-not-exists', 'error'],
            ],
        ];
    }
}