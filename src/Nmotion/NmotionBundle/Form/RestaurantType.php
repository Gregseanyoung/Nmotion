<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantType extends AbstractType
{
    /**
     * @var bool
     */
    private $isRestaurantAdmin;

    /**
     * @param bool $isRestaurantAdmin
     */
    public function __construct($isRestaurantAdmin = false)
    {
        $this->isRestaurantAdmin = $isRestaurantAdmin;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name')
            ->add('fullDescription')
            ->add('logoAsset', 'entity', ['class' => 'NmotionNmotionBundle:Asset', 'property' => 'id'])
            ->add('facebookPlaceId')
            ->add('feedbackUrl')
            ->add('videoUrl', null, ['required' => false])
            ->add('timeZone', null, ['disabled' => true])
            ->add('checkOutTime')
            ->add('visible', 'checkbox', ['required' => false])
            ->add('inHouse', 'checkbox', ['required' => false])
            ->add('takeaway', 'checkbox', ['required' => false])
            ->add('roomService', 'checkbox', ['required' => false])
            ->add('taMember', 'checkbox', ['required' => false, 'disabled' => $this->isRestaurantAdmin])
            ->add('email')
            ->add('phone')
            ->add('siteUrl')
            ->add('contactPersonName', 'text')
            ->add('contactPersonEmail')
            ->add('contactPersonPhone')
            ->add('legalEntity')
            ->add(
                'invoicingPeriod',
                'choice',
                [
                    'choices'  => [
                       'monthly' => 'monthly',
                       'weekly' => 'weekly',
                       '14 days' => '14 days',
                    ],
                    'required' => false,
                    'disabled' => $this->isRestaurantAdmin
                ]
            )
            ->add('currency', 'entity',['class' => 'NmotionNmotionBundle:Currency', 'property' => 'id','required' => false, 'disabled' => $this->isRestaurantAdmin])
            ->add('vatNo', 'text', ['disabled' => $this->isRestaurantAdmin])
            ->add('regNo', 'text', ['disabled' => $this->isRestaurantAdmin])
            ->add('kontoNo', 'text', ['disabled' => $this->isRestaurantAdmin])
            ->add('address', new RestaurantAddressType())
            ->add('adminUser', new RestaurantUserType(), ['disabled' => $this->isRestaurantAdmin])
            ->add(
                'operationTimes',
                'collection',
                [
                    'type'         => new RestaurantOperationTimeType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ]
            )
            ->add('menuCategories', null, ['disabled' => true, 'mapped' => false])
            ->add('menuMeals', null, ['disabled' => true, 'mapped' => false])
            ->add('createdAt', 'integer', ['disabled' => true])
            ->add('updatedAt', 'integer', ['disabled' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\Restaurant',
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
