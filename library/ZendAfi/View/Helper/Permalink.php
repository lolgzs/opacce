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
class ZendAfi_View_Helper_Permalink extends ZendAfi_View_Helper_BaseHelper {
	public function permalink($url) {
		$onclick="oPm=getId('permalink');
							if(oPm.style.display=='block'){oPm.style.display='none'; return;}
							oPm.style.display='block';
							nX=getLeftPos(this);
							nY=getTopPos(this);
							oPm.style.top=nY + this.clientHeight + 3 +'px';
							oPm.style.left=nX + this.clientWidth - oPm.clientWidth + 'px';";

		$html = sprintf('<img src="'.URL_ADMIN_IMG.'reseaux/permalink.gif" style="margin-right:3px;cursor:pointer" title="%s" onclick="%s" alt="%s" />',
									 $this->translate()->_('Lien permanent'),
									 $onclick,
									 $this->translate()->_('Lien permanent'));

		$html.='<div id="permalink" style="background: #FFFFFF url('.URL_IMG.'box/degrade_gris.png) repeat-x bottom;display:none;position:absolute;padding:8px;text-align:center;border:1px solid #C8C8C8">';
		$html.= sprintf('<div style="float:left">%s</div>', $this->translate()->_('Lien permanent'));
		$html.= sprintf('<div align="right"><a href="#" onclick="getId(\'permalink\').style.display=\'none\'">&raquo;&nbsp;%s</a></div>', 
										$this->translate()->_('fermer cette fenêtre'));
		$html.='<input type="text" size="66" value="'.$url.'" style="margin:5px;border:1px solid #C8C8C8" />';
		$html.='</div>';

		return $html;
	}
}

?>