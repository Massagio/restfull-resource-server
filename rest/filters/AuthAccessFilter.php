<?php

namespace rest\filters;

use yii\base\ActionFilter;
use yii\filters\auth\QueryParamAuth;
use yii\web\ForbiddenHttpException;

class AuthAccessFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $check = \Yii::$app->restclient->get(
            \Yii::$app->params['auth_server'],
            array(
                'Authorization:'.\Yii::$app->getRequest()->getHeaders()['Authorization']
            )
        );
        if ($check != "true") {
            throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        return parent::beforeAction($action);
    }
}
