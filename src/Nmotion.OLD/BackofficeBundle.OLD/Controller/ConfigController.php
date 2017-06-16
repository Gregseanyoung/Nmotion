<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\Config;
use Nmotion\NmotionBundle\Form\ConfigType as ConfigTypeForm;

class ConfigController extends BaseRestController
{
    private function processForm(Config $config)
    {
        $statusCode = $config->isNew() ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $valueType = $config->isNew() ? null : $config->getType();
        $form = $this->createForm(new ConfigTypeForm($valueType), $config);
        $form->bind($this->getRequest());

        // dirty hack for setting boolean value
        $config->setSystem((bool)$this->getRequest()->get('system'));

        if ($form->isValid()) {
            /** @var EntityManager $entityManager */
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($config);
            $entityManager->flush();

            return $this->jsonResponseSuccessful('', [$config], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }

    /**
     * Get config instance by id
     *
     * @param int $id
     * @return Config
     * @throws NotFoundHttpException
     */
    private function getConfig($id)
    {
        $config = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Config')->find((int)$id);

        if (! $config instanceof Config) {
            throw new NotFoundHttpException('Config parameter not found with id: ' . $id);
        }

        return $config;
    }

    /**
     * POST /configs.json
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function postConfigsAction()
    {
        $this->setSerializerGroups(['backoffice']);

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $config = new Config();

        return $this->processForm($config);
    }

    /**
     * PUT /configs/{id}.json
     *
     * @param int $id
     *
     * @throws AccessDeniedException
     * @return Response json
     */
    public function putConfigAction($id)
    {
        $this->setSerializerGroups(['backoffice']);

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $config = $this->getConfig($id);

        return $this->processForm($config);
    }

    /**
     * GET /configs.json
     *
     * @return Response
     */
    public function getConfigsAction()
    {
        $this->setSerializerGroups(['backoffice']);

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $config = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Config')->findAll();

        return $this->jsonResponseSuccessful('', $config);
    }

    /**
     * GET /configs/{id}.json
     *
     * @param int $id
     *
     * @return Response
     */
    public function getConfigAction($id)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $config = $this->getConfig($id);

        return $this->jsonResponseSuccessful('', [$config]);
    }

    /**
     * DELETE /configs/{id}.json
     *
     * @param int $id
     *
     * @return Response json
     */
    public function deleteConfigAction($id)
    {
        $this->setSerializerGroups('backoffice');

        if (!$this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException();
        }

        $config = $this->getConfig($id);

        if ($config->isSystem()) {
            throw new AccessDeniedException('Can\'t delete system config parameter: ' . $config->getName());
        }

        $this->getDoctrine()->getManager()->remove($config);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }
}
