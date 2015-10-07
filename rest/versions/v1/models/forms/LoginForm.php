<?php
namespace rest\versions\v1\models\form;

use common\models\User;
use Yii;
use yii\base\Model;
/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $info;
    public $password;
    public $rememberMe = true;
    private $_user = false;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user['status'] === 0) {
                $this->addError($attribute, 'Useraccount is not activated');
            }
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->generateAccessToken();
            $user->save();

            $this->info = array(
                    'key' => $user->access_token,
                    'permission' => User::getPermission($user->id),
                    'id' => $user->username
                );
            return Yii::$app->user->loginByAccessToken($user->access_token);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}
