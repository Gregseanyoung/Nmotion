<?php

/**
 * @author Sergey Shupylo <sshu@ciklum.com>
 */

namespace Nmotion\NmotionBundle\Form\EventListener;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class IdBasedCollectionFormErrorKeyFixerListener implements EventSubscriberInterface
{
    private $builder;

    public function __construct(FormBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_BIND => 'postBind'
        ];
    }

    public function postBind(FormEvent $event)
    {
        /** @var $form Form|Form[] */
        $form = $event->getForm();

        if (! $form->has($this->builder->getName())) {
            return;
        }

        $targetForm = $form[$this->builder->getName()];
        $targetData = $targetForm->getData();

        if (is_array($targetData)) {
            return;
        }

        $childForms = array_values($targetForm->all());
        foreach ($childForms as $index => $childForm) {
            $formConfig = $childForm->getConfig();
            $reflection = new \ReflectionProperty('Symfony\Component\Form\FormConfigBuilder', 'name');
            $reflection->setAccessible(true);
            $reflection->setValue($formConfig, $index);
        }

        $reflection = new \ReflectionProperty(get_class($targetForm), 'children');
        $reflection->setAccessible(true);
        $reflection->setValue($targetForm, $childForms);

        if ($targetData instanceof PersistentCollection) {
            $targetData = $targetData->unwrap();
        }

        if (! $targetData instanceof ArrayCollection) {
            throw new UnexpectedTypeException($targetForm, 'Doctrine\Common\Collections\ArrayCollection');
        }

        $elements        = $targetData->toArray();
        $arrayCollection = $targetData;
        $arrayCollection->clear();

        foreach ($elements as $entity) {
            $arrayCollection[] = $entity;
        }

    }
}
