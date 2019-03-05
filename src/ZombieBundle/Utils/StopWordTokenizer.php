<?php
namespace ZombieBundle\Utils;

use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\AppliToolboxBundle\Utils\StringUtils;

/**
 * Simple white space tokenizer. Breaks either on whitespace or on word
 * boundaries (ex.: dots, commas, etc)
 * Does not include white space in tokens.
 * Does not include punctuation character in token
 * Does not include stop word french in token
 */
class StopWordTokenizer
{
    private $language;
    private $useRacinisation;
    private $stopWords;
    private $stringUtil;
    
    public function __construct($language,$useRacinisation = false)
	{
        $this->language = $language;
        $this->useRacinisation = $useRacinisation;
        $this->stopWords = $this->getStopWord();
        $this->stringUtil = new StringUtils();
	}
    public function tokenize($str)
    {
    	$str= strtolower($str);
    	
        $arr = array();


        // for the character classes
        // see http://php.net/manual/en/regexp.reference.unicode.php
        $pat = '/
                    ([\pZ\pC]*)			# match any separator or other
                                        # in sequence
                    (
                        [^\pP\pZ\pC]+ |	# match a sequence of characters
                                        # that are not punctuation,
                                        # separator or other

                        .				# match punctuations one by one
                    )
                    ([\pZ\pC]*)			# match a sequence of separators
                                        # that follows
                /xu';
        preg_match_all($pat,$str,$arr);
        
        // return list of word in array
        $Words = $arr[2];

        $Words = $this->removeStopWord($Words,$this->stopWords);
        if ($this->useRacinisation ) {
            $Words =  $this->wordsToRacinisation($Words);
        }
        return $Words;
    }

    // use snowball for several word
    public function wordsToRacinisation($Words) {
        $racinWords = array();
        foreach ($Words as $word) {
            $racinWords[] = $this->wordToRacinisation($word);
        }
        return $racinWords;
    }

    // use snowball
    // for use in Information Retrieval
    public function wordToRacinisation($word) {
        
        $word = $this->stringUtil->removeAccents($word);
        $word= str_replace('œ', 'oe', $word);
        $word= str_replace('Œ', 'oe', $word);
        $word= str_replace('«', '', $word);
        $word= str_replace('»', '', $word);
       
        $word= iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $word);
        $word= str_replace('\\', '', $word);
        $word =  stem_french($word);
        if ($word == null) {
            $word = '';
        }
        return $word;
    }

    // remove word in list stop words
    public function removeStopWord($Words,$stopWords) {
        //remove word with length < 2
        $wordscorrect = array();
        foreach ($Words as $word) {
            if(mb_strlen($word) > 1 ) {
                $wordscorrect[] = $word;
            }
        }
        return $arrayArticle = array_diff($wordscorrect, $stopWords);
    }

    // get list stop words
    public function getStopWord() {
        $StopWordMgr = ManagersManager::getManager()->getContainer()->get('stopword_manager');
        $stopwards =  $StopWordMgr->getStopWordByLanguage($this->language);
        $array = array();
        foreach ($stopwards as $stopward) {
            $array[] = $stopward->getWord();
        }
        return $array;
    }

}
