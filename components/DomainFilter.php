<?php
/**
 * Created by PhpStorm.
 * User: nazar2
 * Date: 6/24/14
 * Time: 10:12 AM
 */


namespace frontend\components;

use Yii;
use common\models\Company;
use yii\base\ActionFilter;

class DomainFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $company = Company::getCompanyBySubdomain();
        if ($company) {
            $action->controller->company = $company;
            Yii::$app->session->set('company_id', $company->id);
            return true;
        } else {
            $action->controller->redirect(['/site/company-not-exists']);
            return false;
        }
    }
} 