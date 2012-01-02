function loadajax(frmname)
{
  this.formobj=document.forms[frmname];
	if(!this.formobj)
	{
	  alert("BUG: couldnot get Form object "+frmname);
		return;
	}
	if(this.formobj.onsubmit)
	{
	 this.formobj.old_onsubmit = this.formobj.onsubmit;
	 this.formobj.onsubmit=null;
	}
	else
	{
	 this.formobj.old_onsubmit = null;
	}
	this.formobj.onsubmit=ajax_handler;
	
}

function ajax_handler()
{
	if(ajaxform(this, this.action) =='failed')
	return true;
	
	return false;
}

function formload_ajax(frm){
		var frmloadajax  = new loadajax(frm);
}



var hand = function(str){
	window.document.getElementById('response_span').innerHTML=str;
}
/*function validateUsername(user){
	var strDomain='';
	window.document.getElementById('response_span').innerHTML="Validating username...";
	var valajax = new ValAjax();
	valajax.doGet(strDomain+'validator.php?action=validateUsername&username='+user,hand,'text');
}*/


function ajax_call (url, callback_function, error_function) {
	var xmlHttp = null;
	try {
		// for standard browsers
		xmlHttp = new XMLHttpRequest ();
	} catch (e) {
		// for internet explorer
		try {
			xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
	    } catch (e) {
			xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
	    }
	}
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4)
			try {
				if (xmlHttp.status == 200) {
					
					callback_function (xmlHttp.responseText);
				}
			} catch (e) {
				
				error_function (e.description);
			}
	 }
	
	 xmlHttp.open ("GET", url);
	 xmlHttp.send (null);
 }
 // --------------------------------------------------- USER ----------------------------------------------------------------------------------- //
 
 function usercheck_init(i) {
	var obj = document.getElementById('ajax_output');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	
	var pqr = i.value;
	
	
	ajax_call('validator.php?u='+i.value+'user', usercheck_callback, usercheck_error); 
 }
 
  function usercheck_callback (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }
 
  function usercheck_error (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ USER ---------------------------------------------------------------------------------- //

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

 function usercheck_init_student(i) {
	var obj = document.getElementById('ajax_output_st');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
	ajax_call('validator.php?u='+i.value+'stud', usercheck_callback_student, usercheck_error_student); 
 }

 function usercheck_callback_student (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_st');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }

 function usercheck_error_student (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

 function usercheck_student_id(i) {
	var obj = document.getElementById('ajax_output_stid');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^0-9_]/)) err[err.length] = 'Student ID can only contain numbers';
 	
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	ajax_call ('validator_int.php?u='+i.value+'stid', usercheck_callback_student_id, usercheck_error_student_id); 
 }

 function usercheck_callback_student_id (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_stid');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Student ID OK' : 'Student ID already taken';
 }

 function usercheck_error_student_id (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //


//-----------------Take attn depends on period------------------------------------------------------

function formcheck_periods_attendance_F2(attendance)
{
           if(document.getElementById('cp_period'))
           {
                period_id = document.getElementById('cp_period').value;
           }
           else
           {
                period_id = 0;
           }
    var err = new Array ();
    if(attendance.checked)
        {
           var obj = document.getElementById('ajax_output');
           var period_id;
           
           var cp_id=document.getElementById('cp_id').value;
           obj.innerHTML = '';

           if (attendance.value.length < 1) return;

                if (period_id.length ==0)
                    {
                    err[err.length] = 'Select Period';
                    document.getElementById('get_status').value = 'false';
                    }
                    else
                        err[err.length] ='';
                if (err != '') {
                        obj.style.color = '#ff0000';
                        obj.innerHTML = err.join ('<br />');
                        return;
                }
                var pqr = attendance.value;
                ajax_call('validator_attendance.php?u='+attendance.value+'&p_id='+period_id+'&cp_id='+cp_id, attendance_callback, attendance_error);
        }
        else
            {
                if (period_id.length ==0)
                    {
                        err[err.length] = 'Select Period';
                        document.getElementById('get_status').value = 'false';
                    }
                    else
                        err[err.length] ='';
                if (err != '') {
                        obj.style.color = '#ff0000';
                        obj.innerHTML = err.join ('<br />');
                        return;
                }
                if(err =='')
                {
           document.getElementById('ajax_output').innerHTML = '';
           document.getElementById('get_status').value ='';
            }
}
}

  function attendance_callback (data)
  {
       var response = (data == '1');
 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
        obj.innerHTML = (response == '1' ? '' : 'You cant take attendance');
       if(response==false)
         document.getElementById('get_status').value = response;
       else
        document.getElementById('get_status').value ='';
           }

  function attendance_error (err) {
 	alert ("Error: " + err);
 }

function formcheck_periods_F2()
{
    if(!document.getElementById('cp_does_attendance') || (!document.getElementById('cp_does_attendance').checked))
    {
       var obj = document.getElementById('ajax_output');
       var period_id=document.getElementById('cp_period').value;
       var cp_id=document.getElementById('cp_id').value;
       var err = new Array ();
       if (period_id.length ==0)
       {
            err[err.length] = 'Select Period';
            document.getElementById('get_status').value = 'false';
       }
       else
           err[err.length]='';
       if(err =='')
           {
           document.getElementById('ajax_output').innerHTML = '';
           document.getElementById('get_status').value ='';
           }
       if (err != '')
       {
            obj.style.color = '#ff0000';
            obj.innerHTML = err.join ('<br />');
            return;
       }
       if(!document.getElementById('cp_does_attendance'))
       ajax_call('validator_attendance.php?u=N&p_id='+period_id+'&cp_id='+cp_id, attendance_callback, attendance_error);
    }
    else
    {
      if(document.getElementById('cp_does_attendance').checked)
      {
        formcheck_periods_attendance_F2(document.getElementById('cp_does_attendance'));
      }
      else
        document.getElementById('get_status').value ='';
}
}

//----------------------------------------------------------------------


function ajax_call_modified(url,callback_function,error_function)
{
    var xmlHttp = null;
    try {
        xmlHttp = new XMLHttpRequest ();
    } catch (e) {
    try {
        xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
    } catch (e) {
        xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
    }
    }
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 1){
            try {
                document.getElementById('calculating').style.display="block";
            } catch (e) {
                error_function (e.description);
            }
        }
        if (xmlHttp.readyState == 4){
            try {
                if (xmlHttp.status == 200) {
                    callback_function(xmlHttp.responseText);
                }
            } catch (e) {
                error_function (e.description);
            }
        }
    }
    xmlHttp.open ("GET", url);
    xmlHttp.send (null);
}

//=========================================Missing Attendance===========================
function mi_callback(mi_data)
{
                    document.getElementById("resp").innerHTML=mi_data;
                    document.getElementById("calculating").style.display="none";
                    if(mi_data.search('NEW_MI_YES')!=-1)
                    {
                        document.getElementById("attn_alert").style.display="block"
                    }
}
function calculate_missing_atten()
{
     var url = "calculate_missing_attendance.php";
     ajax_call_modified(url,mi_callback,missing_attn_error);
}

function missing_attn_error(err)
{
    alert ("Error: " + err);
}
//-------------------------------------Missing Attendance end ------------------------------------------------

function rollover_callback(roll_data)
{
    //alert(roll_data);
    var total_data;
    total_data=roll_data.split('|');
	var value=total_data[2];
	if(value==0)
	{
		var rollover_class='rollover_no';
	}
	else
	{
		var rollover_class='rollover_yes';		
	}
	
	if(total_data[0]=='Users'){
            document.getElementById("STAFF").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("STAFF").setAttribute("class", rollover_class);
            document.getElementById("STAFF").setAttribute("className", rollover_class);
            ajax_rollover('SCHOOL_PERIODS');
        }
        else if(total_data[0]=='School Periods')
        {
            document.getElementById("SCHOOL_PERIODS").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("SCHOOL_PERIODS").setAttribute("class", rollover_class);
            document.getElementById("SCHOOL_PERIODS").setAttribute("className", rollover_class);
            ajax_rollover('SCHOOL_YEARS');
        }

       else if(total_data[0]=='Marking Periods')
       {
            document.getElementById("SCHOOL_YEARS").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("SCHOOL_YEARS").setAttribute("class", rollover_class);
            document.getElementById("SCHOOL_YEARS").setAttribute("className", rollover_class);
            ajax_rollover('ATTENDANCE_CALENDARS');
       }
       else if(total_data[0]=='Calendars')
       {
            document.getElementById("ATTENDANCE_CALENDARS").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("ATTENDANCE_CALENDARS").setAttribute("class", rollover_class);
            document.getElementById("ATTENDANCE_CALENDARS").setAttribute("className", rollover_class);
            ajax_rollover('REPORT_CARD_GRADE_SCALES');
       }
       else if(total_data[0]=='Report Card Grade Codes')
       {
            document.getElementById("REPORT_CARD_GRADE_SCALES").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("REPORT_CARD_GRADE_SCALES").setAttribute("class", rollover_class);
            document.getElementById("REPORT_CARD_GRADE_SCALES").setAttribute("className", rollover_class);
            ajax_rollover('COURSES');
       }
       else if(total_data[0]=='Courses')
       {
            document.getElementById("COURSES").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("COURSES").setAttribute("class", rollover_class);
            document.getElementById("COURSES").setAttribute("className", rollover_class);
            ajax_rollover('STUDENT_ENROLLMENT_CODES');
       }
        else if(total_data[0]=='Student Enrollment Codes')
       {
            document.getElementById("STUDENT_ENROLLMENT_CODES").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("STUDENT_ENROLLMENT_CODES").setAttribute("class", rollover_class);
            document.getElementById("STUDENT_ENROLLMENT_CODES").setAttribute("className", rollover_class);
            ajax_rollover('STUDENT_ENROLLMENT');
       }
       else if(total_data[0]=='Students')
       {
            document.getElementById("STUDENT_ENROLLMENT").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("STUDENT_ENROLLMENT").setAttribute("class", rollover_class);
            document.getElementById("STUDENT_ENROLLMENT").setAttribute("className", rollover_class);
            ajax_rollover('ELIGIBILITY_ACTIVITIES');
       }
       else if(total_data[0]=='Eligibility Activity Codes')
       {
            document.getElementById("ELIGIBILITY_ACTIVITIES").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("ELIGIBILITY_ACTIVITIES").setAttribute("class", rollover_class);
            document.getElementById("ELIGIBILITY_ACTIVITIES").setAttribute("className", rollover_class);
            ajax_rollover('ATTENDANCE_CODES');
       }
       else if(total_data[0]=='Attendance Codes')
       {
            document.getElementById("ATTENDANCE_CODES").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("ATTENDANCE_CODES").setAttribute("class", rollover_class);
            document.getElementById("ATTENDANCE_CODES").setAttribute("className", rollover_class);
            ajax_rollover('REPORT_CARD_COMMENTS');
       }

       else if(total_data[0]=='Report Card Comment Codes')
       {
            document.getElementById("REPORT_CARD_COMMENTS").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("REPORT_CARD_COMMENTS").setAttribute("class", rollover_class);
            document.getElementById("REPORT_CARD_COMMENTS").setAttribute("className", rollover_class);
            ajax_rollover('NONE');
       }
        else 
        {
            document.getElementById("response").innerHTML=roll_data;
            document.getElementById("calculating").style.display="none";
        }
}

function rollover_error(err)
{
    alert ("Error: " + err);
}

function ajax_rollover(roll_table)
{   
     var url = 'Rollover_shadow.php?table_name='+roll_table;
     ajax_call_modified(url,rollover_callback,rollover_error);
}
function formcheck_rollover()
{
    var start_month_len=document.getElementById("monthSelect1").value;
    var start_day_len=document.getElementById("daySelect1").value;
    var start_year_len=document.getElementById("yearSelect1").value;
    if(start_month_len=="" || start_day_len=="" || start_year_len=="")
    {     
        document.getElementById("start_date").innerHTML="Please Enter Start Date ";
        return false;
    }
    if(start_month_len!="" && start_day_len!="" && start_year_len!="")
     {
        return true;
     }
    
}
