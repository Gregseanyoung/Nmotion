<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\Currency;
use Nmotion\NmotionBundle\Form\Currency as CurrencyForm;

class CurrencyController extends BaseRestController
{
    private function processForm(Currency $currency)
    {
        $statusCode = $currency->isNew() ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $form = $this->createForm(new CurrencyForm(), $currency);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            /** @var EntityManager $entityManager */
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($currency);
            $entityManager->flush();

            return $this->jsonResponseSuccessful('', [$currency], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }
    
    /**
     * GET /currencies.json
     *
     * @return Response
     */
    public function getCurrenciesAction()
    {
        $this->setSerializerGroups(['backoffice']);

        $config = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Currency')->findAll();

        return $this->jsonResponseSuccessful('', $config);
    }
    
    /**
     * Get currency instance by id
     *
     * @param int $id
     * @return Currency
     * @throws NotFoundHttpException
     */
    private function getCurrency($id)
    {
        $currency = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Currency')->find((int)$id);

        if (! $currency instanceof Currency) {
            throw new NotFoundHttpException('Config parameter not found with id: ' . $id);
        }

        return $currency;
    }
    
    /**
     * POST /currencies.json
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function postCurrenciesAction()
    {
        $this->setSerializerGroups(['backoffice']);

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $currency = new Currency();

        return $this->processForm($currency);
    }
    
    /**
     * PUT /currencies/{id}.json
     *
     * @param int $id
     *
     * @throws AccessDeniedException
     * @return Response json
     */
    public function putCurrenciesAction($id)
    {
        $this->setSerializerGroups(['backoffice']);

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $currency = $this->getCurrency($id);

        return $this->processForm($currency);
    }
}
?>
