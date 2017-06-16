<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Entity;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

class LogEntry extends AbstractLogEntry
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var \DateTime
     */
    protected $loggedAt;

    /**
     * @var string
     */
    protected $objectId;

    /**
     * @var string
     */
    protected $objectClass;

    /**
     * @var integer
     */
    protected $version;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $username;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return LogEntry
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set loggedAt
     *
     * @param \DateTime $loggedAt
     * @return LogEntry
     */
    /*public function setLoggedAt($loggedAt)
    {
        $this->loggedAt = $loggedAt;
    
        return $this;
    }*/

    /**
     * Get loggedAt
     *
     * @return \DateTime 
     */
    public function getLoggedAt()
    {
        return $this->loggedAt;
    }

    /**
     * Set objectId
     *
     * @param string $objectId
     * @return LogEntry
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    
        return $this;
    }

    /**
     * Get objectId
     *
     * @return string 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectClass
     *
     * @param string $objectClass
     * @return LogEntry
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
    
        return $this;
    }

    /**
     * Get objectClass
     *
     * @return string 
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set version
     *
     * @param integer $version
     * @return LogEntry
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return LogEntry
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return LogEntry
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }
}