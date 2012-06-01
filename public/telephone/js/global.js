//-----------------------------------------------------------
// Faire disparaitre le toolbar iphone
//-----------------------------------------------------------
$(document).ready(function(){
	setTimeout(function(){
		window.scrollTo(0, 1);
	}, 100);
});

//-----------------------------------------------------------
// Click sur le container (pour le mode simulation telephone)
//-----------------------------------------------------------
function clickContainer(event) {
	var position=$('#iphone_container').offset();
	var x=event.clientX-position.left;
	var y=event.clientY;
	if(y >559 && y <582)
	{
		if(x >57 && x <80) window.history.back();
		if(x >221 && x <250) document.location=baseUrl + "/admin";
		if(x >311 && x <333) document.location=baseUrl + "?id_profil=1";
	}
}


function showPopWin(url) {
	window.open(url);
}


function hidePopWin() {
	window.location = window.parent.location;
}