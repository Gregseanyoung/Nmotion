<?php

namespace Nmotion\ApiBundle\Controller\V1\Restaurants;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;
use Nmotion\NmotionBundle\Entity\Config;

class SearchController extends BaseRestController
{
    use RestaurantTrait;

    /**
     * GET /restaurants/search?query={phrase}&geocode={50.40,30.54,20}&type={typevalue}:
     *      param "query" part of name for restaurant or meal (it depend on param "type")
     *      param "geocode": is specified by "latitude,longitude,radius",
     *          where radius units is measured in "km" (kilometers). All values are float (must use dot precision).
     *      param "type": available values:  [restaurant, meal]; is not required, default value is "restaurant"
     *
     * @return Response json
     */
    public function getSearchAction()
    {
        $request = $this->getRequest();

        $geocode = $request->get('geocode');
        if (! $geocode) {
            throw new PreconditionFailedException('parameter geocode is required');
        }

        $geocodeParams = explode(',', $geocode);
        if (count($geocodeParams) < 2) {
            throw new PreconditionFailedException(
                'parameter geocode must be specified by "latitude,longitude{,radius}"'
            );
        }

        $query     = $request->get('query') ? : '';
        $query     = mb_strlen($query, 'utf-8') < 3 ? '' : $query;
        $latitude  = (float) $geocodeParams[0];
        $longitude = (float) $geocodeParams[1];
        $radius    = isset($geocodeParams[2]) ? (float) $geocodeParams[2] : $this->getSearchRadiusFromConfig();
        $limit     = 1000;
        $offset    = 0;

        $this->setSerializerGroups('restaurant.search');

        /** @var  array of Nmotion\NmotionBundle\Entity\Restaurant */
        $restaurants = $this->getRepository()
            ->getNearby($query, $latitude, $longitude, $radius, $limit, $offset);

        return $this->jsonResponseSuccessful('', $restaurants);
    }

    private function getSearchRadiusFromConfig()
    {
        $configSearchRadius = $this->getRepository('Config')->findOneBy(['name' => 'restaurant_search_radius']);
        if (!$configSearchRadius instanceof Config) {
            throw new \RuntimeException('Config parameter "restaurant_search_radius" not found');
        }

        return max(1, (float) $configSearchRadius->getValue());
    }
}
