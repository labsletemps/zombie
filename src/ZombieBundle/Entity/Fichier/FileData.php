<?php

namespace ZombieBundle\Entity\Fichier;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="fichier_data",options={"engine"="MyISAM"})
 */
class FileData extends RootObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="blob", unique=false, nullable=false)
	 */
	protected $data;
	

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}

    /**
     * Set data
     *
     * @param binary $data
     * @return FileData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return binary 
     */
    public function getData()
    {
    	if (get_resource_type($this->data) == 'stream') { 
    		$dt = stream_get_contents($this->data);
    		
    		return $dt;
    	}
    	return '';
    }

}
