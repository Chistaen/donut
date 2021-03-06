<?php
// Donut: open source dictionary toolkit
// version    0.11-dev
// author     Thomas de Roo
// license    MIT
// file:      TwoLevelCompiler.class.php

// the Two Level Compiler

class pTwolc{

	protected $_rules, $_parsedRules, $_leftContexts, $_rightContexts, $_pending;

	public function __construct($rules){
		$this->_rules = $rules;
	}

	public function feed($string){

		// Let's create a string object
		$string = new pTwolcString($string);

		// The changes have to be run twice, to give variables a chance!
		// TODO; FOR NO ONLY RUN ONCE
		for ($i=0; $i < 1; $i++) { 
			// First time, to replace variables
			if($this->_pending != null)
				foreach($this->_pending as $change)
					$string->change($change['pattern'], $change['replace'], $change['middle']);
		}


		return $string;

	}

	public function compile(){

		foreach($this->_rules as $rule)
			$this->parseRule($rule);

		foreach($this->_parsedRules as $rule)
			$this->pendChange($rule);
	}


	// Rule looks like this: a_CON_b=>^^ (^ = hitter)
	protected function parseRule($rule){
		// First we need to split the two contexts from eachother
		$splitRule1 = explode('=>', $rule);
		$replace = $splitRule1[1];
		$splitRule = explode('_', $splitRule1[0]);
		$leftPart = $splitRule[0];		// Left context
		$middlePart = $splitRule[1];	// Search context
		$rightPart = $splitRule[2];		// Right context
		$parsedRule = array(
			'left' => $this->parseContext($leftPart),
			'middle' => "(".$this->parseContext($middlePart, false, true).")",
			'right' => $this->parseContext($rightPart, true),
			'replace' => $replace,
			'original_middle' => $middlePart,
		);
		return $this->_parsedRules[] = $parsedRule;
	}

	protected function parseReplace($replacePattern){
		// Might be changed a little
		$replaced = $replacePattern;
		return $replaced;
	} 	

	protected function pendChange($rule){

		// Starting our regular expresion
		$regex = '/';

		if(!empty($rule['left']))
			$regex .= "(?<=".implode('',$rule['left']).")";

		$regex .= $rule['middle'];

		if(!empty($rule['right']))
			$regex .= "(?=".implode('',$rule['right']).")";

		// UTF-8 support enabled
		$regex .= '/u';

		$this->_pending[] = array('pattern' => $regex, 'replace' => $this->parseReplace($rule['replace']), 'middle' => $rule['original_middle']);
		
	}


	public function parseContext($context, $right = false, $middle = false){
		$parsedContext = array();
		$splitContext = explode('.', $context);
		// CON.CON.CON (three consonants)
		// -3.-2.-1.X

		if($middle)
			$count = 0;
		elseif($right)
			$count = 1;
		else
			$count = count($splitContext);


		$captureCount = 1;

		foreach($splitContext as $char){
			//echo $char."<br />";
			if($char == '<:'){
				$parsedContext[$count] = '\A'; 
				continue;
			}elseif($char == ':>'){
				$parsedContext[$count] = '\z'; 
				continue;
			}
			elseif(p::StartsWith($char, '(') AND p::EndsWith($char, ')') AND is_numeric(substr($char, 1, -1))){
				$parsedContext[$count] = '\\'.substr($char, 1, -1);
				continue;
			}
			// Only works in (cap)_%_
			elseif($char == '%'){
				$parsedContext[$count] = '\\'.$captureCount;
				continue;
			}
			elseif($char == ':'){
				$parsedContext[$count] = '[:]{1}';
				continue;
			}
			// Variables
			elseif (p::StartsWith($char, '&')) {
				$char = substr($char, 1);
				$explode = explode(',', $char);
				$output = '(?:';
				$matches = array();
				foreach (explode(',', $char) as $var)
					$matches[] = $var;
				$parsedContext[$count] = "[&]{1}".$output.implode('|', $matches).")";
			}
			// literal occurances 'at'
			elseif(p::StartsWith($char, '\'') AND p::EndsWith($char, '\'')){
				$char = substr($char, 1, -1);
				$parsedContext[$count] = "[".$char."]{1}";
			}
			// one of follolwing [a,b,c]
			elseif(p::StartsWith($char, '[') AND p::EndsWith($char, ']')){
				$char = substr($char, 1, -1);
				$parsedContext[$count] = "[".implode('', explode(',', $char))."]{1}";
			}
			elseif(p::StartsWith($char, '[^') AND p::EndsWith($char, ']')){
				$char = substr($char, 1, -1);
				$parsedContext[$count] = "[^".implode('', explode(',', $char))."]{1}";
			}
			elseif(strlen($char) == 4 AND p::StartsWith($char, '^') AND mb_strtoupper(substr($char, 1), 'utf-8') == substr($char, 1) ){
				$parsedContext[$count] = "[^".pAlphabet::getGroupsAsString(substr($char, 1))."]{1}";
			}
			elseif(strlen($char) == 3 AND mb_strtoupper($char, 'utf-8') == $char)
				if(pAlphabet::getGroupsAsString($char) != '')
					$parsedContext[$count] = "[".pAlphabet::getGroupsAsString($char)."]{1}";
				else
					$parsedContext[$count] = preg_quote($char);
			elseif($char == '+' OR $char == '#')
				$parsedContext[$count] = '['.$char.']{1}';	
			elseif(strlen($char) == 1){
				$parsedContext[$count] = "[".$char."]{1}";
			}
			elseif(p::StartsWith($char, '(') AND p::EndsWith($char, ')')){
				$parsedContext[$count] = '('.$this->parseContext(substr($char, 1, -1))[1].')';
				$captureCount++;
			}
			else{
				$parsedContext[$count] = $char;
			}

			if($right OR $middle)
				$count++;
			else
				$count--;
		}


		if($middle)
			return implode('', $parsedContext);

		
		return $parsedContext;
	}


}