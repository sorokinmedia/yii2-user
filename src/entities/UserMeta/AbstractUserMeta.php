<?php
namespace sorokinmedia\user\entities\UserMeta;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\helpers\TextHelper;
use sorokinmedia\user\entities\{
    User\AbstractUser,User\UserInterface
};
use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Class AbstractUserMeta
 * @package sorokinmedia\user\entities\UserMeta
 *
 * @property integer $user_id
 * @property string $notification_email
 * @property integer $notification_phone
 * @property integer $notification_telegram
 * @property string $full_name
 * @property string $display_name
 * @property string $tz
 * @property string $location
 * @property string $about
 *
 * @property UserInterface $user
 * @property UserMetaForm $form
 */
abstract class AbstractUserMeta extends ActiveRecord implements UserMetaInterface, RelationInterface
{
    public $form;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_meta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'notification_phone', 'notification_telegram',], 'integer'],
            [['full_name', 'display_name', 'tz', 'location', 'about'], 'string'],
            [['notification_email'], 'email'],
            [['tz'], 'string', 'max' => 100],
            [['tz'], 'default', 'value' => 'Europe/Moscow'],
            [['location'], 'string', 'max' => 250],
            [['notification_email', 'full_name', 'display_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => AbstractUser::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'user_id' => \Yii::t('app', 'Пользователь'),
            'notification_email' => \Yii::t('app', 'E-mail для уведомлений'),
            'notification_phone' => \Yii::t('app', 'Телефон для уведомлений'),
            'notification_telegram' => \Yii::t('app', 'Telegram для уведомлений'),
            'full_name' => \Yii::t('app', 'Полное имя'),
            'display_name' => \Yii::t('app', 'Отображаемое имя'),
            'tz' => \Yii::t('app', 'Часовой пояс'),
            'location' => \Yii::t('app', 'Страна/Город'),
            'about' => \Yii::t('app', 'О себе'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
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
        if (!is_null($userMetaForm)){
            $this->form = $userMetaForm;
        }
        parent::__construct($config);
    }

    /**
     * трансфер данных из формы в модель
     */
    public function getFromForm()
    {
        if (!is_null($this->form)){
            $this->notification_email = $this->form->notification_email;
            $this->notification_phone = $this->form->notification_phone;
            $this->full_name = TextHelper::clearText($this->form->full_name);
            $this->tz = $this->form->tz;
            $this->location = TextHelper::clearText($this->form->location);
            $this->about = TextHelper::clearText($this->form->about);
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
    public static function create(UserInterface $user) : UserMetaInterface
    {
        /** @var AbstractUser $user */
        $user_meta = static::findOne(['user_id' => $user->id]);
        if ($user_meta instanceof UserMetaInterface){
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
    public function insertModel() : bool
    {
        if (!$this->insert()){
            throw new Exception(\Yii::t('app', 'Ошибка при добавлении меты'));
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function updateModel() : bool
    {
        $this->getFromForm();
        if ($this->full_name != ''){
            $this->display_name = $this->full_name;
        }
        if (!$this->save()){
            throw new Exception(\Yii::t('app', 'Ошибка при обновлении модели в БД'));
        }
        return true;
    }

    /**
     * добавляет ID телеграм чата
     * @param integer $chat_id
     * @throws Exception
     * @return bool
     */
    public function setTelegram(int $chat_id) : bool
    {
        $this->notification_telegram = $chat_id;
        if (!$this->save()){
            throw new Exception(\Yii::t('app','Ошибка при добавлении ID телеграм чата пользователю: setTelegramId'));
        }
        return true;
    }

    /**
     * получает ID телеграм чата по user_id
     * @param integer $chat_id
     * @return bool|int
     */
    public static function getTelegram(int $chat_id)
    {
        $user_meta = static::findOne(['notification_telegram' => $chat_id]);
        if ($user_meta){
            return $user_meta->notification_telegram;
        }
        return false;
    }
}