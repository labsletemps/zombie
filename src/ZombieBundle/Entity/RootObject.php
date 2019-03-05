<?php

namespace ZombieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\BaseObject;

use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\AppliToolboxBundle\Managers\ListeManager;
use Seriel\AppliToolboxBundle\Utils\Liste\ListeElemRenderer;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Seriel\AppliToolboxBundle\Managers\LinearizationManager;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class RootObject extends BaseObject
{
	protected function __construct()
	{
		$this->setCreatedAt();
	}
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created_at;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $updated_at;
	
	/**
	 * @ORM\Column(type="integer", nullable=true, options={"default":1})
	 */
	protected $save_counter;
	
	public function setSaveCounter($sc) {
		$this->save_counter = $sc;
	}
	
	public function getSaveCounter() {
		return $this->save_counter;
	}
	
	public function getCreatedAt() {
		return $this->created_at;
	}
	
	public function getUpdatedAt() {
		return $this->updated_at;
	}
	
	
	/**
	 * @ORM\PrePersist
	 */
	protected function setCreatedAt() {
		$this->created_at = new \DateTime();
		$this->updated_at = $this->created_at;
	}
	
	/**
	 * @ORM\PreUpdate
	 */
	protected function setUpdatedAt() {
		$this->updated_at = new \DateTime();
	}


	/**
	 * get a linearization array of all fields
	 *
	 * @return array
	 */
	public function getLinearizedFields(){

		$lzm = new LinearizationManager();
		return $lzm->get($this);
	}
	
	// to be overwritten
	public function getSuppInfoForCreaDoc() {
		return null;
	}

	public function updateListePropertiesCache() {
		$listeMgr = ManagersManager::getManager()->getContainer()->get('liste_manager');
		if (false) $listeMgr = new ListeManager();
		$className = SymfonyUtils::get_clean_class($this);
		
		$debug = false;
		$fieldsRenderers = $listeMgr->buildFieldsRenderer($className);
		
		if ($debug) error_log('UPDATE TEST  before foreach : '.$className." >> ".count($fieldsRenderers));
		if ($fieldsRenderers) {
			foreach ($fieldsRenderers as $renderer) {
				if (false) $renderer = new ListeElemRenderer();
				$dbfieldformat = $renderer->getDbfieldformat();
				$dbfieldsort = $renderer->getDbfieldsort();
				
				if ($debug) error_log("UPDATE TEST  fieldRenderer : ".$renderer->getPropertyName()." dbfieldformat[$dbfieldformat] dbfieldsort[$dbfieldsort]");
				
				if ($dbfieldformat) {
					$index = strpos($dbfieldformat, ".");
					if ($index) $dbfieldformat = null;
				}
				
				if ($dbfieldsort) {
					$index = strpos($dbfieldsort, ".");
					if ($index) $dbfieldsort = null;
				}
				
				if ($dbfieldformat && $dbfieldsort) {
					$values = $renderer->getValuesFormattedAndSorted($this);
					
					$formatted = $values[0];
					$sorted = $values[1];
					
					if ($debug) error_log("UPDATE TEST  fieldRenderer : ".$renderer->getPropertyName()." formatted[$formatted] sorted[$sorted]");
					
					eval('$this->'.$dbfieldformat.' = $formatted;');
					eval('$this->'.$dbfieldsort.' = $sorted;');
				} else if ($dbfieldformat) {
					$formatted = $renderer->getValueFormatted($this);
					if ($debug && $dbfieldformat == '_provenanceNom') error_log("UPDATE TEST 4 > $dbfieldformat >>> $formatted");
					eval('$this->'.$dbfieldformat.' = $formatted;');
					if ($debug && $dbfieldformat == '_provenanceNom') eval('error_log("UPDATE TEST 5 : $this->'.$dbfieldformat.' ... $this");');
				} else if ($dbfieldsort) {
					$sorted = $renderer->getValueSorted($this);
					eval('$this->'.$dbfieldsort.' = $sorted;');
				}
			}
		}
	}

}
