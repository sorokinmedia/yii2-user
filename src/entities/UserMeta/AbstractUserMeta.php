<?php

namespace sorokinmedia\user\entities\UserMeta;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\helpers\TextHelper;
use sorokinmedia\user\entities\{User\AbstractUser,
    User\UserInterface,
    UserMeta\json\UserMetaFullName,
    UserMeta\json\UserMetaPhone};
use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use Yii;

/**
 * Class AbstractUserMeta
 * @package sorokinmedia\user\entities\UserMeta
 *
 * @property int $user_id
 * @property string $notification_email
 * @property UserMetaPhone $notification_phone
 * @property int $notification_telegram
 * @property UserMetaFullName $full_name
 * @property string $display_name
 * @property string $tz
 * @property string $location
 * @property string $about
 * @property array $custom_fields
 *
 * @property UserInterface $user
 * @property UserMetaForm $form
 */
abstract class AbstractUserMeta extends ActiveRecord implements UserMetaInterface, RelationInterface
{
    public $form;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_meta';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'notification_telegram',], 'integer'],
            [['tz', 'location', 'about'], 'string'],
            [['notification_email'], 'email'],
            [['tz'], 'string', 'max' => 100],
            [['tz'], 'default', 'value' => 'Europe/Moscow'],
            [['location'], 'string', 'max' => 250],
            [['notification_email'], 'string', 'max' => 255],
            [['display_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => AbstractUser::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'user_id' => Yii::t('sm-user', 'Пользователь'),
            'notification_email' => Yii::t('sm-user', 'E-mail для уведомлений'),
            'notification_phone' => Yii::t('sm-user', 'Телефон для уведомлений'),
            'notification_telegram' => Yii::t('sm-user', 'Telegram для уведомлений'),
            'full_name' => Yii::t('sm-user', 'Полное имя'),
            'display_name' => Yii::t('sm-user', 'Отображаемое имя'),
            'tz' => Yii::t('sm-user', 'Часовой пояс'),
            'location' => Yii::t('sm-user', 'Страна/Город'),
            'about' => Yii::t('sm-user', 'О себе'),
            'custom_fields' => Yii::t('sm-user', 'Дополнительные данные'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne($this->__userClass, ['id' => 'user_id']);
    }

    /**
     * UserMeta constructor.
     * @param array $config
     * @param UserMetaForm|null $userMetaForm
     */
    public function __construct(array $config = [], UserMetaForm $userMetaForm = null)
    {
        if ($userMetaForm !== null) {
            $this->form = $userMetaForm;
        }
        parent::__construct($config);
    }

    /**
     * трансфер данных из формы в модель
     */
    public function getFromForm(): void
    {
        if ($this->form !== null) {
            $this->notification_email = $this->form->notification_email;
            if ($this->form->full_name !== '') {
                $this->full_name = $this->form->full_name;
            }
            $this->display_name = $this->form->display_name;
            $this->tz = $this->form->tz;
            $this->location = TextHelper::clearText($this->form->location);
            $this->about = TextHelper::clearText($this->form->about);
            $this->custom_fields = $this->form->custom_fields;
        }
    }

    /**
     * статический конструктор
     * @param UserInterface $user
     * @return UserMetaInterface
     * @throws Exception
     * @throws \Exception
     * @throws \Throwable
     */
    public static function create(UserInterface $user): UserMetaInterface
    {
        /** @var AbstractUser $user */
        $user_meta = static::findOne(['user_id' => $user->id]);
        if ($user_meta instanceof UserMetaInterface) {
            return $user_meta;
        }
        $user_meta = new static([
            'user_id' => $user->id,
            'notification_email' => $user->email,
            'display_name' => $user->username,
        ]);
        (new UserMetaHandler($user_meta))->create();
        $user_meta->refresh();
        return $user_meta;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function insertModel(): bool
    {
        if (!$this->insert()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при добавлении меты'));
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function updateModel(): bool
    {
        $this->getFromForm();
        if (!$this->save()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при обновлении модели в БД'));
        }
        return true;
    }

    /**
     * добавляет ID телеграм чата
     * @param integer $chat_id
     * @throws Exception
     * @return bool
     */
    public function setTelegram(int $chat_id): bool
    {
        $this->notification_telegram = $chat_id;
        if (!$this->save()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при добавлении ID телеграм чата пользователю: setTelegramId'));
        }
        return true;
    }

    /**
     * получает ID телеграм чата по user_id
     * @param integer $chat_id
     * @return null|int
     */
    public static function checkTelegram(int $chat_id): ?int
    {
        $user_meta = static::findOne(['notification_telegram' => $chat_id]);
        if ($user_meta) {
            return $user_meta->notification_telegram;
        }
        return null;
    }

    /**
     * сброс ID телеграма
     * @return bool
     * @throws Exception
     */
    public function resetTelegram(): bool
    {
        $this->notification_telegram = null;
        if (!$this->save()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при сбросе ID телеграм'));
        }
        return true;
    }

    /**
     * добавить номер телефона в профиль
     * @param UserMetaPhone $userMetaPhone
     * @return bool
     * @throws Exception
     */
    public function setPhone(UserMetaPhone $userMetaPhone): bool
    {
        $this->notification_phone = $userMetaPhone;
        return $this->updateModel();
    }

    /**
     * верификация номер телефона
     * @return bool
     * @throws Exception
     */
    public function verifyPhone(): bool
    {
        $phone = new UserMetaPhone($this->notification_phone);
        $phone->verifyPhone();
        $this->notification_phone = $phone;
        return $this->updateModel();
    }

    /**
     * @param UserMetaFullName $userMetaFullName
     * @return bool
     * @throws Exception
     */
    public function setFullName(UserMetaFullName $userMetaFullName): bool
    {
        $this->full_name = $userMetaFullName;
        if (!$this->save()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при сохранении полного имени'));
        }
        return true;
    }

    /**
     * //TODO: need test
     * дает варианты для выбора отображаемого имени
     * @return array
     */
    public function getDisplayNameVariants(): array
    {
        $array[] = $this->user->username;
        if ($this->full_name !== null) {
            $full_name = new UserMetaFullName($this->full_name);
            if ($full_name->surname !== '' && $full_name->name !== ''){
                $array[] = $full_name->surname . ' ' . $full_name->name;
            }
            if ($full_name->name !== '' && $full_name->name !== null){
                $array[] = $full_name->name;
            }
        }
        return $array;
    }

    /**
     * дает варианты для выбора отображаемого имени, ассоциативный массив
     * @return array
     */
    public function getDisplayNameVariantsArray(): array
    {
        $array = $this->getDisplayNameVariants();
        $assoc_array = [];
        foreach ($array as $value) {
            $assoc_array[$value] = $value;
        }
        return $assoc_array;
    }
}
