<?PHP
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
///////////////////////////////////////////////////////////////////
//
//         TEST DE LA COMMUNICATION AVEC NANOOK
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");
include("classe_http_request.php");
?>

<h1>Test communication avec Nanook</h1>


<?PHP
$url = str_replace(' ', '%20', $_GET['url']);

if ('/' !== substr($url, -1))
	 $url .= '/';

if (false==strpos($url, 'service'))
	 $url .= 'service/GetRecords/id/1';

if (false===strpos($url, '://'))
	 $url = 'http://'.$url;

$http=new HTTPRequest($url);
$response=$http->DownloadToString();
?>

URL: <?php echo $url ?><br />
Réponse:
<code><pre style="white-space:normal">
<?php echo htmlspecialchars($response); ?>
</pre></code>
