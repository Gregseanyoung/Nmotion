<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Nmotion\NmotionBundle\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueUserEmailValidator extends ConstraintValidator
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
     * @param User       $entity
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     */
    public function validate($entity, Constraint $constraint)
    {
        if (! $entity instanceof User) {
            throw new UnexpectedTypeException($entity, 'User');
        }

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
        } else {
            $em = $this->registry->getManagerForClass(get_class($entity));
        }

        $repository = $em->getRepository('NmotionNmotionBundle:User');
        $resultUser = $repository->findBy(['email' => $entity->getEmail()]);

        /* If no User matched the email or result user is the same as requested then email is unique. */
        if (count($resultUser) === 0
            || (count($resultUser) === 1
                && $entity === ($resultUser instanceof \Iterator ? $resultUser->current() : current($resultUser)))
        ) {
            return;
        }

        $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : 'email';

        $this->context->addViolationAt($errorPath, $constraint->message, [], $entity->getEmail());
    }
}
