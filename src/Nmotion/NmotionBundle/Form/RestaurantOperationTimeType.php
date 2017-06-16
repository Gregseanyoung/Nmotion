<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantOperationTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('dayOfTheWeek')
            ->add('timeFrom')
            ->add('timeTo');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'      => 'Nmotion\NmotionBundle\Entity\RestaurantOperationTime',
                 'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        // no name by default
        return '';
    }
}
