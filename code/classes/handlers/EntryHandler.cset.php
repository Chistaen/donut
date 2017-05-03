<?php

	// 	Donut 				🍩 
	//	Dictionary Toolkit
	// 		Version a.1
	//		Written by Thomas de Roo
	//		Licensed under MIT

	//	++	File: EntryObject.cset.php

//$structure, $icon, $surface, $table, $itemsperpage, $dfs, $actions, $actionbar, $paginated, $section, $app = 'dictionary-admin')

class pEntryHandler extends pHandler{

	private $_template, $_meta;

	public function __construct(){
		// First we are calling the parent's constructor (pHandler)
		call_user_func_array('parent::__construct', func_get_args());

		// Now we need to know if there is a 
		if(!isset($this->_activeSection['entry_meta']))
			return trigger_error("pEntryHandler needs a **entry_meta** key in its array structure.", E_USER_ERROR);

		$this->_meta = $this->_activeSection['entry_meta'];
	}


	// This is the internal dispatcher for action of an entry
	public function catchAction($action){

		// reset translations
		if($action == 'resetTranslations'){
			unset($_SESSION['returnLanguage']);
			pUrl('?entry/'.pAdress::arg()['id'], true);
		}

		// Discuss
		if($action == 'discuss'){
			pOut("<div class='wdComments-list'>
					<div class='wdComment'>
						<div class='wdContent'>
							mysql_get_host_info();
						</div>
						<div class='children'>
							<div class='wdComment'>
								<div class='wdContent'>
									hoi
								</div>
								<div class='children'>
										<div class='wdComment'>
								<div class='wdContent'>
									hoi
								</div>
								<div class='children'>

									<div class='wdComment'>
								<div class='wdContent'>
									hoi
								</div>
								<div class='children'>
								</div>
							</div>
								</div>
							</div>
								</div>
							</div>
						</div>
					</div>
				</div>");
		}

	}

	public function render(){

		$this->_actionbar->generate((isset(pAdress::arg()['id'])) ?pAdress::arg()['id'] : -1);

		pOut($this->_actionbar->output);

		// Shortcut to the data
		$data = $this->dataModel->data()->fetchAll()[0];

		// Passing the data on to the template
		$this->_template = new $this->_activeSection['template']($data, $this->_activeSection);

		// We might pass this job to an object if that is specified in the metadata
		if(isset($this->_meta['parseAsObject']) AND class_exists($this->_meta['parseAsObject'])){
			$object = new $this->_meta['parseAsObject']($this->dataModel);
			$object->setTemplate($this->_template);
			if(isset($this->_activeSection['subobjects'])){
				$object->fillSubEntries($this->_activeSection['subobjects']);
			}
			return $object->renderEntry();
		}
	}
}