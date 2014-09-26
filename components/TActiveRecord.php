<?php
/**
 * Created by PhpStorm.
 * User: nazar2
 * Date: 6/24/14
 * Time: 1:09 PM
 */

namespace frontend\components;


use Yii;
use common\models\Company;
use yii\db\ActiveRecord;

class TActiveRecord extends ActiveRecord
{
    //saving model->tenant to all tables automatic ::Rajith::
    public function beforeSave($model)
    {
        if(Yii::$app->id != 'app-backend'){
            $company = $this->getCompanyByDomain();
            $this->company_id = $company->id;
            return parent::beforeSave($model);
        }
    }

    public static function find()
    {
        if(Yii::$app->id == 'app-backend'){
            return parent::find();
        } else {
            $tbl_name = self::getTableSchema()->name;
            return parent::find()->andWhere([ $tbl_name.'.company_id' => self::getCompanyByDomain()->id]);
        }
    }

    //before deletion check for the ownership ::Rajith::
    //not working for deleteAllByAttributes
    public function beforeDelete()
    {
        $company = $this->getCompanyByDomain();
        if ($this->company == $company) {
            return true;
        } else {
            return false; // prevent actual DELETE query from being run
        }
    }

    public static function getCompanyByDomain()
    {
        $comp = Company::findOne(['id' => Yii::$app->session->get('company_id')]);
        if(is_object($comp)) {
            return Company::findOne(['id' => Yii::$app->session->get('company_id')]);
        }
        return Company::getCompanyBySubdomain();

    }
} 