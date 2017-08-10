<?php
	// 	Donut 				🍩 
	//	Dictionary Toolkit
	// 		Version a.1
	//		Written by Thomas de Roo
	//		Licensed under MIT

	//	++		File: inflectionTable.class.php

// Used to build an inflection table

class pInflectionTable extends pTemplatePiece {

	private $_output, $_lemma, $_inflCache;

	public function __construct($dMCache, $lemma, $twolc, $modeArray, $compiledParadigms){
		$this->_lemma = $lemma;

		$dMCache->setCondition(" WHERE lemma_id = ".$lemma->read('id'));

		foreach($dMCache->getObjects()->fetchAll() as $cachedInflection)
			$this->_inflCache[$cachedInflection['hash']] = $cachedInflection['inflected_form'];

		$dMCache->setCondition("");

		$mode = $compiledParadigms[$modeArray['id']];
		$mode_name = $modeArray['name'];

		$output = "<div class='inflections_mode_wrap'><table class='inflections'><tr class='title'><td colspan='2'>".$mode_name."</td></tr>";

		foreach($mode as $headingHolder){
			$heading = $headingHolder['heading'];
			$output .= "<tr class='heading'><td colspan='2'>".$heading['name']."</td></tr>";
			foreach($headingHolder['rows'] as $row){
				// Generating the surface form
				$surface = $twolc->feed($row['inflected'][0])->toSurface();
				// Outing the surface form
				$output .= "<tr><td class='row_name'>".$row['self']['name']."</td><td class='row_inflected'>".$surface."</td></tr>";
				// Chaching the surface form
				$this->cacheInflection($dMCache, $surface, $row, $heading, $modeArray);
			}
		}

		return $this->_output = $output."</table></div>";

	}

	public function cacheInflection($dM, $inflection, $row, $heading, $mode){
		$hash = $row['self']['id'].'_'.$heading['id'].'_'.$mode['id'];
		// Already cached
		if(isset($this->_inflCache[$hash]) AND $this->_inflCache[$hash] == $inflection)
			return false;
		elseif(isset($this->_inflCache[$hash]))
			$dM->customQuery("DELETE FROM lemmatization WHERE lemma_id = ".$this->_lemma->read('id') . " AND hash = '".$hash."'");
		if($inflection != $this->_lemma->read('native') AND $inflection != $this->_lemma->read('lexical_form'))
			return $dM->customQuery("INSERT INTO lemmatization VALUES(NULL, ".p::Quote($inflection).", '".$hash."', '".$this->_lemma->read('id')."');");
		return false;
	}

	public function __toString(){
		return $this->_output;
	}

}