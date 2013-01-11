<?php
// Init de l'appli et controle identification
include("php/_init.php");

// Frameset
?>

<html>
	<head>
		<title>AFI - Intégrateur de données Cosmogramme</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<frameset rows="90px,*">
		<frame src="<?php print(URL_BASE)?>php/_banniere.php" frameborder="no" scrolling="no" marginheight="0" marginwidth="0" noresize="noresize">
		<frameset cols="265px,*" rows="*">
  			<frame src="<?php print(URL_BASE)?>php/_menu.php" name="menu" frameborder="no" marginheight="0" marginwidth="0" noresize="noresize">
  			<frame src="<?php print(URL_BASE)?>php/_accueil.php" name="droite" frameborder="no" marginheight="0" marginwidth="0" noresize="noresize">
		</frameset>
	</frameset>
	<noframes>
		<body bgcolor="#FFFFFF">Votre navigateur ne supporte pas les frames</body>
	</noframes>
</html>
