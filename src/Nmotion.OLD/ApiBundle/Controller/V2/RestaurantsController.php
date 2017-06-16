<?php

namespace Nmotion\ApiBundle\Controller\V2;

use Nmotion\ApiBundle\Controller\V1 as V1;

use Symfony\Component\HttpFoundation\Response;
use FOS\Rest\Util\Codes;

use Nmotion\NmotionBundle\Entity\RestaurantCheckin;
use Nmotion\NmotionBundle\Entity\RestaurantServiceType;
use Nmotion\NmotionBundle\Exception\ConflictException;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;

class RestaurantsController extends V1\RestaurantsController
{

    /**
     * Pre-conditions:
     * - Authenticated user
     * - Restaurant must be open
     * - serviceType is provided
     * - Table is provided if serviceType != 2 (takeaway)
     * - Table must be free if serviceType != 2 (takeaway)
     *
     * @param int $restaurantId
     *
     * @return Response json
     * @throws PreconditionFailedException
     */
    public function postRestaurantCheckinAction($restaurantId)
    {
        $this->setSerializerGroups('api');

        $user        = $this->getUser();
        $restaurant  = $this->getRestaurant($restaurantId, true);
        $serviceTypeId       = $this->getRequest()->get('serviceType');
        $tableNumber         = $this->getRequest()->get('table');
        $fbRestaurantCheckin = $this->getRequest()->get('fbRestaurantCheckin');
        $takeawayPickupTime  = $this->getRequest()->get('takeawayPickupTime');

        if (!$serviceTypeId) {
            throw new PreconditionFailedException('Parameter "serviceType" is required.');
        }

        $serviceType = $this->getRepository('RestaurantServiceType')->find((int)$serviceTypeId);

        if (!$serviceType instanceof RestaurantServiceType) {
            throw new PreconditionFailedException('Unknown restaurant\'s service type id: ' . $serviceTypeId);
        }

        switch ($serviceType->getId()) {
            case RestaurantServiceType::IN_HOUSE:
                if (!$restaurant->isInHouse()) {
                    throw new PreconditionFailedException(
                        'Restaurant "' . $restaurant->getName() . '" does not support service type '
                        . $serviceType->getName()
                    );
                }
                break;
            case RestaurantServiceType::TAKEAWAY:
                if (!$restaurant->isTakeaway()) {
                    throw new PreconditionFailedException(
                        'Restaurant "' . $restaurant->getName() . '" does not support service type '
                        . $serviceType->getName()
                    );
                } else if(!$takeawayPickupTime) {
                    throw new PreconditionFailedException(
                    	'Parameter "takeawayPickupTime" is required for requests of type TAKEAWAY.'
                    );
                }
                break;
            case RestaurantServiceType::ROOM_SERVICE:
                if (!$restaurant->isRoomService()) {
                    throw new PreconditionFailedException(
                        'Restaurant "' . $restaurant->getName() . '" does not support service type '
                        . $serviceType->getName()
                    );
                }
                break;
            default:
                throw new PreconditionFailedException('Unknown restaurant\'s service type id: ' . $serviceTypeId);
                break;
        }

        if (!$tableNumber) {
            throw new PreconditionFailedException('Parameter "table" is required.');
        }

        if (!$restaurant->isOpen()) {
            throw new PreconditionFailedException('Restaurant is closed.');
        }
        
        // Fill takeawayPickupTime with non-null value
        if(!$takeawayPickupTime) {
        	$takeawayPickupTime = 0;
        }

        if ($serviceType->getId() != RestaurantServiceType::TAKEAWAY) {
            $checkinRepository = $this->getRepository('RestaurantCheckin');
            $tableCheckins     = $checkinRepository->getAllCheckedInFromTable($restaurant, $tableNumber, $serviceType);

            if (!empty($tableCheckins)) {
                $hasActualCheckin = false;
                $time             = time();
                /** @var $tableCheckin RestaurantCheckin */
                foreach ($tableCheckins as $tableCheckin) {
                    if ($checkinRepository->isCheckInActual($tableCheckin, $time)) {
                        if ($tableCheckin->getUser() === $user) {
                            $tableCheckin->setUpdatedAt(time());
                            $this->getDoctrine()->getManager()->persist($tableCheckin);
                            $this->getDoctrine()->getManager()->flush();

                            if ($fbRestaurantCheckin) {
                                $this->checkinToRestaurantLocation($restaurant);
                            }

                            return $this->jsonResponseSuccessful('', [$tableCheckin], Codes::HTTP_OK);
                        }
                        $hasActualCheckin = true;
                        // break; - import break should be absent, because we compare all checkin->user with user
                    }
                }

                if ($hasActualCheckin) {
                    if (!$this->getRequest()->get('force')) {
                        // table is not empty, you can just force JOIN;
                        throw new ConflictException(
                            $this->get('translator')->trans(
                                'restaurant.checkin.tableNotEmptyWonnaForceJoin',
                                ['{{ tableNumber }}' => $tableNumber]
                            )
                        );
                    }
                } else {
                    if (!$this->getRequest()->get('empty')) {
                        // does table empty? system checks out all and check me in;
                        throw new PreconditionFailedException(
                            $this->get('translator')->trans(
                                'restaurant.checkin.submitCheckinIfTableEmpty',
                                ['{{ tableNumber }}' => $tableNumber]
                            ),
                            null,
                            RestaurantCheckin::TABLE_MAYBE_EMPTY
                        );
                    } else {
                        foreach ($tableCheckins as $tableCheckin) {
                            $tableCheckin->setCheckedOut(true);
                            $this->updateCheckoutOrders($tableCheckin);
                        }

                    }
                }
            }
        }

        $checkin = new RestaurantCheckin($restaurant, $user);
        $checkin->setServiceType($serviceType);
        $checkin->setTableNumber($tableNumber);
        $checkin->setTakeawayPickupTime($takeawayPickupTime);
        $this->getDoctrine()->getManager()->persist($checkin);
        $this->getDoctrine()->getManager()->flush();

        if ($fbRestaurantCheckin) {
            $this->checkinToRestaurantLocation($restaurant);
        }

        return $this->jsonResponseSuccessful('', [$checkin], Codes::HTTP_CREATED);
    }
}
