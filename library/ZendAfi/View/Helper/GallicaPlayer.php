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

class ZendAfi_View_Helper_GallicaPlayer extends Zend_View_Helper_HtmlElement {
	public function gallicaPlayer($album_or_id_ark) {
		if (is_object($album_or_id_ark))
			$id_ark = $album_or_id_ark->getGallicaArkId();
		else
			$id_ark = $album_or_id_ark;

		return sprintf(
					 '<object
							classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
							width="640" 
							height="415" 
							codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">
						<param name="id" value="LecteurExportable" />
						<param name="allowScriptAccess" value="always" />
						<param name="wmode" value="window" />
						<param 
								name="FlashVars" 
								value="ark=%s&amp;lang=fr&amp;mode=dp&amp;showArrows=1&amp;bgColor=8553603&amp;autoFlip=0&amp;startPage=71&amp;widthWidget=640&amp;heightWidget=415" />
						<param 
								name="src" 
								value="http://gallica.bnf.fr/flash/LecteurExportable.swf" />
						<embed 
								id="LecteurExportable" 
								type="application/x-shockwave-flash" 
								width="640" 
								height="415" 
								src="http://gallica.bnf.fr/flash/LecteurExportable.swf" 
								flashvars="ark=%s&amp;lang=fr&amp;mode=dp&amp;showArrows=1&amp;bgColor=8553603&amp;autoFlip=0&amp;startPage=71&amp;widthWidget=640&amp;heightWidget=415" 
								allowscriptaccess="always" 
								wmode="window" />
					</object>', $id_ark, $id_ark);

	}
}

?>