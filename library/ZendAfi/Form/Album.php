<?php
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README file).
 *
 * AFI-OPAC 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */

class ZendAfi_Form_Album extends ZendAfi_Form {
	use Trait_Translator;

	const RIGHT_PUBLIC_DOMAIN = 'Domaine public';

	public static function newWith($album) {
		$form = new self();

		$form
			->populate($album->toArray())
			->addRightsFor($album)
			->addVignetteFor($album)
			->addFileFor($album)
			->addDisplayGroup(['titre', 
												 'sous_titre',
												 'cat_id',
												 'visible',
												 'fichier',
												 'pdf'], 
				                 'album', 
				                 ['legend' => $form->_('Album')])

			->addDisplayGroup(['description'],
												 'album_desc', 
				                 ['legend' => $form->_('Description')])

			->addDisplayGroup(['auteur',
					               'droits',
					               'droits_precision',
												 'annee', 
												 'editeur',
												 'provenance',
												 'id_langue',
												 'type_doc_id',
												 'cote',
												 'matiere',
												 'dewey',
												 'genre',
												 'tags'],
												 'album_metadata', 
				                 ['legend' => $form->_('Metadonnées')]);

		return $form;
	}

		
	public function init() {
		parent::init();
		Class_ScriptLoader::getInstance()
				->addInlineScript('function toggleAlbumRights() {
													   var target = $("#droits_precision").parent().parent();
													   (2 == $("input[name=\'droits\']:checked").val()) ?
													     target.show() :
													     target.hide();
													 }')
				->addJQueryReady('$("input[name=\'droits\']").change(toggleAlbumRights);
													toggleAlbumRights();');
						
		$this
			->setAttrib('id', 'album')
			->setAttrib('enctype', self::ENCTYPE_MULTIPART)
			->addElement('text', 'titre', ['label'			=> 'Titre *',
																		 'size'				=> 80,	
																		 'required'		=> true,
																		 'allowEmpty'	=> false])

			->addElement('text', 'sous_titre', ['label'			=> 'Sous-titre',
																					'size'				=> 80])

			->addElement('select', 'cat_id', ['label' => 'Catégorie',
																				'multiOptions' => Class_AlbumCategorie::getAllLibelles()])

			->addElement('checkbox', 'visible', ['label' => 'Visible'])

			->addElement('text', 'auteur', ['label' => 'Auteur', 
																			'size' => 80])
				
			->addElement('ckeditor', 'description')

			->addElement('text', 'annee', ['label' => "Année d'édition", 
																		 'size' => 4, 
																		 'maxlength' => 4])

			->addElement('text', 'editeur', ['label' => 'Editeur', 
																			 'size' => 80])

			->addElement('text', 'cote', ['label' => 'Cote', 
																		'size' => 20])

			->addElement('text', 'provenance', ['label' => 'Provenance', 
																					'size' => 80])

			->addElement('select', 'id_langue', ['label' => 'Langue', 
																					 'multioptions' => Class_CodifLangue::allByIdLibelle()])

			->addElement('select', 'type_doc_id', ['label' => 'Type de document', 
					                                   'multioptions' => Class_TypeDoc::allByIdLabelForAlbum()])

			->addElement('listeSuggestion', 'matiere',
									 ['label' => 'Matières / sujets',
										'name' => 'matiere',
										'rubrique' => 'matiere'])

			->addElement('listeSuggestion', 'dewey', ['label' => 'Indices dewey',
																								'name' => 'dewey',
																								'rubrique' => 'dewey'])

			->addElement('cochesSuggestion', 'genre', ['label' => 'Genres',
																								 'name' => 'genre',
																								 'rubrique' => 'genre'])

			->addElement('textarea', 'tags', ['label' => 'Tags',
					                              'rows' => 2]);
	}


	/**
	 * @param $album Class_Album
	 * @return ZendAfi_Form_Album
	 */
	public function addVignetteFor($album) {
		$vignette_element = new ZendAfi_Form_Element_Image(
				'fichier',
				['label'			=> 'Vignette<br/><em style="font-size:80%;font-weight:normal">(jpg, gif, png)</em>',
					'escape'    => false,
					'basePath'	=> $album->getBasePath(),
					'baseUrl'		=> $album->getBaseUrl(),
					'thumbnailUrl' => $album->getThumbnailUrl(),
					'actionUrl'	=> $this->getView()->url(['action' => 'album-delete-vignette'])]);

		$vignette_element
			->getDecorator('label')
			->setOption('escape', false);

		return $this->addElement($vignette_element);
	}


	/**
	 * @param $album Class_Album
	 * @return ZendAfi_Form_Album
	 */
	public function addFileFor($album) {
		return $this->addElement(new ZendAfi_Form_Element_File('pdf',
				[ 'label'			=> 'Album PDF',
					'escape'    => false,
					'basePath'	=> $album->getBasePath(),
					'baseUrl'		=> $album->getBaseUrl(),
					'actionUrl'	=> $this->getView()->url(['action' => 'album-delete-pdf'])]));
	}


	/**
	 * @param $album Class_Album
	 * @return ZendAfi_Form_Album
	 */
	public function addRightsFor($album) {
		$current = (self::RIGHT_PUBLIC_DOMAIN == $album->getDroits() || $album->isNew()) ? 1: 2;
		$current_precision = (self::RIGHT_PUBLIC_DOMAIN != $album->getDroits()) ?
				$album->getDroits(): '';

		return $this
				->addElement('radio', 'droits',
					['label' => 'Droits',
						'value' => $current,
						'separator' => '',
						'multiOptions' => [1 => self::RIGHT_PUBLIC_DOMAIN,
							                 2 => 'Autre, Précisez']])

				->addElement('text', 'droits_precision',
					['label' => '', 'value' => $current_precision, 'size' => 80]);
	}
}

?>