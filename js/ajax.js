var loadingImage = new Image();
loadingImage.src = "images/ajax-loader_clock.gif";

function showLoading()
{
	var str;
	str = '<table><tr><td width=200px></td></tr>';
	str = str+ '<tr><td align=center><img border=0 src=\'images/loading.gif\'></td></tr>';
	str = str + '<tr><td align=center>Loading...</td></tr></table>';
	
	return str;
}

function makeObject(){ 
var x; 
if (window.ActiveXObject) { 
x = new ActiveXObject("Microsoft.XMLHTTP"); 
}else if (window.XMLHttpRequest) { 
x = new XMLHttpRequest(); 
} 
return x; 
} 
var request = makeObject(); 

var the_content; 
function check_content(the_content){ 
request.open('get', the_content); 
request.onreadystatechange = parseCheck_content; 
request.send(''); 
} 
function parseCheck_content(){ 
if(request.readyState == 1){ 
document.getElementById('content').innerHTML = '<center><img border=0 src=assets/ajax_loader.gif><br>Loading...</center>'; 
} 
if(request.readyState == 4){ 
var answer = request.responseText; 
document.getElementById('content').innerHTML = answer; 
} 
} 
function load_link(the_content){ 
the_content = the_content.replace("Modules.php", "ajax.php");
request.open('get', the_content); 
request.onreadystatechange = parseCheck_content; 
request.send(''); 
} 

function ajaxform(thisform,formhandler) 
{ 
var formdata = ""; 
try {xmlhttp = window.XMLHttpRequest?new XMLHttpRequest(): new ActiveXObject("Microsoft.XMLHTTP");} catch (e) { alert("Error: Could not load page.");} 
for (i=0; i < thisform.length; i++) 
{
if(thisform.elements[i].value != 'Delete')
{
if(thisform.elements[i].type == "text"){ 
formdata = formdata + thisform.elements[i].name + "=" + escape(thisform.elements[i].value) + "&"; 
}else if(thisform.elements[i].type == "textarea"){ 
formdata = formdata + thisform.elements[i].name + "=" + escape(thisform.elements[i].value) + "&"; 
}else if(thisform.elements[i].type == "checkbox"){ 
if(thisform.elements[i].value && thisform.elements[i].checked)
formdata = formdata + thisform.elements[i].name + "=" + thisform.elements[i].value + "&";
}else if(thisform.elements[i].type == "radio"){
if(thisform.elements[i].checked==true){ 
formdata = formdata + thisform.elements[i].name + "=" + thisform.elements[i].value + "&"; 
} 
}else{ 
formdata = formdata + thisform.elements[i].name + "=" + escape(thisform.elements[i].value) + "&"; 
}
}
}

xmlhttp.onreadystatechange = function(){ 
if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
return 'send';
else
return 'failed';
} 
formhandler = formhandler.replace("Modules.php", "ajax.php");
if(formdata.length < 1900)
check_content(formhandler+"&"+formdata+"ajax=true"); 
else
{
	xmlhttp.open("POST", formhandler, true); 
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
	xmlhttp.setRequestHeader("Content-length", parameters.length);
	xmlhttp.setRequestHeader("Connection", "close");

	xmlhttp.send(formdata); 
}
} 

function loadformani(thisform,formhandler) 
{ 
var formdata = ""; 
try {xmlhttp = window.XMLHttpRequest?new XMLHttpRequest(): new ActiveXObject("Microsoft.XMLHTTP");} catch (e) { alert("Error: Could not load page.");} 
for (i=0; i < thisform.length; i++) 
{
if(thisform.elements[i].name != 'button' && thisform.elements[i].value != 'Delete')
{
if(thisform.elements[i].type == "text"){ 
formdata = formdata + thisform.elements[i].name + "=" + escape(thisform.elements[i].value) + "&"; 
}else if(thisform.elements[i].type == "textarea"){ 
formdata = formdata + thisform.elements[i].name + "=" + escape(thisform.elements[i].value) + "&"; 
}else if(thisform.elements[i].type == "checkbox"){ 
formdata = formdata + thisform.elements[i].name + "=" + thisform.elements[i].checked + "&"; 
}else if(thisform.elements[i].type == "radio"){ 
if(thisform.elements[i].checked==true){ 
formdata = formdata + thisform.elements[i].name + "=" + thisform.elements[i].value + "&"; 
} 
}else{ 
formdata = formdata + thisform.elements[i].name + "=" + escape(thisform.elements[i].value) + "&"; 
}
}
}

xmlhttp.onreadystatechange = function(){ 
if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
return 'send';
else
return 'failed';


} 
formhandler = formhandler.replace("Modules.php", "ajax.php");
xmlhttp.open("POST", formhandler, true); 
xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
xmlhttp.setRequestHeader("Content-length", parameters.length);
xmlhttp.setRequestHeader("Connection", "close");
xmlhttp.send(formdata); 
} 

function grabA(alink)
{
	var oldlink = alink.href;
	oldlink = oldlink.replace("Modules.php", "ajax.php");
	oldlink = oldlink + "&ajax=true";
	check_content(oldlink);
	
}

function cancelEvent(e) {
    if (!e) e = window.event;
    if (e.preventDefault) {
        e.preventDefault();
    } else {
        e.returnValue = false;
    }
}
function stopEvent(e) {
    if (!e) e = window.event;
    if (e.stopPropagation) {
        e.stopPropagation();
    } else {
        e.cancelBubble = true;
    }
}