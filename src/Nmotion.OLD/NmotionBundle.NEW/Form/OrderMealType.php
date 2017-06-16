<?php

namespace Nmotion\NmotionBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderMealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('mealComment')
            ->add('name', 'text', ['disabled' => true])
            ->add('description', 'text', ['disabled' => true])
            ->add('price', 'number', ['disabled' => true])
            ->add('discountPercent', 'integer', ['disabled' => true])
            ->add('mealOptionName', 'text', ['disabled' => true])
            ->add('mealOptionPrice', 'number', ['disabled' => true])
            ->add('mealComment')
            ->add('quantity')
            ->add(
                'meal',
                'entity',
                [
                    'class'    => 'Nmotion\NmotionBundle\Entity\Meal',
                    'property' => 'id',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('m');
                    }
                ]
            )
            ->add(
                'mealOption',
                'entity',
                [
                    'class'    => 'Nmotion\NmotionBundle\Entity\MealOption',
                    'property' => 'id',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('o');
                    }
                ]
            )
            ->add(
                'orderMealExtraIngredients',
                'collection',
                [
                    'type'         => new OrderMealExtraIngredientType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ]
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'           => 'Nmotion\NmotionBundle\Entity\OrderMeal',
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
