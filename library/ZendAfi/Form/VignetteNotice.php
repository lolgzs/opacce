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

class ZendAfi_Form_VignetteNotice extends ZendAfi_Form {
	use Trait_Translator;

	public function init() {
		parent::init();
		$this
			->addElement('url', 
									 'url_vignette', 
									 ['label' => $this->_('URL de la vignette'),
										'size' => 80,
										'required' => true,
										'placeholder' => 'ex: http://upload.wikimedia.org/wikipedia/en/2/2c/Harry_Potter_and_the_Philosopher%27s_Stone.jpg',
										'validators' => ['url', 'vignetteUrl']])
			->addElement('submit',
									 'envoyer',
									 ['label' => 'Envoyer']);
	}
}

?>