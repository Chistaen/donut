<?php

	// 	Donut 				🍩 
	//	Dictionary Toolkit
	// 		Version a.1
	//		Written by Thomas de Roo
	//		Licensed under MIT

	//	++	File: admin.structure.cset.php

class pAuthStructure extends pStructure{
	
	private $_ajax, $_section, $_template;

	public function compile(){

		if(isset(pAdress::arg()['section']))
			$this->_section = pAdress::arg()['section'];
		else
			$this->_section = $this->_default_section;

		$this->_template = new pLoginTemplate();

		pMainTemplate::setTitle($this->_page_title);

	}

	protected function logInAjax(){

		if(isset(pAdress::post()['username'], pAdress::post()['password']) AND empty(pAdress::post()['username']) OR empty(pAdress::post()['password']))
			return p::Out($this->_template->warning());

		if(pUser::checkCre(pAdress::post()['username'], pAdress::post()['password'])){
			(new pUser)->logIn(pAdress::post()['username']);
			return p::Out($this->_template->succes()."<script>window.location = '".p::Url('?home')."';</script>");
		}
		else
			return p::Out($this->_template->errorMessage());
	}

	protected function logOut(){
		(new pUser)->logOut();	
		return p::Url('?auth', true);
	}

	public function render(){

		if($this->_section == 'profile')
			return p::Out("hoi");

		if($this->_section == 'logout')
			return $this->logOut();

		if(isset(pAdress::arg()['ajax']))
			return $this->logInAjax();

		if(pUser::noGuest())
			return p::Url('?home', true);

		p::Out("<div class='home-margin'>".$this->_template->loginForm()."</div>");

	}

}