<?php

namespace Nmotion\NmotionBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Asset
 */
class Asset
{
    use EntityAux;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $originalFilename;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $path;

    /**
     * @var boolean
     */
    private $isAbsolutePath;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * @var string
     */
    private $md5;

    /**
     * @var integer
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $updatedAt;

    /**
     * @var UploadedFile
     */
    private $file;


    public function getUrl()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/upload/' . $this->path . '/' . $this->filename;
    }

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
     * Set mimeType
     *
     * @param string $mimeType
     * @return Asset
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Asset
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set originalFilename
     *
     * @param string $originalFilename
     * @return Asset
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;
    
        return $this;
    }

    /**
     * Get originalFilename
     *
     * @return string 
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Asset
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Asset
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set isAbsolutePath
     *
     * @param boolean $isAbsolutePath
     * @return Asset
     */
    public function setIsAbsolutePath($isAbsolutePath)
    {
        $this->isAbsolutePath = (boolean) $isAbsolutePath;
    
        return $this;
    }

    /**
     * Get isAbsolutePath
     *
     * @return boolean 
     */
    public function getIsAbsolutePath()
    {
        return $this->isAbsolutePath;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Asset
     */
    public function setSize($size)
    {
        $this->size = $size;
    
        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Asset
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Asset
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get md5
     *
     * @return string 
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * @param string $md5
     * @return Restaurant
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;

        return $this;
    }
    
    /**
     * Set createdAt
     *
     * @param integer $createdAt
     * @return Restaurant
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param integer $updatedAt
     * @return Restaurant
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return integer
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getDotExtension($filename = null)
    {
        $filename = $filename ? : $this->getOriginalFilename();
        if (!$filename && !$this->file) {
            return null;
        }
        $dotPosition = strrpos($filename, '.');

        return $dotPosition > -1 ? substr($filename, $dotPosition) : '.' . $this->file->guessExtension();

    }

    /**
     * @param $uploadRootDir
     *
     * @return Asset
     */
    public function upload($uploadRootDir)
    {
        if (null === $this->file) {
            return $this;
        }

        list($width, $height) = @getimagesize($this->file->getPathname());

        $data = [
            'md5'               => md5_file($this->file->getPathname()),
            'mime_type'         => $this->file->getMimeType(),
            'original_filename' => $this->file->getClientOriginalName(),
            'size'              => $this->file->getClientSize(),
        ];

        $data['filename'] = 'o' . $this->getDotExtension($data['original_filename']);
        $data['path']     = join('/', str_split($data['md5'], 3));

        $this->file->move($uploadRootDir . '/' . $data['path'], $data['filename']);

        if (!$this->getName()) {
            $this->setName('File');
        }
        $this->setMimeType($data['mime_type']);
        $this->setOriginalFilename($data['original_filename']);
        $this->setFilename($data['filename']);
        $this->setPath($data['path']);
        $this->setIsAbsolutePath(false);
        $this->setSize($data['size']);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->md5  = $data['md5'];
        $this->file = null;

        return $this;
    }
}