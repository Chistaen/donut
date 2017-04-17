<?php
	// 	Donut 				🍩 
	//	Dictionary Toolkit
	// 		Version a.1
	//		Written by Thomas de Roo
	//		Licensed under MIT

	//	++	File: dictionary.admin.struct.php
	// The structure of the dictionary admin panel

$saveStrings = array(null, SAVE, SAVING, SAVED_EMPTY, SAVED_ERROR, SAVED, SAVE_LINKBACK);

// Datafields: __construct($name, $surface = '', $width= '20%', $type = '', $showTable = true, $showForm = true, $required = false, $class = '', $disableOnNull = false, $selection_values = null)
// Action array: array('name', 'surface', 'icon', 'class', 'follow-up-tables', 'follow-up-field(s)')
return array(
		'MAGIC_META' => array(
			'title' => DA_TITLE,
			'icon' => 'fa-book',
			'default_permission' => 0,
		),
		'search' => array(
			'section_key' => 'search',
			'type' => 'pSearchObject',
			'template' => 'pLemmaTemplate',
			'table' => 'words',
			// Beware: the information fields need to exitst in the structure's datafields array
			'entry_meta' => array(
				'title_field' => 'native',
				'information_fields' => array(
					'type_id', 'classification_id', 'subclassification_id',
				),
				'enable_categories' => false,
				'categories_table' => null,
				'categories_field' => 'word_id',
			),
			'permission' => 0,
			'icon' => 'fa-font',
			'id_as_hash' => true,
			'hash_app' => 'lemma',
			'surface' => DA_TITLE_WORDS,
			'condition' => false,
			'items_per_page' => 20,
			'disable_pagination' => false,
			'datafields' => array(
				new pDataField('native', DA_WORDS_NATIVE, '15%', 'input', true, true, true),
				new pDataField('ipa', DA_WORDS_IPA, '10%', 'input'),
				new pDataField('type_id', DA_LEXCAT, '10%', 'select', true, true, true, 'small-caps', false, new pSelector('types', null, 'name', true, 'lexcat')),
				new pDataField('classification_id', DA_GRAMCAT, '20%', 'select', true, true, true, 'small-caps', false, new pSelector('classifications', null, 'name', true, 'gramcat')),
				new pDataField('subclassification_id', DA_GRAMTAG, '20%', 'select', true, true, true, 'small-caps', false, new pSelector('subclassifications', null, 'name', true, 'gramtags')),
			),
			'actions_item' => array(
			),
			'actions_bar' => array(
				'new' => array('new', DA_WORDS_NEW_EXTERN, 'fa-plus-circle fa-12', 'btAction wikiEdit', null, null, pUrl('?addword')),
			),
			'save_strings' => $saveStrings,
			'subobjects' => array(
				new pEntryDataObject("Usage notes", 'usage_notes', 'word_id', false, null, 'pLemmaTemplate'),
			),
		),

	);