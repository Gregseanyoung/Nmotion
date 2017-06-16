<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Util;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PrinterMailbox
{
    /**
     * @var Container
     */
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Generate password and return it
     *
     * @return string
     */
    private function getGeneratedPassword()
    {
        return substr($this->container->get('fos_user.util.token_generator')->generateToken(), 0, 6);
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    private function getDoctrine()
    {
        if (! $this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    private function getServerIP()
    {
        /** @var Request $request */
        $request = $this->container->get('request');
        return $request->server->get('SERVER_ADDR');
    }

    /**
     * @param int $restaurantId
     *
     * @throws \Exception
     */
    public function createNewMailbox($restaurantId)
    {
        /** @var Connection $connection */
        $connection = $this->getDoctrine()->getConnection('mailsystem');

        $name    = 'restaurant' . $restaurantId;
        $domain  = 'printer.nmotion.dk';
        $address = $name . '@' . $domain;
        $date    = date('Y-m-d H:m:i');

        try {
            $connection->beginTransaction();
            $connection->insert(
                'mailbox',
                [
                    'username'   => $address,
                    'password'   => $this->getGeneratedPassword(),
                    'name'       => $name,
                    'maildir'    => $domain . '/' . $name . '/',
                    'local_part' => $name,
                    'domain'     => $domain,
                    'created'    => $date,
                    'modified'   => $date,
                    'active'     => 1
                ]
            );
            $connection->insert(
                'alias',
                [
                    'address'  => $address,
                    'goto'     => $address,
                    'domain'   => $domain,
                    'created'  => $date,
                    'modified' => $date,
                    'active'   => 1
                ]
            );
            $connection->insert(
                'log',
                [
                    'timestamp' => $date,
                    'username'  => 'php (' . $this->getServerIP() . ')',
                    'domain'    => $domain,
                    'action'    => 'create_mailbox',
                    'data'      => $address,
                ]
            );
        } catch (\Exception $e) {
            if ($connection->isConnected()) {
                $connection->rollBack();
            }
            throw $e;
        }
        $connection->commit();
    }
}
