var allMonth=[31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
var allNameOfWeekDays;
var allNameOfMonths;
var style;
var daycollected;
var yearcollected;
var monthcollected;
var allLanguage=["English","French","Italian","German","Spanish","Chinese","Janpanese"];
var newDate=new Date();
var yearZero=newDate.getFullYear();
var monthZero=newDate.getMonth();
var day=newDate.getDate();
var currentDay=0, currentDayZero=0;
var month=monthZero, year=yearZero;
var language=0;
var yearMin=1950, yearMax=2050;
var target='';
var hoverEle=false;
var contextPath = '';
function setTarget(e){
	if(e) return e.target;
    if(event) return event.srcElement;
}
function newElement(type, attrs, content, toNode) {
    var ele=document.createElement(type);
    if(attrs) {
    	for(var i=0; i<attrs.length; i++) {
        	eval('ele.'+attrs[i][0]+(attrs[i][2] ? '=\u0027' :'=')+attrs[i][1]+(attrs[i][2] ? '\u0027' :''));
    	}
    }
    if(content) ele.appendChild(document.createTextNode(content));
    if(toNode) toNode.appendChild(ele);
   	return ele;
}
function newTable(type, attrs, content, toNode) {
	var ele=document.createElement(type);
    if(attrs) {
    	for(var i=0; i<attrs.length; i++) {
        	eval('ele.'+attrs[i][0]+(attrs[i][2] ? '=\u0027' :'=')+attrs[i][1]+(attrs[i][2] ? '\u0027' :''));
        }
    }
    if(content) ele.appendChild(document.createTextNode(content));
    if(toNode) toNode.appendChild(ele);
    return ele;
}
function setMonth(ele){month=parseInt(ele.value);calendar()}
function setYear(ele){year=parseInt(ele.value);calendar()}
function setLanguage(ele){
	var s=parseInt(ele.value);
	language=s;
	if(s==1) {
		allNameOfWeekDays=["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	else if(s==2) {
		allNameOfWeekDays=["Lun","Mar","Mer","Gio","Ven","Sab","Dom"];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	else if(s==3) {
		allNameOfWeekDays=["Mo","Di","Mi","Do","Fr","Sa","So"];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	else if(s==4) {
		allNameOfWeekDays=["Lun","Mar","Mie","Jue","Vie","Sab","Dom"];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	else if(s==5) {
		allNameOfWeekDays=["","","","","","",""];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	else if(s==6) {
		allNameOfWeekDays=["","","","","","",""];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	else {
		allNameOfWeekDays=["Mon","Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
		allNameOfMonths=["01","02","03","04","05","06","07","08","09","10","11","12"];
	}
	calendar();
}
function setValue(ele) {
	collectedyear=year;
	collectedmonth=month;
	collectedday= ele.firstChild.nodeValue;
	if(ele.parentNode.className=='week' && ele.firstChild){
    	var dayOut=ele.firstChild.nodeValue;
    	var monthOut;
    	if(style == 1) {
	        monthOut=month+1;
	       	if(dayOut < 10) dayOut='0'+dayOut;
			if(monthOut < 10) monthOut='0'+monthOut;
			target.value=dayOut+'/'+monthOut+'/'+year;
	    }
	    if(style == 4) {
	    	monthOut=allNameOfMonths[month];
			target.value=dayOut+'/'+monthOut+'/'+year;
			/*target.value=;*/
	    }
	    if(style == 2) {
	    	monthOut=month+1;

			if(monthOut < 10) monthOut='0'+monthOut;
			target.value=dayOut+'/'+monthOut+'/'+year;
	    }
	    if(style == 3) {

			monthOut=allNameOfMonths[month];
			target.value=dayOut+'/'+monthOut+'/'+year;
		}
		if(style == 0) {
			monthOut=month+1;

			if(dayOut < 10) dayOut='0'+dayOut;
			if(monthOut < 10) monthOut='0'+monthOut;
			target.value=monthOut+'/'+dayOut+'/'+year;
		}
		if(style == 5) {
			monthOut=month+1;

			if(monthOut < 10) monthOut='0'+monthOut;
			target.value=dayOut+'/'+monthOut+'/'+year;
		}
        if(style == 6) {
			monthOut=month+1;

			if(monthOut < 10) monthOut='0'+monthOut;
			target.value=dayOut+'/'+monthOut+'/'+year;
		}
		
		//fermeture du calendrier
		removeCalendar();
		
		//var onchangeTarget = onchangeTarget.replace(/this/gi, "target");
		//eval(onchangeTarget);
		
        //if(monthOut < 10) monthOut='0'+monthOut;
       
   
    }
}
function removeCalendar() {
	var parentEle=document.getElementById("calendar");
    while(parentEle.firstChild) parentEle.removeChild(parentEle.firstChild);
    document.getElementById('basis').parentNode.removeChild(document.getElementById('basis'));
    
}          
function calendar() {
	var parentEle=document.getElementById("calendar");
	if (parentEle != null){

  	 if (calculeOffsetTop(parentEle) < 0){
   		parentEle.style.top = parentEle.offsetTop - calculeOffsetTop(parentEle) + 20;
	}
	
   if (calculeOffsetLeft(parentEle) < 0){
		parentEle.style.left = parentEle.offsetLeft - calculeOffsetLeft(parentEle) + 20;
	}
	
	parentEle.onmouseover=function(e) {
    	var ele=setTarget(e);
        if(ele.parentNode.className=='week' && ele.firstChild && ele!=hoverEle) {
        	if(hoverEle) hoverEle.className=hoverEle.className.replace(/hoverEle ?/,'');
            hoverEle=ele;
            ele.className='hoverEle '+ele.className;
        } else {
          	if(hoverEle) {
            	hoverEle.className=hoverEle.className.replace(/hoverEle ?/,'');
                hoverEle=false;
            }
          }
	}
    while(parentEle.firstChild) parentEle.removeChild(parentEle.firstChild);
    function check(){
    	if(year%4==0&&(year%100!=0||year%400==0))allMonth[1]=29;
        else allMonth[1]=28;
    }
    function addClass (name) { if(!currentClass){currentClass=name} else {currentClass+=' '+name} };
    if(month < 0){month+=12; year-=1}
    if(month > 11){month-=12; year+=1}
    if(year==yearMax-1) yearMax+=1;
    if(year==yearMin) yearMin-=1;
    check();
    var control=newElement('p',[['id','control',1]],false,parentEle);
	var cancel=newElement('img',[['id','cancel',1],['src',contextPath + '/images/remove.gif',1],['onclick',function(){removeCalendar()}],['title','Fermeture de la fenêtre',1]],false,parentEle);
	//var controlPlus=newElement('a', [['href','javascript:month--;calendar()',1],['className','controlPlus',1]], '<', control);
    var select=newElement('select', [['onchange',function(){setMonth(this)}]], false, control);
    for(var i=0; i<allNameOfMonths.length; i++) newElement('option', [['value',i,1]], allNameOfMonths[i], select);
    select.selectedIndex=month;
    select=newElement('select', [['onchange',function(){setYear(this)}]], false, control);
    for(var i=yearMin; i<yearMax; i++) newElement('option', [['value',i,1]], i, select);
    select.selectedIndex=year-yearMin;
	//controlPlus=newElement('a', [['href','javascript:month++;calendar()',1],['className','controlPlus',1]], '>', control);
	check();
    currentDay=1-new Date(year,month,1).getDay();
    if(currentDay > 0) currentDay-=7;
    currentDayZero=currentDay;
    var newMonth=newTable('table',[['cellSpacing',0,1]], false, parentEle);
    var newMonthBody=newElement('tbody', false, false, newMonth);
    var tr=newElement('tr', [['className','head',1]], false, newMonthBody);
    tr=newElement('tr', [['className','weekdays',1]], false, newMonthBody);
    for(i=0;i<7;i++) td=newElement('td', false, allNameOfWeekDays[i], tr);     
    tr=newElement('tr', [['className','week',1]], false, newMonthBody);
    for(i=0; i<allMonth[month]-currentDayZero; i++){
    	var currentClass=false;               
        currentDay++;
        if(currentDay==day && month==monthZero && year==yearZero) {
        	addClass ('today');
        }
        if(daycollected==currentDay && month==monthcollected && year==yearcollected){
			addClass ('collected');
        }
		if(currentDay <= 0 ) {
        	if(currentDayZero!=-7) td=newElement('td', false, false, tr);
        }
        else {
        	if((currentDay-currentDayZero)%7==0) addClass ('holiday');
            td=newElement('td', ([['onclick',
            	function(e){setValue(setTarget(e))}],['className',
            	currentClass,1]]), currentDay,
            	tr);
            if((currentDay-currentDayZero)%7==0) tr=newElement('tr',
            	[['className','week',1]], false, newMonthBody);
        }
        if(i==allMonth[month]-currentDayZero-1){
        	i++;
            while(i%7!=0){i++;td=newElement('td', false, false, tr)};
        }
	}
	}
}
function calculeOffsetLeft(r){
  return calculeOffset(r,"offsetLeft")
}

function calculeOffsetTop(r){
  return calculeOffset(r,"offsetTop")
}
function calculeOffsetRight(r){
  return calculeOffset(r,"offsetRight")
}

function calculeOffset(element,attr){
  var offset=0;
  while(element){
    offset+=element[attr];
    element=element.offsetParent
  }
  return offset
}
function showCalendar(ele, paramContextPath, w, m, s, paramYearMin, paramYearMax, defaultDate) {
	yearZero=newDate.getFullYear();
	monthZero=newDate.getMonth();
	day=newDate.getDate();
		
	if(document.getElementById('basis'))
	{	if(target==document.getElementById(ele.id.replace(/for_/,'')))
			{ removeCalendar(); }
    }
    else {
		allNameOfWeekDays = w;
		allNameOfMonths = m;
		style = s;
        target=document.getElementById(ele.id.replace(/for_/,'')); 
		var stringdate=document.getElementById(target.id).value;
		var sd=[" "," "," "];
		var end=0;
		var start=0,n=0;
		yearMin = paramYearMin;
		yearMax = paramYearMax;
		contextPath = paramContextPath;
		
		for(j=1;j<3;j++,n++){
			end=stringdate.indexOf('/',start);
			sd[n]=stringdate.substring(start,end);
			start=end+1;
		}
		sd[n]=stringdate.substring(start,stringdate.length);
		if(target.value){
			
			if (style == 1){
				if(parseInt(sd[2],10)>yearMin && parseInt(sd[2],10)<yearMax && parseInt(sd[1],10)>=1
				&& parseInt(sd[1],10)<=12 && parseInt(sd[0],10)>0
				&& parseInt(sd[0],10)<=allMonth[parseInt(sd[1],10)-1]){
				year=parseInt(sd[2],10);
				month=parseInt(sd[1],10)-1;
				daycollected=parseInt(sd[0],10);
				yearcollected=parseInt(sd[2],10);
				monthcollected=parseInt(sd[1],10) -1;
				}
				else {
				year=newDate.getFullYear();
				month=newDate.getMonth();
				day=newDate.getDate();
				daycollected=0;
				yearcollected=0;
				monthcollected=0;
				}
			}
			if (style == 0){
				if(parseInt(sd[2],10)>yearMin && parseInt(sd[2],10)<yearMax && parseInt(sd[0],10)>=1
				&& parseInt(sd[0],10)<=12 && parseInt(sd[1],10)>0
				&& parseInt(sd[1],10)<=allMonth[parseInt(sd[0],10)-1]){
				year=parseInt(sd[2],10);
				month=parseInt(sd[0],10)-1;
				daycollected=parseInt(sd[1],10);
				yearcollected=parseInt(sd[2],10);
				monthcollected=parseInt(sd[0],10) -1;
				}
				else {
				year=newDate.getFullYear();
				month=newDate.getMonth();
				day=newDate.getDate();
				daycollected=0;
				yearcollected=0;
				monthcollected=0;
				}
			}
			
		}
		else {
		
			if (defaultDate != '' && defaultDate != 'null'){
				year = eval(defaultDate.substring(6));
				month= eval(defaultDate.substring(3, 5))-1;
				day= eval(defaultDate.substring(0, 2));
			}else{
				year = newDate.getFullYear();
				month=newDate.getMonth();
				day=newDate.getDate();
			}
			daycollected=0;
			yearcollected=0;
			monthcollected=0;
		}
		var basis=ele.parentNode.insertBefore(document.createElement('div'),ele);
		basis.id='basis';
        newElement('div', [['id','calendar',1]], false, basis);
		//target=document.getElementById(ele.id.replace(/for_/,''));
		
		calendar();
	}
}
function getMonthNumber(s) {
	var i;
	for(i=0;i<12;i++){
		if(allNameOfMonths[i]==s) return i;
	}
	return null;
}