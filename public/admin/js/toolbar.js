//////////////////////////////////////////////////////////////////////////////////////
// Java Script pour afficher les 3 états d'un bouton toolbar
/////////////////////////////////////////////////////////////////////////////////////

// Init des images : gauche, milieu et droite
pic_toolbar_gauche = new Image(8,26); pic_toolbar_gauche.src= imagesUrl + "bouton/bouton_gauche.gif";  
pic_toolbar_gauche_over= new Image(8,26); pic_toolbar_gauche_over.src= imagesUrl + "bouton/bouton_gauche_over.gif";
pic_toolbar_gauche_down= new Image(8,26); pic_toolbar_gauche_down.src= imagesUrl + "bouton/bouton_gauche_down.gif";

pic_toolbar_milieu = new Image(5,26); pic_toolbar_milieu.src= imagesUrl + "bouton/bouton_milieu.gif";  
pic_toolbar_milieu_over= new Image(5,26); pic_toolbar_milieu_over.src= imagesUrl + "bouton/bouton_milieu_over.gif";  
pic_toolbar_milieu_down= new Image(5,26); pic_toolbar_milieu_down.src= imagesUrl + "bouton/bouton_milieu_down.gif";  
	
pic_toolbar_droite = new Image(7,26); pic_toolbar_droite.src= imagesUrl + "bouton/bouton_droite.gif";  
pic_toolbar_droite_over= new Image(7,26); pic_toolbar_droite_over.src= imagesUrl + "bouton/bouton_droite_over.gif";
pic_toolbar_droite_down= new Image(7,26); pic_toolbar_droite_down.src= imagesUrl + "bouton/bouton_droite_down.gif";

// Bouton au repos
function PicToolbarNormal(handleHref,toolbarItem)
{
	nomObj=toolbarItem + "_gauche"; document[nomObj].src= pic_toolbar_gauche.src;
	nomObj=toolbarItem + "_droite"; document[nomObj].src= pic_toolbar_droite.src;
	nomObj=toolbarItem + "_milieu"; handleObj = document.getElementById(nomObj);
	handleObj.style.backgroundImage = 'url( "' + imagesUrl + 'bouton/bouton_milieu.gif")';
	nomObj=toolbarItem + "_texte"; handleObj = document.getElementById(nomObj);
	handleObj.style.backgroundImage = 'url( "' + imagesUrl + 'bouton/bouton_milieu.gif")';
}

// Bouton au mouseOver
function PicToolbarOver(handleHref,toolbarItem)
{
	if (handleHref==null) return;
 	handleHref.style.cursor="pointer";
	nomObj=toolbarItem + "_gauche"; document[nomObj].src= pic_toolbar_gauche_over.src;
 	nomObj=toolbarItem + "_droite"; document[nomObj].src= pic_toolbar_droite_over.src;
	nomObj=toolbarItem + "_milieu"; handleObj = document.getElementById(nomObj);
  	handleObj.style.backgroundImage ='url( "' + imagesUrl + 'bouton/bouton_milieu_over.gif")';
  	nomObj=toolbarItem + "_texte"; handleObj = document.getElementById(nomObj);
  	handleObj.style.backgroundImage ='url( "' + imagesUrl + 'bouton/bouton_milieu_over.gif")';
 }

// Bouton au click
function PicToolbarDown(handleHref,toolbarItem)
{
	nomObj=toolbarItem + "_gauche"; document[nomObj].src= pic_toolbar_gauche_down.src;
 	nomObj=toolbarItem + "_droite"; document[nomObj].src= pic_toolbar_droite_down.src;

  	nomObj=toolbarItem + "_milieu"; handleObj = document.getElementById(nomObj);
  	handleObj.style.backgroundImage ='url( "' + imagesUrl + 'bouton/bouton_milieu_down.gif")';
  	nomObj=toolbarItem + "_texte"; handleObj = document.getElementById(nomObj);
 	handleObj.style.backgroundImage ='url( "' + imagesUrl + 'bouton/bouton_milieu_down.gif")';
  
	handleHref.style.cursor="pointer";
 }