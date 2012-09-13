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

class ZendAfi_Form_Album_Ressource extends ZendAfi_Form {
	use Trait_Translator;

	/**
	 * @param $model Class_AlbumRessource
	 * @return ZendAfi_Form_Album_Ressource
	 */
	public static function newWith($model) {
		$form = new self();

		$form
			->populate($model->toArray())
			->addFileFor($model)

			->addDisplayGroup(array('titre', 'folio', 'fichier', 'link_to', 'matiere'),
												'ressource',
												array('legend' => 'Media'))

			->addDisplayGroup(array('description'),
												'ressource_desc',
												array('legend' => 'Description'));

		return $form;
	}

		
	public function init() {
		parent::init();
		$this
			->setAttrib('id', 'ressourcesForm')
			->setAttrib('enctype', self::ENCTYPE_MULTIPART)

			->addElement('text', 'titre', ['label' => 'Titre',
					                           'size' => '80'])

			->addElement('text', 'folio', ['label' => 'Folio',
																		 'size' => '20'])


			->addElement('url', 'link_to', ['label' => 'Lien vers',
																			'size' => '80'])

			->addElement('ckeditor', 'description')

			->addElement('listeSuggestion', 'matiere', ['label' => 'Matières / sujets',
																									'name' => 'matiere',
					                                        'rubrique' => 'matiere']);
			
	}


	/**
	 * @param $album Class_AlbumRessource
	 * @return ZendAfi_Form_Album_Ressource
	 */
	public function addFileFor($model) {
		$element = new ZendAfi_Form_Element_Image('fichier', ['label' => 'Fichier']);
		if ($model) {
			$element
				->setBasePath($model->getOriginalsPath())
				->setBaseUrl($model->getThumbnailsUrl())
				->setThumbnailUrl($model->getThumbnailUrl());
		}
		return $this->addElement($element);
	}
}

?>