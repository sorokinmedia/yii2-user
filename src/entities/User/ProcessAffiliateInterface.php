<?php
namespace sorokinmedia\user\entities\User;

/**
 * Interface ProcessAffiliateInterface
 * @package sorokinmedia\user\entities\User
 */
interface ProcessAffiliateInterface
{
    /**
     * работа с аффилиатами при регистрации
     * @param int $affiliate_id
     * @return bool
     */
    public function processAffiliate(int $affiliate_id): bool;
}