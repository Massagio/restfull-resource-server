<?php

namespace rest\versions\v1\models;

use common\models\User as CommonUser;
use yii\filters\RateLimitInterface;

/**
 * This is the model class for table "tbl_user". *
 * @property  mixed user_id
 * @property  mixed type
 * @property  string title
 * @property  string title_clean
 * @property  string teaser
 * @property  mixed|null category
 * @property  mixed|null genre
 * @property  string content
 * @property  string author
 * @property  array image
 */
class User extends CommonUser implements RateLimitInterface
{

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        if (($request->isPut || $request->isDelete || $request->isPost)) {
            return [\Yii::$app->params['maxRateLimit'], \Yii::$app->params['perRateLimit']];
        }

        return [\Yii::$app->params['maxGetRateLimit'], \Yii::$app->params['perGetRateLimit']];
    }
    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
        return [
            \Yii::$app->cache->get($request->getPathInfo() . $request->getMethod() . '_remaining'),
            \Yii::$app->cache->get($request->getPathInfo() . $request->getMethod() . '_ts')
        ];
    }
    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        \Yii::$app->cache->set($request->getPathInfo() . $request->getMethod() . '_remaining', $allowance);
        \Yii::$app->cache->set($request->getPathInfo() . $request->getMethod() . '_ts', $timestamp);
    }
}
