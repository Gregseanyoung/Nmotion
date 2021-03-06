<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Util;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GooglePushNotification extends ContainerAware
{
    var $api_access_key = "AIzaSyAYOpWTnKeu5Gf6x_zXGkY1othtrNmcnFM";
    
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }
    
    public function sendNotification($registrationIds, $message){
        $fields = array
        (
                'registration_ids' 	=> $registrationIds,
                'data'			=> $message
        );

        $headers = array
        (
                'Authorization: key=' . $this->api_access_key,
                'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        
        //echo $result;
		return true;
    }
}
?>
