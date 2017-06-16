<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Entity;

trait EntityAux
{
    /**
     * Returns true if entity does not have an ID, and false if it does
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
