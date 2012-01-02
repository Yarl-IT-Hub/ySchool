
		function formcheck_school_setup_school()
		{
			var sel = document.getElementsByTagName('input');
			for(var i=1; i<sel.length; i++)
			{
				var inp_value = sel[i].value;
				if(inp_value == "")
				{
					var inp_name = sel[i].name;
					if(inp_name == 'values[TITLE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter School Name")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[ADDRESS]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter Address")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[CITY]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter City")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[STATE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter State")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[ZIPCODE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter Zip/Postal Code")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[PHONE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter Phone")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[PRINCIPAL]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter Principal")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[REPORTING_GP_SCALE]')
					{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter Base Grading Scale")+"</font></b>";
						return false;
					}
					
				}
				else if(inp_value != "")
				{
					var val = inp_value;
					var inp_name1 = sel[i].name;
					
					if(inp_name1 == 'values[ZIPCODE]')
					{
					
						var charpos = val.search("[^0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter a Valid Zip/Postal Code.")+"</font></b>";
							return false;
						}
					}
					if(inp_name1 == 'values[PHONE]')
					{
					
						var charpos = val.search("[^0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter a Valid Phone Number.")+"</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[REPORTING_GP_SCALE]')
					{
					
						var charpos = val.search("[^0-9.]");
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter Decimal Value Only.")+"</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[E_MAIL]')
					{
						var emailRegxp = /^(.+)@(.+)$/;
						if (emailRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter a Valid Email.")+"</font></b>";
							return false;
						}
					}
					/*else if(inp_name1 == 'values[WWW_ADDRESS]')
					{
						var urlRegxp = /^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([\w]+)(.[\w]+){1,2}$/;
						if (urlRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter a Valid url.")+"</font></b>";
							return false;
						}
					}*/
				}
			}
                        return true;
//			document.school.submit();
		}

	function formcheck_school_setup_portalnotes()
	{
	
		var frmvalidator  = new Validator("F2");
		
		frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Sort Order is 5");
		
		frmvalidator.setAddnlValidationFunction("ValidateDate_Portal_Notes");

	
	}
	
	
	function formcheck_student_advnc_srch()
	{
	
	var day_to=  $('day_to_birthdate');
    var month_to=  $('month_to_birthdate');
	var day_from=  $('day_from_birthdate');
    var month_from=  $('month_from_birthdate');
	if(!day_to.value && !month_to.value && !day_from.value && !month_from.value ){
		return true;
		}
    if(!day_to.value || !month_to.value || !day_from.value || !month_from.value )
		{ 
		strError="Please Provide Birthday TO Day, To Month, From Day, From Month.";
	document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";return false;
		}	
				 				strError="To date must be equal to or greater than From date.";	

								if(month_from.value > month_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";                   
                                return false;
    							}else if(month_from.value == month_to.value && day_from.value > day_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";
                                return false;
    							}return true;
                                    
	
	}
	
		
	function ValidateDate_Portal_Notes()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F2"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))

		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the Grade Posting Start Date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}



	function formcheck_school_setup_marking(){

  	var frmvalidator  = new Validator("marking_period");
  	frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for title is 50");
	
	frmvalidator.addValidation("tables[new][SHORT_NAME]","req","Please enter the Short Name");
  	frmvalidator.addValidation("tables[new][SHORT_NAME]","maxlen=10", "Max length for Short Name is 10");
	
	frmvalidator.addValidation("tables[new][SORT_ORDER]","maxlen=5", "Max length for Short Order is 5");
  	frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "Enter Only Numeric Value");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Marking_Periods");
}

function ValidateDate_Marking_Periods()
{
var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey, grd ;
var frm = document.forms["marking_period"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}


if(elem[i].name=="month_tables[new][POST_START_DATE]")
{
psm=elem[i];
}
if(elem[i].name=="day_tables[new][POST_START_DATE]")
{
psd=elem[i];
}
if(elem[i].name=="year_tables[new][POST_START_DATE]")
{
psy=elem[i];
}


if(elem[i].name=="month_tables[new][POST_END_DATE]")
{
pem=elem[i];
}
if(elem[i].name=="day_tables[new][POST_END_DATE]")
{
ped=elem[i];
}
if(elem[i].name=="year_tables[new][POST_END_DATE]")
{
pey=elem[i];
}

if(elem[i].name=="tables[new][DOES_GRADES]")
{
grd=elem[i];
}

}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the Start Date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please Enter the End Date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDate(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}

if (true==validate_chk(grd))
{

try
{  
   if (false==isDate(psm, psd, psy))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the Grade Posting Start Date."+"</font></b>";
   psm.focus();
   return false;
   }
}   
catch(err)
{

}

try
{  
   if (true==isDate(pem, ped, pey))
   {
   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
   {
   pem.focus();
   return false;
   }
   }

}   
catch(err)
{

}






try
{
   if (false==CheckDateMar(sm, sd, sy, psm, psd, psy))
   {
	   psm.focus();
	   return false;
   }
}
catch(err)
{

}



}




   return true;
}



function formcheck_school_setup_copyschool()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the New School's Title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for Title is 100");
}



function formcheck_school_setup_calender()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the Title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for Title is 100");
}



function formcheck_school_setup_periods()
{
  	var frmvalidator  = new Validator("F1");

	var p_name = document.getElementById('values[new][TITLE]');
	var p_name_val = p_name.value;
	
	if(p_name_val != "")
	{
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for Short Name is 50");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Short Order is 5");
		
		frmvalidator.addValidation("values[new][START_HOUR]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_MINUTE]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_M]","req","Please select start time");
		
		frmvalidator.addValidation("values[new][END_HOUR]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_MINUTE]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_M]","req","Please select end time");
	} 
	
}


function formcheck_school_setup_grade_levels()
{
		var frmvalidator  = new Validator("F1");
		
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
		
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for Short Name is 50");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Sort Order is 5");
		
}


function formcheck_student_student()
{

  	var frmvalidator  = new Validator("student");
  	frmvalidator.addValidation("students[FIRST_NAME]","req","Please enter the First Name");
	frmvalidator.addValidation("students[FIRST_NAME]","maxlen=100", "Max length for School Name is 100");
	
	frmvalidator.addValidation("students[LAST_NAME]","req","Please enter the Last Name");
	frmvalidator.addValidation("students[LAST_NAME]","maxlen=100", "Max length for Address is 100");
    frmvalidator.addValidation("students[GENDER]","req","Please select Gender");
    frmvalidator.addValidation("students[ETHNICITY]","req","Please select Ethnicity");
	
	frmvalidator.addValidation("assign_student_id","num", "Student ID allows only numeric value");



  	frmvalidator.addValidation("values[STUDENT_ENROLLMENT][new][GRADE_ID]","req","Please select a Grade");
	
	frmvalidator.addValidation("students[USERNAME]","maxlen=50", "Max length for Username is 50");
	
	frmvalidator.addValidation("students[PASSWORD]","maxlen=20", "Max length for Password is 20");
	frmvalidator.addValidation("students[EMAIL]","email");
	frmvalidator.addValidation("students[PHONE]","phone","Invalid Phone number");
	
	
  		
	
	
	frmvalidator.addValidation("values[STUDENT_ENROLLMENT][new][NEXT_SCHOOL]","req","Please select Rolling / Retention Options");
	
	frmvalidator.addValidation("values[ADDRESS][ADDRESS]","req","Please enter address");
	
	frmvalidator.addValidation("values[ADDRESS][CITY]","req","Please enter city");
	
	frmvalidator.addValidation("values[ADDRESS][STATE]","req","Please enter state");
		
	frmvalidator.addValidation("values[ADDRESS][ZIPCODE]","req","Please enter zipcode");	
	
	frmvalidator.addValidation("values[ADDRESS][PRIM_STUDENT_RELATION]","req","Relation");

	frmvalidator.addValidation("values[ADDRESS][PRI_FIRST_NAME]","req","Please enter First Name");	
	
	frmvalidator.addValidation("values[ADDRESS][PRI_LAST_NAME]","req","Please enter Last Name");	
	
	frmvalidator.addValidation("values[ADDRESS][SEC_STUDENT_RELATION]","req","Please enter Secondary Relation");
	
	frmvalidator.addValidation("values[ADDRESS][SEC_FIRST_NAME]","req","Please enter Secondary Emergency Contact Frist Name ");	
	
	frmvalidator.addValidation("values[ADDRESS][SEC_LAST_NAME]","req","Please enter  Secondary Emergency Contact Last Name");	
	
	frmvalidator.addValidation("values[STUDENTS_JOIN_PEOPLE][STUDENT_RELATION]","req","Relation");
	
	
	
	frmvalidator.addValidation("values[PEOPLE][FIRST_NAME]","req","Please enter First Name");		
	
	frmvalidator.addValidation("values[PEOPLE][LAST_NAME]","req","Please enter Last Name");		



 	frmvalidator.addValidation("values[ADDRESS][ADDRESS]","req","Please Enter Address");
	frmvalidator.addValidation("values[ADDRESS][PHONE]","ph","Please enter a valid phone number");
	
	frmvalidator.addValidation("values[PEOPLE][FIRST_NAME]","alphabetic","first name allows only alphabetic value");
	frmvalidator.addValidation("values[PEOPLE][LAST_NAME]","alpha","last name allows only alphabetic value");
	
	frmvalidator.addValidation("students[PHYSICIAN]","req","Please enter the Physician name");
	
	frmvalidator.addValidation("students[PHYSICIAN_PHONE]","ph","Phone Number Should not be alphabetic.");
	
	
 	frmvalidator.addValidation("tables[GOAL][new][GOAL_TITLE]","req","Please enter Goal Title");
        frmvalidator.addValidation("tables[GOAL][new][GOAL_TITLE]","req","Please enter Goal Title");

	frmvalidator.addValidation("tables[GOAL][new][GOAL_DESCRIPTION]","req","Please enter Goal Description");
	
	
 	frmvalidator.addValidation("tables[PROGRESS][new][PROGRESS_NAME]","req","Please enter Progress Period Name");
	frmvalidator.addValidation("tables[PROGRESS][new][PROFICIENCY]","req","Please select Proficiency Scale");
	frmvalidator.addValidation("tables[PROGRESS][new][PROGRESS_DESCRIPTION]","req","Please enter Progress Assessment");
	
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Student");


}

function change_pass()
 {	
 	
	var frmvalidator  = new Validator("change_password");
	frmvalidator.addValidation("old","req","Please enter old password");
	frmvalidator.addValidation("new","req","Please enter new password");
	frmvalidator.addValidation("retype","req","Please retype password");
	
		
 }

function ValidateDate_Student()
{
var bm, bd, by ;
var frm = document.forms["student"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_students[BIRTHDATE]")
{
bm=elem[i];
}
if(elem[i].name=="day_students[BIRTHDATE]")
{
bd=elem[i];
}
if(elem[i].name=="year_students[BIRTHDATE]")
{
by=elem[i];
}


}

for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}



}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter Date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter End Date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDateGoal(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}
//-----
try
{
   if (false==CheckValidDateGoal(sm, sd, sy, em, ed, ey))
   {
   sm.focus();
   return false;
   }
}
catch(err)
{

}


try
{
if (false==CheckBirthDate(bm, bd, by))
   {
   bm.focus();
   return false;
   }
}
catch(err)
{

}

return true;

}

   




	function formcheck_student_studentField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order Code allows only numeric value");
	}
	
	



	function formcheck_student_studentField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	
                    function formcheck_student_studentField_F1_defalut()
                    {
                           var type=document.getElementById('type');
                           if(type.value=='textarea')
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=true;
                           else
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=false;
                    }

///////////////////////////////////////// Student Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Address Field Start //////////////////////////////////////////////////////////



	function formcheck_student_addressField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order Code allows only numeric value");
	}
	
	


	function formcheck_student_addressField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



///////////////////////////////////////// Address Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Contact Field Start //////////////////////////////////////////////////////////


	
	function formcheck_student_contactField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order Code allows only numeric value");
	}
	
	


	function formcheck_student_contactField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



	function formcheck_user_user(staff_school_chkbox_id){

  	var frmvalidator  = new Validator("staff");
  	frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the First Name");
//	frmvalidator.addValidation("staff[FIRST_NAME]","alphabetic", "First name allows only alphabetic value");
  	frmvalidator.addValidation("staff[FIRST_NAME]","maxlen=100", "Max length for First Name is 100");
	
		
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
//	frmvalidator.addValidation("staff[LAST_NAME]","alphabetic", "Last Name allows only alphabetic value");
  	frmvalidator.addValidation("staff[LAST_NAME]","maxlen=100", "Max length for Address is 100");

	frmvalidator.addValidation("staff[PROFILE]","req","Please Select the User Profile");
	

	frmvalidator.addValidation("staff[PHONE]","ph","Please enter a valid telephone number");

     return   school_check(staff_school_chkbox_id);

	
}

        function school_check(staff_school_chkbox_id)
		{
			chk='n';
			if(staff_school_chkbox_id)
			{
				for(i=1;i<=staff_school_chkbox_id;i++)
				{
					if(document.getElementById('staff_SCHOOLS'+i).checked==true)
					{
						chk='y';
					}
				}
				if(chk!='y')
				{
					var d = $('divErr');
					var err = "Please assign at least one school to this new user.";
					d.innerHTML="<b><font color=red>"+err+"</font></b>";
					return false;
				}
				else
				{
					return true;
				}
			}
	    }
	

/////////////////////////////////////////  Add User End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User Fields Start  //////////////////////////////////////////////////////////

	function formcheck_user_userfields_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the Title");
		frmvalidator.addValidation("tables[new][TITLE]","alphabetic", "Title allows only alphabetic value");
		frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Title is 100");
	}
	
	function formcheck_user_userfields_F1()
	{
		var frmvalidator1  = new Validator("F1");
		frmvalidator1.addValidation("tables[new][TITLE]","req","Please enter the Field Name");
		frmvalidator1.addValidation("tables[new][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Field Name is 100");
                //frmvalidator1.addValidation("tables[new][SORT_ORDER]","req","Please enter the Sort Order");
                frmvalidator1.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}

/////////////////////////////////////////  User Fields End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User End  ////////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling start ///////////////////////////////////////////////////////

//////////////////////////////////////// Course start ///////////////////////////////////////////////////////

function formcheck_scheduling_course_F4()
{
	var frmvalidator  = new Validator("F4");
  	frmvalidator.addValidation("tables[COURSE_SUBJECTS][new][TITLE]","req","Please enter the Title");
  	frmvalidator.addValidation("tables[COURSE_SUBJECTS][new][TITLE]","maxlen=100", "Max length for Title is 100");
}

function formcheck_scheduling_course_F3()
{
	var frmvalidator  = new Validator("F3");
  	frmvalidator.addValidation("tables[COURSES][new][TITLE]","req","Please enter the Title");
  	frmvalidator.addValidation("tables[COURSES][new][TITLE]","maxlen=50", "Max length for Title is 50");
	
  	frmvalidator.addValidation("tables[COURSES][new][SHORT_NAME]","req","Please enter the Short Name");
  	frmvalidator.addValidation("tables[COURSES][new][SHORT_NAME]","maxlen=10", "Max length for Short Name is 10");
}

function formcheck_scheduling_course_F2()
{
	var frmvalidator  = new Validator("F2");
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][SHORT_NAME]","req","Please enter the Short Name");
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][SHORT_NAME]","maxlen=20", "Max length for Short Name is 20");

  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][TEACHER_ID]","req","Please select the Teacher");

  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][ROOM]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][ROOM]","maxlen=10", "Max length for Room is 10");
	
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][PERIOD_ID]","req","Please select the Period");
	
	frmvalidator.addValidation("tables[COURSE_PERIODS][new][TOTAL_SEATS]","req","Please input Total Seats");
	frmvalidator.addValidation("tables[COURSE_PERIODS][new][TOTAL_SEATS]","maxlen=100","Max length for Seats is 100");
       
        frmvalidator.addValidation("get_status","attendance=0","Can't take attendance in this period");
}


///////////////////////////////////////// Course End ////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling End ///////////////////////////////////////////////////////

//////////////////////////////////////// Grade Start ///////////////////////////////////////////////////////


function formcheck_grade_grade()
{
    var frmvalidator  = new Validator("F1");
    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
    frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for Short Name is 50");
    frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
    frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Short Order is 5");

}
function formcheck_honor_roll()
{
    var frmvalidator  = new Validator("F1");
 
    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
 
    frmvalidator.addValidation("values[new][VALUE]","maxlen=50", "Max length for Short Name is 50");
}

//////////////////////////////////////// Report Card Comment Start ///////////////////////////////////////////////////////

function formcheck_grade_comment()
{

		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "ID allows only numeric value");
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Comment is 50");
	
}

////////////////////////////////////////  Report Card Comment End  ///////////////////////////////////////////////////////


//////////////////////////////////////// Grade End ///////////////////////////////////////////////////////

///////////////////////////////////////// Eligibility Start ////////////////////////////////////////////////////

///////////////////////////////////////// Activies Start //////////////////////////////////////////////////

function formcheck_eligibility_activies()
{
	
	var frmvalidator  = new Validator("F1");
	
	frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_eligibility_activies");

}


	
	function ValidateDate_eligibility_activies()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))
		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the Grade Posting Start Date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}




///////////////////////////////////////// Activies End ////////////////////////////////////////////////////



///////////////////////////////////////// Entry Times Start ////////////////////////////////////////////////

function formcheck_eligibility_entrytimes()
{
  	var frmvalidator  = new Validator("F1");
	frmvalidator.setAddnlValidationFunction("ValidateTime_eligibility_entrytimes");
}

	function ValidateTime_eligibility_entrytimes()
	{
		var sd, sh, sm, sp, ed, eh, em, ep, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="values[START_DAY]")
			{
				sd=elem[i];
			}
			if(elem[i].name=="values[START_HOUR]")
			{
				sh=elem[i];
			}
			if(elem[i].name=="values[START_MINUTE]")
			{
				sm=elem[i];
			}
			if(elem[i].name=="values[START_M]")
			{
				sp=elem[i];
			}
			if(elem[i].name=="values[END_DAY]")
			{
				ed=elem[i];
			}
			if(elem[i].name=="values[END_HOUR]")
			{
				eh=elem[i];
			}
			if(elem[i].name=="values[END_MINUTE]")
			{
				em=elem[i];
			}
			if(elem[i].name=="values[END_M]")
			{
				ep=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckTime(sd, sh, sm, sp, ed, eh, em, ep))
		   {
			   sh.focus();
			   return false;
		   }
		}
		catch(err)
		{
		}
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		}
		
		   return true;
	}




///////////////////////////////////////// Entry Times End //////////////////////////////////////////////////

function formcheck_discipline()
{

    var frmvalidator  = new Validator("disc");
    var a =        frmvalidator.addValidation("students[location]","req","Please select a location");
        var b = frmvalidator.addValidation("students[possible_motivation]","req","Please select a posssible motivation");
        var c = frmvalidator.addValidation("students[Others_Involved]","req","Please select Others Involved");
        var d = frmvalidator.addValidation("students[Administrative_Decision]","req","Please select a Administrative Decision");
        frmvalidator.setAddnlValidationFunction("validatecheckbox");
}
 function validatecheckbox()
                        {

                                var frm = document.forms["disc"];
                                var elem = frm.elements;
                          
                     for(var i = 1; i < elem.length; i++)
                     {
                            if(elem[i].name=='students[probbehavior_minor][]')
                            {
                                  var sd=elem[i];
                                  break;
                            }
             }
              for(var i = 1; i < elem.length; i++)
                     {
                            if(elem[i].name=='students[probbehavior_major][]')
                            {
                                  var major=elem[i];
                                  break;
                            }
             }
            
     	for(i=1;i<=document.getElementById("size_students_probbehavior_major").value;i++){
if(document.getElementById("size_students_probbehavior_major_"+i).checked==true ){
	return true;
	}
	}
        for(i=1;i<=document.getElementById("size_students_probbehavior_minor").value;i++){
if(document.getElementById("size_students_probbehavior_minor_"+i).checked==true ){
return true;
	}
	}
 
		 alert("please select  at least one behavior");
         return false;
     
             }
       
function formcheck_mass_drop()
{
    if(document.getElementById("course_div").innerHTML=='')
    {    
        alert("Please choose a course period to drop");
        return false;
    }
    else
        return true;
}



function formcheck_attendance_category()
{
        var frmvalidator  = new Validator("F1");
        frmvalidator.addValidation("new_category_title","req","Please enter Attendance Category Name");
        frmvalidator.addValidation("new_category_title","maxlen=50", "Max length for Category Name is 50");
			
}

function formcheck_attendance_codes()
{
        if(document.getElementById("title").value!='')
        {
            var frmvalidator  = new Validator("F1");
            frmvalidator.addValidation("values[new][STATE_CODE]","req","Please select state code");
        }
}
//-------------------------------------------------assignments Title Validation Starts---------------------------------------------
function formcheck_assignments()
{

           var frmvalidator  = new Validator("F3");
           frmvalidator.addValidation("tables[new][TITLE]","req","Title Cannot be Blank");
           frmvalidator.addValidation("month_tables[new][ASSIGNED_DATE]","req","Month Cannot be Blank");
           frmvalidator.addValidation("day_tables[new][ASSIGNED_DATE]","req","Day Cannot be Blank");
           frmvalidator.addValidation("year_tables[new][ASSIGNED_DATE]","req","Year Cannot be Blank");
            frmvalidator.addValidation("month_tables[new][DUE_DATE]","req","Month Cannot be Blank");
           frmvalidator.addValidation("day_tables[new][DUE_DATE]","req","Day Cannot be Blank");
           frmvalidator.addValidation("year_tables[new][DUE_DATE]","req","Year Cannot be Blank");
}
//-------------------------------------------------assignments Title Validation Ends---------------------------------------------
