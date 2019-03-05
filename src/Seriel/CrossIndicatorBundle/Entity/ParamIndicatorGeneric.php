<?php

namespace Seriel\CrossIndicatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * ParamIndicatorGeneric
 *
 * @ORM\Entity
 * @ORM\Table(name="param_indicator_generic",options={"engine"="MyISAM"})
 */
class ParamIndicatorGeneric extends RootObject implements Listable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="data_updated", type="boolean", nullable=false)
     */
    private $dataupdated;
    
    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator1", type="string", length=255)
     */
    private $labelIndicator1;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator2", type="string", length=255)
     */
    private $labelIndicator2;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator3", type="string", length=255)
     */
    private $labelIndicator3;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator4", type="string", length=255)
     */
    private $labelIndicator4;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator5", type="string", length=255)
     */
    private $labelIndicator5;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator6", type="string", length=255)
     */
    private $labelIndicator6;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator7", type="string", length=255)
     */
    private $labelIndicator7;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator8", type="string", length=255)
     */
    private $labelIndicator8;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator9", type="string", length=255)
     */
    private $labelIndicator9;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator10", type="string", length=255)
     */
    private $labelIndicator10;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator11", type="string", length=255)
     */
    private $labelIndicator11;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator12", type="string", length=255)
     */
    private $labelIndicator12;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator13", type="string", length=255)
     */
    private $labelIndicator13;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator14", type="string", length=255)
     */
    private $labelIndicator14;

    /**
     * @var string
     *
     * @ORM\Column(name="label_indicator15", type="string", length=255)
     */
    private $labelIndicator15;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator1", type="string", length=255)
     */
    private $formulaIndicator1;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator2", type="string", length=255)
     */
    private $formulaIndicator2;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator3", type="string", length=255)
     */
    private $formulaIndicator3;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator4", type="string", length=255)
     */
    private $formulaIndicator4;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator5", type="string", length=255)
     */
    private $formulaIndicator5;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator6", type="string", length=255)
     */
    private $formulaIndicator6;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator7", type="string", length=255)
     */
    private $formulaIndicator7;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator8", type="string", length=255)
     */
    private $formulaIndicator8;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator9", type="string", length=255)
     */
    private $formulaIndicator9;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator10", type="string", length=255)
     */
    private $formulaIndicator10;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator11", type="string", length=255)
     */
    private $formulaIndicator11;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator12", type="string", length=255)
     */
    private $formulaIndicator12;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator13", type="string", length=255)
     */
    private $formulaIndicator13;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator14", type="string", length=255)
     */
    private $formulaIndicator14;

    /**
     * @var string
     *
     * @ORM\Column(name="formula_indicator15", type="string", length=255)
     */
    private $formulaIndicator15;

    public function __construct()
    {
    	parent::__construct();
    	$this->dataupdated = false;
    }
    
    public function getListUid() {
    	return $this->getId();
    }
    
    public function getTuilesParamsSupp() {
    	return array();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dataupdated
     *
     * @param string $dataupdated
     *
     * @return ParamIndicatorGeneric
     */
    public function setDataupdated($dataupdated)
    {
    	$this->dataupdated= $dataupdated;
    	return $this;
    }
    
    /**
     * Get dataupdated
     *
     * @return string
     */
    public function getDataupdated()
    {
    	return $this->dataupdated;
    }
    
    /**
     * Set labelIndicator1
     *
     * @param string $labelIndicator1
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator1($labelIndicator1)
    {
        $this->labelIndicator1 = $labelIndicator1;

        return $this;
    }

    /**
     * Get labelIndicator1
     *
     * @return string
     */
    public function getLabelIndicator1()
    {
        return $this->labelIndicator1;
    }

    /**
     * Set labelIndicator2
     *
     * @param string $labelIndicator2
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator2($labelIndicator2)
    {
        $this->labelIndicator2 = $labelIndicator2;

        return $this;
    }

    /**
     * Get labelIndicator2
     *
     * @return string
     */
    public function getLabelIndicator2()
    {
        return $this->labelIndicator2;
    }

    /**
     * Set labelIndicator3
     *
     * @param string $labelIndicator3
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator3($labelIndicator3)
    {
        $this->labelIndicator3 = $labelIndicator3;

        return $this;
    }

    /**
     * Get labelIndicator3
     *
     * @return string
     */
    public function getLabelIndicator3()
    {
        return $this->labelIndicator3;
    }

    /**
     * Set labelIndicator4
     *
     * @param string $labelIndicator4
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator4($labelIndicator4)
    {
        $this->labelIndicator4 = $labelIndicator4;

        return $this;
    }

    /**
     * Get labelIndicator4
     *
     * @return string
     */
    public function getLabelIndicator4()
    {
        return $this->labelIndicator4;
    }

    /**
     * Set labelIndicator5
     *
     * @param string $labelIndicator5
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator5($labelIndicator5)
    {
        $this->labelIndicator5 = $labelIndicator5;

        return $this;
    }

    /**
     * Get labelIndicator5
     *
     * @return string
     */
    public function getLabelIndicator5()
    {
        return $this->labelIndicator5;
    }

    /**
     * Set labelIndicator6
     *
     * @param string $labelIndicator6
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator6($labelIndicator6)
    {
        $this->labelIndicator6 = $labelIndicator6;

        return $this;
    }

    /**
     * Get labelIndicator6
     *
     * @return string
     */
    public function getLabelIndicator6()
    {
        return $this->labelIndicator6;
    }

    /**
     * Set labelIndicator7
     *
     * @param string $labelIndicator7
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator7($labelIndicator7)
    {
        $this->labelIndicator7 = $labelIndicator7;

        return $this;
    }

    /**
     * Get labelIndicator7
     *
     * @return string
     */
    public function getLabelIndicator7()
    {
        return $this->labelIndicator7;
    }

    /**
     * Set labelIndicator8
     *
     * @param string $labelIndicator8
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator8($labelIndicator8)
    {
        $this->labelIndicator8 = $labelIndicator8;

        return $this;
    }

    /**
     * Get labelIndicator8
     *
     * @return string
     */
    public function getLabelIndicator8()
    {
        return $this->labelIndicator8;
    }

    /**
     * Set labelIndicator9
     *
     * @param string $labelIndicator9
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator9($labelIndicator9)
    {
        $this->labelIndicator9 = $labelIndicator9;

        return $this;
    }

    /**
     * Get labelIndicator9
     *
     * @return string
     */
    public function getLabelIndicator9()
    {
        return $this->labelIndicator9;
    }

    /**
     * Set labelIndicator10
     *
     * @param string $labelIndicator10
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator10($labelIndicator10)
    {
        $this->labelIndicator10 = $labelIndicator10;

        return $this;
    }

    /**
     * Get labelIndicator10
     *
     * @return string
     */
    public function getLabelIndicator10()
    {
        return $this->labelIndicator10;
    }

    /**
     * Set labelIndicator11
     *
     * @param string $labelIndicator11
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator11($labelIndicator11)
    {
        $this->labelIndicator11 = $labelIndicator11;

        return $this;
    }

    /**
     * Get labelIndicator11
     *
     * @return string
     */
    public function getLabelIndicator11()
    {
        return $this->labelIndicator11;
    }

    /**
     * Set labelIndicator12
     *
     * @param string $labelIndicator12
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator12($labelIndicator12)
    {
        $this->labelIndicator12 = $labelIndicator12;

        return $this;
    }

    /**
     * Get labelIndicator12
     *
     * @return string
     */
    public function getLabelIndicator12()
    {
        return $this->labelIndicator12;
    }

    /**
     * Set labelIndicator13
     *
     * @param string $labelIndicator13
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator13($labelIndicator13)
    {
        $this->labelIndicator13 = $labelIndicator13;

        return $this;
    }

    /**
     * Get labelIndicator13
     *
     * @return string
     */
    public function getLabelIndicator13()
    {
        return $this->labelIndicator13;
    }

    /**
     * Set labelIndicator14
     *
     * @param string $labelIndicator14
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator14($labelIndicator14)
    {
        $this->labelIndicator14 = $labelIndicator14;

        return $this;
    }

    /**
     * Get labelIndicator14
     *
     * @return string
     */
    public function getLabelIndicator14()
    {
        return $this->labelIndicator14;
    }

    /**
     * Set labelIndicator15
     *
     * @param string $labelIndicator15
     *
     * @return ParamIndicatorGeneric
     */
    public function setLabelIndicator15($labelIndicator15)
    {
        $this->labelIndicator15 = $labelIndicator15;

        return $this;
    }

    /**
     * Get labelIndicator15
     *
     * @return string
     */
    public function getLabelIndicator15()
    {
        return $this->labelIndicator15;
    }

    /**
     * Set formulaIndicator1
     *
     * @param string $formulaIndicator1
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator1($formulaIndicator1)
    {
        $this->formulaIndicator1 = $formulaIndicator1;

        return $this;
    }

    /**
     * Get formulaIndicator1
     *
     * @return string
     */
    public function getFormulaIndicator1()
    {
        return $this->formulaIndicator1;
    }

    /**
     * Set formulaIndicator2
     *
     * @param string $formulaIndicator2
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator2($formulaIndicator2)
    {
        $this->formulaIndicator2 = $formulaIndicator2;

        return $this;
    }

    /**
     * Get formulaIndicator2
     *
     * @return string
     */
    public function getFormulaIndicator2()
    {
        return $this->formulaIndicator2;
    }

    /**
     * Set formulaIndicator3
     *
     * @param string $formulaIndicator3
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator3($formulaIndicator3)
    {
        $this->formulaIndicator3 = $formulaIndicator3;

        return $this;
    }

    /**
     * Get formulaIndicator3
     *
     * @return string
     */
    public function getFormulaIndicator3()
    {
        return $this->formulaIndicator3;
    }

    /**
     * Set formulaIndicator4
     *
     * @param string $formulaIndicator4
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator4($formulaIndicator4)
    {
        $this->formulaIndicator4 = $formulaIndicator4;

        return $this;
    }

    /**
     * Get formulaIndicator4
     *
     * @return string
     */
    public function getFormulaIndicator4()
    {
        return $this->formulaIndicator4;
    }

    /**
     * Set formulaIndicator5
     *
     * @param string $formulaIndicator5
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator5($formulaIndicator5)
    {
        $this->formulaIndicator5 = $formulaIndicator5;

        return $this;
    }

    /**
     * Get formulaIndicator5
     *
     * @return string
     */
    public function getFormulaIndicator5()
    {
        return $this->formulaIndicator5;
    }

    /**
     * Set formulaIndicator6
     *
     * @param string $formulaIndicator6
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator6($formulaIndicator6)
    {
        $this->formulaIndicator6 = $formulaIndicator6;

        return $this;
    }

    /**
     * Get formulaIndicator6
     *
     * @return string
     */
    public function getFormulaIndicator6()
    {
        return $this->formulaIndicator6;
    }

    /**
     * Set formulaIndicator7
     *
     * @param string $formulaIndicator7
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator7($formulaIndicator7)
    {
        $this->formulaIndicator7 = $formulaIndicator7;

        return $this;
    }

    /**
     * Get formulaIndicator7
     *
     * @return string
     */
    public function getFormulaIndicator7()
    {
        return $this->formulaIndicator7;
    }

    /**
     * Set formulaIndicator8
     *
     * @param string $formulaIndicator8
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator8($formulaIndicator8)
    {
        $this->formulaIndicator8 = $formulaIndicator8;

        return $this;
    }

    /**
     * Get formulaIndicator8
     *
     * @return string
     */
    public function getFormulaIndicator8()
    {
        return $this->formulaIndicator8;
    }

    /**
     * Set formulaIndicator9
     *
     * @param string $formulaIndicator9
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator9($formulaIndicator9)
    {
        $this->formulaIndicator9 = $formulaIndicator9;

        return $this;
    }

    /**
     * Get formulaIndicator9
     *
     * @return string
     */
    public function getFormulaIndicator9()
    {
        return $this->formulaIndicator9;
    }

    /**
     * Set formulaIndicator10
     *
     * @param string $formulaIndicator10
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator10($formulaIndicator10)
    {
        $this->formulaIndicator10 = $formulaIndicator10;

        return $this;
    }

    /**
     * Get formulaIndicator10
     *
     * @return string
     */
    public function getFormulaIndicator10()
    {
        return $this->formulaIndicator10;
    }

    /**
     * Set formulaIndicator11
     *
     * @param string $formulaIndicator11
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator11($formulaIndicator11)
    {
        $this->formulaIndicator11 = $formulaIndicator11;

        return $this;
    }

    /**
     * Get formulaIndicator11
     *
     * @return string
     */
    public function getFormulaIndicator11()
    {
        return $this->formulaIndicator11;
    }

    /**
     * Set formulaIndicator12
     *
     * @param string $formulaIndicator12
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator12($formulaIndicator12)
    {
        $this->formulaIndicator12 = $formulaIndicator12;

        return $this;
    }

    /**
     * Get formulaIndicator12
     *
     * @return string
     */
    public function getFormulaIndicator12()
    {
        return $this->formulaIndicator12;
    }

    /**
     * Set formulaIndicator13
     *
     * @param string $formulaIndicator13
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator13($formulaIndicator13)
    {
        $this->formulaIndicator13 = $formulaIndicator13;

        return $this;
    }

    /**
     * Get formulaIndicator13
     *
     * @return string
     */
    public function getFormulaIndicator13()
    {
        return $this->formulaIndicator13;
    }

    /**
     * Set formulaIndicator14
     *
     * @param string $formulaIndicator14
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator14($formulaIndicator14)
    {
        $this->formulaIndicator14 = $formulaIndicator14;

        return $this;
    }

    /**
     * Get formulaIndicator14
     *
     * @return string
     */
    public function getFormulaIndicator14()
    {
        return $this->formulaIndicator14;
    }

    /**
     * Set formulaIndicator15
     *
     * @param string $formulaIndicator15
     *
     * @return ParamIndicatorGeneric
     */
    public function setFormulaIndicator15($formulaIndicator15)
    {
        $this->formulaIndicator15 = $formulaIndicator15;

        return $this;
    }

    /**
     * Get formulaIndicator15
     *
     * @return string
     */
    public function getFormulaIndicator15()
    {
        return $this->formulaIndicator15;
    }
    
    /**
     * Get labelsGeneric
     *
     * @return array
     */
    public function getLabelsGeneric()
    {
    	$labels =array();
    	$labels['indicator1'] = $this->getLabelIndicator1();
    	$labels['indicator2'] = $this->getLabelIndicator2();
    	$labels['indicator3'] = $this->getLabelIndicator3();
    	$labels['indicator4'] = $this->getLabelIndicator4();
    	$labels['indicator5'] = $this->getLabelIndicator5();
    	$labels['indicator6'] = $this->getLabelIndicator6();
    	$labels['indicator7'] = $this->getLabelIndicator7();
    	$labels['indicator8'] = $this->getLabelIndicator8();
    	$labels['indicator9'] = $this->getLabelIndicator9();
    	$labels['indicator10'] = $this->getLabelIndicator10();
    	$labels['indicator11'] = $this->getLabelIndicator11();
    	$labels['indicator12'] = $this->getLabelIndicator12();
    	$labels['indicator13'] = $this->getLabelIndicator13();
    	$labels['indicator14'] = $this->getLabelIndicator14();
    	$labels['indicator15'] = $this->getLabelIndicator15();
    	return $labels;
    }
    
    /**
     * set labelsGeneric
     * @param array(id => label) $labels
     * @return ParamIndicatorGeneric
     */
    public function setLabelsGeneric($labels)
    {

    	$label = isset($labels['indicator1']) ? $labels['indicator1'] : '';
		$this->setLabelIndicator1($label);
		$label = isset($labels['indicator2']) ? $labels['indicator2'] : '';
		$this->setLabelIndicator2($label);
		$label = isset($labels['indicator3']) ? $labels['indicator3'] : '';
		$this->setLabelIndicator3($label);
		$label = isset($labels['indicator4']) ? $labels['indicator4'] : '';
		$this->setLabelIndicator4($label);
		$label = isset($labels['indicator5']) ? $labels['indicator5'] : '';
		$this->setLabelIndicator5($label);
		$label = isset($labels['indicator6']) ? $labels['indicator6'] : '';
		$this->setLabelIndicator6($label);
		$label = isset($labels['indicator7']) ? $labels['indicator7'] : '';
		$this->setLabelIndicator7($label);
		$label = isset($labels['indicator8']) ? $labels['indicator8'] : '';
		$this->setLabelIndicator8($label);
		$label = isset($labels['indicator9']) ? $labels['indicator9'] : '';
		$this->setLabelIndicator9($label);
		$label = isset($labels['indicator10']) ? $labels['indicator10'] : '';
		$this->setLabelIndicator10($label);
		$label = isset($labels['indicator11']) ? $labels['indicator11'] : '';
		$this->setLabelIndicator11($label);
		$label = isset($labels['indicator12']) ? $labels['indicator12'] : '';
		$this->setLabelIndicator12($label);
		$label = isset($labels['indicator13']) ? $labels['indicator13'] : '';
		$this->setLabelIndicator13($label);
		$label = isset($labels['indicator14']) ? $labels['indicator14'] : '';
		$this->setLabelIndicator14($label);
		$label = isset($labels['indicator15']) ? $labels['indicator15'] : '';
		$this->setLabelIndicator15($label);
		
		return $this;
    }
    /**
     * Get formulasGeneric
     *
     * @return array
     */
    public function getFormulasGeneric()
    {
    	$formulas =array();
    	$formulas['indicator1'] = $this->getFormulaIndicator1();
    	$formulas['indicator2'] = $this->getFormulaIndicator2();
    	$formulas['indicator3'] = $this->getFormulaIndicator3();
    	$formulas['indicator4'] = $this->getFormulaIndicator4();
    	$formulas['indicator5'] = $this->getFormulaIndicator5();
    	$formulas['indicator6'] = $this->getFormulaIndicator6();
    	$formulas['indicator7'] = $this->getFormulaIndicator7();
    	$formulas['indicator8'] = $this->getFormulaIndicator8();
    	$formulas['indicator9'] = $this->getFormulaIndicator9();
    	$formulas['indicator10'] = $this->getFormulaIndicator10();
    	$formulas['indicator11'] = $this->getFormulaIndicator11();
    	$formulas['indicator12'] = $this->getFormulaIndicator12();
    	$formulas['indicator13'] = $this->getFormulaIndicator13();
    	$formulas['indicator14'] = $this->getFormulaIndicator14();
    	$formulas['indicator15'] = $this->getFormulaIndicator15();
    	return $formulas;
    }
    
    /**
     * set formulasGeneric
     * @param array(id => formula) $formulas
     * @return ParamIndicatorGeneric
     */
    public function setFormulasGeneric($formulas)
    {
    	
    	$formula = isset($formulas['indicator1']) ? $formulas['indicator1'] : '';
    	$this->setFormulaIndicator1($formula);
    	$formula = isset($formulas['indicator2']) ? $formulas['indicator2'] : '';
    	$this->setFormulaIndicator2($formula);
    	$formula = isset($formulas['indicator3']) ? $formulas['indicator3'] : '';
    	$this->setFormulaIndicator3($formula);
    	$formula = isset($formulas['indicator4']) ? $formulas['indicator4'] : '';
    	$this->setFormulaIndicator4($formula);
    	$formula = isset($formulas['indicator5']) ? $formulas['indicator5'] : '';
    	$this->setFormulaIndicator5($formula);
    	$formula = isset($formulas['indicator6']) ? $formulas['indicator6'] : '';
    	$this->setFormulaIndicator6($formula);
    	$formula = isset($formulas['indicator7']) ? $formulas['indicator7'] : '';
    	$this->setFormulaIndicator7($formula);
    	$formula = isset($formulas['indicator8']) ? $formulas['indicator8'] : '';
    	$this->setFormulaIndicator8($formula);
    	$formula = isset($formulas['indicator9']) ? $formulas['indicator9'] : '';
    	$this->setFormulaIndicator9($formula);
    	$formula = isset($formulas['indicator10']) ? $formulas['indicator10'] : '';
    	$this->setFormulaIndicator10($formula);
    	$formula = isset($formulas['indicator11']) ? $formulas['indicator11'] : '';
    	$this->setFormulaIndicator11($formula);
    	$formula = isset($formulas['indicator12']) ? $formulas['indicator12'] : '';
    	$this->setFormulaIndicator12($formula);
    	$formula = isset($formulas['indicator13']) ? $formulas['indicator13'] : '';
    	$this->setFormulaIndicator13($formula);
    	$formula = isset($formulas['indicator14']) ? $formulas['indicator14'] : '';
    	$this->setFormulaIndicator14($formula);
    	$formula = isset($formulas['indicator15']) ? $formulas['indicator15'] : '';
    	$this->setFormulaIndicator15($formula);
    	
    	$this->dataupdated = false;
    	
    	return $this;
    }
}

