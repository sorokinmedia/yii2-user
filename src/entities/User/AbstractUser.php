<?php

namespace sorokinmedia\user\entities\User;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\helpers\DateHelper;
use sorokinmedia\user\entities\UserAccessToken\{AbstractUserAccessToken, UserAccessTokenInterface};
use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use sorokinmedia\user\forms\{SignUpFormConsole, SignUpFormEmail, SignupForm};
use sorokinmedia\user\handlers\{
    UserAccessToken\UserAccessTokenHandler, UserInvite\interfaces\ProcessInvitesInterface
};
use yii\behaviors\TimestampBehavior;
use yii\db\{
    ActiveRecord, Exception
};
use yii\web\{
    IdentityInterface, ServerErrorHttpException
};
use yii\rbac\Role;

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
 * @property string $displayName
 */
abstract class AbstractUser extends ActiveRecord implements IdentityInterface, UserInterface, RelationInterface, ProcessInvitesInterface, ProcessAffiliateInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT_EMAIL = 2;

    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * @return int
     */
    public function getId(): int
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
            ['username', 'unique', 'on' => 'create', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Такое имя пользователя уже занято')],
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
    public function attributeLabels(): array
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
    public function behaviors(): array
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
    public static function getStatusesArray(): array
    {
        return [
            self::STATUS_BLOCKED => \Yii::t('app', 'Заблокирован'),
            self::STATUS_ACTIVE => \Yii::t('app', 'Активен'),
            self::STATUS_WAIT_EMAIL => \Yii::t('app', 'Ожидает подтверждения e-mail'),
        ];
    }

    /**
     * вернет текст статуса
     * @return string
     */
    public function getStatus(): string
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
    public function blockUser(): bool
    {
        $this->deactivate();
        if (!$this->save()) {
            print_r($this->getErrors());
            throw new Exception(\Yii::t('app', 'Ошибка при блокировании пользователя'));
        }
        return true;
    }

    /**
     * разблокировка юзера
     * @return bool
     * @throws \Exception
     */
    public function unblockUser(): bool
    {
        $this->activate();
        if (!$this->save()) {
            throw new Exception(\Yii::t('app', 'Ошибка при разблокировке пользователя'));
        }
        return true;
    }

    /**
     * активация аккаунта после всех верификаций
     * @return bool
     * @throws Exception
     */
    public function verifyAccount(): bool
    {
        $this->activate();
        if (!$this->save()) {
            throw new Exception(\Yii::t('app', 'Ошибка при активации аккаунат'));
        }
        return true;
    }

    /**
     * TODO: need test
     * получает объект роли по ее названию
     * @param string $role_name
     * @return null|Role
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
    abstract public function getPrimaryRole(): string;

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
        if (static::findOne(['auth_key' => $token])) {
            return static::findOne(['auth_key' => $token]);
        }
        $class = new static();
        $access_token = $class->__userAccessTokenClass::findOne(['access_token' => $token]);
        if ($access_token instanceof $class->__userAccessTokenClass) {
            return static::findOne($access_token->user_id);
        }
        return null;
    }

    /**
     * получение API ключа
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * валидация API ключа
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
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
    public static function findByPasswordResetToken(int $expired, string $token = null): AbstractUser
    {
        if (!static::isPasswordResetTokenValid($expired, $token)) {
            throw new \RuntimeException(\Yii::t('app', 'Недействительный токен. Запросите сброс пароля еще раз.'));
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status_id' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * проверяет валидность токена сброса пароля (по времени)
     * @param int $expired
     * @param string $token
     * @return boolean
     */
    public static function isPasswordResetTokenValid(int $expired, string $token = null): bool
    {
        if ($token === null) {
            return false;
        }
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
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
     * отправка письма с ссылкой сброса пароля
     * необходима реализация метода в дочернем классе
     * @return bool
     */
    abstract public function sendPasswordResetMail(): bool;

    /**
     * поиск пользователя по e-mail
     * @param string $email
     * @return UserInterface|null
     */
    public static function findByEmail(string $email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * TODO: need test
     * возвращает всех пользователей заданной роли
     * @param string $role
     * @return array
     */
    public static function findByRole(string $role): array
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
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status_id' => [self::STATUS_WAIT_EMAIL]]);
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
    public function validatePassword(string $password): bool
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
        if ($reset_token === true) {
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
     * TODO: need test
     * Апгрейд пользователя до нужной роли
     * @param Role $role
     * @return bool
     * @throws \Exception
     */
    public function upgradeToRole(Role $role): bool
    {
        $auth = \Yii::$app->getAuthManager();
        if ($auth->assign($role, $this->id)) {
            return true;
        }
        return false;
    }

    /**
     * TODO: need test
     * Даунгрейд пользователя до нужной роли
     * @param Role $role
     * @return bool
     */
    public function downgradeFromRole(Role $role): bool
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
    public function deactivateTokens(): bool
    {
        $tokens = $this->getTokens()->all();
        if ($tokens) {
            foreach ($tokens as $token) {
                /** @var $token UserAccessTokenInterface */
                (new UserAccessTokenHandler($token))->deactivate();
            }
        }
        return true;
    }

    /**
     * TODO: need test
     * Проставляем токены и куки после логина
     * @param string $cookie_url
     * @return bool
     * @throws \Throwable
     * @deprecated spa
     */
    public function afterLogin(string $cookie_url): bool
    {
        /** @var AbstractUserAccessToken $token */
        $token = $this->__userAccessTokenClass::create($this, true);
        if ($token instanceof $this->__userAccessTokenClass && $token->is_active == true) {
            // записываем токен в куки
            if (\Yii::$app->getRequest()->getCookies()->getValue('auth_token')) {
                \Yii::$app->getResponse()->getCookies()->remove('auth_token');
            }
            setcookie('auth_token', $token->access_token, time() + DateHelper::TIME_DAY_THIRTY, '/', $cookie_url, false, false);
            return true;
        }
        return false;
    }

    /**
     * TODO: need test
     * Заменяет токен при заходе под другим юзером
     * @param string $token
     * @param string $cookie_url
     * @return bool
     * @deprecated spa
     */
    public function addCheckToken(string $token, string $cookie_url): bool
    {
        if (\Yii::$app->getRequest()->getCookies()->getValue('auth_token')) {
            \Yii::$app->getResponse()->getCookies()->remove('auth_token');
        }
        setcookie('auth_token', $token, time() + DateHelper::TIME_DAY_THIRTY, '/', $cookie_url, false, false);
        return true;
    }

    /**
     * Получить api токен для Header Bearer авторизации
     * @return bool|string
     * @deprecated spa
     */
    public function getToken(): string
    {
        if (\Yii::$app->session->get('auth_token')) { // берем из куки
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
    public function getCheckToken(): string
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
    public function afterLogout(): bool
    {
        $token = \Yii::$app->getRequest()->getCookies()->getValue('auth_token');
        $websiteToken = $this->__userAccessTokenClass::findOne(['user_id' => $this->id, 'access_token' => $token]);
        if ($websiteToken instanceof $this->__userAccessTokenClass) {
            $websiteToken->deactivate();
        }
        \Yii::$app->getResponse()->getCookies()->remove('auth_token');
        return true;
    }

    /**
     * TODO: need test
     * обновление времени последнего захода пользователя
     * @return bool
     * @throws Exception
     */
    public function updateLastEntering(): bool
    {
        $this->last_entering_date = time();
        if (!$this->save()) {
            throw new Exception(\Yii::t('app', 'Ошибка обновления даты входа'));
        }
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
    public function signUp(SignupForm $form): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->username = $form->username;
            $this->email = $form->email;
            $this->setPassword($form->password);
            $this->status_id = self::STATUS_WAIT_EMAIL;
            $this->generateAuthKey();
            $this->generateEmailConfirmToken();
            if (!$this->save()) {
                throw new \Exception('Ошибка при регистрации #1');
            }
            $this->afterSignUp($form->role);
            $this->sendEmailConfirmation();
            $this->processInvites();
            if ($form->affiliate_id !== null){
                $this->processAffiliate($form->affiliate_id);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException($e->getMessage());
        }
        return true;
    }

    /**
     * регистрация пользователя по email. логином будет email с замененнными символами @ и . на _
     * пароль будет сгенерирован и выслан на email
     * @param SignUpFormEmail $form
     * @return bool
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function signUpEmail(SignUpFormEmail $form): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->username = $form->username;
            $this->email = $form->email;
            $this->setPassword($form->password);
            $this->status_id = $form->status_id;
            $this->generateAuthKey();
            if ($this->status_id === self::STATUS_WAIT_EMAIL){
                $this->generateEmailConfirmToken();
            }
            if (!$this->save()) {
                throw new \Exception('Ошибка при регистрации #1');
            }
            $transaction->commit();
            $this->afterSignUpEmail($form->role);
            $this->sendEmailConfirmationWithPassword($form->password);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException($e->getMessage());
        }
        return true;
    }

    /**
     * регистрация пользователя по email. логином будет email с замененнными символами @ и . на _
     * пароль будет сгенерирован и выслан на email
     * @param SignUpFormConsole $form
     * @return bool
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function signUpConsole(SignUpFormConsole $form): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->username = $form->username;
            $this->email = $form->email;
            $this->setPassword($form->password);
            $this->status_id = $form->status_id;
            $this->generateAuthKey();
            if ($this->status_id === self::STATUS_WAIT_EMAIL){
                $this->generateEmailConfirmToken();
            }
            if (!$this->save()) {
                throw new \Exception('Ошибка при регистрации #1');
            }
            $transaction->commit();
            $this->afterSignUpConsole($form->role);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException($e->getMessage());
        }
        return true;
    }

    /**
     * метод, вызываемой после создания сущности пользователя. требует реализации в дочернем классе.
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string $role
     * @return mixed
     */
    abstract public function afterSignUp(string $role = null);

    /**
     * метод, вызываемой после создания сущности пользователя по email. требует реализации в дочернем классе.
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string $role
     * @return mixed
     */
    abstract public function afterSignUpEmail(string $role = null);

    /**
     * метод, вызываемый после создания сущности пользователя консольным способом (регистрации по апи и т.д.)
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * используется заглушка, чтобы не делать абстракт. если метод нужен - нужно переопределить на проекте
     * @param string|null $role
     * @return mixed
     */
    public function afterSignUpConsole(string $role = null)
    {
        return true;
    }

    /**
     * отправка письма с подтверждением e-mail
     * @return bool
     */
    abstract public function sendEmailConfirmation(): bool;

    /**
     * отправка письма с подтверждением e-mail и сгенерированным паролем
     * @param string $password
     * @return bool
     */
    abstract public function sendEmailConfirmationWithPassword(string $password): bool;

    /******************************************************************************************************************
     * СПИСКИ ПОЛЬЗОВАТЕЛЕЙ
     *****************************************************************************************************************/

    /**
     * список пользователей в виде id=>username
     * @return array
     */
    public static function getUsersArray(): array
    {
        return static::find()->select(['username', 'id'])->where(['status_id' => self::STATUS_ACTIVE])->indexBy('id')->orderBy(['id' => SORT_ASC])->column();
    }

    /**
     * список всех активных пользователей
     * @return array|mixed|AbstractUser[]|ActiveRecord[]
     */
    public static function getActiveUsers()
    {
        return static::find()->where(['status_id' => self::STATUS_ACTIVE])->all();
    }

    /**
     * //TODO: need test
     * отображаемое имя
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->userMeta->display_name;
    }

    /**
     * //todo: need test
     * получение ID телеграма пользователя
     * @return int|null
     */
    public function getTelegramId()
    {
        return $this->userMeta->notification_telegram;
    }

    /**
     * //todo: need test
     * установка ID телеграма пользователю
     * @param int $id
     * @param string $auth_key
     * @return null|AbstractUser
     */
    public static function setTelegramId(int $id, string $auth_key)
    {
        $user = static::findOne(['auth_key' => $auth_key]);
        if ($user !== null) {
            $user->userMeta->setTelegram($id);
            $user->telegramOn();
            return $user;
        }
        return null;
    }

    /**
     * //todo:need test
     * включение телеграма в уведомлениях
     * @return bool
     */
    abstract public function telegramOn(): bool;

    /**
     * //todo: need test
     * выключение телеграма в уведомлениях
     * @return bool
     */
    abstract public function telegramOff(): bool;

    /**
     * //todo: need test
     * собрать номер телефона
     * @return string
     */
    public function getPhone(): string
    {
        $string = '';
        if ($this->userMeta->notification_phone !== null) {
            $phone = new UserMetaPhone($this->userMeta->notification_phone);
            $string = $phone->country . $phone->number;
        }
        return $string;
    }

    /**
     * получить e-mail, на который отправлять уведомления
     * @return string
     */
    public function getNotificationEmail(): string
    {
        if ($this->userMeta->notification_email !== null && $this->userMeta->notification_email !== '') {
            return $this->userMeta->notification_email;
        }
        return $this->email;
    }

    /**
     * работа с аффилиатами при регистрации
     * @param int $affiliate_id
     * @return bool
     */
    public function processAffiliate(int $affiliate_id = null): bool
    {
        return true;
    }
}
