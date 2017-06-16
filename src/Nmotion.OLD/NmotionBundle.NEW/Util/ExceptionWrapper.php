<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nmotion\NmotionBundle\Util;

class ExceptionWrapper
{
    private $success;
    private $status;
    private $exceptionCode;
    private $statusCode;
    private $statusText;
    private $currentContent;
    private $message;
    private $trace;

    public function __construct($data)
    {
        $this->success = false;
        $this->status = $data['status'];
        $this->statusCode = $data['status_code'];
        if (isset($data['exceptionCode'])) {
            $this->exceptionCode = $data['exceptionCode'];
        }
        $this->statusText = $data['status_text'];
        $this->currentContent = $data['currentContent'];
        $this->message = $data['message'];
        if (array_key_exists('trace', $data)) {
            $this->trace = $data['trace'];
        }
    }
}
