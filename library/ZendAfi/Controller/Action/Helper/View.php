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
////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Surcharge de la class Zend_View
////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Action_Helper_View extends Zend_View
{
	private $ouverture_boite;					// Html du haut de la boite
	private $fermeture_boite;					// Html du bas de la boite

	public function init() {
		parent::init();

		$this->addHelperPath('ZendAfi/View/Helper', 'ZendAfi_View_Helper');

		// Utf8 et fonction d'échappement
		$this->setEncoding('UTF-8');
		$this->setEscape('htmlentities');

		$this->doctype('XHTML1_TRANSITIONAL');

		// Traducteur et user connecté
		$this->translate = Zend_Registry::get('translate');

	}

	/**
	 * @param string $libelle
	 * @return string
	 */
	public function traduire($libelle)	{
		return $this->_($libelle);
	}

	/**
	 * @return Zend_Translate
	 */
	public function _translate() {
		if (!$this->translate)
			$this->translate = Zend_Registry::get('translate');
		return $this->translate;
	}

	/**
	 * @return string
	 */
	public function _()	{
		$args = func_get_args();
		if ('' == $args[0]) 
			return '';
		return call_user_func_array(array($this->_translate(), '_'), $args);
  }

	/**
	 * @return string
	 */
	public function _plural()	{
		$args = func_get_args();
		return call_user_func_array(array($this->_translate(), 'plural'), $args);
	}


	/**
	 * @param string $template
	 * @param array $html_array
	 * @return string
	 */
	public function getBoite($template, $html_array) {
		// Fil rss interne
		if($html_array["RSS"]) {
			$html_array["RSS"]='<a href="'.$html_array["RSS"].'" target="_blank"><img src="'.URL_IMG.'rss.gif" border="0"/></a>';

		} else {
			$html_array["RSS"]="&nbsp;";

		}

		// Lire le template
		$template = Zend_Registry::get('path_templates') . "boites/" . $template . ".html";

		if (file_exists($template)) {
			$html = file_get_contents($template);
			$blocs = '';
			// Interpretation des IF-xxx
			$pos_fin = 0;
			while (true) {
				$pos = strPos($html,"{IF-", $pos_fin);
				if ($pos === false)
					break;

				$pos_fin = strpos($html, "{ENDIF}", $pos);
				if ($pos_fin===false)
					break;
				$blocs[] = substr($html, $pos, ($pos_fin + 7 - $pos));
			}

			if ($blocs) {
				foreach ($blocs as $bloc) {
					$pos = strpos($bloc, "}");
					$var = substr($bloc , 4 , $pos-4);

					if (!trim($html_array[$var])) {
						$html = str_replace($bloc, "", $html);
						continue;
					}

					$suppr[] = substr($bloc , 0, ($pos+1));

				}

				$suppr[] = "{ENDIF}";

				foreach ($suppr as $var) {
					$html = str_replace($var, "", $html);
				}

			}

			// Fusion des variables
			$html = str_replace("{URL_IMG}", URL_IMG, $html);
			foreach ($html_array as $clef => $valeur) {
				$html = str_replace("{" . $clef . "}", $valeur, $html);

			}

		} else {
			$html = $html_array["TITRE"] . BR . $html_array["CONTENU"];

		}

		return $html;

	}

//------------------------------------------------------------------------------------------------------
// Initialisation d'une boite a partir d'un template
//------------------------------------------------------------------------------------------------------
	public function initBoite($template)
	{
		// Template par défaut : boite du milieu
		if(!$template) $template = "boite_de_la_division_du_milieu";

		// On prend tout le html du template
		$html_array=array("TITRE" => "[=TITRE=]",
											"CONTENU" => "[=CONTENU=]",
											"RSS" => "");
		$html=$this->getBoite($template,$html_array);

		// On decoupe en ouverture et fermeture
		$pos=strpos($html,"[=CONTENU=]");
		$this->ouverture_boite=substr($html,0,$pos);
		$this->fermeture_boite=substr($html,($pos+11));
	}

//------------------------------------------------------------------------------------------------------
// ouverture d'une  boite
//------------------------------------------------------------------------------------------------------
	public function openBoite($titre)	{
		if ($titre)
			$titre = $this->_($titre);
		$html=str_replace("[=TITRE=]", $titre, $this->ouverture_boite);
		print($html);
	}

//------------------------------------------------------------------------------------------------------
// Fermeture d'une  boite
//------------------------------------------------------------------------------------------------------
	public function closeBoite()
	{
		print($this->fermeture_boite);
	}


	// Génère le titre de la page
	public function getTitre() {
		if (isset($this->titre))
			$titre = $this->titre;
		else {
			$titre = Class_Profil::getCurrentProfil()->getTitreSite();
			$this->titre = $titre;
		}
		return $titre;
	}


	public function titreAdd($subtitle) {
		$this->titre = $this->getTitre() . ' - ' . $subtitle;
	}



	public function newForm($options = null) {
		$form = new Zend_Form($options);
		$form
			->getPluginLoader(Zend_Form::ELEMENT)
			->addPrefixPath('ZendAfi_Form_Element', 'ZendAfi/Form/Element');
		$form
			->getPluginLoader(Zend_Form::DECORATOR)
			->addPrefixPath('ZendAfi_Form_Decorator', 'ZendAfi/Form/Decorator');
		return $form;
	}


	public function newFormElementFile($name, $extension, $destination = PATH_TEMP) {
		$file_element = new Zend_Form_Element_File($name);
		$file_element->loadDefaultDecorators();
		$file_element
			->setDestination($destination)
			->addValidator('Count', false, 1)
			->addValidator('Extension', false, $extension);
		return $file_element;
	}
}

?>