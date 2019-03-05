<?php

namespace Seriel\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="seriel_user",options={"engine"="MyISAM"})
 * @UniqueEntity("username")
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $param1;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $param2;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $param3;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $param4;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function getId() {
		return $this->id;
	}
	
    /**
     * Set param1
     *
     * @param string $param1
     *
     * @return User
     */
    public function setParam1($param1)
    {
        $this->param1 = $param1;

        return $this;
    }

    /**
     * Get param1
     *
     * @return string
     */
    public function getParam1()
    {
        return $this->param1;
    }

    /**
     * Set param2
     *
     * @param string $param2
     *
     * @return User
     */
    public function setParam2($param2)
    {
        $this->param2 = $param2;

        return $this;
    }

    /**
     * Get param2
     *
     * @return string
     */
    public function getParam2()
    {
        return $this->param2;
    }

    /**
     * Set param3
     *
     * @param string $param3
     *
     * @return User
     */
    public function setParam3($param3)
    {
        $this->param3 = $param3;

        return $this;
    }

    /**
     * Get param3
     *
     * @return string
     */
    public function getParam3()
    {
        return $this->param3;
    }

    /**
     * Set param4
     *
     * @param string $param4
     *
     * @return User
     */
    public function setParam4($param4)
    {
        $this->param4 = $param4;

        return $this;
    }

    /**
     * Get param4
     *
     * @return string
     */
    public function getParam4()
    {
        return $this->param4;
    }
}
