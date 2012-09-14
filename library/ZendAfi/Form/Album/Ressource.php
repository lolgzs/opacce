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
			->addPosterFor($model)
			->detectMediaType($model)
			->addDisplayGroup(['media_type', 'fichier', 'url', 'poster', 'link_to'],
				                 'file',
				                 ['legend' => 'Fichier'])

			->addDisplayGroup(['titre', 'folio', 'matiere'],
												'ressource',
												['legend' => 'Media'])

			->addDisplayGroup(['description'],
												'ressource_desc',
												['legend' => 'Description'])
			->addJs();

		if ($album = $model->getAlbum()
			  and !$album->isLivreNumerique())
			$form->removeElement('folio');

		
		return $form;
	}

		
	public function init() {
		parent::init();
		$this
			->setAttrib('id', 'ressourcesForm')
			->setAttrib('enctype', self::ENCTYPE_MULTIPART)

			->addElement('text', 'titre', ['label' => 'Titre', 'size' => '80'])

			->addElement('text', 'folio', ['label' => 'Folio', 'size' => '20'])

			->addElement('radio', 'media_type',
					['label' => 'Type de média',
					 'separator' => '',
					 'multioptions' => [1 => 'Image', 2 => 'Autre fichier', 3 => 'Média en ligne'],
					 'value' => 1])
				
			->addElement('url', 'url', ['label' => 'Url *', 'size' => '80'])
				
			->addElement('url', 'link_to', ['label' => 'Lien vers', 'size' => '80'])

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
		$element = new ZendAfi_Form_Element_Image('fichier', ['label' => 'Fichier *']);
		if ($model) {
			$element
				->setBasePath($model->getOriginalsPath())
				->setBaseUrl($model->getThumbnailsUrl())
				->setThumbnailUrl($model->getThumbnailUrl());
		}
		return $this->addElement($element);
	}


	/**
	 * @param $album Class_AlbumRessource
	 * @return ZendAfi_Form_Album_Ressource
	 */
	public function addPosterFor($model) {
		$element = new ZendAfi_Form_Element_Image('poster', ['label' => 'Affiche / Jacquette']);
		if ($model) {
			$element
				->setBasePath($model->getOriginalsPath())
				->setBaseUrl($model->getThumbnailsUrl())
				->setThumbnailUrl($model->getThumbnailUrl());
		}
		return $this->addElement($element);
	}

		

	public function addJs() {
		Class_ScriptLoader::getInstance()
				->addInlineScript('
				 function showMediaInputRowWithId(id) {
					 getMediaRowOfInputId(id).show();
				 }

				 function hideMediaInputRowWithId(id) {
					 getMediaRowOfInputId(id).hide();
				 }

				 function changeMediaInputLabelWithId(id, label) {
					 getMediaRowOfInputId(id).find("label").first().html(label);
				 }

				 function getMediaRowOfInputId(id) {
					 return $("#" + id).parent("td").parent("tr");
				 }

				 function toggleMediaType() {
					 var currentVal = $("input:radio[name=media_type]:checked").val();
					 if (1 == currentVal) {
						 hideMediaInputRowWithId("url");
						 hideMediaInputRowWithId("poster");
						 changeMediaInputLabelWithId("fichier", "Image *");
						 showMediaInputRowWithId("fichier");
 						 showMediaInputRowWithId("link_to");
						 return;
					 }

					 if (2 == currentVal) {
						 hideMediaInputRowWithId("url");
						 hideMediaInputRowWithId("link_to");
						 changeMediaInputLabelWithId("fichier", "Fichier *");
						 showMediaInputRowWithId("fichier");
						 showMediaInputRowWithId("poster");
						 return;
					 }

					 hideMediaInputRowWithId("fichier");
					 hideMediaInputRowWithId("link_to");
					 showMediaInputRowWithId("url");
					 showMediaInputRowWithId("poster");
				}')

				->addJQueryReady('$("input:radio[name=media_type]").change(function(){toggleMediaType();});
													toggleMediaType();');
		return $this;
	}


	/**
	 * @param $model Class_AlbumRessource
	 * @return Zendafi_Form_Album_Ressource
	 */
	public function detectMediaType($model) {
		if ($model->isImage()) {
			$this->getElement('media_type')->setValue(1);
			return $this;
		}

		if ($model->getFichier()) {
			$this->getElement('media_type')->setValue(2);
			return $this;
		}

		$this->getElement('media_type')->setValue(3);
		return $this;
	}
}

?>