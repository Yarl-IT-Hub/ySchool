function showDiv()
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("attach").style.display = "block";
	} 
} 
function showDiv1()
{ 
	if (document.getElementById) 
	{ 
		document.getElementById("attach1").style.display = "block";
	} 
} 
function showGroups($group_id){
	var group_id=$group_id;
       // alert(group_id);
       check_content('ajax.php?modname=EasyCom/Groups.php&TAB=1&group_id='+group_id);
};

function showContactList($group_id){
	var group_id=$group_id;
       // alert(group_id);
       check_content('ajax.php?modname=EasyCom/Groups.php&TAB=2&group_id='+group_id);
};

function AddContactList($group_id){
	var group_id=$group_id;
       // alert(group_id);
       check_content('ajax.php?modname=EasyCom/Groups.php&TAB=3&group_id='+group_id);
};

