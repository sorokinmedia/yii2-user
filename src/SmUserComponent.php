<?php
namespace sorokinmedia\user;

use yii\base\Component;

class SmUserComponent extends Component
{
    public $user_class;
    public $user_relation_trait;

    public function init()
    {
        parent::init();
    }

    public function getUserClass()
    {
        return $this->user_class;
    }

    public function getUserRelationTrait()
    {
        return $this->user_relation_trait;
    }
}