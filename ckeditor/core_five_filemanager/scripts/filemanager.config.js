/*---------------------------------------------------------
  Configuration
---------------------------------------------------------*/


/*---------------------------------------------------------
  Configuration
---------------------------------------------------------*/

// Set culture to display localized messages
var culture = 'fr';

// Set default view mode : 'grid' or 'list'
var defaultViewMode = 'list';

// Autoload text in GUI
// If set to false, set values manually into the HTML file
var autoload = true;

// Display full path - default : false
var showFullPath = false;

// Browse only - default : false
var browseOnly = false;

// Set this to the server side language you wish to use.
var lang = 'php'; // options: php, jsp, lasso, asp, cfm // we are looking for contributors for lasso, python connectors (partially developed)

//var am = document.location.pathname.substring(1, document.location.pathname.indexOf('ckeditor'));
// Set this to the directory you wish to manage.
//var fileRoot = '/' + am + 'userfiles/';
var fileRoot = (new RegExp('[\\?&]ServerPath=([^&#]*)').exec(window.location.href))[1];

// Show image previews in grid views?
var showThumbs = true;

// Allowed image extensions when type is 'image'
var imagesExt = ['jpg', 'jpeg', 'gif', 'png', 'ico'];
