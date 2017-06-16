<?php

namespace Nmotion\BackofficeBundle\Controller;

use Nmotion\NmotionBundle\Util\RestaurantExport;
use Nmotion\NmotionBundle\Util\RestaurantImport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Controller\FormTrait;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\Restaurant;
use Nmotion\NmotionBundle\Entity\RestaurantAdmin;
use Nmotion\NmotionBundle\Entity\RestaurantStaff;
use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Form\RestaurantType as RestaurantTypeForm;
use Nmotion\NmotionBundle\Util\PrinterMailbox;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;

class RestaurantsController extends BackofficeController
{
    use FormTrait;
    use RestaurantAssertAccess;

    const RESTAURANT_ADMIN_DEFAULT_PASSWORD = '123123';

    /**
     * @return PrinterMailbox
     */
    private function getPrinterMailboxService()
    {
        return $this->get('nmotion.printer.mailbox');
    }

    private function processForm(Restaurant $restaurant)
    {
        $action = $restaurant->getId() ? 'edit' : 'new';

        $statusCode = $action == 'new' ? Codes::HTTP_CREATED : Codes::HTTP_OK;

        $request = $this->getRequest();
        $oldAdminUserEmail = $restaurant->getAdminUser()->getEmail();

        // hack for being able to set boolean value
        foreach (['visible', 'inHouse', 'takeaway', 'roomService', 'taMember', 'nmWebsite'] as $key) {
            if ($request->get($key)) {
                $request->request->set($key, 1);
            } else {
                $request->request->remove($key);
            }
        }

        $isRestaurantAdmin = $this->getUser() instanceof RestaurantAdmin;
        $form = $this->createForm(new RestaurantTypeForm($isRestaurantAdmin), $restaurant);
        
        if (!$request->request->has('currency')) {
            $form->remove('currency');
        }
        
        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $newAdminUser = true;

            if ($action == 'new') {
                $this->setConfirmationTokenForAdminUser($restaurant->getAdminUser());
                $this->getUserManager()->updateUser($restaurant->getAdminUser(), false);
            } elseif ($oldAdminUserEmail != $restaurant->getAdminUser()->getEmail()) {
                $restaurant->getAdminUser()->setUsername($restaurant->getAdminUser()->getEmail());
                $this->setConfirmationTokenForAdminUser($restaurant->getAdminUser());
                $this->getUserManager()->updateUser($restaurant->getAdminUser(), false);
            } else {
                $restaurant->getAdminUser()->setPlainPassword(null);
                $newAdminUser = false;
            }

            $this->getDoctrine()->getManager()->persist($restaurant);
            $this->getDoctrine()->getManager()->flush();

            if ($action == 'new') {
                $this->getPrinterMailboxService()->createNewMailbox($restaurant->getId());
            }

            if ($newAdminUser) {
                $this->sendEmailWithConfirmationTokenToNewAdmin($restaurant);
            }

            return $this->jsonResponseSuccessful('', [$restaurant], $statusCode);
        }

        return $this->jsonResponseFailed('Validation failed', [$form], Codes::HTTP_PRECONDITION_FAILED);
    }

    /**
     * Set auto generated confirmation token for new restaurant admin
     *
     * @param User $adminUser
     */
    private function setConfirmationTokenForAdminUser($adminUser)
    {
        // generate unique token and save to user field
        $adminUser->setConfirmationToken($this->getGeneratedUniqueToken());
    }

    /**
     * Send email with confirmation token to just created admin
     *
     * @param Restaurant $restaurant
     */
    private function sendEmailWithConfirmationTokenToNewAdmin(Restaurant $restaurant)
    {
        // send message to user with link with new token
        $message = \Swift_Message::newInstance()
            ->setSubject('Nmotion restaurant admin account created')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($restaurant->getAdminUser()->getEmail())
            ->setBody(
                $this->renderView(
                    'NmotionBackofficeBundle:Restaurant:restaurant_admin_account_created.txt.twig',
                    ['restaurant' => $restaurant]
                )
            );
        // actual send
        $this->get('mailer')->send($message);
    }

    /**
     * @return User
     * @throws AccessDeniedException
     */
    public function getUser()
    {
        $user = parent::getUser();

        if (! $user instanceof User) {
            throw new AccessDeniedException;
        }

        return $user;
    }

    /**
     * GET /restaurants
     *
     * @throws AccessDeniedException
     * @return Response json
     */
    public function getRestaurantsAction()
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.list']);

        $repo = $this->getRepository();

        if ($this->isSolutionAdmin()) {
            $searchFilter = null;
            $filterSet = $this->getRequest()->get('filter');
            if ($filterSet && is_array($filterSet)) {
                foreach ($filterSet as $filter) {
                    if ($filter['property'] === 'search') {
                        $searchFilter = $filter['value'];
                    }
                }
            }

            $orderBy = null;
            if ($this->getRequest()->get('sort')) {
                $orderBy = [
                    $this->getRequest()->get('sort') => ($this->getRequest()->get('order') === 'DESC' ? 'DESC' : 'ASC')
                ];
            }

            $start       = $this->getRequest()->get('start');
            $limit       = $this->getRequest()->get('limit');
            $restaurants = [];

            if ($searchFilter) {
                $total = $repo->getCountForFindByIdOrNameOrAdminEmail($searchFilter);
                if ($total) {
                    $restaurants = $repo->findByIdOrNameOrAdminEmail($searchFilter, $orderBy, $limit, $start);
                }
            } else {
                $total = $repo->getCountAllRestaurants();
                
                if ($total) {
                    $restaurants = $repo->findBy([], $orderBy, $limit, $start);
                }
                
            }
        } elseif ($this->isRestaurantAdmin() || $this->isRestaurantStaff()) {
            $user = $this->getUser();
            
            if ($user instanceof RestaurantAdmin) {
                $restaurants = [$user->getRestaurant()];
            } elseif ($user instanceof RestaurantStaff) {
                $restaurants = [$user->getRestaurant()];
            }
            $total = 1;
        } else {
            throw new AccessDeniedException;
        }

        return $this->entriesResponse($restaurants, $total);
    }

    /**
     * GET /restaurants/{id}
     *
     * @param integer $id
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response json
     */
    public function getRestaurantAction($id)
    {
        $this->assertUserHasAccessToRestaurantRead($id);

        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $restaurant = $this->getRestaurant($id);
        var_dump($restaurant); die;
        return $this->jsonResponseSuccessful('', [$restaurant]);
    }

    /**
     * GET /restaurants/{id}/export
     *
     * @param integer $id
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response json
     */
    public function getRestaurantExportAction($id)
    {
        if (! $this->isSolutionAdmin()) {
            throw new AccessDeniedException;
        }

        $this->setSerializerGroups(['backoffice', 'backoffice.entity', 'export']);

        $restaurant = $this->getRestaurant($id);

        /** @var RestaurantExport $restaurantExport */
        $restaurantExport = $this->get('nmotion.restaurant.export');
        $export = $restaurantExport->exportRestaurant($restaurant);

        return $this->view($export, 200);
    }

    /**
     * POST /restaurants
     *
     * @throws AccessDeniedException
     *
     * @return Response json
     */
    public function postRestaurantsAction()
    {
        if (! $this->isSolutionAdmin()) {
            throw new AccessDeniedException;
        }

        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $restaurant = new Restaurant;

        /** @var $adminUser User */
        $adminUser = (new RestaurantAdmin())
            ->setPlainPassword(self::RESTAURANT_ADMIN_DEFAULT_PASSWORD);

        $requestAdminUser = $this->getRequest()->get('adminUser');
        if (!empty($requestAdminUser) && isset($requestAdminUser['email'])) {
            $adminUser->setUsername($requestAdminUser['email']);
        }

        $restaurant->setAdminUser($adminUser);

        return $this->processForm($restaurant);
    }

    /**
     * POST /restaurants/import
     *
     * @throws AccessDeniedException
     *
     * @return Response json
     */
    public function postRestaurantsImportAction()
    {
        if (! $this->isSolutionAdmin()) {
            throw new AccessDeniedException;
        }

        /** @var RestaurantImport $restaurantImport */
        $restaurantImport = $this->get('nmotion.restaurant.import');

        try {
            $restaurantImport->importFromRequest();
        } catch (\RuntimeException $e) {
            return new Response($e->getMessage());
        }

        return new Response('Ok');
    }

    /**
     * PUT /restaurants/{id}
     *
     * @param integer $id
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response json
     */
    public function putRestaurantAction($id)
    {
        $this->assertUserHasAccessToRestaurantUpdate($id);

        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $restaurant = $this->getRestaurant($id);
        $restaurant->getAdminUser()->setPlainPassword(self::RESTAURANT_ADMIN_DEFAULT_PASSWORD);

        return $this->processForm($restaurant);
    }

    /**
     * DELETE /restaurants/{id}
     *
     * @param integer $id
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     * @throws PreconditionFailedException
     *
     * @return Response json
     */
    public function deleteRestaurantAction($id)
    {
        if (! $this->isSolutionAdmin()) {
            throw new AccessDeniedException;
        }

        $restaurant = $this->getRestaurant($id);

        $order = $this->getRepository('Order')->findOneByRestaurant($restaurant);
        if ($order instanceof Order) {
            throw new PreconditionFailedException('Can\'t delete restaurant with existing orders');
        }

        $this->getDoctrine()->getManager()->remove($restaurant);
        $this->getDoctrine()->getManager()->flush();

        return $this->jsonResponseSuccessful();
    }

    /**
     * GET /backoffice/restaurants/{restaurantId}/income?period={period}
     *
     * @param $restaurantId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRestaurantIncomeAction($restaurantId)
    {
        $this->checkRestaurantAccess($restaurantId);

        $period = $this->getRequest()->get('period');
        if (empty($period)) {
            $period = 'd';
        }

        $data = $this->getRepository()->getRestaurantIncome((int)$restaurantId, $period);

        return $this->jsonResponseSuccessful('', $data);
    }
}
