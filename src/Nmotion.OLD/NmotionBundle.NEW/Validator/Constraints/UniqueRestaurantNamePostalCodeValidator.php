<?php

namespace Nmotion\NmotionBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Nmotion\NmotionBundle\Entity\Repositories\RestaurantRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Nmotion\NmotionBundle\Entity\Restaurant;

/**
 * Validator checks if restaurant name and address postal code are unique
 *
 * @author samva <vas@ciklum.com>
 */
class UniqueRestaurantNamePostalCodeValidator extends ConstraintValidator
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Restaurant $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
        } else {
            $em = $this->registry->getManagerForClass(get_class($entity));
        }

        /** @var $repository RestaurantRepository */
        $repository = $em->getRepository('NmotionNmotionBundle:Restaurant');
        $postalCode = $entity->getAddress() ? $entity->getAddress()->getPostalCode() : null;
        $result     = $repository->findByNameAndPostalCode($entity->getName(), $postalCode);

        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($result instanceof \Iterator) {
            $result->rewind();
        }

        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */
        if (0 === count($result) || (1 === count($result)
            && $entity === ($result instanceof \Iterator ? $result->current() : current($result)))
        ) {
            return;
        }

        $this->context->addViolationAt('name', $constraint->message, array(), 'name');
    }
}
