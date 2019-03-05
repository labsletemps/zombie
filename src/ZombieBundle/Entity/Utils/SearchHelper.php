<?php

namespace ZombieBundle\Entity\Utils;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="search_helper",options={"engine"="MyISAM"},
 * 				indexes={@ORM\Index(name="sh_obj_id_idx", columns={"obj_id"}),
 * 						 @ORM\Index(name="sh_uid_idx", columns={"uid"}),
 * 						 @ORM\Index(name="sh_num1_idx", columns={"num1"}),
 * 						 @ORM\Index(name="sh_num2_idx", columns={"num2"}),
 * 						 @ORM\Index(name="sh_num3_idx", columns={"num3"}),
 * 						 @ORM\Index(name="sh_num4_idx", columns={"num4"}),
 * 						 @ORM\Index(name="sh_num5_idx", columns={"num5"}),
 * 						 @ORM\Index(name="sh_num6_idx", columns={"num6"}),
 * 						 @ORM\Index(name="sh_num7_idx", columns={"num7"}),
 * 						 @ORM\Index(name="sh_num8_idx", columns={"num8"}),
 * 						 @ORM\Index(name="sh_num9_idx", columns={"num9"}),
 * 						 @ORM\Index(name="sh_num10_idx", columns={"num10"}),
 * 						 @ORM\Index(name="sh_str1_idx", columns={"str1"}),
 * 						 @ORM\Index(name="sh_str2_idx", columns={"str2"}),
 * 						 @ORM\Index(name="sh_str3_idx", columns={"str3"}),
 * 						 @ORM\Index(name="sh_str4_idx", columns={"str4"}),
 * 						 @ORM\Index(name="sh_str5_idx", columns={"str5"}),
 * 						 @ORM\Index(name="sh_str6_idx", columns={"str6"}),
 * 						 @ORM\Index(name="sh_str7_idx", columns={"str7"}),
 * 						 @ORM\Index(name="sh_str8_idx", columns={"str8"}),
 * 						 @ORM\Index(name="sh_str9_idx", columns={"str9"}),
 * 						 @ORM\Index(name="sh_str10_idx", columns={"str10"})
 * 			})
 */
class SearchHelper extends RootObject
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=30, unique=false, nullable=false)
	 */
	protected $uid;
	
   /**
	* @ORM\Column(type="integer", nullable=false)
	*/
	protected $obj_id;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num1;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num2;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num3;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num4;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num5;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num6;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num7;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num8;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num9;
	
	/**
	 * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $num10;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str1;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str2;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str3;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str4;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str5;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str6;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str7;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str8;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str9;
	
	/**
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $str10;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getId() {
		return $this->id;
	}
	


    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return SearchHelper
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set objId
     *
     * @param integer $objId
     *
     * @return SearchHelper
     */
    public function setObjId($objId)
    {
        $this->obj_id = $objId;

        return $this;
    }

    /**
     * Get objId
     *
     * @return integer
     */
    public function getObjId()
    {
        return $this->obj_id;
    }

    /**
     * Set num1
     *
     * @param string $num1
     *
     * @return SearchHelper
     */
    public function setNum1($num1)
    {
        $this->num1 = $num1;

        return $this;
    }

    /**
     * Get num1
     *
     * @return string
     */
    public function getNum1()
    {
        return $this->num1;
    }

    /**
     * Set num2
     *
     * @param string $num2
     *
     * @return SearchHelper
     */
    public function setNum2($num2)
    {
        $this->num2 = $num2;

        return $this;
    }

    /**
     * Get num2
     *
     * @return string
     */
    public function getNum2()
    {
        return $this->num2;
    }

    /**
     * Set num3
     *
     * @param string $num3
     *
     * @return SearchHelper
     */
    public function setNum3($num3)
    {
        $this->num3 = $num3;

        return $this;
    }

    /**
     * Get num3
     *
     * @return string
     */
    public function getNum3()
    {
        return $this->num3;
    }

    /**
     * Set num4
     *
     * @param string $num4
     *
     * @return SearchHelper
     */
    public function setNum4($num4)
    {
        $this->num4 = $num4;

        return $this;
    }

    /**
     * Get num4
     *
     * @return string
     */
    public function getNum4()
    {
        return $this->num4;
    }

    /**
     * Set num5
     *
     * @param string $num5
     *
     * @return SearchHelper
     */
    public function setNum5($num5)
    {
        $this->num5 = $num5;

        return $this;
    }

    /**
     * Get num5
     *
     * @return string
     */
    public function getNum5()
    {
        return $this->num5;
    }

    /**
     * Set num6
     *
     * @param string $num6
     *
     * @return SearchHelper
     */
    public function setNum6($num6)
    {
        $this->num6 = $num6;

        return $this;
    }

    /**
     * Get num6
     *
     * @return string
     */
    public function getNum6()
    {
        return $this->num6;
    }

    /**
     * Set num7
     *
     * @param string $num7
     *
     * @return SearchHelper
     */
    public function setNum7($num7)
    {
        $this->num7 = $num7;

        return $this;
    }

    /**
     * Get num7
     *
     * @return string
     */
    public function getNum7()
    {
        return $this->num7;
    }

    /**
     * Set num8
     *
     * @param string $num8
     *
     * @return SearchHelper
     */
    public function setNum8($num8)
    {
        $this->num8 = $num8;

        return $this;
    }

    /**
     * Get num8
     *
     * @return string
     */
    public function getNum8()
    {
        return $this->num8;
    }

    /**
     * Set num9
     *
     * @param string $num9
     *
     * @return SearchHelper
     */
    public function setNum9($num9)
    {
        $this->num9 = $num9;

        return $this;
    }

    /**
     * Get num9
     *
     * @return string
     */
    public function getNum9()
    {
        return $this->num9;
    }

    /**
     * Set num10
     *
     * @param string $num10
     *
     * @return SearchHelper
     */
    public function setNum10($num10)
    {
        $this->num10 = $num10;

        return $this;
    }

    /**
     * Get num10
     *
     * @return string
     */
    public function getNum10()
    {
        return $this->num10;
    }

    /**
     * Set str1
     *
     * @param string $str1
     *
     * @return SearchHelper
     */
    public function setStr1($str1)
    {
        $this->str1 = $str1;

        return $this;
    }

    /**
     * Get str1
     *
     * @return string
     */
    public function getStr1()
    {
        return $this->str1;
    }

    /**
     * Set str2
     *
     * @param string $str2
     *
     * @return SearchHelper
     */
    public function setStr2($str2)
    {
        $this->str2 = $str2;

        return $this;
    }

    /**
     * Get str2
     *
     * @return string
     */
    public function getStr2()
    {
        return $this->str2;
    }

    /**
     * Set str3
     *
     * @param string $str3
     *
     * @return SearchHelper
     */
    public function setStr3($str3)
    {
        $this->str3 = $str3;

        return $this;
    }

    /**
     * Get str3
     *
     * @return string
     */
    public function getStr3()
    {
        return $this->str3;
    }

    /**
     * Set str4
     *
     * @param string $str4
     *
     * @return SearchHelper
     */
    public function setStr4($str4)
    {
        $this->str4 = $str4;

        return $this;
    }

    /**
     * Get str4
     *
     * @return string
     */
    public function getStr4()
    {
        return $this->str4;
    }

    /**
     * Set str5
     *
     * @param string $str5
     *
     * @return SearchHelper
     */
    public function setStr5($str5)
    {
        $this->str5 = $str5;

        return $this;
    }

    /**
     * Get str5
     *
     * @return string
     */
    public function getStr5()
    {
        return $this->str5;
    }

    /**
     * Set str6
     *
     * @param string $str6
     *
     * @return SearchHelper
     */
    public function setStr6($str6)
    {
        $this->str6 = $str6;

        return $this;
    }

    /**
     * Get str6
     *
     * @return string
     */
    public function getStr6()
    {
        return $this->str6;
    }

    /**
     * Set str7
     *
     * @param string $str7
     *
     * @return SearchHelper
     */
    public function setStr7($str7)
    {
        $this->str7 = $str7;

        return $this;
    }

    /**
     * Get str7
     *
     * @return string
     */
    public function getStr7()
    {
        return $this->str7;
    }

    /**
     * Set str8
     *
     * @param string $str8
     *
     * @return SearchHelper
     */
    public function setStr8($str8)
    {
        $this->str8 = $str8;

        return $this;
    }

    /**
     * Get str8
     *
     * @return string
     */
    public function getStr8()
    {
        return $this->str8;
    }

    /**
     * Set str9
     *
     * @param string $str9
     *
     * @return SearchHelper
     */
    public function setStr9($str9)
    {
        $this->str9 = $str9;

        return $this;
    }

    /**
     * Get str9
     *
     * @return string
     */
    public function getStr9()
    {
        return $this->str9;
    }

    /**
     * Set str10
     *
     * @param string $str10
     *
     * @return SearchHelper
     */
    public function setStr10($str10)
    {
        $this->str10 = $str10;

        return $this;
    }

    /**
     * Get str10
     *
     * @return string
     */
    public function getStr10()
    {
        return $this->str10;
    }
}
