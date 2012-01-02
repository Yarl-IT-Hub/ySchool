<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
error_reporting(0);
session_start();
echo '<script type="text/javascript">
var page=parent.location.href.replace(/.*\//,"");
if(page && page!="index.php"){
	window.location.href="index.php";
	}

</script>';


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../styles/installer.css" type="text/css" />
<script type="text/javascript" src="js/validator.js"></script>
<!-- <script type="text/javascript" src="js/datetimepicker_css.js"></script> -->
<script type="text/javascript" src="js/datetimepicker.js"></script>
<script type="text/javascript" src="js/prototype.js"></script>

</head>
<body>
<div class="heading">Database created
<div style="background-image:url(images/step3_new.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">
  <form name='step3' id='step3' method="post" action="ins3.php">
    <table border="0" cellspacing="2" cellpadding="3" align="center">
      <tr>
        <td  align="center" style="padding-top:40px; padding-bottom:10px">Step 3 of 5</td>
      </tr>
	  <tr>        <td align="center" valign="top">
		<table width="400" border="0" cellpadding="0" align="center" cellspacing="0" id="table1">
		
		<tr><td>
		<div>Please enter your School Name, beginning and ending dates of the school year.</div>
		<div style="height:6px;"></div>
		<div>
		 <table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">

                <tr>
                    <td align="left">School Name </td><td> : </td><td><input type="text" name="sname" id="sname" size="30" value=""  /></td>
            </tr>

            <!--
            </tr>
            <td align="center">Begining Date : <input name="beg_date" id="beg_date" maxlength="25" size="10" type="Text" readonly>
                  <a href="javascript:NewCssCal('beg_date','mmddyyyy')" >
                            <img src="images/cal.gif" alt="Pick a date" height="16" width="16" border="0"></a></td>
            </tr>
             <tr>
                 <td align="center">End Date : <input name="end_date" id="end_date" maxlength="25" size="10" type="Text" readonly>
                        <a href="javascript:NewCssCal('end_date','mmddyyyy')" >
                            <img src="images/cal.gif" alt="Pick a date" height="16" width="16" border="0"></a></td>
            </tr> -->
           <!-- <tr>
              <td align="center"><input type="text" name="syear" size="20" value="<?php # echo date("Y"); ?>" /></td>
            </tr> -->

             
             <tr>
            <td align="left">Begining Date (mm/dd/yyyy)</td><td> : </td><td> <input name="beg_date" id="beg_date" maxlength="25" size="10" type="Text"  />
                   <a href="javascript:NewCal('beg_date','mmddyyyy')"><img src="images/cal.gif" width="16" height="16" border="0" alt="Pick a date" /></a></td>
            </tr>
             <tr>
                 <td align="left">Ending Date (mm/dd/yyyy)</td><td> : </td><td> <input name="end_date" id="end_date" maxlength="25" size="10" type="Text" />
                 <a href="javascript:NewCal('end_date','mmddyyyy')"><img src="images/cal.gif" width="16" height="16" border="0" alt="Pick a date" /></a>
                 </td>
            </tr>
			</table>
			</div>
			<div style="height:6px;" align="center"></div>
			<div>
			 <table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">
            <tr>

	  <td align="center" valign="top"><input type="checkbox" name="sample_data" id="sample_data" value="insert" /><strong>Install with sample school data</strong></td>
	</tr>
	</table>
	</div>
	<div style="height:6px;"></div>
			<table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">
            <tr>
              <td  align="center"><input type="submit" value="Save & Next" class=btn_wide name="btnsyear" /></td>
            </tr>
          </table>
		  </div>
		  </td></tr></table>
          <script language="JavaScript" type="text/javascript">
				
				/*function CheckYear()
				{
					  var frm = document.forms["step4"];
					  if(frm.syear.value <2000)
						{
							alert('The year should start from 2000');
							frm.syear.focus();
							return false;
						  }
						  else
						  {
							return true;
						  }
				} */

                               function blankValidation(){
                                   var school_name=  $('sname');
                                   var beg_date=  $('beg_date');
                                   var end_date=  $('end_date');
                                   var sample_data = $('sample_data');
                                   
//                                   var bd= beg_date.value.split("-");
//                                   var ed= end_date.value.split("-");
                                     var bd= beg_date.value.split("/");
                                   var ed= end_date.value.split("/");
                                  	
                                  //beg_date.value=bd[2]+"-"+bd[0]+"-"+bd[1];
                                  //end_date.value=ed[2]+"-"+ed[0]+"-"+ed[1];
                                    

                                   
         if((school_name.value!='' && beg_date.value!='' && end_date.value!='' )||sample_data.checked==true){
			 if(school_name.value!=''|| beg_date.value!=''|| end_date.value!=''){
				 if(!(school_name.value!='' && beg_date.value!='' && end_date.value!='')){
					  alert("please provide required info.");
                                    return false;
					 }
				 
				 }		
				 				 bd[0]=parseInt(bd[0]);
								 bd[1]=parseInt(bd[1]);
								 bd[2]=parseInt(bd[2]);
								 ed[0]=parseInt(ed[0]);
								 ed[1]=parseInt(ed[1]);
								 ed[2]=parseInt(ed[2]);
				 
                                if(bd[2] > ed[2]){
                                    alert("End date must be greater than Begin date.");
                                    return false;
                                }else if(bd[2] < ed[2]){
                                     return true;

                                }
                                else if(bd[2] == ed[2] &&  bd[0] > ed[0]){
                                    alert("End date must be greater than Begin date.");
                                    return false;

                                }else if(bd[0] < ed[0]){
                                    
                                    return true;
                                }
                                else if( bd[0] == ed[0] &&  bd[1] > ed[1] ){
                            alert("End date must be greater than Begin date.");
                                    return false;
    
                            }else if(bd[1] <= ed[1]){
                            return true;
                            }


                                        return true;
                                    }
                                    else
                                        {
                                            alert("please provide required info.");
                                            return false;
					}

                                   
                               }

					var frmvalidator  = new Validator("step3");
					// frmvalidator.addValidation("syear","req","Please enter the System Year");
					 //  frmvalidator.addValidation("syear","maxlen=4", "Maximum length of year is 4");
					 //  frmvalidator.addValidation("syear","numeric");
					 // frmvalidator.setAddnlValidationFunction("CheckYear");
                                         frmvalidator.setAddnlValidationFunction("blankValidation");
				</script>        </td>
      </tr>
    </table>
  </form>
</div>
</div>
</body>
</html>
