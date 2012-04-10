var currentDate=new Date();
var day=currentDate.getDate();
var month=currentDate.getMonth();
var year=currentDate.getFullYear();
var dateValide=true;

//function to check the date fields format
function forDateFormat(ele,bottom,top,format){
	dateValide=true;
	var currentValue="";
	day=currentDate.getDate();
	month=currentDate.getMonth();
	year=currentDate.getFullYear();
	var sign=['/', '.', '-'];
	
	if (format!="dd/MM/yyyy" && format!="MM/dd/yyyy" && format!="dd/MM/YYYY" && format!="MM/dd/YYYY"){
		alert("Date invalide");
		ele.value = "";
		return false;		
	}

	// if there are too many numbers, it cannot be a date
	if(ele.value.length > 0 && ele.value.length<11){
		
		// Loop for the different signs 
		for(i=0;i<sign.length;i++){
			var tmpRestDate=ele.value;
			var j=tmpRestDate.indexOf(sign[i]);
			while(j>0){
				// If the signs are at a right place it can be a date, otherwise not
				if((j==1)||(j==2)||(j==4)){
					currentValue+=tmpRestDate.substring(0, j)+"/";
					tmpRestDate=tmpRestDate.substring(j+1);
					j=tmpRestDate.indexOf(sign[i]);
				}else{
					alert("Date invalide");
					ele.value = "";
					return false;
				}
			}
			// If the separator was found but the remaining string is not a number	
			if(isNaN(tmpRestDate) && tmpRestDate!=ele.value && tmpRestDate!=""){
					alert("Date invalide");
					ele.value = "";
					return false;				
			}		
			// The date had symbols, the last number needs to be saved 
			if(currentValue!="" && tmpRestDate!=ele.value){
				currentValue+=tmpRestDate;
			}
		}
		
		// Handles the "with sign" dates 
		if(currentValue!=""){	
			setFormatDayMonthYear(ele,currentValue,currentValue.indexOf("/"),currentValue.lastIndexOf("/"),1,format);
		}
		// Handles the "without sign" dates 
		else if(currentValue=="" && !isNaN(ele.value)){	
			setFormatDayMonthYear(ele,ele.value,2,4,0,format);			
		}
		// If the date has signs but they are not adequate
		else{
			alert("Date invalide");
			ele.value = "";
			return false;
		}

		// If the day, month and year are in an adequate format
		//if(ele.value!='Pas une date'){
		if(dateValide){
			if ( (format=="dd/MM/yyyy") || (format=="dd/MM/YYYY") ){
				ele.value=day+'/'+month+'/'+year;
			}else if( (format=="MM/dd/yyyy") || ((format=="MM/dd/YYYY")) ){
				ele.value=month+'/'+day+'/'+year;
			}
		}else{
			alert("Date invalide");
			ele.value = "";
			return false;
		}
	}
	// If there is no data in the field, no error (mandatory case not handled here)
	else if(ele.value.length > 0){
		alert("Date invalide");
		ele.value = "";
		return false;					
	}
	
	return true;
}

// Separates all the fields of the input date into day, month and year
function setFormatDayMonthYear(ele,inputDate,firstIndexSep,lastIndexSep,hasSep,format){
	// if only information about the day is given		
	if(inputDate.length<3){
		setFormatDay(ele,inputDate.substring(0,firstIndexSep));
	}
	// at least information for day and month are given
	else {
		if ( (format=="dd/MM/yyyy") || (format=="dd/MM/YYYY") ){
			setFormatDay(ele,inputDate.substring(0, firstIndexSep));
			// information for the year is also given
			if(firstIndexSep != lastIndexSep){
				setFormatMonth(ele,inputDate.substring(firstIndexSep+hasSep, lastIndexSep));
				setFormatYear(ele,inputDate.substring(lastIndexSep+hasSep));
			}
			// information for the year is not given
			else{
				setFormatMonth(ele,inputDate.substring(firstIndexSep+hasSep));
			}
		}else if( (format=="MM/dd/yyyy") || (format=="MM/dd/YYYY") ){
			// information for the year is also given
			if(firstIndexSep != lastIndexSep){
				setFormatDay(ele,inputDate.substring(firstIndexSep+hasSep, lastIndexSep));
				setFormatYear(ele,inputDate.substring(lastIndexSep+hasSep));
			}
			// information for the year is not given
			else{
				setFormatDay(ele,inputDate.substring(firstIndexSep+hasSep));
			}
			setFormatDay(ele,inputDate.substring(firstIndexSep+hasSep, lastIndexSep));
			setFormatMonth(ele,inputDate.substring(0, firstIndexSep));
		}
	}
}

function setFormatDay(ele,d){
	if (d!="")
		day="";
	
	// If the length is too small, add a 0
	if (d.length==1) {
		day="0";
	}
	day+=d;

	// If the value is outside the day range of the month or not a number
	if(isNaN(day) || day>31  || day.length!=2){
		//ele.value='Pas une date';
		dateValide = false;
	}
}

function setFormatMonth(ele,m){
	if (m!=""){
		month="";
	}
	
	// If the length is too small, add a 0
	if (m.length==1) {
		month="0";
	}
	month+=m;
	// If the value is outside the month range or not a number
	if(isNaN(month) || month>12 || month.length!=2){
		//ele.value='Pas une date';
		dateValide = false;
	}
}

function setFormatYear(ele,y){
	if (y!="")
		year="";
	
	// If the length is too small, add a 0
	if (y.length==2) {
		if (y < 40){
			year="20";
		}
		else{
			year="19";
		}
	}
	year+=y;
	
	// If the value is not a number or outside the year range
	if(isNaN(year) || year.length!=4){
		//ele.value='Pas une date';
		dateValide = false;
	}
}