<?php

namespace Nmotion\NmotionBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add(
                'order',
                'entity',
                [
                    'required'      => false,
                    'class'         => 'Nmotion\NmotionBundle\Entity\Order',
                    'property'      => 'id',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('o');
                    }
                ]
            )
            ->add('status', 'text', ['required' => false])
            ->add('amount', 'number', ['required' => false])
            ->add('fee', 'number', ['required' => false])
            ->add('currency', 'text', ['required' => false])
            ->add('test', 'text', ['required' => false])
            ->add('transaction', 'text', ['required' => false])
            ->add('acquirer', 'text', ['required' => false])
            ->add('cardNumberMasked', 'text', ['required' => false])
            ->add('expMonth', 'text', ['required' => false])
            ->add('expYear', 'text', ['required' => false])
            ->add('cardTypeName', 'text', ['required' => false])
            ->add('merchant', 'text', ['required' => false])
            ->add('ticket', 'text', ['required' => false])
            ->add('allParameters')
            ->add('paymentComment', 'text', ['required' => false])
            ->add('createdAt', 'integer', ['disabled' => true])
            ->add('updatedAt', 'integer', ['disabled' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'           => 'Nmotion\NmotionBundle\Entity\Payment',
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
