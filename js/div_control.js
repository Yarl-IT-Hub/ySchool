function hide_search_div() 
{ 
	document.getElementById("searchdiv").style.display = "none";
	document.getElementById("addiv").style.display = "block";
} 

function show_search_div() 
{ 
	document.getElementById("searchdiv").style.display = "block";
	document.getElementById("addiv").style.display = "none";
} 

function hidediv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("hideShow").style.display = "none";
	} 
} 

function showdiv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("hideShow").style.display = "block";
	} 
} 

function prim_hidediv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("prim_hideShow").style.display = "none";
	} 
} 

function prim_showdiv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("prim_hideShow").style.display = "block";
	} 
} 

function sec_hidediv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("sec_hideShow").style.display = "none";
	} 
} 

function sec_showdiv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("sec_hideShow").style.display = "block";
	} 
} 

function addn_hidediv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("addn_hideShow").style.display = "none";
	} 
} 

function addn_showdiv() 
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("addn_hideShow").style.display = "block";
	} 
}
function confirmAction(){
    chk='n';
    var option="";
if(document.run_schedule.test_mode.checked==false)
{
    
    if(document.run_schedule.delete_mode.checked==false)
        {
            
            chk='y';
        }
        else
            var option="delete current schedules ? ";
}
else
    var option="run the scheduler to schedule unscheduled requests? ";
if(chk=='y')
{
    var d = $('divErr');
    var err = "Please select one options.";
    d.innerHTML="<b><font color=red>"+err+"</font></b>";
    return false;
}
else
{
      if (confirm("Do you really want to "+option) == true)
         return true;
      else
         return false;
}
  }

function showhidediv(it,box)
{
	if (document.getElementById)
	{



                var vis = (box.checked) ? "block" : "none";

		document.getElementById(it).style.display = vis;
	}
}