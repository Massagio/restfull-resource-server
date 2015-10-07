<?php
namespace rest\versions\v1\controllers;

use common\models\LoginForm;
use yii\rest\Controller;

/**
 * Class UserController
 * @package rest\versions\v1\controllers
 */
class UserController extends Controller
{

  public function behaviors()
      {
          $behaviors = parent::behaviors();

          $behaviors['rateLimiter'] = [
              'class' => RateLimiter::className(),
              'enableRateLimitHeaders' => true,
          ];

          $behaviors['authenticator'] = [
              'class' => CompositeAuth::className(),

              'authMethods' => [
                  HttpBasicAuth::className(),
                  HttpBearerAuth::className(),
                  QueryParamAuth::className(),
              ],
              //'except' => ['this']
          ];

          $behaviors['contentNegotiator'] = [
              'class' => ContentNegotiator::className(),
              'only' => [
                  'option',
              ],
              'formats' => [
                  'application/json' => Response::FORMAT_JSON,
              ]
          ];
          $behaviors['authaccess'] = [
              'class' => AuthAccessFilter::className()
          ];
          $behaviors['verbs'] = [
              'class' => VerbFilter::className(),
              'actions' => [
                  'option' => ['OPTIONS'],
              ],
          ];
          return $behaviors;
      }
    /**
     * This method implemented to demonstrate the receipt of the token.
     * Do not use it on production systems.
     * @return string AuthKey or model with errors
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return \Yii::$app->user->identity->getAuthKey();
        } else {
            return $model;
        }
    }
}
