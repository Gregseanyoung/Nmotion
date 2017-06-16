<?php

namespace Nmotion\NmotionBundle\Form\RestaurantImport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name')
            ->add('timeFrom')
            ->add('timeTo')
            ->add('discountPercent', 'integer')
            ->add('visible')
            ->add('position', 'integer')
            ->add(
                'meals',
                'collection',
                [
                    'type'         => new MealType,
                    'allow_add'    => true,
                    'by_reference' => false
                ]
            )
            ->add('createdAt', 'integer')
            ->add('updatedAt', 'integer');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\MenuCategory',
                 'csrf_protection'      => false,
                 'cascade_validation'   => true,
                 'extra_fields_message' => 'This form should not contain extra fields.{{ extra_fields }}'
            )
        );
    }

    public function getName()
    {
        // no name by default
        return '';
    }
}
