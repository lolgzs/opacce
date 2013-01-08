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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// TESTS 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");
include ('pdf/ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont('./php/classes/pdf/fonts/Courier-BoldOblique.afm');
//$pdf->ezText(utf8_decode('test avec des caractères accentués!'),10);
$pdf->ezText(utf8_decode('test avec des caractères accentués!'),10);

$pdf->addJpegFromFile("test.jpg",0,0,200,50);
$data = array
(
	array('num'=>1,'name'=>'gandalf','type'=>'wizard')
	,array('num'=>2,'name'=>'bilbo','type'=>'hobbit','url'=>'http://www.ros.co.nz/pdf/')
	,array('num'=>3,'name'=>'frodo','type'=>'hobbit')
	,array('num'=>4,'name'=>'saruman','type'=>'baddude','url'=>'http://sourceforge.net/projects/pdf-php')
	,array('num'=>5,'name'=>'sauron','type'=>'really bad dude')
);
$pdf->ezTable($data);



$pdf->ezNewPage();
$pdf->ezText('Hello World 2!',10);

//$pdf->ezStream();
//exit;



//Dans un fichier OK CA MARCHE
$pdfcode = $pdf->output();
$fname = "test.pdf";
$fp = fopen($fname,'w');
fwrite($fp,$pdfcode);
fclose($fp);

?>