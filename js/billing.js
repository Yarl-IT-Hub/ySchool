var xmlHttp

function GetAmount(str)
{
if (str.length==0)
  {
  document.getElementById("am_val").value=""
  return
  }
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
  {
  alert ("Browser does not support HTTP Request")
  return
  }
var url="modules/Billing/GetAmount.php"
url=url+"?q="+str
xmlHttp.onreadystatechange=stateChanged
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
}

function stateChanged()
{
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 {
  document.getElementById("am_val").value=xmlHttp.responseText
 }
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 // Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}

var billing = {};
billing.STUDENT = {};
billing.STUDENTS;

billing.CheckForm = function(form)
{
	var length = form.elements.length;
	for(var i = 0; i < length; i++)
	{
		if(form.elements[i].type == "text" || form.elements[i].type == "password" || form.elements[i].type == "textarea")
		{
			if(form.elements[i].value == '' || form.elements[i].value == null)
			{
				alert("Please Complete All Fields");
				return false;
			}
		}
	}
	return true;
};

billing.formatCurrency = function(num) {
    num = num.toString().replace(/\$|\,/g,'');

    if(isNaN(num))
        num = "0";

    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();

    if(cents<10)
        cents = "0" + cents;

    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
        num = num.substring(0,num.length-(4*i+3))+','+

    num.substring(num.length-(4*i+3));

    return (((sign)?'':'-') + '$' + num + '.' + cents);
}

billing.showBalances = function(){
	check_content('ajax.php?modname=Billing/reports.php&TAB=1');
};

billing.showDaliyTrans = function(){
	check_content('ajax.php?modname=Billing/reports.php&TAB=2');
};

billing.showPayments = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=2');
};

billing.showFees = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=1');
};

billing.showMassFees = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=3');
};

billing.showMassPayments = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=4');
};