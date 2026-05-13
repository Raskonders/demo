<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class SignupForm extends Model
{
    public $login;
    public $username;
    public $password;
    public $email;
    public $phone;
    


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
       return [
        [['username','login','phone','email','password'], 'trim'],
        [['username','login','phone','email','password'], 'required'],
        ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
        ['username', 'string', 'min' => 8, 'max' => 255],
        ['login', 'string', 'min' => 8, 'max' => 255],
        ['password', 'string', 'min' => 8, 'max' => 255],
        ['email', 'email'],
        ['email', 'string', 'max' => 255],
        ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This us address has already been taken.'],
        ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],
        ['phone', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This ph address has already been taken.'],


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

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function signup()
{
    if (!$this->validate()) {
        return null;
    }
    
    $user = new User();
    $user->login = $this->username;
    $user->username = $this->username;
    $user->email = $this->email;
    $user->phone = $this->email;
    $user->setPassword($this->password);
    $user->generateAuthKey();
    return $user->save() ? $user : null;
}


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'username' => 'ФИО',
            'password' => 'Паролоь',
        ];
    }
}
