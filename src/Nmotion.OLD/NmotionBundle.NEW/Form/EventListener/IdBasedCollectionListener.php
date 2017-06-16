<?php

/**
 * @author Sergey Shupylo <sshu@ciklum.com>
 */

namespace Nmotion\NmotionBundle\Form\EventListener;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IdBasedCollectionListener implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    public function __construct(FormFactoryInterface $factory, $type, array $options = array())
    {
        $this->factory = $factory;
        $this->type    = $type;
        $this->options = $options;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND     => 'preBind'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof PersistentCollection) {
            $data = $data->unwrap();
        }

        if (! $data instanceof ArrayCollection) {
            throw new UnexpectedTypeException($data, 'Doctrine\Common\Collections\ArrayCollection');
        }

        $elements        = $data->toArray();
        $arrayCollection = $data;
        $arrayCollection->clear();

        foreach ($elements as $entity) {
            $arrayCollection->set($entity->getId(), $entity);
        }
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data || '' === $data) {
            $data = [];
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        $transformedData = [];

        // first pass: resolve existing entities
        foreach ($data as $value) {
            if (array_key_exists('id', $value) && ! empty($value['id'])) {
                $transformedData[$value['id']] = $value;
            }
        }

        // second pass: resolve new entities
        foreach ($data as $value) {
            if (! array_key_exists('id', $value) || empty($value['id'])) {
                $transformedData[] = $value;
            }
        }

        $event->setData($transformedData);
    }
}
