<?php

namespace ZombieBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice;
use ZombieBundle\Managers\Securite\SecurityManager;
use ZombieBundle\Entity\Geo\Region;
use ZombieBundle\Entity\Securite\ZombieCredential;
use Seriel\AppliToolboxBundle\Utils\StringUtils;

class ZombieExtension extends \Twig_Extension {

	protected $container;
    /**
     *
     * @var type 
     */
    protected $requestStack;

    /**
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(RequestStack $request_stack, $container) {
        $this->requestStack = $request_stack;
        $this->container = $container;
    }

    /**
     * 
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment) {
        $this->environment = $environment;
    }

    /**
     * 
     * @return type
     */
    public function getFunctions() {
        return array(
        	'error_log' => new \Twig_Function_Method($this, 'errorLog'),
        		
        	'current_individu' => new \Twig_Function_Method($this, 'currentIndividu'),
        		
        	'open_list_widget' => new \Twig_Function_Method($this, 'openListWidget', array('is_safe' => array('html'))),
        		
        	'evt_agenda' => new \Twig_Function_Method($this, 'evtAgenda', array('is_safe' => array('html'))),

        	'section_widget' => new \Twig_Function_Method($this, 'sectionWidget', array('is_safe' => array('html'))),
			'trend_widget' => new \Twig_Function_Method($this, 'trendWidget', array('is_safe' => array('html'))),
        	'orderby_widget' => new \Twig_Function_Method($this, 'orderbyWidget', array('is_safe' => array('html'))),
        	'region_widget' => new \Twig_Function_Method($this, 'regionWidget', array('is_safe' => array('html'))),
        	'pays_widget' => new \Twig_Function_Method($this, 'paysWidget', array('is_safe' => array('html'))),
        	'reg_widget' => new \Twig_Function_Method($this, 'regWidget', array('is_safe' => array('html'))),
        	'departement_widget' => new \Twig_Function_Method($this, 'departementWidget', array('is_safe' => array('html'))),
        	'select_compte_widget' => new \Twig_Function_Method($this, 'selectCompteWidget', array('is_safe' => array('html'))),
        	'cred_access_level_choice' => new \Twig_Function_Method($this, 'credAccessLevelChoice', array('is_safe' => array('html'))),
        	'cred_choices' => new \Twig_Function_Method($this, 'credChoices', array('is_safe' => array('html'))),
        	'tuile_individu_entite' => new \Twig_Function_Method($this, 'tuileIndividuEntite', array('is_safe' => array('html'))),
        	'clean_list_files' => new \Twig_Function_Method($this, 'cleanListFiles', array('is_safe' => array('html'))),
        	'entity_nice_name' => new \Twig_Function_Method($this, 'entityNiceName', array('is_safe' => array('html'))),
        	'dynamic_list_builder' => new \Twig_Function_Method($this, 'dynamicListBuilder', array('is_safe' => array('html'))),
        	'type_elem_planning_widget' => new \Twig_Function_Method($this, 'typeElemPlanningWidget', array('is_safe' => array('html'))),
        		
        	'multi_select_array_ids' => new \Twig_Function_Method($this, 'multiSelectArrayIds', array('is_safe' => array('html')))
        );
    }
    
    /**
     *
     * @return type
     */
    public function getFilters() {
    	return array(
    			'email_addr' => new \Twig_Filter_Method($this, 'emailAddr', array('is_safe' => array('html'))),
    			'remove_accent' => new \Twig_Filter_Method($this, 'remove_accent', array('is_safe' => array('html')))
    			
    	);
    }
    
    public function getTests() {
    	return array(
    	);
    }
    
    public function emailAddr($email) {
    	$twig = $this->container->get('templating');
    	return $twig->render('ZombieBundle:Utils/divers:email.html.twig', array('email' => $email));
    }
    public function remove_accent($string) {
    	return StringUtils::removeAccents($string);
    }
    
    public function multiSelectArrayIds($array_objs) {
    	if (!$array_objs) return '';
    	
    	$str = "";
    	foreach ($array_objs as $obj) {
    		if ($str != "") $str .= "-";
    		$str .= $obj->getId();
    	}
    	
    	return $str;
    }
    
    public function errorLog($message) {
    	if ($message) error_log($message);
    }
    
    public function currentIndividu() {
    	$secuMgr = $this->container->get('security_manager');
    	if (false) $secuMgr = new SecurityManager();
    	$individu = $secuMgr->getCurrentIndividu();
    	 
    	return $individu;
    }
    
    /**
     * 
     * @return string
     */
    public function getName() {
        return 'zombie_extension';
    }
    
    public function evtAgenda($evt) {
    	if (!$evt) {
    		$logger = $this->container->get('logger');
    		$logger->error('evtAgenda : evt undefined');
    		return;
    	}
    	 
    	$datas = array('evt' => $evt);
    	 
    	$twig = $this->container->get('templating');
    	return $twig->render('ZombieBundle:Utils/agenda:evenement.html.twig', $datas);
    }
    
    public function openListWidget($openList, $name = null, $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    	 
    	$datas = array();
    	 
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;

    	$openList_id = null;
    	if ($openList instanceof OpenList) {
    		$openList_id = $openList->getId();
    	} else {
    		$openListsMgr = $this->getContainer()->get('open_lists_manager');
    		if (false) $openListsMgr = new OpenListsManager();
    		
    		$openList_id = $openList;
    		$openList = $openListsMgr->getOpenLists($openList_id);
    	}
    	 
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	 
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    	
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    	
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    	
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    		
    		if (isset($options['liste_ouverte']) && $options['liste_ouverte']) $datas['liste_ouverte'] = $options['liste_ouverte'];
    	}
    	 
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    	$datas['open_list'] = $openList;
    	 
    	return $twig->render('ZombieBundle:Utils/widgets:open_list_widget.html.twig', $datas);
    }

    public function paysWidget($name = null, $value = null, $options = null) {
    	if (!$name) $name = 'pays';
    	
    	if (!$options) $options = array();
    	$options['type'] = 'pays';
    	
    	return $this->regionWidget($name, $value, $options);
    }
    
    public function regWidget($name = null, $value = null, $options = null) {
    	if (!$name) $name = 'region';
    	 
    	if (!$options) $options = array();
    	$options['type'] = 'region';
    	 
    	return $this->regionWidget($name, $value, $options);
    }
    
    public function departementWidget($name = null, $value = null, $options = null) {
    	if (!$name) $name = 'departement';
    
    	if (!$options) $options = array();
    	$options['type'] = 'departement';
    
    	return $this->regionWidget($name, $value, $options);
    }
    
    public function regionWidget($name = null, $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    
    	$regionsMgr = $this->container->get('regions_manager');
    	if (false) $regionsMgr = new RegionsManager();
    	
    	// Options type required.
    	if ((!$options) || (!isset($options['type']))) return "** type ? **";
    	
    	$str_type = strtolower($options['type']);
    	
    	$type = 0;
    	if ($str_type == 'pays') $type = Region::TYPE_REGION_PAYS;
    	else if ($str_type == 'region' || $str_type == 'reg') $type = Region::TYPE_REGION_REGION;
    	else if ($str_type == 'departement' || $str_type == 'dep') $type = Region::TYPE_REGION_DEPARTEMENT;
    	else return "$str_type ?";
    
    	$regions = $regionsMgr->getAllRegionsForType($type);
    
    	$datas = array('regions' => $regions);
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;
    	 
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	 
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    		
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    	}
    	 
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    
    	return $twig->render('ZombieBundle:Utils/widgets:region_widget.html.twig', $datas);
    }
    
    public function sectionWidget($name = null, $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    	
    	$articleMgr = $this->container->get('articles_manager');
    	if (false) $articleMgr= new ArticleManager();
    	
    	$sections= $articleMgr->getAllSection();

    	$datas = array('sections' => $sections);
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;
    	
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    		
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    		
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    		
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    	}
    	
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    	
    	return $twig->render('ZombieBundle:Utils/widgets:section_widget.html.twig', $datas);
    }
    
    public function trendWidget($name , $NameService , $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    	
    	$trendMgr = $this->container->get($NameService);
    	$date = new \DateTime();
    	$date->sub(new \DateInterval('P1D'));
    	$trends= $trendMgr->getTrends(20, $date, $date);
    	$datas = array('trends' => $trends);
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;
    	
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    		
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    		
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    		
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    	}
    	
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    	
    	return $twig->render('ZombieBundle:Utils/widgets:trend_widget.html.twig', $datas);
    }
    
    public function orderbyWidget($name , $className, $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    	
    	// column name
    	$listeMgr = $this->container->get('liste_manager');
    	if (false) $listeMgr = new ListeManager();
    	$fieldsRenderers = $listeMgr->buildFieldsRenderer($className);
    	
    	// Let's build a Map.
    	$fieldsRenderersMap = array();
    	foreach ($fieldsRenderers as $fieldRenderer) {
    		$fieldsRenderersMap[$fieldRenderer->getPropertyName()] = $fieldRenderer;
    	}
    	
    	$columns = array();
    	$datas = array('orderby' => $fieldsRenderersMap);
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;
    	
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    		
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    		
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    		
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    	}
    	
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    	
    	return $twig->render('ZombieBundle:Utils/widgets:orderby_widget.html.twig', $datas);
    }
    
    public function selectCompteWidget($name = null, $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    	
    	$datas = array();
    	
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;
    	
    	$filtre_structure = null;
    	if ($options && isset($options['structure']) && $options['structure']) {
    		$filtre_structure = $options['structure'];
    	}
    	
    	$comptes = array();
    	if ($value) {
    		$ids = explode('-', $value);
    		$comptesMgr = $this->container->get('comptes_manager');
    		if (false) $comptesMgr = new ComptesManager();
    		$logger = $this->container->get('logger');
    		
    		$query_params = array('ids' => $ids);
    		if ($filtre_structure) $query_parmas['structure'] = $filtre_structure;
    		
    		$comptes = $comptesMgr->query($query_params);
    	}
    	
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    		
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    		
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    		
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    	}
    	
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    	$datas['comptes'] = $comptes;
    	if ($filtre_structure) $datas['filtre_structure'] = $filtre_structure;
    	
    	return $twig->render('ZombieBundle:Utils/widgets:select_compte_widget.html.twig', $datas);
    }

    
    public function tuileIndividuEntite($individuEntite) {
    	if (!$individuEntite) {
    		$logger = $this->container->get('logger');
    		$logger->error('tuileIndividuEntite : individuEntite undefined');
    		return;
    	}
    	
    	$individuEntite_id = null;
    	
    	// contact is object individuEntite, or id individuEntite.
    	if (is_string($individuEntite) || is_int($individuEntite)) {
    		$individuEntite_id = ''.$individuEntite;
    		// get contact.
    		$individuEntitesMgr = $this->container->get('individu_entites_manager');
    		if (false) $individuEntitesMgr = new IndividuEntitesManager();
    		
    		$individuEntite = $individuEntitesMgr->getIndividuEntite($individuEntite_id);
    		
    		if (!$individuEntite) {
    			$logger = $this->container->get('logger');
    			$logger->error('tuileIndividuEntite : individuEntite not found : '.$individuEntite_id);
    			return;
    		}
    	} else {
    		$individuEntite_id = $individuEntite->getId();
    	}
    	
    	$datas = array('individuEntite' => $individuEntite);
    	
    	$twig = $this->container->get('templating');
    	return $twig->render('ZombieBundle:Utils/tuiles:individu_entite.html.twig', $datas);
    }
    
    
    public function typeElemPlanningWidget($name = null, $value = null, $options = null) {
    	$twig = $this->container->get('templating');
    	
    	$typesElems = array('art' => 'Articles'
    	);
    	
    	$datas = array('types_elems' => $typesElems);
    	if ($name) $datas['name'] = $name;
    	if ($value) $datas['value'] = $value;
    	
    	$multi = false;
    	$thin = false;
    	$full_open = false;
    	
    	if ($options && is_array($options)) {
    		if (isset($options['resume_name']) && $options['resume_name']) $datas['resume_name'] = $options['resume_name'];
    		if (isset($options['resume_pos']) && $options['resume_pos']) $datas['resume_pos'] = $options['resume_pos'];
    		if (isset($options['multi']) && $options['multi']) $multi = true;
    		if (isset($options['thin']) && $options['thin']) $thin = true;
    		if (isset($options['full_open']) && $options['full_open']) $full_open = true;
    		
    		if (isset($options['title']) && $options['title']) $datas['title'] = $options['title'];
    		if (isset($options['fixed_title']) && $options['fixed_title']) $datas['fixed_title'] = $options['fixed_title'];
    		
    		if (isset($options['disabled']) && $options['disabled']) $datas['disabled'] = $options['disabled'];
    		
    		if (isset($options['has_resume']) && $options['has_resume']) $datas['has_resume'] = $options['has_resume'];
    	}
    	
    	$datas['multi'] = $multi;
    	$datas['thin'] = $thin;
    	$datas['full_open'] = $full_open;
    	
    	return $twig->render('ZombieBundle:Utils/widgets:type_elem_planning_widget.html.twig', $datas);
    }
    
    public function entityNiceName($entity) {
    	$last_slash = strrpos($entity, '\\');
    	if ($last_slash !== false) $entity = substr($entity, $last_slash+1);
    	 
    	if ($entity == 'Article') return "Article";
    	 
    	return $entity;
    }
    
    public function credAccessLevelChoice(ZombieCredential $credential, $selected = null) {
    	if (!$credential) return "";
    	
    	$options = array();
    	if ($credential->isLevelAvailable(ZombieCredential::ACCESS_LEVEL_SELF)) $options[ZombieCredential::ACCESS_LEVEL_SELF]  = 'perso';
    	if ($credential->isLevelAvailable(ZombieCredential::ACCESS_LEVEL_ENTITE)) $options[ZombieCredential::ACCESS_LEVEL_ENTITE]  = 'entité';
    	if ($credential->isLevelAvailable(ZombieCredential::ACCESS_LEVEL_COMPANY)) $options[ZombieCredential::ACCESS_LEVEL_COMPANY]  = 'structure';
    	
    	$twig = $this->container->get('templating');
    	
    	return $twig->render('ZombieBundle:Admin:cred_access_level_choice.html.twig', array('options' => $options, 'selected' => $selected));
    }
    
    public function credChoices(ZombieCredential $credential, $selected = null) {
    	if (!$credential) return "";
    	
    	$twig = $this->container->get('templating');
    	$choices = $credential->getChoices();
    	
    	$options = array();
    	foreach ($choices as $choice) {
    		if (false) $choice = new CredentialMultiChoice();
    		$options[$choice->getId()] = $choice->getName();
    	}
    	 
    	return $twig->render('ZombieBundle:Utils/admin:cred_choices.html.twig', array('options' => $options, 'selected' => $selected));
    }
    
    public function dynamicListBuilder($values = null) {
    	$twig = $this->container->get('templating');
    	 
    	return $twig->render('ZombieBundle:Utils/divers:dynamic_list_builder.html.twig', array('values' => $values));
    }
    
    public function cleanListFiles($list_files) {
    	// TODO ... or not.
    	return $list_files;
    }
}
