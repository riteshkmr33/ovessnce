<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino Socket 2.0                                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

class LS_smiley
{

	// The all new smiley parser
	private $smileyList = array( 
	    ':)' => '<img src="sparser/smilies/smiley-smile.png" alt="smile" />', 
	    ':-)' => '<img src="sparser/smilies/smiley-smile.png" alt="smile" />', 
	    ':(' => '<img src="sparser/smilies/smiley-frown.png" alt="frown" />', 
	    ':-(' => '<img src="sparser/smilies/smiley-frown.png" alt="frown" />', 
	    ':D' => '<img src="sparser/smilies/smiley-laughing.png" alt="laugh" />', 
	    ':=D' => '<img src="sparser/smilies/smiley-laughing.png" alt="laugh" />', 
	    ';)' => '<img src="sparser/smilies/smiley-wink.png" alt="wink" />', 
	    ';-)' => '<img src="sparser/smilies/smiley-wink.png" alt="wink" />', 
	    ':V' => '<img src="sparser/smilies/smiley-shout.png" alt="shout" />', 
	    ':-V' => '<img src="sparser/smilies/smiley-shout.png" alt="shout" />', 
	    ':p' => '<img src="sparser/smilies/smiley-tongue.png" alt="tongue" />', 
	    ':-p' => '<img src="sparser/smilies/smiley-tongue.png" alt="tongue" />', 
	    '8)' => '<img src="sparser/smilies/smiley-cool.png" alt="cool" />', 
	    '8-)' => '<img src="sparser/smilies/smiley-cool.png" alt="cool" />', 
	    ':*' => '<img src="sparser/smilies/smiley-kiss.png" alt="kiss" />', 
	    ':-*' => '<img src="sparser/smilies/smiley-kiss.png" alt="kiss" />', 
	    ';(' => '<img src="sparser/smilies/smiley-cry.png" alt="cry" />', 
	    ';-(' => '<img src="sparser/smilies/smiley-cry.png" alt="cry" />', 
	    ':-/' => '<img src="sparser/smilies/smiley-what.png" alt="what" />', 
	    ':/' => '<img src="sparser/smilies/smiley-what.png" alt="what" />'
	    );
	
	// Bad word list 
	private $badWordList = array("javascript:", "expression:", "src=\"java", "src=\"script", "/*comment*/", "script:", "alert(", ":expr"); 
	// Replace with nothing
	private $goodWord = ''; 
	 
	## Main Functions to interact with class 
	public function parseSmileytext($text, $smileys = 1, $badwords = 1) {
		
		// do not change any url to images
	    $text = str_replace('://', '#link#', $text);
	    
	    // parse the smilies
	    if ($smileys == 1) { 
	        $text = $this->parseSmiley($text); 
	    } 
	    
	    if ($badwords == 1) { 
	    	$text = $this->parseBadWords($text); 
	    }
	    
	    $text = str_replace('sparser/smilies/smiley-', '../img/smilies/smiley-', $text);
	    
	    //fix 
	    return str_replace('#link#', '://', $text); 
	}
	
	//get back orignal string 
	public function unparseSmileytext($text){ 
	    return $this->unparseSmiley($text); 
	} 
	 
	## Functions to perform actions 
	private function parseSmiley($text) { 
	    return str_ireplace(array_keys($this->smileyList), $this->smileyList, $text); 
	}
	
	private function unparseSmiley($text) { 
	    return str_replace($this->smileyList, array_keys($this->smileyList), $text); 
	}
	
	private function parseBadWords($text) { 
	    return str_replace($this->badWordList, $this->goodWord, $text); 
	}

}
?>