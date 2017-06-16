<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Entity;

class RestaurantGuest extends User
{
    public function __construct()
    {
        parent::__construct();

        $this->setRole(self::ROLE_RESTAURANT_GUEST);
        $this->addRole(self::ROLE_RESTAURANT_GUEST);
    }
}
