<?php

namespace Nmotion\NmotionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Nmotion\NmotionBundle\Form\Type\ResetPasswordFormType;

class UserController extends Controller
{
    public function forgotPasswordAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email', 'email', ['label' => 'form.email', 'translation_domain' => 'NmotionNmotionBundle'])
            ->getForm();

        return $this->render('NmotionNmotionBundle:User:forgot_password.html.twig', ['form' => $form->createView()]);
    }

    public function resetPasswordAction($token, Request $request = null)
    {
        $user = $this
            ->getDoctrine()
            ->getRepository('NmotionNmotionBundle:User')
            ->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw new HttpException(404, 'User not found');
        }

        $form = $this->createForm(new ResetPasswordFormType('Nmotion\NmotionBundle\Entity\User'), $user);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $user->setConfirmationToken(null);
                $this->get('fos_user.user_manager')->updateUser($user, true);
                return $this->render('NmotionNmotionBundle:User:reset_password_success.html.twig', ['user' => $user]);
            }
        }

        return $this->render(
            'NmotionNmotionBundle:User:reset_password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
