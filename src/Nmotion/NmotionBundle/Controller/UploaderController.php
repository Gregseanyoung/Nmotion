<?php

namespace Nmotion\NmotionBundle\Controller;

use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\Rest\Util\Codes;
use Nmotion\NmotionBundle\Entity\Asset;
use Nmotion\NmotionBundle\Form\Type\FileUploadFormType;

class UploaderController extends FOSRestController
{
    /**
     * POST /file
     *
     * @return Response json
     */
    public function postFileAction()
    {
        $asset = new Asset();
        $form  = $this->createForm(new FileUploadFormType(), $asset);
        $form->bind($this->getRequest());

        $result     = [];
        $statusCode = null;

        if ($asset->getFile()) {
            $assetExisted = $this->getDoctrine()
                ->getRepository('NmotionNmotionBundle:Asset')
                ->findOneBy(['md5' => md5_file($asset->getFile()->getPathname())]);

            $result['success'] = true;
            $result['total']   = 1;

            if ($assetExisted === null) {
                $asset->upload(
                    $this->container->getParameter('nmotion_nmotion.upload.root_dir')
                );

                $em = $this->getDoctrine()->getManager();
                $em->persist($asset);
                $em->flush();

                $result     = [
                    'success' => true,
                    'entries' => [$asset],
                    'total'   => 1
                ];
                $statusCode = codes::HTTP_CREATED;
            } else {
                $result     = [
                    'success' => true,
                    'entries' => [$assetExisted],
                    'total'   => 1
                ];
                $statusCode = codes::HTTP_OK;
            }
        } else {
            $result = [
                'success' => false,
                'errors'  => ['File is required']
            ];
            $statusCode = codes::HTTP_PRECONDITION_FAILED;
        }

        /** @var $viewHandler ViewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        $view = $this->view($result, $statusCode);
        $context = $viewHandler->getSerializationContext($view);
        $context->setGroups(['backoffice']);
        $view->setSerializationContext($context);

        $response = $this->handleView($view);

        $userAgent = $this->getRequest()->server->get('HTTP_USER_AGENT');
        if (strpos($userAgent, 'MSIE') !== false) {
            // FUCKING IE, SUKO!!!! DAMNIT MICROSOFT'S PIECE OF SHIT!!!!
            $response->headers->set('Content-Type', 'text/plain');
        }

        return $response;
    }
}
