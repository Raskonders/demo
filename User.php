<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $username
 * @property string $email
 * @property string $phone
 * @property string $password_hash
 * @property string $auth_key
 * @property int $status
 * @property int $role
 *
 * @property Request[] $requests
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }



    /**
     * {@inheritdoc}
     */
   public static function findIdentity($id)
{
    return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
}


    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
{
    foreach (self::$users as $user) {
        if ($user['accessToken'] === $token) {
            return new static($user);
        }
    }
    return null;
}


    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
{
    return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
}


    /**
     * {@inheritdoc}
     */
   public function getId()
{
    return $this->getPrimaryKey();
}


    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
{
    return $this->auth_key;
}



    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
{
    return $this->getAuthKey() === $authKey;
}


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
return Yii:: $app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
{
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
}

public function generateAuthKey()
{
    $this->auth_key = Yii::$app->security->generateRandomString();
}



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE,self::STATUS_DELETED]],
            [['role'], 'default', 'value' => 2],
            [['login', 'username', 'email', 'phone', 'password_hash', 'auth_key'], 'required'],
            [['status', 'role'], 'integer'],
            [['login', 'username', 'email', 'phone', 'password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['login'], 'unique'],
            [['email'], 'unique'],
            [['phone'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'username' => 'Username',
            'email' => 'Email',
            'phone' => 'Phone',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'role' => 'Role',
        ];
    }

    /**
     * Gets query for [[Requests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::class, ['user_id' => 'id']);
    }

}
