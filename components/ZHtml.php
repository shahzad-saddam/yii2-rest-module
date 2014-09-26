<?php

namespace frontend\components;
use yii\helpers\html;

class ZHtml extends Html
{
    public static function enumDropDownList($form, $model, $attribute, $htmlOptions=array())
    {
        return $form->field($model, $attribute)->dropDownList(self::enumItem($model, $attribute),$htmlOptions);
    }

    public static function enumItem($model,$attribute) {
        //$attr=$attribute;

        preg_match('/\((.*)\)/',$model->tableSchema->columns[$attribute]->dbType,$matches);
        $matches_new = explode('\',\'', $matches[1]);
        //print_r($matches_new);exit;
        foreach($matches_new as $value) {
            $value=str_replace("'",null,$value);
            $values[$value]=ucfirst($value);
        }
       return $values;
    }
}
?>