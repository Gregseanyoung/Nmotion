<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Util;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Nmotion\NmotionBundle\Entity\Asset;
use Nmotion\NmotionBundle\Entity\MenuCategory;
use Nmotion\NmotionBundle\Entity\Repositories\RestaurantRepository;
use Nmotion\NmotionBundle\Form\RestaurantImport\MenuCategoryType;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class RestaurantImport extends ContainerAware
{
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed                    $data    The initial data for the form
     * @param array                    $options Options for the form
     *
     * @return Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * Shortcut to return the request service.
     *
     * @return Request
     */
    protected function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @param string $base64Data
     *
     * @return Asset
     */
    protected function createAssetFromBase64($base64Data)
    {
        $path = tempnam(sys_get_temp_dir(), 'asset-');
        file_put_contents($path, base64_decode($base64Data));

        // simulate
        $file = new UploadedFile($path, 'importedOne.jpg', null, filesize($path), UPLOAD_ERR_OK, true);

        return (new Asset())
            ->setFile($file)
            ->upload($this->container->getParameter('nmotion_nmotion.upload.root_dir'));
    }

    public function importFromRequest(Request $request = null)
    {
        $em             = $this->getEntityManager();
        $request        = $request ? : $this->getRequest();
        $rawData        = $request->request;
        $assets         = $rawData->get('assets');
        $restaurantId   = $rawData->get('entries[0][id]', null, true);
        $menuCategories = $rawData->get('entries[0][menuCategories]', null, true);

        /** @var EntityRepository $assetRepo */
        $assetRepo = $em->getRepository('NmotionNmotionBundle:Asset');

        foreach ($assets as $md5 => $assetBase64Data) {
            $asset = $assetRepo->findOneByMd5($md5);
            if (! $asset instanceof Asset) {
                $asset = $this->createAssetFromBase64($assetBase64Data);
                $em->persist($asset);
            }
            $assets[$md5] = $asset;
        }

        // save assets
        $em->flush();

        /** @var RestaurantRepository $restaurantRepo */
        $restaurantRepo = $em->getRepository('NmotionNmotionBundle:Restaurant');
        $restaurant     = $restaurantRepo->find($restaurantId);

        foreach ($restaurant->getMenuCategories() as $menuCategory) {
            $em->remove($menuCategory);
        }
        $restaurant->setMenuCategories([]);

        // remove current menu categories and meals; because of the MySQL limitations we can not do this in transaction
        $em->flush();

        foreach ($menuCategories as $menuCategoryRaw) {
            $menuCategory = new MenuCategory();

            $form = $this->createForm(new MenuCategoryType, $menuCategory);
            $form->bind($menuCategoryRaw);
            if (! $form->isValid()) {
                $em->clear();
                throw new \RuntimeException('Validation failed. Aborting.');
            }

            $restaurant->addMenuCategory($menuCategory);

            foreach ($menuCategory->getMeals() as $meal) {
                if ($meal->hasLogoAsset()) {
                    $md5 = $meal->getLogoAsset()->getMd5();
                    if (array_key_exists($md5, $assets)) {
                        $meal->setLogoAsset($assets[$md5]);
                    } else {
                        $meal->setLogoAsset(null); // unset asset
                    }
                }
                if ($meal->hasThumbLogoAsset()) {
                    $md5 = $meal->getThumbLogoAsset()->getMd5();
                    if (array_key_exists($md5, $assets)) {
                        $meal->setThumbLogoAsset($assets[$md5]);
                    } else {
                        $meal->setThumbLogoAsset(null); // unset asset
                    }
                }
            }
        }

        $em->flush();
    }
}
