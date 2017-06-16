<?php

namespace Nmotion\NmotionBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class ResetPasswordFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'plainPassword',
            'repeated',
            [
                'type'              => 'password',
                'options'           => array('translation_domain' => 'NmotionNmotionBundle'),
                'first_options'     => array('label' => 'form.new_password'),
                'second_options'    => array('label' => 'form.new_password_confirmation')
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(['validation_groups' => ['registration']]);
    }

    public function getName()
    {
        return 'nmotion_user_reset_password';
    }
}
