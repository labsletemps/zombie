<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Managers\Individu\IndividusManager;
use ZombieBundle\Entity\Reporting\ReportingSauvegarde;
use Seriel\AppliToolboxBundle\Managers\ReportingManager;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\AppliToolboxBundle\Utils\Reporting\Report;
use Seriel\AppliToolboxBundle\Utils\ExcelHelper;
use Seriel\AppliToolboxBundle\Utils\Reporting\ReportingDataRenderer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReportingController extends Controller {

    private $user;
    private $em;

    public function preExecute() {
        $this->user = SymfonyUtils::getTokenUser();
        if (!is_object($this->user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $this->em = $this->get('doctrine')->getManager();
    }

    /**
     * Main page reporting
     * index
     */
    public function indexAction() {

    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	
    	$ModuleMetrics = array();
    	$LogoModuleMetrics = array();
    	$SearchSemantique= array();
    	//Search Modules
    	$modulesMgr = $this->container->get('zombie_modules_manager');
    	if (false) $modulesMgr = new ModulesManager();
    	$modules = $modulesMgr->getZombieModules();
    	if ($modules) {
    		foreach ($modules as $name => $paramsModule) {
    			//METRICS
    			if (isset($paramsModule['metrics_object_class']) && $paramsModule['metrics_object_class']) {
    				
    				//get indicator in object metrics
    				$minmaxMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllIdIndicators();
    				foreach ($indicators as $indicator => $label ) {
    					$minmaxMetrics[$name.'_'. $indicator] = $label ;
    				}
    				
    				$ModuleMetrics[$name] = $minmaxMetrics;
    				
    				//get logoindicator in object metrics
    				$logoMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllLogoIndicators();
    				foreach ($indicators as $indicator => $labellogo ) {
    					$logoMetrics[$name.'_'. $indicator] = $labellogo;
    				}
    				$LogoModuleMetrics[$name] = $logoMetrics;
    			}
    			
    			
    			
    			
    			//SEARCH SEMANTIQUE
    			if (isset($paramsModule['search_semantique']) && $paramsModule['search_semantique']) {
    				
    				$SearchSemantique[] = $name;
    			}
    			//Trends
    			if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
    				$ServiceTrend[$name] = $paramsModule['service_trend'];
    			}
    		}
    	}	
    	
    	return $this->render('ZombieBundle:Reporting:reporting.html.twig', array('moduleMetrics'=> $ModuleMetrics,'logoModuleMetrics'=> $LogoModuleMetrics,'searchSemantique' => $SearchSemantique, 'serviceTrend' => $ServiceTrend));
    	
    }


    
    protected function splitRowColOption($value) {
    	if (!$value) return $value;
    	
    	$matches = array();
    	preg_match('/^.+\(.+\)$/', $value, $matches);
    	
    	if ($matches && count($matches) == 1) {
    		$splitted = explode('(', $value);
    		$val = $splitted[0];
    		$opt = substr($splitted[1], 0, strlen($splitted[1]) - 1);

    		
    		return array($val, $opt);
    	}
    	
    	return $value;
    }
    
    public function renderAction(Request $request, $type) {
    	
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	$row = $request->get('row');
    	$col = $request->get('col');

    	$to_show = $request->get('datas');
    	if ($to_show) $to_show = explode('-', $to_show);
    	else ($to_show = array());
    	$row_option = null;
    	$row_and_option = $this->splitRowColOption($row);
    	if (is_array($row_and_option)) {
    		$row = $row_and_option[0];
    		$row_option = count($row_and_option > 1) ? $row_and_option[1] : null;
    	} else {
    		$row = $row_and_option;
    	}
    	
    	$col_option = null;
    	$col_and_option = $this->splitRowColOption($col);
    	if (is_array($col_and_option)) {
    		$col = $col_and_option[0];
    		$col_option = count($col_and_option > 1) ? $col_and_option[1] : null;
    	} else {
    		$col = $col_and_option;
    	}
    	
    	$manager = ManagersManager::getManager()->getManagerForType($type);
    	
    	$reportingMgr = $this->get('reporting_manager');
    	if (false) $reportingMgr = new ReportingManager();
    	
    	$colRowRenderers = $reportingMgr->buildColRowsRenderer($manager->getObjectClass());
    	$reportingMgr->addSuppColRowsRenderer($manager, $colRowRenderers);
    	$rowRenderer = null;
    	$colRenderer = null;
    	if ($colRowRenderers) {
    		foreach ($colRowRenderers as $renderer) {
    			if ($renderer->getPropertyName() == $row) {
    				$rowRenderer = $renderer;
    				if ($row_option) {
    					$rowRenderer->setOptionValue($row_option);
    				}
    			}
    			if ($renderer->getPropertyName() == $col) {
    				$colRenderer = $renderer;
    				if ($col_option) {
    					$colRenderer->setOptionValue($col_option);
    				}
    			}
    			if ($rowRenderer && $colRenderer) break;
    		}
    	}
    	$datasRenderersAll = $reportingMgr->buildDatasRenderer($manager->getObjectClass());

    	$datasRenderer = array();
    	if ($datasRenderersAll !== null) {
    		$datasRendererAllMap = array();
    		foreach ($datasRenderersAll as $renderer) {
    			$datasRendererAllMap[$renderer->getPropertyName()] = $renderer;
    		}
    		foreach ($to_show as $name) {
    			if ($name == 'qte') $datasRenderer['qte'] = 'qte';
    			else {
    				if (isset($datasRendererAllMap[$name])) {
    					$datasRenderer[$name] = $datasRendererAllMap[$name];
    				}
    			}
    		}
    	}
    	$datas = array();
    	if ($type == 'article') {
    		if (!$this->get('security.authorization_checker')->isGranted('nav_reporting_article')) {
    			throw $this->createAccessDeniedException();
    		}
    		$articleManager = $manager;
    		$params = $articleManager->getSearchParamsFromRequest($request);
    		$datas = $articleManager->query($params, array());
    	}
    	
    	if ($reportingMgr->activeSecurity()) {
    		$secu = $this->container->get('security.context');
    		
    		// Security is on view list.
    		$authorizedDatas = array();
    		foreach ($datas as $data) {
    			if ($secu->isGranted('view', $data)) $authorizedDatas[] = $data;
    		}
    		
    		$datas = $authorizedDatas;
    	}
    	$report = new Report($rowRenderer, $colRenderer, $datasRenderer, $this->get('templating'));
    	$report->setDatas($datas);
    	
    	$report->parseDatas();
   	
    	return new Response($report->render());
    }
    
    public function saveAction(Request $request, $type) {

    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	
    	$report = $request->get('report');
    	$editables = $request->get('editables');
    	
    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();
    	
    	$saved_report = new ReportingSauvegarde();
    	$saved_report->setType($type);
    	
    	$form = $this->createFormBuilder($saved_report)
    	->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
    	->add('submit', SubmitType::class)
    	->getForm();
    	
    	$rsMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $rsMgr = new ReportingsSauvegardesManager();
    	
 
    	
    	$form->handleRequest($request);
    	
    	// Verify form and return the good template
    	if ($form->isSubmitted() && $form->isValid()) {
    		// get individu user.
    		$secuMgr = $this->get('security_manager');
    		$individu = $secuMgr->getCurrentIndividu();
    		
    		// Attention, if is exist, get report element.
    		$db_rs = $rsMgr->getReportingSauvegardeForTypeNomAndIndividu($type, $saved_report->getNom(), $individu->getId());
    		
    		if ($db_rs) {
    			// update db_rs.
    			$saved_report = $db_rs;
    		}
    		$saved_report->setChaineReporting($report);
    		
    		
    		$saved_report->setIndividu($individu);
    		
    		$saved_report->setChaineEditables($editables);
    		
    		$rsMgr->save($saved_report, true);
    		
    		return $this->render('ZombieBundle:Reporting:save_report_success.html.twig');
    	}
    	
    	// Get all reporting for the type
    	$list_reportings = $rsMgr->getAllReportingsSauvegardesForTypeAndIndividu($type, $individu->getId());
    	usort($list_reportings, array('ZombieBundle\Entity\Reporting\ReportingSauvegarde', 'sort_by_name'));
    	
    	//get all modules metrics
    	$ModuleMetrics = array();
    	$LogoModuleMetrics = array();
    	$SearchSemantique= array();
    	//Search Modules
    	$modulesMgr = $this->container->get('zombie_modules_manager');
    	if (false) $modulesMgr = new ModulesManager();
    	$modules = $modulesMgr->getZombieModules();
    	if ($modules) {
    		foreach ($modules as $name => $paramsModule) {
    			//METRICS
    			if (isset($paramsModule['metrics_object_class']) && $paramsModule['metrics_object_class']) {
    				
    				//get indicator in object metrics
    				$minmaxMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllIdIndicators();
    				foreach ($indicators as $indicator => $label ) {
    					$minmaxMetrics[$name.'_'. $indicator] = $label ;
    				}
    				
    				$ModuleMetrics[$name] = $minmaxMetrics;
    				
    				//get logoindicator in object metrics
    				$logoMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllLogoIndicators();
    				foreach ($indicators as $indicator => $labellogo ) {
    					$logoMetrics[$name.'_'. $indicator] = $labellogo;
    				}
    				$LogoModuleMetrics[$name] = $logoMetrics;
    			}
    			
    			
    			
    			
    			//SEARCH SEMANTIQUE
    			if (isset($paramsModule['search_semantique']) && $paramsModule['search_semantique']) {
    				
    				$SearchSemantique[] = $name;
    			}
    			//Trends
    			if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
    				$ServiceTrend[$name] = $paramsModule['service_trend'];
    			}
    		}
    	}	
    	
    	return $this->render('ZombieBundle:Reporting:save_report.html.twig', array('type' => $type, 'report' => $report, 'form' => $form->createView(),'list_reportings' => $list_reportings, 'moduleMetrics'=> $ModuleMetrics,'logoModuleMetrics'=> $LogoModuleMetrics,'searchSemantique' => $SearchSemantique, 'serviceTrend' => $ServiceTrend));
    }
    
    public function loadAction($type) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	
    	$rsMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $rsMgr = new ReportingsSauvegardesManager();
    	
    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();
    	
    	$secu = SymfonyUtils::getAuthorizationChecker();

    	$shared_reports = $individu->getSharedReportByTypeActive();
    	
    	$datas = array();
    	
    	$datas['type'] = $type;
    	
    	if ($secu->isGranted('nav_reporting_article')) {
    		$article_perso = $rsMgr->getAllReportingsSauvegardesForTypeAndIndividu('article', $individu->getId());
    		usort($article_perso, array('ZombieBundle\Entity\Reporting\ReportingSauvegarde', 'sort_by_name'));
    		
    		$article_fourni = ($shared_reports && isset($shared_reports['article'])) ? $shared_reports['article'] : array();
    		usort($article_fourni, array('ZombieBundle\Entity\Reporting\ReportingSauvegarde', 'sort_by_name'));
    		
    		$datas['article_perso'] = $article_perso;
    		$datas['article_fourni'] = $article_fourni;
    	}
   
    	
    	return $this->render('ZombieBundle:Reporting:load_report.html.twig', $datas);
    }
    
    public function configAction($type) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	$rsMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $rsMgr = new ReportingsSauvegardesManager();
    	
    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();
    	
    	$secu = SymfonyUtils::getAuthorizationChecker();
    	
    	$shared_reports = $individu->getSharedReportByTypeActive();
    	
    	$datas = array();
    	
    	$datas['type'] = $type;
    	
    	if ($secu->isGranted('nav_reporting_article')) {
	    	$article_perso = $rsMgr->getAllReportingsSauvegardesForTypeAndIndividu('article', $individu->getId());
	    	usort($article_perso, array('ZombieBundle\Entity\Reporting\ReportingSauvegarde', 'sort_by_name'));
	    	
	    	$article_fourni = ($shared_reports && isset($shared_reports['article'])) ? $shared_reports['article'] : array();
	    	usort($article_fourni, array('ZombieBundle\Entity\Reporting\ReportingSauvegarde', 'sort_by_name'));
	    		
	    	$datas['article_perso'] = $article_perso;
	    	$datas['article_fourni'] = $article_fourni;
    	}
    	return $this->render('ZombieBundle:Reporting:config_report.html.twig',$datas);
    }
    
    public function exportAction(Request $request) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	$reportingMgr = $this->get('reporting_manager');
    	if (false) $reportingMgr = new ReportingManager();
    	
    	
    	$type = $request->get('type');
    	$line1_head = $request->get('line1_head');
    	$line2_head = $request->get('line2_head');
    	$lines = $request->get('lines');
    	$footer = $request->get('footer');
    	
    	$line1_colspan = 1;
    	if (count($line1_head)) $line1_colspan = count($line2_head) / count($line1_head);
    	
    	$manager = ManagersManager::getManager()->getManagerForType($type);
    	
    	$to_show = array();
    	foreach ($line2_head as $dt) {
    		$type = $dt['type'];
    		$to_show[$type] = $type;
    	}
    	$to_show = array_values($to_show);
    	
    	$datasRenderersAll = $reportingMgr->buildDatasRenderer($manager->getObjectClass());
    	$datasRenderer = array();
    	if ($datasRenderersAll !== null) {
    		$datasRendererAllMap = array();
    		foreach ($datasRenderersAll as $renderer) {
    			$datasRendererAllMap[$renderer->getPropertyName()] = $renderer;
    		}
    		
    		foreach ($to_show as $name) {
    			if ($name == 'qte') $datasRenderer['qte'] = 'qte';
    			else {
    				if (isset($datasRendererAllMap[$name])) {
    					$datasRenderer[$name] = $datasRendererAllMap[$name];
    				}
    			}
    		}
    	}
    	
    	
    	// create excel
    	// Let's create the worksheet.
    	$locale = 'fr_fr';
    	
    	$black = new \PHPExcel_Style_Color();
    	$black->setRGB('000000');
    	
    	$light_blue = new \PHPExcel_Style_Color();
    	$light_blue->setRGB('DAEAF2');
    	
    	$blue = new \PHPExcel_Style_Color();
    	$blue->setRGB('B5D6EB');
    	
    	$light_grey = new \PHPExcel_Style_Color();
    	$light_grey->setRGB('F0F0F0');
    	
    	$excel = new \PHPExcel();
    	
    	$sheet = new \PHPExcel_Worksheet($excel, 'export');
    	
    	$excel->addSheet($sheet, 0);
    	$excel->removeSheetByIndex(1);
    	
    	$excel->setActiveSheetIndex(0);
    	
    	$sheet->getDefaultRowDimension()->setRowHeight(18);
    	$sheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
    	
    	// create headers.
    	$currCol = 3;
    	foreach ($line1_head as $val) {
    		$col = ExcelHelper::numToCol($currCol);
    		
    		$cell = $col.'2';
    		
    		//alignement.
    		$align = 'center';
    		if ($align == 'right') {
    			$sheet->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    		} else if ($align == 'center') {
    			$sheet->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		} else {
    			$sheet->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    		}
    		
    		
    		$cell_style = $sheet->getStyle($cell);
    		$cell_style->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    		$cell_style->getFill()->setStartColor($blue);
    		$cell_style->getFont()->setBold(true);
    		$cell_style->getAlignment()->setIndent(1);
    		$cell_style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    		$sheet->getRowDimension('2')->setRowHeight(24);
    		
    		$sheet->setCellValue($cell, html_entity_decode($val));
    		
    		if ($line1_colspan > 1) {
    			$endCell = ExcelHelper::numToCol(($currCol + $line1_colspan - 1)).'2';
    			$sheet->mergeCells($cell.":".$endCell);
    		}
    		
    		$currCol += $line1_colspan;
    	}
    	
    	$col_lengths = array();
    	
    	$currCol = 3;
    	$type_by_col = array();
    	$index = 0;
    	foreach ($line2_head as $dt) {
    		$type = $dt['type'];
    		$val = $dt['val'];
    		$col = ExcelHelper::numToCol($currCol);
    		
    		$type_by_col[$col] = $type;
    		
    		$cell = $col.'3';
    		
    		// alignement.
    		$align = 'center';
    		if ($align == 'right') {
    			$sheet->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    		} else if ($align == 'center') {
    			$sheet->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		} else {
    			$sheet->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    		}
    		    		
    		$cell_style = $sheet->getStyle($cell);
    		$cell_style->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    		$cell_style->getFill()->setStartColor($light_blue);
    		$cell_style->getAlignment()->setIndent(1);
    		$cell_style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    		$sheet->getRowDimension('3')->setRowHeight(24);
    		
    		$sheet->setCellValue($cell, html_entity_decode($val));
    		
    		$col_lengths[$col] = strlen($val);
    		
    		$currCol ++;
    	}
    	
    	$title_max_length = 0;
    	
    	// rows.
    	$currRow = 4;
    	foreach ($lines as $line) {
    		$title = $line['title'];
    		$cell_title = 'B'.$currRow;
    		
    		if (strlen($cell_title) > $title_max_length) $title_max_length = strlen($title);
    		
    		$sheet->setCellValue($cell_title, html_entity_decode($title));
    		
    		$cell_style = $sheet->getStyle($cell_title);
    		$cell_style->getAlignment()->setIndent(1);
    		$cell_style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    		
    		$currCol = 3;
    		foreach ($line['values'] as $value) {
    			$col = ExcelHelper::numToCol($currCol);
    			$cell_value = $col.$currRow;
    			
    			$type = $type_by_col[$col];
    			
    			$cell_style = $sheet->getStyle($cell_value);
    			$cell_style->getAlignment()->setIndent(1);
    			$cell_style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    			if (isset($datasRenderer[$type])) {
    				$renderer = $datasRenderer[$type];
    				if (false) $renderer = new ReportingDataRenderer();
    				
    				if ($renderer == 'qte') {
    					
    				} else {
    					$value = $renderer->transformValueForExcel($value);
    					$cell_style->getNumberFormat()->setFormatCode($renderer->getExcelFormatCode());
    				}
    				
    			}
    			
    			$sheet->setCellValue($cell_value, $value);
    			
    			if (strlen(''.$value) > $col_lengths[$col]) $col_lengths[$col] = strlen(''.$value);
    			
    			$currCol++;
    		}
    		
    		$currRow++;
    	}
    	
    	// footer.
    	$foot_title = $footer['title'];
    	$foot_title_cell = 'B'.$currRow;
    	
    	if (strlen($foot_title) > $title_max_length) $title_max_length = strlen($foot_title);
    	
    	$cell_style = $sheet->getStyle($foot_title_cell);
    	$cell_style->getAlignment()->setIndent(1);
    	$cell_style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    	$cell_style->getFont()->setBold(true);
    	$cell_style->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    	$cell_style->getFill()->setStartColor($light_grey);
    	
    	$sheet->setCellValue($foot_title_cell, html_entity_decode($foot_title));
    	
    	$currCol = 3;
    	
    	$cell_lenght_ratio = 1.5;
    	
    	// with of columns.
    	if ($title_max_length > 0) $sheet->getColumnDimension('B')->setWidth($title_max_length * $cell_lenght_ratio);
    	foreach ($col_lengths as $col => $length) {
    		$sheet->getColumnDimension($col)->setWidth($length * $cell_lenght_ratio);
    	}
    	
    	
    	$objWriter = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    	
    	$destFile = "export_report_".time()."_".rand(1000, 9999)."_".md5(''.rand(10000, 99999)).".xlsx";
    	
    	$objWriter = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    	$fileName = $this->get('kernel')->getRootDir().'/../web/documents/exports/'.$destFile;
    	$objWriter->save($fileName);
    	
    	return $this->render('SerielAppliToolboxBundle:Files:download.html.twig', array('destFile' => '/documents/exports/'.$destFile));
    }
    
    public function deleteAction($rs_id){
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	
    	$rsMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $rsMgr = new ReportingsSauvegardesManager();
    	
    	$rs = $rsMgr->getReportingSauvegarde($rs_id);
    	if (isset($rs)) {
	    	$rs->setDeleted(true);
	    	$rs->setDateDelete(new \DateTime());
	    	
	    	$securityMgr = $this->get('security_manager');
	    	$individu = $securityMgr->getCurrentIndividu();
	    	
	    	if ($individu) {
	    		$rs->setIndividuDelete($individu);
	    	}
	    	
	    	$rsMgr->save($rs,true);
   		}
    	return $this->render('ZombieBundle:Reporting:delete_report.html.twig',array('rs_id'=>$rs_id));
    }
 
    
    public function shareAction(Request $request, $rs_id) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	$rsMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $rsMgr = new ReportingsSauvegardesManager();
    	
    	$rs = $rsMgr->getReportingSauvegarde($rs_id);
    	if (!isset($rs)) {
    		return $this->render('ZombieBundle:Reporting:share_report_error.html.twig',array());
    	}
    	$submit = $request->get('submit');
    	if ($submit) {
    		// Save.
    		$individus_ids = $request->get('individus');
    		
    		$individus = array();
    		if ($individus_ids) {
    			$individusMgr = $this->get('individus_manager');
    			if (false) $individusMgr = new IndividusManager();
    			
    			$individus = $individusMgr->query(array('ids' => $individus_ids));
    		}
    		
    		$rs->setSharedWith($individus);
    		
    		$rsMgr->save($rs, true);
    		
    		return $this->render('ZombieBundle:Reporting:share_report_success.html.twig',array('rs' => $rs));
    	}
    	
    	$individusByEntite = array();
    	$entitesMap = array();
    	
    	$datas = $rs->getSharedWithByEntite();
    	if ($datas) {
    		foreach ($datas as $entite_id => $data) {
    			$entite = $data['entite'];
    			$entitesMap[$entite_id] = $entite;
    			
    			foreach ($data['individus'] as $individu) {
    				if (!isset($individusByEntite[$entite_id])) $individusByEntite[$entite_id] = array();
    				$individusByEntite[$entite_id][] = $individu;
    			}
    		}
    	}
    	
    	return $this->render('ZombieBundle:Reporting:share_report.html.twig',array('rs' => $rs, 'individusByEntite' => $individusByEntite, 'entitesMap' => $entitesMap));
    }
    
    public function searchIndivAction(Request $request,$type) {

    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_reporting')) {
    		throw $this->createAccessDeniedException();
    	}
    	$nom = $request->get('nom');
    	$results = array();
    
    	$individusMgr = $this->get('individus_manager');

    	$individus = $individusMgr->getIndividuForSearch($nom);
    	foreach ($individus as $ind) {
    		$results[] = array('entite' => $ind->getMainEntity(), 'individu' => $ind);
    	}
    
    	return $this->render('ZombieBundle:Recherche:search_indiv.html.twig', array('results' => $results));
    }

 
}
