	var displayWaitMessage=true;	// Display a please wait message while images are loading?		
	var activeImage = false;
	var imageGalleryLeftPos = false;
	var imageGalleryWidth = false;
	var imageGalleryObj = false;
	var maxGalleryXPos = false;
	var slideSpeed = 0;
	var imageGalleryCaptions = new Array();
	
	function startSlide(e)
	{
		if(document.all)e = event;
		var id=this.id;
		var img_over=this.getElementsByTagName('IMG')[0].src.replace(".gif","_over.gif");
		this.getElementsByTagName('IMG')[0].src = img_over;
		if(this.id=='arrow_right'){
			slideSpeedMultiply = Math.floor((e.clientX - this.offsetLeft) / 5);
			slideSpeed = -1*slideSpeedMultiply;
			slideSpeed = Math.max(-10,slideSpeed);
		}else{			
			slideSpeedMultiply = 10 - Math.floor((e.clientX - this.offsetLeft) / 5);
			slideSpeed = 1*slideSpeedMultiply;
			slideSpeed = Math.min(10,slideSpeed);
			if(slideSpeed<0)slideSpeed=10;
		}
	}
	
	function releaseSlide()
	{
		var id = this.id;
		var img=this.getElementsByTagName('IMG')[0].src.replace("_over.gif", ".gif");
		this.getElementsByTagName('IMG')[0].src = img;
		slideSpeed=0;
	}
		
	function gallerySlide()
	{
		if(slideSpeed!=0){
			var leftPos = imageGalleryObj.offsetLeft;
			leftPos = leftPos/1 + slideSpeed;
			if(leftPos>maxGalleryXPos){
				leftPos = maxGalleryXPos;
				slideSpeed = 0;
				
			}
			if(leftPos<minGalleryXPos){
				leftPos = minGalleryXPos;
				slideSpeed=0;
			}
			
			imageGalleryObj.style.left = leftPos + 'px';
		}
		setTimeout('gallerySlide()',20);
		
	}
	
	function initSlideShow()
	{
		document.getElementById('arrow_left').onmousedown = startSlide;
		document.getElementById('arrow_left').onmouseup = releaseSlide;
		document.getElementById('arrow_left').onmouseout = releaseSlide;
		document.getElementById('arrow_right').onmousedown = startSlide;
		document.getElementById('arrow_right').onmouseup = releaseSlide;
		document.getElementById('arrow_right').onmouseout = releaseSlide;
		
		imageGalleryObj = document.getElementById('theImages');
		imageGalleryLeftPos = imageGalleryObj.offsetLeft;
		imageGalleryWidth = document.getElementById('galleryContainer').offsetWidth - 80;
		maxGalleryXPos = imageGalleryObj.offsetLeft; 
		minGalleryXPos = imageGalleryWidth - document.getElementById('slideEnd').offsetLeft;
		
		var divs = imageGalleryObj.getElementsByTagName('DIV');
		for(var no=0;no<divs.length;no++){
			if(divs[no].className=='imageCaption')imageGalleryCaptions[imageGalleryCaptions.length] = divs[no].innerHTML;
		}
		gallerySlide();
	}
	
	window.onload=initSlideShow;