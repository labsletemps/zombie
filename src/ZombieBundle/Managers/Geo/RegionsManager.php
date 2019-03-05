<?php

namespace ZombieBundle\Managers\Geo;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Entity\Geo\Region;

class RegionsManager extends SerielManager
{
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Geo\Region';
	}
	protected function addSecurityFilters($qb, $individu) {
		// No security there .
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
	
		if (isset($params['types']) && $params['types']) {
			$types = $params['types'];
			if (!is_array($types)) $types = array($types);
			
			$qb->andwhere("region.type in (:types)")->setParameter('types', $types);
			
			$hasParams = true;
		}
		
		if (isset($params['codes']) && $params['codes']) {
			$codes = $params['codes'];
			
			if (!is_array($codes)) $codes = explode('-', $codes);
			
			$qb->andwhere("region.code in (:codes)")->setParameter('codes', $codes);
			$hasParams = true;
		}
		
		if (isset($params['listIds']) && $params['listIds']) {
			$ids = $params['listIds'];
			
			$ids_str = "";
			foreach ($ids as $id) {
				if ($ids_str != "") $ids_str .= ", ";
				$ids_str .= addslashes($id);
			}
			
			$qb->andwhere("region.id in ($ids_str)");
			
			$hasParams = true;
		}
		
		if (isset($params['listParentsIds']) && $params['listParentsIds']) {
			$ids = $params['listParentsIds'];
			
			$ids_str = "";
			foreach ($ids as $id) {
				if ($ids_str != "") $ids_str .= ", ";
				$ids_str .= addslashes($id);
			}
			
			$qb->andwhere("region.regionParent in ($ids_str)");
			
			$hasParams = true;
		}
		
		return $hasParams;
	}
	
	/**
	 * @return Region
	 */
	public function getRegion($id) {
		return $this->get($id);
	}
	
	/**
	 * @return Region
	 */
	public function getRegionForTypeAndCode($type, $code) {
		if ((!$type) || (!$code)) return null;
		
		$params = array('types' => $type, 'codes' => $code);
		
		return $this->query($params, array('one' => true));
	}
	
	public function getAllRegionsForTypesAndCodes($types, $codes) {
		if ((!$types) || (!$codes)) return array();
		
		$params = array('types' => $types, 'codes' => $codes);
		
		return $this->query($params);
	}
	
	public function getAllRegionsForType($type) {
		if (!$type) return array();
		
		$params = array('types' => $type);
		if ($type == Region::TYPE_REGION_DEPARTEMENT) $orderBy = 'region.code';
		else $orderBy = 'region.label';
		
		return $this->query($params, array('orderBy' => array($orderBy => 'asc')));
	}
	
	public function getAllDepartements() {
		return $this->getAllRegionsForType(Region::TYPE_REGION_DEPARTEMENT);
	}
	
	public function getAllRegionsRegion() {
		return $this->getAllRegionsForType(Region::TYPE_REGION_REGION);
	}
	
	public function getAllPays() {
		return $this->getAllRegionsForType(Region::TYPE_REGION_PAYS);
	}
	
	/**
	 * @return Region[]
	 */
	public function getAllRegionsForListIds($list_ids) {
		if (!$list_ids) return array();
		
		$params = array('listIds' => $list_ids);
		return $this->query($params);
	}
	
	public function getAllRegionsChildrenForListParentIds($list_ids) {
		if (!$list_ids) return array();
		
		$params = array('listParentsIds' => $list_ids);
		return $this->query($params);
	}
	
	/**
	 * @return Region[]
	 */
	public function getAllRegions($options = array()) {
		return $this->getAll($options);
	}
	
	public function buildDB() {
		$france = new Region();
		$france->setType(Region::TYPE_REGION_PAYS);
		$france->setLabel("France");
		$france->setCode("FR");
		$this->save($france, true);
		
		// Create Region.
		$alsace = new Region();
		$alsace->setType(Region::TYPE_REGION_REGION);
		$alsace->setLabel("Alsace");
		$alsace->setCode("alsace");
		$this->save($alsace, true);
		
		$aquitaine = new Region();
		$aquitaine->setType(Region::TYPE_REGION_REGION);
		$aquitaine->setLabel("Aquitaine");
		$aquitaine->setCode("aquitaine");
		$this->save($aquitaine, true);
		
		$auvergne = new Region();
		$auvergne->setType(Region::TYPE_REGION_REGION);
		$auvergne->setLabel("Auvergne");
		$auvergne->setCode("auvergne");
		$this->save($auvergne, true);
		
		$basse_normandie = new Region();
		$basse_normandie->setType(Region::TYPE_REGION_REGION);
		$basse_normandie->setLabel("Basse-Normandie");
		$basse_normandie->setCode("basse_normandie");
		$this->save($basse_normandie, true);
		
		$bourgogne = new Region();
		$bourgogne->setType(Region::TYPE_REGION_REGION);
		$bourgogne->setLabel("Bourgogne");
		$bourgogne->setCode("bourgogne");
		$this->save($bourgogne, true);
		
		$bretagne = new Region();
		$bretagne->setType(Region::TYPE_REGION_REGION);
		$bretagne->setLabel("Bretagne");
		$bretagne->setCode("bretagne");
		$this->save($bretagne, true);
		
		$centre = new Region();
		$centre->setType(Region::TYPE_REGION_REGION);
		$centre->setLabel("Centre");
		$centre->setCode("centre");
		$this->save($centre, true);
		
		$champagne = new Region();
		$champagne->setType(Region::TYPE_REGION_REGION);
		$champagne->setLabel("Champagne");
		$champagne->setCode("champagne");
		$this->save($champagne, true);
		
		$corse = new Region();
		$corse->setType(Region::TYPE_REGION_REGION);
		$corse->setLabel("Corse");
		$corse->setCode("corse");
		$this->save($corse, true);
		
		$franche_comte = new Region();
		$franche_comte->setType(Region::TYPE_REGION_REGION);
		$franche_comte->setLabel("Franche-Comté");
		$franche_comte->setCode("franche_comte");
		$this->save($franche_comte, true);
		
		$haute_normandie = new Region();
		$haute_normandie->setType(Region::TYPE_REGION_REGION);
		$haute_normandie->setLabel("Haute-Normandie");
		$haute_normandie->setCode("haute_normandie");
		$this->save($haute_normandie, true);
		
		$ile_de_france = new Region();
		$ile_de_france->setType(Region::TYPE_REGION_REGION);
		$ile_de_france->setLabel("Ile-de-France");
		$ile_de_france->setCode("ile_de_france");
		$this->save($ile_de_france, true);
		
		$languedoc = new Region();
		$languedoc->setType(Region::TYPE_REGION_REGION);
		$languedoc->setLabel("Languedoc");
		$languedoc->setCode("languedoc");
		$this->save($languedoc, true);
		
		$limousin = new Region();
		$limousin->setType(Region::TYPE_REGION_REGION);
		$limousin->setLabel("Limousin");
		$limousin->setCode("limousin");
		$this->save($limousin, true);
		
		$lorraine = new Region();
		$lorraine->setType(Region::TYPE_REGION_REGION);
		$lorraine->setLabel("Lorraine");
		$lorraine->setCode("lorraine");
		$this->save($lorraine, true);
		
		$midi_pyrenees = new Region();
		$midi_pyrenees->setType(Region::TYPE_REGION_REGION);
		$midi_pyrenees->setLabel("Midi-Pyrénées");
		$midi_pyrenees->setCode("midi_pyrenees");
		$this->save($midi_pyrenees, true);
		
		$nord = new Region();
		$nord->setType(Region::TYPE_REGION_REGION);
		$nord->setLabel("Nord");
		$nord->setCode("nord");
		$this->save($nord, true);
		
		$normandie = new Region();
		$normandie->setType(Region::TYPE_REGION_REGION);
		$normandie->setLabel("Normandie");
		$normandie->setCode("normandie");
		$this->save($normandie, true);
		
		$pays_de_la_loire = new Region();
		$pays_de_la_loire->setType(Region::TYPE_REGION_REGION);
		$pays_de_la_loire->setLabel("Pays-de-la-Loire");
		$pays_de_la_loire->setCode("pays_de_la_loire");
		$this->save($pays_de_la_loire, true);
		
		$picardie = new Region();
		$picardie->setType(Region::TYPE_REGION_REGION);
		$picardie->setLabel("Picardie");
		$picardie->setCode("picardie");
		$this->save($picardie, true);
		
		$poitou_charente = new Region();
		$poitou_charente->setType(Region::TYPE_REGION_REGION);
		$poitou_charente->setLabel("Poitou-Charente");
		$poitou_charente->setCode("poitou_charente");
		$this->save($poitou_charente, true);
		
		$provence_alpes_cote_d_azur = new Region();
		$provence_alpes_cote_d_azur->setType(Region::TYPE_REGION_REGION);
		$provence_alpes_cote_d_azur->setLabel("Provence-Alpes-Côte d'Azur");
		$provence_alpes_cote_d_azur->setCode("provence_alpes_cote_d_azur");
		$this->save($provence_alpes_cote_d_azur, true);
		
		$rhone_alpes = new Region();
		$rhone_alpes->setType(Region::TYPE_REGION_REGION);
		$rhone_alpes->setLabel("Rhône-Alpes");
		$rhone_alpes->setCode("rhone_alpes");
		$this->save($rhone_alpes, true);
		
		// Department
		$bas_rhin = new Region();
		$bas_rhin->setType(Region::TYPE_REGION_DEPARTEMENT);
		$bas_rhin->setRegionParent($alsace);
		$bas_rhin->setLabel("Bas-Rhin");
		$bas_rhin->setCode("67");
		$this->save($bas_rhin, true);
		
		$haut_rhin = new Region();
		$haut_rhin->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haut_rhin->setRegionParent($alsace);
		$haut_rhin->setLabel("Haut-Rhin");
		$haut_rhin->setCode("68");
		$this->save($haut_rhin, true);
		
		$dordogne = new Region();
		$dordogne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$dordogne->setRegionParent($aquitaine);
		$dordogne->setLabel("Dordogne");
		$dordogne->setCode("24");
		$this->save($dordogne, true);
		
		$gironde = new Region();
		$gironde->setType(Region::TYPE_REGION_DEPARTEMENT);
		$gironde->setRegionParent($aquitaine);
		$gironde->setLabel("Gironde");
		$gironde->setCode("33");
		$this->save($gironde, true);
		
		$landes = new Region();
		$landes->setType(Region::TYPE_REGION_DEPARTEMENT);
		$landes->setRegionParent($aquitaine);
		$landes->setLabel("Landes");
		$landes->setCode("40");
		$this->save($landes, true);
		
		$lot_et_garonne = new Region();
		$lot_et_garonne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$lot_et_garonne->setRegionParent($aquitaine);
		$lot_et_garonne->setLabel("Lot-et-Garonne");
		$lot_et_garonne->setCode("47");
		$this->save($lot_et_garonne, true);
		
		$pyrenees_atlantiques = new Region();
		$pyrenees_atlantiques->setType(Region::TYPE_REGION_DEPARTEMENT);
		$pyrenees_atlantiques->setRegionParent($aquitaine);
		$pyrenees_atlantiques->setLabel("Pyrénées-Atlantiques");
		$pyrenees_atlantiques->setCode("64");
		$this->save($pyrenees_atlantiques, true);
		
		$allier = new Region();
		$allier->setType(Region::TYPE_REGION_DEPARTEMENT);
		$allier->setRegionParent($auvergne);
		$allier->setLabel("Allier");
		$allier->setCode("03");
		$this->save($allier, true);
		
		$cantal = new Region();
		$cantal->setType(Region::TYPE_REGION_DEPARTEMENT);
		$cantal->setRegionParent($auvergne);
		$cantal->setLabel("Cantal");
		$cantal->setCode("15");
		$this->save($cantal, true);
		
		$haute_loire = new Region();
		$haute_loire->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_loire->setRegionParent($auvergne);
		$haute_loire->setLabel("Haute-Loire");
		$haute_loire->setCode("43");
		$this->save($haute_loire, true);
		
		$puy_de_dome = new Region();
		$puy_de_dome->setType(Region::TYPE_REGION_DEPARTEMENT);
		$puy_de_dome->setRegionParent($auvergne);
		$puy_de_dome->setLabel("Puy-de-Dôme");
		$puy_de_dome->setCode("63");
		$this->save($puy_de_dome, true);
		
		$calvados = new Region();
		$calvados->setType(Region::TYPE_REGION_DEPARTEMENT);
		$calvados->setRegionParent($basse_normandie);
		$calvados->setLabel("Calvados");
		$calvados->setCode("14");
		$this->save($calvados, true);
		
		$orne = new Region();
		$orne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$orne->setRegionParent($basse_normandie);
		$orne->setLabel("Orne");
		$orne->setCode("61");
		$this->save($orne, true);
		
		$cote_d_or = new Region();
		$cote_d_or->setType(Region::TYPE_REGION_DEPARTEMENT);
		$cote_d_or->setRegionParent($bourgogne);
		$cote_d_or->setLabel("Côte d'Or");
		$cote_d_or->setCode("21");
		$this->save($cote_d_or, true);
		
		$nievre = new Region();
		$nievre->setType(Region::TYPE_REGION_DEPARTEMENT);
		$nievre->setRegionParent($bourgogne);
		$nievre->setLabel("Nièvre");
		$nievre->setCode("58");
		$this->save($nievre, true);
		
		$saone_et_loire = new Region();
		$saone_et_loire->setType(Region::TYPE_REGION_DEPARTEMENT);
		$saone_et_loire->setRegionParent($bourgogne);
		$saone_et_loire->setLabel("Saône-et-Loire");
		$saone_et_loire->setCode("71");
		$this->save($saone_et_loire, true);
		
		$yonne = new Region();
		$yonne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$yonne->setRegionParent($bourgogne);
		$yonne->setLabel("Yonne");
		$yonne->setCode("89");
		$this->save($yonne, true);
		
		$cotes_d_armor = new Region();
		$cotes_d_armor->setType(Region::TYPE_REGION_DEPARTEMENT);
		$cotes_d_armor->setRegionParent($bretagne);
		$cotes_d_armor->setLabel("Côtes d'Armor");
		$cotes_d_armor->setCode("22");
		$this->save($cotes_d_armor, true);
		
		$finistere = new Region();
		$finistere->setType(Region::TYPE_REGION_DEPARTEMENT);
		$finistere->setRegionParent($bretagne);
		$finistere->setLabel("Finistère");
		$finistere->setCode("29");
		$this->save($finistere, true);
		
		$ille_et_vilaine = new Region();
		$ille_et_vilaine->setType(Region::TYPE_REGION_DEPARTEMENT);
		$ille_et_vilaine->setRegionParent($bretagne);
		$ille_et_vilaine->setLabel("Ille-et-Vilaine");
		$ille_et_vilaine->setCode("35");
		$this->save($ille_et_vilaine, true);
		
		$morbihan = new Region();
		$morbihan->setType(Region::TYPE_REGION_DEPARTEMENT);
		$morbihan->setRegionParent($bretagne);
		$morbihan->setLabel("Morbihan");
		$morbihan->setCode("56");
		$this->save($morbihan, true);
		
		$cher = new Region();
		$cher->setType(Region::TYPE_REGION_DEPARTEMENT);
		$cher->setRegionParent($centre);
		$cher->setLabel("Cher");
		$cher->setCode("18");
		$this->save($cher, true);
		
		$eure_et_loir = new Region();
		$eure_et_loir->setType(Region::TYPE_REGION_DEPARTEMENT);
		$eure_et_loir->setRegionParent($centre);
		$eure_et_loir->setLabel("Eure-et-Loir");
		$eure_et_loir->setCode("28");
		$this->save($eure_et_loir, true);
		
		$indre = new Region();
		$indre->setType(Region::TYPE_REGION_DEPARTEMENT);
		$indre->setRegionParent($centre);
		$indre->setLabel("Indre");
		$indre->setCode("36");
		$this->save($indre, true);
		
		$indre_et_loire = new Region();
		$indre_et_loire->setType(Region::TYPE_REGION_DEPARTEMENT);
		$indre_et_loire->setRegionParent($centre);
		$indre_et_loire->setLabel("Indre-et-Loire");
		$indre_et_loire->setCode("37");
		$this->save($indre_et_loire, true);
		
		$loiret = new Region();
		$loiret->setType(Region::TYPE_REGION_DEPARTEMENT);
		$loiret->setRegionParent($centre);
		$loiret->setLabel("Loiret");
		$loiret->setCode("45");
		$this->save($loiret, true);
		
		$loir_et_cher = new Region();
		$loir_et_cher->setType(Region::TYPE_REGION_DEPARTEMENT);
		$loir_et_cher->setRegionParent($centre);
		$loir_et_cher->setLabel("Loir-et-Cher");
		$loir_et_cher->setCode("41");
		$this->save($loir_et_cher, true);
		
		$ardennes = new Region();
		$ardennes->setType(Region::TYPE_REGION_DEPARTEMENT);
		$ardennes->setRegionParent($champagne);
		$ardennes->setLabel("Ardennes");
		$ardennes->setCode("08");
		$this->save($ardennes, true);
		
		$aube = new Region();
		$aube->setType(Region::TYPE_REGION_DEPARTEMENT);
		$aube->setRegionParent($champagne);
		$aube->setLabel("Aube");
		$aube->setCode("10");
		$this->save($aube, true);
		
		$haute_marne = new Region();
		$haute_marne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_marne->setRegionParent($champagne);
		$haute_marne->setLabel("Haute-Marne");
		$haute_marne->setCode("52");
		$this->save($haute_marne, true);
		
		$marne = new Region();
		$marne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$marne->setRegionParent($champagne);
		$marne->setLabel("Marne");
		$marne->setCode("51");
		$this->save($marne, true);
		
		$corse_du_sud = new Region();
		$corse_du_sud->setType(Region::TYPE_REGION_DEPARTEMENT);
		$corse_du_sud->setRegionParent($corse);
		$corse_du_sud->setLabel("Corse du Sud");
		$corse_du_sud->setCode("2A");
		$this->save($corse_du_sud, true);
		
		$haute_corse = new Region();
		$haute_corse->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_corse->setRegionParent($corse);
		$haute_corse->setLabel("Haute-Corse");
		$haute_corse->setCode("2B");
		$this->save($haute_corse, true);
		
		$doubs = new Region();
		$doubs->setType(Region::TYPE_REGION_DEPARTEMENT);
		$doubs->setRegionParent($franche_comte);
		$doubs->setLabel("Doubs");
		$doubs->setCode("25");
		$this->save($doubs, true);
		
		$haute_saone = new Region();
		$haute_saone->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_saone->setRegionParent($franche_comte);
		$haute_saone->setLabel("Haute-Saône");
		$haute_saone->setCode("70");
		$this->save($haute_saone, true);
		
		$jura = new Region();
		$jura->setType(Region::TYPE_REGION_DEPARTEMENT);
		$jura->setRegionParent($franche_comte);
		$jura->setLabel("Jura");
		$jura->setCode("39");
		$this->save($jura, true);
		
		$territoire_de_belfort = new Region();
		$territoire_de_belfort->setType(Region::TYPE_REGION_DEPARTEMENT);
		$territoire_de_belfort->setRegionParent($franche_comte);
		$territoire_de_belfort->setLabel("Territoire-de-Belfort");
		$territoire_de_belfort->setCode("90");
		$this->save($territoire_de_belfort, true);
		
		$eure = new Region();
		$eure->setType(Region::TYPE_REGION_DEPARTEMENT);
		$eure->setRegionParent($haute_normandie);
		$eure->setLabel("Eure");
		$eure->setCode("27");
		$this->save($eure, true);
		
		$seine_maritime = new Region();
		$seine_maritime->setType(Region::TYPE_REGION_DEPARTEMENT);
		$seine_maritime->setRegionParent($haute_normandie);
		$seine_maritime->setLabel("Seine-Maritime");
		$seine_maritime->setCode("76");
		$this->save($seine_maritime, true);
		
		$essonne = new Region();
		$essonne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$essonne->setRegionParent($ile_de_france);
		$essonne->setLabel("Essonne");
		$essonne->setCode("91");
		$this->save($essonne, true);
		
		$hauts_de_seine = new Region();
		$hauts_de_seine->setType(Region::TYPE_REGION_DEPARTEMENT);
		$hauts_de_seine->setRegionParent($ile_de_france);
		$hauts_de_seine->setLabel("Hauts-de-Seine");
		$hauts_de_seine->setCode("92");
		$this->save($hauts_de_seine, true);
		
		$paris = new Region();
		$paris->setType(Region::TYPE_REGION_DEPARTEMENT);
		$paris->setRegionParent($ile_de_france);
		$paris->setLabel("Paris");
		$paris->setCode("75");
		$this->save($paris, true);
		
		$seine_et_marne = new Region();
		$seine_et_marne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$seine_et_marne->setRegionParent($ile_de_france);
		$seine_et_marne->setLabel("Seine-et-Marne");
		$seine_et_marne->setCode("77");
		$this->save($seine_et_marne, true);
		
		$seine_st_denis = new Region();
		$seine_st_denis->setType(Region::TYPE_REGION_DEPARTEMENT);
		$seine_st_denis->setRegionParent($ile_de_france);
		$seine_st_denis->setLabel("Seine-St-Denis");
		$seine_st_denis->setCode("93");
		$this->save($seine_st_denis, true);
		
		$val_de_marne = new Region();
		$val_de_marne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$val_de_marne->setRegionParent($ile_de_france);
		$val_de_marne->setLabel("Val-de-Marne");
		$val_de_marne->setCode("94");
		$this->save($val_de_marne, true);
		
		$val_d_oise = new Region();
		$val_d_oise->setType(Region::TYPE_REGION_DEPARTEMENT);
		$val_d_oise->setRegionParent($ile_de_france);
		$val_d_oise->setLabel("Val-d'Oise");
		$val_d_oise->setCode("95");
		$this->save($val_d_oise, true);
		
		$yvelines = new Region();
		$yvelines->setType(Region::TYPE_REGION_DEPARTEMENT);
		$yvelines->setRegionParent($ile_de_france);
		$yvelines->setLabel("Yvelines");
		$yvelines->setCode("78");
		$this->save($yvelines, true);
		
		$aude = new Region();
		$aude->setType(Region::TYPE_REGION_DEPARTEMENT);
		$aude->setRegionParent($languedoc);
		$aude->setLabel("Aude");
		$aude->setCode("11");
		$this->save($aude, true);
		
		$gard = new Region();
		$gard->setType(Region::TYPE_REGION_DEPARTEMENT);
		$gard->setRegionParent($languedoc);
		$gard->setLabel("Gard");
		$gard->setCode("30");
		$this->save($gard, true);
		
		$herault = new Region();
		$herault->setType(Region::TYPE_REGION_DEPARTEMENT);
		$herault->setRegionParent($languedoc);
		$herault->setLabel("Hérault");
		$herault->setCode("34");
		$this->save($herault, true);
		
		$lozere = new Region();
		$lozere->setType(Region::TYPE_REGION_DEPARTEMENT);
		$lozere->setRegionParent($languedoc);
		$lozere->setLabel("Lozère");
		$lozere->setCode("48");
		$this->save($lozere, true);
		
		$pyrenees_orientales = new Region();
		$pyrenees_orientales->setType(Region::TYPE_REGION_DEPARTEMENT);
		$pyrenees_orientales->setRegionParent($languedoc);
		$pyrenees_orientales->setLabel("Pyrénées-Orientales");
		$pyrenees_orientales->setCode("66");
		$this->save($pyrenees_orientales, true);
		
		$correze = new Region();
		$correze->setType(Region::TYPE_REGION_DEPARTEMENT);
		$correze->setRegionParent($limousin);
		$correze->setLabel("Corrèze");
		$correze->setCode("19");
		$this->save($correze, true);
		
		$creuse = new Region();
		$creuse->setType(Region::TYPE_REGION_DEPARTEMENT);
		$creuse->setRegionParent($limousin);
		$creuse->setLabel("Creuse");
		$creuse->setCode("23");
		$this->save($creuse, true);
		
		$haute_vienne = new Region();
		$haute_vienne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_vienne->setRegionParent($limousin);
		$haute_vienne->setLabel("Haute-Vienne");
		$haute_vienne->setCode("87");
		$this->save($haute_vienne, true);
		
		$meurthe_et_moselle = new Region();
		$meurthe_et_moselle->setType(Region::TYPE_REGION_DEPARTEMENT);
		$meurthe_et_moselle->setRegionParent($lorraine);
		$meurthe_et_moselle->setLabel("Meurthe-et-Moselle");
		$meurthe_et_moselle->setCode("54");
		$this->save($meurthe_et_moselle, true);
		
		$meuse = new Region();
		$meuse->setType(Region::TYPE_REGION_DEPARTEMENT);
		$meuse->setRegionParent($lorraine);
		$meuse->setLabel("Meuse");
		$meuse->setCode("55");
		$this->save($meuse, true);
		
		$moselle = new Region();
		$moselle->setType(Region::TYPE_REGION_DEPARTEMENT);
		$moselle->setRegionParent($lorraine);
		$moselle->setLabel("Moselle");
		$moselle->setCode("57");
		$this->save($moselle, true);
		
		$vosges = new Region();
		$vosges->setType(Region::TYPE_REGION_DEPARTEMENT);
		$vosges->setRegionParent($lorraine);
		$vosges->setLabel("Vosges");
		$vosges->setCode("88");
		$this->save($vosges, true);
		
		$ariege = new Region();
		$ariege->setType(Region::TYPE_REGION_DEPARTEMENT);
		$ariege->setRegionParent($midi_pyrenees);
		$ariege->setLabel("Ariège");
		$ariege->setCode("09");
		$this->save($ariege, true);
		
		$aveyron = new Region();
		$aveyron->setType(Region::TYPE_REGION_DEPARTEMENT);
		$aveyron->setRegionParent($midi_pyrenees);
		$aveyron->setLabel("Aveyron");
		$aveyron->setCode("12");
		$this->save($aveyron, true);
		
		$gers = new Region();
		$gers->setType(Region::TYPE_REGION_DEPARTEMENT);
		$gers->setRegionParent($midi_pyrenees);
		$gers->setLabel("Gers");
		$gers->setCode("32");
		$this->save($gers, true);
		
		$haute_garonne = new Region();
		$haute_garonne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_garonne->setRegionParent($midi_pyrenees);
		$haute_garonne->setLabel("Haute-Garonne");
		$haute_garonne->setCode("31");
		$this->save($haute_garonne, true);
		
		$hautes_pyrenees = new Region();
		$hautes_pyrenees->setType(Region::TYPE_REGION_DEPARTEMENT);
		$hautes_pyrenees->setRegionParent($midi_pyrenees);
		$hautes_pyrenees->setLabel("Hautes-Pyrénées");
		$hautes_pyrenees->setCode("65");
		$this->save($hautes_pyrenees, true);
		
		$lot = new Region();
		$lot->setType(Region::TYPE_REGION_DEPARTEMENT);
		$lot->setRegionParent($midi_pyrenees);
		$lot->setLabel("Lot");
		$lot->setCode("46");
		$this->save($lot, true);
		
		$tarn = new Region();
		$tarn->setType(Region::TYPE_REGION_DEPARTEMENT);
		$tarn->setRegionParent($midi_pyrenees);
		$tarn->setLabel("Tarn");
		$tarn->setCode("81");
		$this->save($tarn, true);
		
		$tarn_et_garonne = new Region();
		$tarn_et_garonne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$tarn_et_garonne->setRegionParent($midi_pyrenees);
		$tarn_et_garonne->setLabel("Tarn-et-Garonne");
		$tarn_et_garonne->setCode("82");
		$this->save($tarn_et_garonne, true);
		
		$nord_dep = new Region();
		$nord_dep->setType(Region::TYPE_REGION_DEPARTEMENT);
		$nord_dep->setRegionParent($nord);
		$nord_dep->setLabel("Nord");
		$nord_dep->setCode("59");
		$this->save($nord_dep, true);
		
		$pas_de_calais = new Region();
		$pas_de_calais->setType(Region::TYPE_REGION_DEPARTEMENT);
		$pas_de_calais->setRegionParent($nord);
		$pas_de_calais->setLabel("Pas-de-Calais");
		$pas_de_calais->setCode("62");
		$this->save($pas_de_calais, true);
		
		$manche = new Region();
		$manche->setType(Region::TYPE_REGION_DEPARTEMENT);
		$manche->setRegionParent($normandie);
		$manche->setLabel("Manche");
		$manche->setCode("50");
		$this->save($manche, true);
		
		$loire_atlantique = new Region();
		$loire_atlantique->setType(Region::TYPE_REGION_DEPARTEMENT);
		$loire_atlantique->setRegionParent($pays_de_la_loire);
		$loire_atlantique->setLabel("Loire-Atlantique");
		$loire_atlantique->setCode("44");
		$this->save($loire_atlantique, true);
		
		$maine_et_loire = new Region();
		$maine_et_loire->setType(Region::TYPE_REGION_DEPARTEMENT);
		$maine_et_loire->setRegionParent($pays_de_la_loire);
		$maine_et_loire->setLabel("Maine-et-Loire");
		$maine_et_loire->setCode("49");
		$this->save($maine_et_loire, true);
		
		$mayenne = new Region();
		$mayenne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$mayenne->setRegionParent($pays_de_la_loire);
		$mayenne->setLabel("Mayenne");
		$mayenne->setCode("53");
		$this->save($mayenne, true);
		
		$sarthe = new Region();
		$sarthe->setType(Region::TYPE_REGION_DEPARTEMENT);
		$sarthe->setRegionParent($pays_de_la_loire);
		$sarthe->setLabel("Sarthe");
		$sarthe->setCode("72");
		$this->save($sarthe, true);
		
		$vendee = new Region();
		$vendee->setType(Region::TYPE_REGION_DEPARTEMENT);
		$vendee->setRegionParent($pays_de_la_loire);
		$vendee->setLabel("Vendée");
		$vendee->setCode("85");
		$this->save($vendee, true);
		
		$aisne = new Region();
		$aisne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$aisne->setRegionParent($picardie);
		$aisne->setLabel("Aisne");
		$aisne->setCode("02");
		$this->save($aisne, true);
		
		$oise = new Region();
		$oise->setType(Region::TYPE_REGION_DEPARTEMENT);
		$oise->setRegionParent($picardie);
		$oise->setLabel("Oise");
		$oise->setCode("60");
		$this->save($oise, true);
		
		$somme = new Region();
		$somme->setType(Region::TYPE_REGION_DEPARTEMENT);
		$somme->setRegionParent($picardie);
		$somme->setLabel("Somme");
		$somme->setCode("80");
		$this->save($somme, true);
		
		$charente = new Region();
		$charente->setType(Region::TYPE_REGION_DEPARTEMENT);
		$charente->setRegionParent($poitou_charente);
		$charente->setLabel("Charente");
		$charente->setCode("16");
		$this->save($charente, true);
		
		$charente_maritime = new Region();
		$charente_maritime->setType(Region::TYPE_REGION_DEPARTEMENT);
		$charente_maritime->setRegionParent($poitou_charente);
		$charente_maritime->setLabel("Charente Maritime");
		$charente_maritime->setCode("17");
		$this->save($charente_maritime, true);
		
		$deux_sevres = new Region();
		$deux_sevres->setType(Region::TYPE_REGION_DEPARTEMENT);
		$deux_sevres->setRegionParent($poitou_charente);
		$deux_sevres->setLabel("Deux-Sèvres");
		$deux_sevres->setCode("79");
		$this->save($deux_sevres, true);
		
		$vienne = new Region();
		$vienne->setType(Region::TYPE_REGION_DEPARTEMENT);
		$vienne->setRegionParent($poitou_charente);
		$vienne->setLabel("Vienne");
		$vienne->setCode("86");
		$this->save($vienne, true);
		
		$alpes_de_haute_provence = new Region();
		$alpes_de_haute_provence->setType(Region::TYPE_REGION_DEPARTEMENT);
		$alpes_de_haute_provence->setRegionParent($provence_alpes_cote_d_azur);
		$alpes_de_haute_provence->setLabel("Alpes de Haute-Provence");
		$alpes_de_haute_provence->setCode("04");
		$this->save($alpes_de_haute_provence, true);
		
		$alpes_maritimes = new Region();
		$alpes_maritimes->setType(Region::TYPE_REGION_DEPARTEMENT);
		$alpes_maritimes->setRegionParent($provence_alpes_cote_d_azur);
		$alpes_maritimes->setLabel("Alpes-Maritimes");
		$alpes_maritimes->setCode("06");
		$this->save($alpes_maritimes, true);
		
		$bouches_du_rhone = new Region();
		$bouches_du_rhone->setType(Region::TYPE_REGION_DEPARTEMENT);
		$bouches_du_rhone->setRegionParent($provence_alpes_cote_d_azur);
		$bouches_du_rhone->setLabel("Bouches du Rhône");
		$bouches_du_rhone->setCode("13");
		$this->save($bouches_du_rhone, true);
		
		$hautes_alpes = new Region();
		$hautes_alpes->setType(Region::TYPE_REGION_DEPARTEMENT);
		$hautes_alpes->setRegionParent($provence_alpes_cote_d_azur);
		$hautes_alpes->setLabel("Hautes-Alpes");
		$hautes_alpes->setCode("05");
		$this->save($hautes_alpes, true);
		
		$var = new Region();
		$var->setType(Region::TYPE_REGION_DEPARTEMENT);
		$var->setRegionParent($provence_alpes_cote_d_azur);
		$var->setLabel("Var");
		$var->setCode("83");
		$this->save($var, true);
		
		$vaucluse = new Region();
		$vaucluse->setType(Region::TYPE_REGION_DEPARTEMENT);
		$vaucluse->setRegionParent($provence_alpes_cote_d_azur);
		$vaucluse->setLabel("Vaucluse");
		$vaucluse->setCode("84");
		$this->save($vaucluse, true);
		
		$ain = new Region();
		$ain->setType(Region::TYPE_REGION_DEPARTEMENT);
		$ain->setRegionParent($rhone_alpes);
		$ain->setLabel("Ain");
		$ain->setCode("01");
		$this->save($ain, true);
		
		$ardeche = new Region();
		$ardeche->setType(Region::TYPE_REGION_DEPARTEMENT);
		$ardeche->setRegionParent($rhone_alpes);
		$ardeche->setLabel("Ardèche");
		$ardeche->setCode("07");
		$this->save($ardeche, true);
		
		$drome = new Region();
		$drome->setType(Region::TYPE_REGION_DEPARTEMENT);
		$drome->setRegionParent($rhone_alpes);
		$drome->setLabel("Drôme");
		$drome->setCode("26");
		$this->save($drome, true);
		
		$haute_savoie = new Region();
		$haute_savoie->setType(Region::TYPE_REGION_DEPARTEMENT);
		$haute_savoie->setRegionParent($rhone_alpes);
		$haute_savoie->setLabel("Haute-Savoie");
		$haute_savoie->setCode("74");
		$this->save($haute_savoie, true);
		
		$isere = new Region();
		$isere->setType(Region::TYPE_REGION_DEPARTEMENT);
		$isere->setRegionParent($rhone_alpes);
		$isere->setLabel("Isère");
		$isere->setCode("38");
		$this->save($isere, true);
		
		$loire = new Region();
		$loire->setType(Region::TYPE_REGION_DEPARTEMENT);
		$loire->setRegionParent($rhone_alpes);
		$loire->setLabel("Loire");
		$loire->setCode("42");
		$this->save($loire, true);
		
		$rhone = new Region();
		$rhone->setType(Region::TYPE_REGION_DEPARTEMENT);
		$rhone->setRegionParent($rhone_alpes);
		$rhone->setLabel("Rhône");
		$rhone->setCode("69");
		$this->save($rhone, true);
		
		$savoie = new Region();
		$savoie->setType(Region::TYPE_REGION_DEPARTEMENT);
		$savoie->setRegionParent($rhone_alpes);
		$savoie->setLabel("Savoie");
		$savoie->setCode("73");
		$this->save($savoie, true);
	}
	
}

?>
