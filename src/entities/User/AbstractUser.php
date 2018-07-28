<?php
namespace sorokinmedia\user\entities\User;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\user\entities\UserAccessToken\{
    UserAccessToken
};
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\rbac\Role;
use yii\web\IdentityInterface;

/**
 * Модель пользователя для работы с таблицей 'user'
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $auth_key
 * @property string $username
 * @property integer $status_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $last_entering_date
 * @property string $email_confirm_token
 */
abstract class AbstractUser extends ActiveRecord implements IdentityInterface, UserInterface, RelationInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Такое имя пользователя уже занято')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Такой e-mail уже зарегистрирован')],
            ['email', 'string', 'max' => 255],

            ['status_id', 'integer'],
            ['status_id', 'default', 'value' => AbstractUser::STATUS_ACTIVE],
            ['status_id', 'in', 'range' => array_keys(AbstractUser::getStatusesArray())],

            ['newPassword', 'string', 'min' => 6],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
            [['last_entering_date'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'created_at' => \Yii::t('app', 'Дата регистрации'),
            'updated_at' => \Yii::t('app', 'Обновлен'),
            'username' => \Yii::t('app', 'Никнейм'),
            'auth_key' => \Yii::t('app', 'API ключ'),
            'email_confirm_token' => \Yii::t('app', 'Токен подтверждения e-mail'),
            'password_hash' => \Yii::t('app', 'Password hash'),
            'password_reset_token' => \Yii::t('app', 'Токен сброса пароля'),
            'email' => \Yii::t('app', 'Email'),
            'status_id' => \Yii::t('app', 'Статус'),
            'last_entering_date' => \Yii::t('app', 'Последний вход'),
        ];
    }

    /**
     * обработка created_at/updated_at дат
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class
            ],
        ];
    }

    /**
     * вернет массив статусов
     * @return array
     */
    public static function getStatusesArray() : array
    {
        return [
            self::STATUS_BLOCKED => \Yii::t('app', 'Заблокирован'),
            self::STATUS_ACTIVE => \Yii::t('app','Активен'),
            self::STATUS_WAIT => \Yii::t('app','Ожидает подтверждения'),
        ];
    }

    /**
     * вернет текст статуса
     * @return string
     */
    public function getStatus() : string
    {
        return (self::getStatusesArray())[$this->status_id];
    }

    /**********************************
     * реализация интерфейсных методов
     *********************************/

    /**
     * Идентификация пользователя для авторизации в Yii
     * @param int|string $id
     * @return null|IdentityInterface|static
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     * ищет пользователя сначала по user.auth_token
     * если не найден ищет по токену из UserAccessToken
     * в остальных случаях вернет null и 401 ошибку в АПИ
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (static::findOne(['auth_key' => $token])){
            return static::findOne(['auth_key' => $token]);
        }
        $access_token = UserAccessToken::findOne(['access_token' => $token]);
        if ($access_token instanceof UserAccessToken){
            return static::findOne($access_token->user_id);
        }
        return null;
    }

    /**
     * получение API ключа
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * валидация API ключа
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**********************************************************
     * методы для работы с паролем, сбросом пароля, поиск юзера
     *********************************************************/

    /**
     * генерация токена для сброса пароля
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = \Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * поиск по токену сброса пароля
     * @param string $token
     * @return AbstractUser
     */
    public static function findByPasswordResetToken(string $token) : UserInterface
    {
        if (!static::isPasswordResetTokenValid($token)) {
            throw new \RuntimeException(\Yii::t('app', 'Недействительный токен. Запросите сброс пароля еще раз.'));
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * проверяет валидность токена сброса пароля (по времени)
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) : bool
    {
        if (is_null($token)) {
            return false;
        }
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * сбрасывает токен сброса пароля
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * поиск пользователя по e-mail
     * @param string $email
     * @return UserAR|null
     */
    public static function findByEmail(string $email) : UserInterface
    {
        return self::findOne(['email' => $email]);
    }

    /**
     * возвращает всех пользователей заданной роли
     * @param string $role
     * @return array
     */
    public static function findByRole(string $role) : array
    {
        return self::find()->where(['id' => \Yii::$app->authManager->getUserIdsByRole($role)])->all();
    }

    /**
     * ищет пользователя по токену подтверждения мыла
     * @param string $email_confirm_token
     * @return static|null
     */
    public static function findByEmailConfirmToken(string $email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status_id' => self::STATUS_WAIT]);
    }

    /**
     * генерит токен для подтверждения мыла
     * @throws \yii\base\Exception
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = \Yii::$app->security->generateRandomString();
    }

    /**
     * обнуляет токен для подтверждения мыла
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

    /**
     * Валидация пароля
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password) : bool
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    /**
     * Генерация и сохранение хэша пароля
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword(string $password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Генерация и сохранение аутификационного ключа
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    /**
     * работа с ролями
     */

    /**
     * Апгрейд пользователя до нужной роли
     * @param Role $role
     * @return bool
     * @throws \Exception
     */
    public function upgradeToRole(Role $role) : bool
    {
        $auth = \Yii::$app->getAuthManager();
        if ($auth->assign($role, $this->id)) {
            return true;
        }
        return false;
    }

    /**
     * Даунгрейд пользователя до нужной роли
     * @param Role $role
     * @return bool
     */
    public function downgradeFromRole(Role $role) : bool
    {
        $auth = \Yii::$app->getAuthManager();
        if ($auth->revoke($role, $this->id)) {
            return true;
        }
        return false;
    }

    /********************************
     * работа с токенами авторизации
     *******************************/

    /**
     * @return mixed|\yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasOne(UserAccessToken::class, ['user_id' => 'id']);
    }

    /**
     * удаляет все токены пользователя
     * @return bool
     * @throws Exception
     */
    public function deactivateTokens() : bool
    {
        $tokens = $this->getTokens()->all();
        if ($tokens){
            foreach ($tokens as $token){
                /** @var $token UserAccessToken */
                $token->deactivate();
            }
        }
        return true;
    }

    /**
     * Проставляем токены и куки после логина
     * @return bool
     * @throws \Throwable
     */
    public function afterLogin() : bool
    {
        $this->deactivateTokens();
        $token = UserAccessToken::create($this, true);
        if($token instanceof UserAccessToken && $token->is_active === true) {
            // записываем токен в куки
            if (\Yii::$app->getRequest()->getCookies()->getValue('auth_token')) {
                \Yii::$app->getResponse()->getCookies()->remove('auth_token');
            }
            //TODO: настройку урла брать из настроек компонента
            setcookie('auth_token', $token->access_token, time()+60*60*24*30, '/', \Yii::$app->params['cookieUrl'], false, false);
            return true;
        }
        return false;
    }

    /**
     * Заменяет токен при заходе под другим юзером
     * @param string $token
     * @return bool
     */
    public function addCheckToken(string $token) : bool
    {
        if (\Yii::$app->getRequest()->getCookies()->getValue('auth_token')) {
            \Yii::$app->getResponse()->getCookies()->remove('auth_token');
        }
        //TODO: настройку урла брать из настроек компонента
        setcookie('auth_token', $token, time()+60*60*24*30, '/', \Yii::$app->params['cookieUrl'], false, false);
        return true;
    }

    /**
     * Получить api токен для Header Bearer авторизации
     * @return bool|string
     */
    public function getToken() : string
    {
        if (\Yii::$app->session->get('auth_token')){ // берем из куки
            $token = UserAccessToken::findOne(['access_token' => \Yii::$app->session->get('auth_token')]);
            return $token->access_token;
        }
        return '';
    }

    /**
     * Получить последний токен при заходе под другим юзером
     * @return mixed|string
     * @throws \yii\db\Exception
     */
    public function getCheckToken() : string
    {
        /** @var UserAccessToken $token */
        $token = UserAccessToken::create($this, false);
        return $token->access_token;
    }

    /**
     * Удаляем токены и куки после логаута
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     */
    public function afterLogout() : bool
    {
        $token = \Yii::$app->getRequest()->getCookies()->getValue('auth_token');
        $websiteToken = UserAccessToken::findOne(['user_id' => $this->id, 'access_token' => $token]);
        if ($websiteToken instanceof UserAccessToken) {
            $websiteToken->deactivate();
        }
        \Yii::$app->getResponse()->getCookies()->remove('auth_token');
        return true;
    }

    /******************************************************************************
     * общие методы
     *****************************************************************************/

    /**
     * смена статуса на активный
     */
    public function activate()
    {
        $this->status_id = self::STATUS_ACTIVE;
    }

    /**
     * смена статуса на заблокированный
     */
    public function deactivate()
    {
        $this->status_id = self::STATUS_BLOCKED;
    }

    /**
     * блокировака юзера
     * @return bool
     * @throws \Exception
     */
    public function blockUser() : bool
    {
        $this->activate();
        if (!$this->save()){
            throw new Exception(\Yii::t('app','Ошибка при блокировании пользователя'));
        }
        return true;
    }

    /**
     * разблокировка юзера
     * @return bool
     * @throws \Exception
     */
    public function unblockUser() : bool
    {
        $this->deactivate();
        if (!$this->save()){
            throw new Exception(\Yii::t('app','Ошибка при разблокировке пользователя'));
        }
        return true;
    }
}
