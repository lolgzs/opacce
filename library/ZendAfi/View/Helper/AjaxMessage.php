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
class ZendAfi_View_Helper_AjaxMessage extends ZendAfi_View_Helper_BaseHelper
{
	/*
	 * @param array $messageArray =
	 * 				$messageArray['type'] erreur|message
	 * 				$messageArray['titre'] 
	 * 				$messageArray['cause'] (pour type=erreur)
	 * 				$messageArray['remede'] (pour type=erreur)
	 *  			$messageArray['message'] (pour type=message)
	 */
	public function ajaxMessage($messageArray)
	{
		if ($messageArray['type'] == "erreur"){
			$image = URL_IMG.'panneau_alerte.gif';
			$texte = $this->translate()->_('Attention...');
		}else{
			$image = URL_IMG.'bulle_message.gif';
			$texte = $this->translate()->_('Message...');
		}

		$html[]='<div align="center">';
		$html[]='<table class="erreur_cadre" width="450" cellpadding="0" cellspacing="0"><tr><td>';
		$html[]='	<table class="erreur" cellspacing="0" cellpadding="0">';
		$html[]='		<tr>';
		$html[]='			<td>';
		$html[]='				<table class="erreur_bandeau" width="100%"><tr>';
		$html[]='					<td style="padding-left:6;"><img src="'.$image.'"></td>';
		$html[]='					<td width="100%" style="padding-left:20;">'.$texte.'</td>';
		$html[]='				</tr></table>';
		$html[]='		</tr>';
		$html[]='		<tr>';
		$html[]='    	<td class="erreur_titre" align="center">' . $messageArray['titre'] . '</td>';
		$html[]='  	</tr>';

		if ($messageArray['type'] == "erreur"){
			$html[]='		<tr>';
			$html[]='    	<td class="erreur_p">' . $this->traduire('Cause') . ' :</td>';
			$html[]='  	</tr>';
			$html[]='  	<tr>';
			$html[]='    	<td class="erreur_texte">' . $this->traduire($messageArray['cause']) . '</td>';
			$html[]='  	</tr>';
			$html[]='  	<tr>';
			$html[]='    	<td class="erreur_p">' . $this->traduire('Reméde') . ' :</td>';
			$html[]='  	</tr>';
			$html[]='  	<tr>';
			$html[]='    	<td class="erreur_texte">' . $this->traduire($messageArray['remede']) . '</td>';
			$html[]='  	</tr>';
		}else{
			$html[]='  	<tr>';
			$html[]='    	<td class="erreur_texte">' . $this->traduire($messageArray['message']) . '</td>';
			$html[]='  	</tr>';
		}

		$html[]='  	<tr><td height="15px"></td></tr>';
		$html[]='	</table>';
		$html[]='</td></tr>';
		$html[]='</table>';
		$html[]='</div><br>';

		return implode('',$html);
	}
}