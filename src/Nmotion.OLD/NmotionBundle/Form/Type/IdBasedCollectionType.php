<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Nmotion\NmotionBundle\Form\EventListener\IdBasedCollectionListener;
use Nmotion\NmotionBundle\Form\EventListener\IdBasedCollectionFormErrorKeyFixerListener;

class IdBasedCollectionType extends CollectionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // should be added before parent
        $subscriber = new IdBasedCollectionListener(
            $builder->getFormFactory(),
            $options['type'],
            $options['options']
        );
        $builder->addEventSubscriber($subscriber);

        $rootFormBuilder = $builder;
        while ($rootFormBuilder->hasParent()) {
            $rootFormBuilder = $rootFormBuilder->getParent();
        }

        $subscriber = new IdBasedCollectionFormErrorKeyFixerListener($builder);
        $rootFormBuilder->addEventSubscriber($subscriber);

        parent::buildForm($builder, $options);
    }
}
