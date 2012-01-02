ReturnFunc = '';
function Calendar(iYear, iMonth, iDay, ContainerId, ClassName, cnt) 
{ 
    MonthNames = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'); 
    oDate = new Date();
    Year = (iYear == null) ? oDate.getFullYear() : iYear; 
    Month = (iMonth == null) ? oDate.getMonth() : iMonth;
    while(Month < 0){Month += 12;Year--;} 
    while(Month >= 12){Month -= 12;Year++;}
    Day = (iDay == null) ? 0 : iDay; 
    oDate = new Date(Year, Month, 1);
    NextMonth = new Date(Year, Month + 1, 1); 
    WeekStart = oDate.getDay(); 
    MonthDays = Math.round((NextMonth.getTime() - oDate.getTime()) / 86400000) + 1; 
    if(ContainerId != null) 
    { 
        ContainerId = ContainerId; 
        Container = document.getElementById(ContainerId); 
        if(!Container) 
            document.write('<div id="' + ContainerId + '">&nbsp;</div>'); 
    } 
    else 
    { 
        do 
        { 
            ContainerId = 'tblCalendar' + Math.round(Math.random() * 1000); 
        } 
        while(document.getElementById(ContainerId)); 
        document.write('<div style="z-index:999" id="' + ContainerId + '">&nbsp;</div>'); 
    } 
    Container = document.getElementById(ContainerId); 
    Container.style.width="200px"; 
    Container.style.height = "200px"
    ClassName = (ClassName == null) ? 'tblCalendar' : ClassName; 
    HTML = '<table class="' + ClassName + '" cellspacing="0" style="z-index:999">'; 
        HTML += '<tr><td colspan=6 bgcolor=000><font color=white><b>openSIS Calendar</b></font></td><td bgcolor=000 onclick="document.getElementById(\'cal\').style.visibility=\'hidden\'; make_visible();"><a style="cursor:pointer; text-decoration:none;" onclick="document.getElementById(\'cal\').style.visibility=\'hidden\'; make_visible();"><font color=white><b>X</b></font></a></td></tr>';

	HTML += '<tr class="TitleBar"><td colspan=7><table width="100%" border="0"><tr class="TitleBar">';
	for(var i=0;i<=5;i++){
  	HTML +='<td class="Nav" style="border:0;" onMouseDown="Calendar(' + Year + ', ' + i + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');"><a href="javascript:void(0)" onMouseDown="Calendar(' + Year + ', ' + i + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\', '+ cnt +');" style="text-decoration:none;">' + MonthNames[i] + '</a></td>';
	}
	HTML +='</tr><tr class="TitleBar">'; 
	for(var i=6;i<12;i++){
  	HTML +='<td class="Nav" style="border:0;" onMouseDown="Calendar(' + Year + ', ' + i + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');"><a href="javascript:void(0)" onMouseDown="Calendar(' + Year + ', ' + i + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');" style="text-decoration:none;">' + MonthNames[i] + '</a></td>';
	}
	HTML +='</tr></table>'; 


	
	HTML += '<tr class="TitleBar"><td class="Nav" onMouseDown="Calendar(' + (Year-1) + ', ' + Month + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');"><a href="javascript:void(0)" onMouseDown="Calendar(' + (Year-1) + ', ' + Month + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');">&lt;</a></td><td class="Nav" onMouseDown="Calendar(' + Year + ', ' + (Month-1) + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');"><a href="javascript:void(0)" onMouseDown="Calendar(' + Year + ', ' + (Month-1) + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\','+ cnt +');">&laquo;</a></td><td colspan="3" class="Title">' + MonthNames[Month] + ' ' + Year + '</td><td width="25" class="Nav" onMouseDown="Calendar(' + Year + ', ' + (Month + 1) + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\', '+ cnt +');"><a href="javascript:void(0)" onMouseDown="Calendar(' + Year + ', ' + (Month + 1) + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\', '+ cnt +');">&raquo;</a></td><td  width="25" class="Nav" onMouseDown="Calendar(' + (Year + 1) + ', ' + Month + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\', '+ cnt +');"><a href="javascript:void(0)" onMouseDown="Calendar(' + (Year + 1) + ', ' + Month + ', ' + Day+', \''+ContainerId+'\', \''+ClassName+'\', '+ cnt +');">&gt;</a></td></tr>'; 
    HTML += '<tr class="WeekName"><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td></tr>'; 
    HTML += '<tr class="Days">'; 
    for(DayCounter = 0; DayCounter < WeekStart; DayCounter++) 
    { 
        HTML += '<td>&nbsp;</td>'; 
    } 
    for(DayCounter = 1; DayCounter < MonthDays; DayCounter++) 
    { 
        if((DayCounter + WeekStart) % 7 == 1) HTML += '<tr class="Days">'; 
        if(DayCounter == Day) 
            HTML += '<td class="SelectedDay"><a style="cursor:pointer; text-decoration:none" href="javascript:ReturnDate(' + DayCounter + ',' + cnt +')">' + DayCounter + '</a></td>'; 
        else HTML += '<td class="SelectedDay"><a style="cursor:pointer; text-decoration:none" href="javascript:ReturnDate(' + DayCounter + ',' + cnt + ')">' + DayCounter + '</a></td>'; 
        if((DayCounter + WeekStart) % 7 == 0) HTML += '</tr>'; 
    } 
    for(j = (42 - (MonthDays + WeekStart)), DayCounter = 0; DayCounter <= j; DayCounter++) 
    { 
        HTML += '<td>&nbsp;</td>'; 
        if((j - DayCounter) % 7 == 0) HTML += '</tr>'; 
    } 
    HTML += '</table>'; 
    Container.innerHTML = HTML; 
    return ContainerId; 
} 

function mon_change(sis)
{
	if(sis==1)
	return "JAN";
	if(sis==2)
	return "FEB";
	if(sis==3)
	return "MAR";
	if(sis==4)
	return "APR";
	if(sis==5)
	return "MAY";
	if(sis==6)
	return "JUN";
	if(sis==7)
	return "JUL";
	if(sis==8)
	return "AUG";
	if(sis==9)
	return "SEP";
	if(sis==10)
	return "OCT";
	if(sis==11)
	return "NOV";
	if(sis==12)
	return "DEC";

}

function make_visible()
{
		var sel = document.getElementsByTagName('select');
		for(var i=0; i<sel.length; i++)
		{
			sel[i].style.visibility="visible";
		}

}


function ReturnDate(Day, cnt) 
{ 
	make_visible();
	
	if(Day < 10)
	document.getElementById("daySelect"+cnt).value = "0" + Day;
	else
	document.getElementById("daySelect"+cnt).value = Day;
	
	document.getElementById("monthSelect"+cnt).value = mon_change(Month+1);
	document.getElementById("yearSelect"+cnt).value = Year;
	
    document.getElementById("cal").innerHTML='';
    document.getElementById("cal").style.width="0px";
    document.getElementById("cal").style.height="0px";
    
    
} 
function MakeDate(cnt,obj, iYear, iMonth, iDay, fn) 
{ 
	var pos = document.getElementById("cal");
	pos.style.visibility = "visible";
	pos.style.zIndex = "999";
	pos.style.top = GetY(obj.id)-198 + 'px';
	pos.style.left = GetX(obj.id)-183 + 'px';
	
	var MSIE = navigator.userAgent.indexOf('MSIE')>=0?true:false;
	var navigatorVersion = navigator.appVersion.replace(/.*?MSIE ([0-9]\.[0-9]).*/g,'$1')/1;
	var sel = document.getElementsByTagName('select');
	if(MSIE && navigatorVersion<=6)
	{
		for(var i=0; i<sel.length; i++)
		{
			sel[i].style.visibility="hidden";
		}
	}
	
    D = new Date(); 
    Year = (typeof(iYear) != 'undefined') ? iYear : D.getFullYear(); 
    Month = (typeof(iMonth) != 'undefined') ? iMonth : D.getMonth(); 
    Day = (typeof(iDay) != 'undefined') ? iDay : D.getDate(); 
    ReturnFunc = fn; 
    id = Calendar(Year, Month, Day, 'cal', 'CalendarRed', cnt);
} 


function GetY(xElement){
  var selectedPosY = 0;
  
  var theElement = document.getElementById(xElement);
  while(theElement != null){
	 
    selectedPosY += theElement.offsetTop;
    theElement = theElement.offsetParent;
  }
                     		      		      
  return selectedPosY 
}

function GetX(xElement){
  var selectedPosX = 0;
  var theElement = document.getElementById(xElement);
  while(theElement != null){
    selectedPosX += theElement.offsetLeft;
    theElement = theElement.offsetParent;
  }
                     		      		      
  return selectedPosX 
}
