<?php

namespace Nmotion\NmotionBundle\Controller;

use Symfony\Component\Form\Form;

trait FormTrait
{
    /**
     * Returns array of form errors in readable format
     *
     * @param Form $form
     *
     * @return array
     */
    public function getFormErrorMessages(Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template   = $this->get('translator')->trans($error->getMessageTemplate(), [], 'validators');
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }
            $errors[$key] = $template;
        }
        if (count($form->all())) {
            foreach ($form->all() as $child) {
                if (! $child->isValid()) {
                    $errors[$child->getName()] = $this->getFormErrorMessages($child);
                }
            }
        }

        return $errors;
    }
}
