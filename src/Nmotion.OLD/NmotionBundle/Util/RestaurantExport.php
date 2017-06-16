<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Util;

use Doctrine\ORM\EntityManager;
use Nmotion\NmotionBundle\Entity\Asset;
use Nmotion\NmotionBundle\Entity\Restaurant;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RestaurantExport extends ContainerAware
{
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @param Restaurant $restaurant
     *
     * @return array
     */
    public function exportRestaurant(Restaurant $restaurant)
    {
        $uploadRootDir = $this->container->getParameter('nmotion_nmotion.upload.root_dir');

        /** @var Asset[] $assets */
        $assets = [];
        if ($restaurant->getLogoAsset()) {
            $assets[] = $restaurant->getLogoAsset();
        }
        foreach ($restaurant->getMenuMeals() as $meal) {
            if ($meal->getLogoAsset()) {
                $assets[] = $meal->getLogoAsset();
            }
            if ($meal->getThumbLogoAsset() && $meal->getLogoAsset() !== $meal->getThumbLogoAsset()) {
                $assets[] = $meal->getThumbLogoAsset();
            }
        }

        $assetsHashMap = [];
        foreach ($assets as $asset) {
            if (array_key_exists($asset->getMd5(), $assetsHashMap)) {
                continue;
            }

            $assetRealPath = $uploadRootDir . DIRECTORY_SEPARATOR
                . $asset->getPath() . DIRECTORY_SEPARATOR
                . $asset->getFilename();

            $assetsHashMap[$asset->getMd5()] = base64_encode(file_get_contents($assetRealPath));
        }

        return [
            'entries' => [
                $restaurant
            ],
            'assets'  => $assetsHashMap
        ];
    }
}
