<?php

namespace ZombieBundle\Entity\Gui;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Individu\Individu;
use ZombieBundle\Entity\Securite\ZombieProfil;

/**
 * @ORM\Entity
 * @ORM\Table(name="interface_param",options={"engine"="MyISAM"})
 */
class InterfaceParam extends RootObject
{
	const INTERFACE_TYPE_PAGE_ACCUEIL = 1;
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="integer", unique=false, nullable=false)
	 */
	protected $type;

	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 */
	protected $individu;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Securite\ZombieProfil")
	 * @ORM\JoinColumn(name="profil_id", referencedColumnName="id")
	 */
	protected $profil;
	
	/**
	 * @ORM\Column(type="integer", unique=false, nullable=true)
	 */
	protected $pos;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option1;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option2;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option3;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option4;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option5;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option6;
	
	

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
	

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return InterfaceParam
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set pos
     *
     * @param integer $pos
     *
     * @return InterfaceParam
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * Get pos
     *
     * @return integer
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Set option1
     *
     * @param string $option1
     *
     * @return InterfaceParam
     */
    public function setOption1($option1)
    {
        $this->option1 = $option1;

        return $this;
    }

    /**
     * Get option1
     *
     * @return string
     */
    public function getOption1()
    {
        return $this->option1;
    }

    /**
     * Set option2
     *
     * @param string $option2
     *
     * @return InterfaceParam
     */
    public function setOption2($option2)
    {
        $this->option2 = $option2;

        return $this;
    }

    /**
     * Get option2
     *
     * @return string
     */
    public function getOption2()
    {
        return $this->option2;
    }

    /**
     * Set option3
     *
     * @param string $option3
     *
     * @return InterfaceParam
     */
    public function setOption3($option3)
    {
        $this->option3 = $option3;

        return $this;
    }

    /**
     * Get option3
     *
     * @return string
     */
    public function getOption3()
    {
        return $this->option3;
    }

    /**
     * Set option4
     *
     * @param string $option4
     *
     * @return InterfaceParam
     */
    public function setOption4($option4)
    {
        $this->option4 = $option4;

        return $this;
    }

    /**
     * Get option4
     *
     * @return string
     */
    public function getOption4()
    {
        return $this->option4;
    }

    /**
     * Set individu
     *
     * @param Individu $individu
     *
     * @return InterfaceParam
     */
    public function setIndividu(Individu $individu = null)
    {
        $this->individu = $individu;

        return $this;
    }

    /**
     * Get individu
     *
     * @return Individu
     */
    public function getIndividu()
    {
        return $this->individu;
    }

    /**
     * Set profil
     *
     * @param Profil $profil
     *
     * @return InterfaceParam
     */
    public function setProfil(ZombieProfil $profil = null)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return Profil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set option5
     *
     * @param string $option5
     *
     * @return InterfaceParam
     */
    public function setOption5($option5)
    {
        $this->option5 = $option5;

        return $this;
    }

    /**
     * Get option5
     *
     * @return string
     */
    public function getOption5()
    {
        return $this->option5;
    }

    /**
     * Set option6
     *
     * @param string $option6
     *
     * @return InterfaceParam
     */
    public function setOption6($option6)
    {
        $this->option6 = $option6;

        return $this;
    }

    /**
     * Get option6
     *
     * @return string
     */
    public function getOption6()
    {
        return $this->option6;
    }
}
