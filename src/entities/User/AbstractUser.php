<?php
namespace sorokinmedia\user\entities\User;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\user\entities\UserAccessToken\{AbstractUserAccessToken, UserAccessTokenInterface};
use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\handlers\UserAccessToken\UserAccessTokenHandler;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\rbac\Role;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;

/**
 * Модель пользователя для работы с таблицей 'user'
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $username
 * @property integer $status_id
 * @property int $created_at
 * @property int $last_entering_date
 * @property string $email_confirm_token
 *
 * @property string $status
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
            ['username', 'unique', 'on' => 'create','targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Такое имя пользователя уже занято')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'on' => 'create', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Такой e-mail уже зарегистрирован')],
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
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
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
        return (static::getStatusesArray())[$this->status_id];
    }

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
            print_r($this->getErrors());
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

    /**
     * получает объект роли по ее названию
     * @param string $role_name
     * @return null|Role
     * //TODO: need test
     */
    public static function getRole(string $role_name)
    {
        return \Yii::$app->authManager->getRole($role_name);
    }

    /**
     * получить основную роль пользователя
     * требует реализации в дочернем классе
     * @return string
     */
    abstract function getPrimaryRole() : string;

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
        return static::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);
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
        $class = new static();
        $access_token = $class->__userAccessTokenClass::findOne(['access_token' => $token]);
        if ($access_token instanceof $class->__userAccessTokenClass){
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
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveGeneratedPasswordResetToken(): bool
    {
        $this->generatePasswordResetToken();
        return $this->save();
    }

    /**
     * поиск по токену сброса пароля
     * @param int $expired
     * @param string $token
     * @return AbstractUser
     */
    public static function findByPasswordResetToken(int $expired, string $token = null) : UserInterface
    {
        if (!static::isPasswordResetTokenValid($expired, $token)) {
            throw new \RuntimeException(\Yii::t('app', 'Недействительный токен. Запросите сброс пароля еще раз.'));
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * проверяет валидность токена сброса пароля (по времени)
     * @param int $expired
     * @param string $token
     * @return boolean
     */
    public static function isPasswordResetTokenValid(int $expired, string $token = null) : bool
    {
        if (is_null($token)) {
            return false;
        }
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expired >= time();
    }

    /**
     * сбрасывает токен сброса пароля
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * TODO: need test
     * отправка письма с ссылкой сброса пароля
     * необходима реализация метода в дочернем классе
     * @return bool
     */
    abstract public function sendPasswordResetMail() : bool;

    /**
     * поиск пользователя по e-mail
     * @param string $email
     * @return UserInterface|null
     */
    public static function findByEmail(string $email) : UserInterface
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * возвращает всех пользователей заданной роли
     * @param string $role
     * @return array
     * //TODO: need test
     */
    public static function findByRole(string $role) : array
    {
        return static::find()->where(['id' => \Yii::$app->authManager->getUserIdsByRole($role)])->all();
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
     * действия при подтверждении e-mail
     * @return bool
     */
    public function confirmEmailAction(): bool
    {
        $this->activate();
        $this->removeEmailConfirmToken();
        return $this->save();
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
     * сохранение нового пароля
     * @param bool $reset_token
     * @param string $password
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveNewPassword(string $password, bool $reset_token = false): bool
    {
        $this->setPassword($password);
        if ($reset_token === true){
            $this->removePasswordResetToken();
        }
        return $this->save();
    }

    /**
     * Генерация и сохранение аутификационного ключа
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    /******************************************************************************************************************
     * РАБОТА С РОЛЯМИ
     *****************************************************************************************************************/

    /**
     * Апгрейд пользователя до нужной роли
     * @param Role $role
     * @return bool
     * @throws \Exception
     * //TODO: need test
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
     * //TODO: need test
     */
    public function downgradeFromRole(Role $role) : bool
    {
        $auth = \Yii::$app->getAuthManager();
        if ($auth->revoke($role, $this->id)) {
            return true;
        }
        return false;
    }

    /**
     * список ролей или текстовка роли
     * @param string|null $role
     * @return mixed
     */
    abstract public static function getRolesArray(string $role = null);

    /**
     * список ссылок по роли или ссылка по роли
     * @param string|null $role
     * @return mixed
     */
    abstract public static function getRoleLink(string $role = null);

    /********************************
     * работа с токенами авторизации
     *******************************/

    /**
     * @return mixed|\yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasOne($this->__userAccessTokenClass, ['user_id' => 'id']);
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
                /** @var $token UserAccessTokenInterface */
                (new UserAccessTokenHandler($token))->deactivate();
            }
        }
        return true;
    }

    /**
     * Проставляем токены и куки после логина
     * @return bool
     * @throws \Throwable
     * @deprecated spa
     * //TODO: need test
     */
    public function afterLogin() : bool
    {
        $this->deactivateTokens();
        $token = $this->__userAccessTokenClass::create($this, true);
        if($token instanceof $this->__userAccessTokenClass && $token->is_active === true) {
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
     * @deprecated spa
     * //TODO: need test
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
     * @deprecated spa
     */
    public function getToken() : string
    {
        if (\Yii::$app->session->get('auth_token')){ // берем из куки
            $token = $this->__userAccessTokenClass::findOne(['access_token' => \Yii::$app->session->get('auth_token')]);
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
        /** @var AbstractUserAccessToken $token */
        $token = $this->__userAccessTokenClass::create($this, false);
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
        $websiteToken = $this->__userAccessTokenClass::findOne(['user_id' => $this->id, 'access_token' => $token]);
        if ($websiteToken instanceof $this->__userAccessTokenClass) {
            $websiteToken->deactivate();
        }
        \Yii::$app->getResponse()->getCookies()->remove('auth_token');
        return true;
    }

    /******************************************************************************************************************
     * РЕГИСТРАЦИЯ
     *****************************************************************************************************************/

    /**
     * регистрация пользователя
     * @param SignupForm $form
     * @return bool
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function signUp(SignupForm $form) : bool
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->username = $form->username;
            $this->email = $form->email;
            $this->setPassword($form->password);
            $this->status_id = self::STATUS_WAIT;
            $this->generateAuthKey();
            $this->generateEmailConfirmToken();
            if (!$this->save()) {
                throw new \Exception('Ошибка при регистрации #1');
            }
            $transaction->commit();
            $this->afterSignUp();
            $this->sendEmailConfirmation();
        }
        catch(\Exception $e){
            $transaction->rollBack();
            throw new ServerErrorHttpException($e->getMessage());
        }
        return true;
    }

    /**
     * метод, вызываемой после создания сущности пользователя. требует реализации в дочернем классе.
     * сюда вписывать доп действия - создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @return mixed
     */
    abstract public function afterSignUp();

    /**
     * TODO: need test
     * отправка письма с подтверждением e-mail
     * @return bool
     */
    abstract public function sendEmailConfirmation() : bool;

    /******************************************************************************************************************
     * СПИСКИ ПОЛЬЗОВАТЕЛЕЙ
     *****************************************************************************************************************/

    /**
     * TODO: need test
     * список пользователей в виде id=>username
     * @return array
     */
    public static function getUsersArray() : array
    {
        return static::find()->select(['username', 'id'])->where(['status_id' => self::STATUS_ACTIVE])->indexBy('id')->orderBy(['id' =>SORT_ASC])->column();
    }

    /**
     * TODO: need test
     * список всех активных пользователей
     * @return array|mixed|AbstractUser[]|ActiveRecord[]
     */
    public static function getActiveUsers()
    {
        return static::find()->where(['status_id' => self::STATUS_ACTIVE])->all();
    }
}
