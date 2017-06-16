<?php

namespace Nmotion\NmotionBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderMealExtraIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name', 'text', ['disabled' => true])
            ->add('price', 'number', ['disabled' => true])
            ->add(
                'mealExtraIngredient',
                'entity',
                [
                    'class'    => 'Nmotion\NmotionBundle\Entity\MealExtraIngredient',
                    'property' => 'id',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('i');
                    }
                ]
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'           => 'Nmotion\NmotionBundle\Entity\OrderMealExtraIngredient',
                'csrf_protection'      => false,
                'extra_fields_message' => 'This form should not contain extra fields.{{ extra_fields }}'
            ]
        );
    }

    public function getName()
    {
        return '';
    }
}
