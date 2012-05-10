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

class Class_File_Mime {
	const DEFAULT_TYPE = 'application/octet-stream';

	/**
	 * @var array
	 */
	public static $extToTypes = array(
		'ez'		=> 'application/andrew-inset',
		'anx'		=> 'application/annodex',
		'atom'		=> 'application/atom+xml',
		'atomcat'	=> 'application/atomcat+xml',
		'atomsrv'	=> 'application/atomserv+xml',
		'lin'		=> 'application/bbolin',
		'cap'		=> 'application/cap',
		'pcap'		=> 'application/cap',
		'hta'		=> 'application/hta',
		'jar'		=> 'application/java-archive',
		'ser'		=> 'application/java-serialized-object',
		'class'		=> 'application/java-vm',
		'js'		=> 'application/javascript',
		'hqx'		=> 'application/mac-binhex40',
		'mdb'		=> 'application/msaccess',
		'doc'		=> 'application/msword',
		'dot'		=> 'application/msword',
		'docx'		=> 'application/msword',
		'bin'		=> 'application/octet-stream',
		'pdf'		=> 'application/pdf',
		'ps'		=> 'application/postscript',
		'ai'		=> 'application/postscript',
		'eps'		=> 'application/postscript',
		'espi'		=> 'application/postscript',
		'epsf'		=> 'application/postscript',
		'eps2'		=> 'application/postscript',
		'eps3'		=> 'application/postscript',
		'rar'		=> 'application/rar',
		'rdf'		=> 'application/rdf+xml',
		'rss'		=> 'application/rss+xml',
		'rtf'		=> 'application/rtf',
		'smi'		=> 'application/smi',
		'smil'		=> 'application/smi',
		'xhtml'		=> 'application/xhtml+xml',
		'xht'		=> 'application/xhtml+xml',
		'xml'		=> 'application/xml',
		'xsd'		=> 'application/xml',
		'xsl'		=> 'application/xml',
		'zip'		=> 'application/zip',
		'kml'		=> 'application/vnd.google-earth.kml+xml',
		'kmz'		=> 'application/vnd.google-earth.kmz',
		'xul'		=> 'application/vnd.mozilla.xul+xml',
		'xls'		=> 'application/vnd.ms-excel',
		'xlb'		=> 'application/vnd.ms-excel',
		'xlt'		=> 'application/vnd.ms-excel',
		'ppt'		=> 'application/vnd.ms-powerpoint',
		'pps'		=> 'application/vnd.ms-powerpoint',
		'odc'		=> 'application/vnd.oasis.opendocument.chart',
		'odb'		=> 'application/vnd.oasis.opendocument.database',
		'odf'		=> 'application/vnd.oasis.opendocument.formula',
		'odg'		=> 'application/vnd.oasis.opendocument.graphics',
		'otg'		=> 'application/vnd.oasis.opendocument.graphics-template',
		'odi'		=> 'application/vnd.oasis.opendocument.image',
		'odp'		=> 'application/vnd.oasis.opendocument.presentation',
		'otp'		=> 'application/vnd.oasis.opendocument.presentation-template',
		'ods'		=> 'application/vnd.oasis.opendocument.spreadsheet',
		'ots'		=> 'application/vnd.oasis.opendocument.spreadsheet-template',
		'odt'		=> 'application/vnd.oasis.opendocument.text',
		'odm'		=> 'application/vnd.oasis.opendocument.text-master',
		'ott'		=> 'application/vnd.oasis.opendocument.text-template',
		'oth'		=> 'application/vnd.oasis.opendocument.text-web',
		'sdc'		=> 'application/vnd.stardivision.calc',
		'sds'		=> 'application/vnd.stardivision.chart',
		'sda'		=> 'application/vnd.stardivision.chart',
		'sdd'		=> 'application/vnd.stardivision.impress',
		'sdf'		=> 'application/vnd.stardivision.math',
		'sdw'		=> 'application/vnd.stardivision.writer',
		'sgl'		=> 'application/vnd.stardivision.writer-global',
		'sxc'		=> 'application/vnd.sun.xml.calc',
		'stc'		=> 'application/vnd.sun.xml.calc.template',
		'sxd'		=> 'application/vnd.sun.xml.draw',
		'std'		=> 'application/vnd.sun.xml.draw.template',
		'sxi'		=> 'application/vnd.sun.xml.impress',
		'sti'		=> 'application/vnd.sun.xml.impress.template',
		'sxm'		=> 'application/vnd.sun.xml.math',
		'sxw'		=> 'application/vnd.sun.xml.writer',
		'sxg'		=> 'application/vnd.sun.xml.writer.global',
		'stw'		=> 'application/vnd.sun.xml.writer.template',
		'vsd'		=> 'application/vnd.visio',
		'wpd'		=> 'application/vnd.wordperfect',
		'wp5'		=> 'application/vnd.wordperfect5.1',
		'7z'		=> 'application/x-7z-compressed',
		'abw'		=> 'application/x-abiword',
		'dmg'		=> 'application/x-apple-diskimage',
		'torrent'	=> 'application/x-bittorrent',
		'cab'		=> 'application/x-cab',
		'gtar'		=> 'application/x-gtar',
		'tgz'		=> 'application/x-gtar',
		'taz'		=> 'application/x-gtar',
		'iso'		=> 'application/x-iso9660-image',
		'com'		=> 'application/x-msdos-program',
		'exe'		=> 'application/x-msdos-program',
		'bat'		=> 'application/x-msdos-program',
		'dll'		=> 'application/x-msdos-program',
		'msi'		=> 'application/x-msi',
		'swf'		=> 'application/x-shockwave-flash',
		'swfl'		=> 'application/x-shockwave-flash',
		'sit'		=> 'application/x-stuffit',
		'sitx'		=> 'application/x-stuffit',
		'tar'		=> 'application/x-tar',

		'flac'		=> 'audio/flac',
		'midi'		=> 'audio/midi',
		'mid'		=> 'audio/midi',
		'kar'		=> 'audio/midi',
		'mpga'		=> 'audio/mpeg',
		'mpega'		=> 'audio/mpeg',
		'mp2'		=> 'audio/mpeg',
		'mp3'		=> 'audio/mpeg',
		'm4a'		=> 'audio/mpeg',
		'oga'		=> 'audio/ogg',
		'ogg'		=> 'audio/ogg',
		'spx'		=> 'audio/ogg',
		'aif'		=> 'audio/x-aiff',
		'aiff'		=> 'audio/x-aiff',
		'aifc'		=> 'audio/x-aiff',
		'm3u'		=> 'audio/x-mpegurl',
		'wma'		=> 'audio/x-ms-wma',
		'wax'		=> 'audio/x-ms-wax',
		'ra'		=> 'audio/x-pn-realaudio',
		'rm'		=> 'audio/x-pn-realaudio',
		'ram'		=> 'audio/x-pn-realaudio',
		'wav'		=> 'audio/x-wav',

		'gif'		=> 'image/gif',
		'jpg'		=> 'image/jpeg',
		'jpe'		=> 'image/jpeg',
		'jpeg'		=> 'image/jpeg',
		'pcx'		=> 'image/pcx',
		'png'		=> 'image/png',
		'svg'		=> 'image/svg+xml',
		'svgz'		=> 'image/svg+xml',
		'tiff'		=> 'image/tiff',
		'tif'		=> 'image/tiff',
		'ico'		=> 'image/x-icon',
		'bmp'		=> 'image/x-ms-bmp',
		'psd'		=> 'image/x-photoshop',

		'eml'		=> 'message/rfc822',

		'vrml'		=> 'model/vrml',
		'vrm'		=> 'model/vrml',
		'wrl'		=> 'model/vrml',

		'ics'		=> 'text/calendar',
		'icz'		=> 'text/calendar',
		'css'		=> 'text/css',
		'csv'		=> 'text/csv',
		'asc'		=> 'text/plain',
		'txt'		=> 'text/plain',
		'text'		=> 'text/plain',
		'rtx'		=> 'text/richtext',
		'vcs'		=> 'text/x-vcalendar',
		'vcf'		=> 'text/vcard',

		'mpg'		=> 'video/mpeg',
		'mpeg'		=> 'video/mpeg',
		'mpe'		=> 'video/mpeg',
		'mp4'		=> 'video/mp4',
		'mov'		=> 'video/quicktime',
		'qt'		=> 'video/quicktime',
		'ogv'		=> 'video/ogg',
		'mxu'		=> 'video/vnd/mpegurl',
		'flv'		=> 'video/x-flv',
		'asf'		=> 'video/x-ms-asf',
		'asx'		=> 'video/x-ms-asf',
		'wm'		=> 'video/x-ms-wm',
		'wmv'		=> 'video/x-ms-wmv',
		'wmx'		=> 'video/x-ms-wmx',
		'wvx'		=> 'video/x-ms-wvx',
		'avi'		=> 'video/x-msvideo',
		'mpv'		=> 'video/matroska',

		'epub' => 'application/epub+zip'
	);

	/**
	 * retourne un type à partir d'une extension, au pire le type par défaut
	 *
	 * @param string $ext
	 * @return string
	 */
	public static function getType($ext)
	{
		if (isset(self::$extToTypes[$ext])) {
			return self::$extToTypes[$ext];
		}

		return self::DEFAULT_TYPE;
	}
}