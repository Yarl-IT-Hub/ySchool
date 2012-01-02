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
include('../../Redirect_modules.php');
unset($_SESSION['_REQUEST_vars']['values']);unset($_SESSION['_REQUEST_vars']['modfunc']);
DrawBC("School Setup > ".ProgramTitle());
// --------------------------------------------------------------- Test SQL ------------------------------------------------------------------ //
/*
$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC");
$RET = DBGet($QI);
//print_r($RET);
echo $RET[1]['ID'];
echo "<br>";
echo $RET[1]['TITLE'];
*/
// --------------------------------------------------------------- Tset SQL ------------------------------------------------------------------ //

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save' || clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Update' || clean_param($_REQUEST['button'],PARAM_ALPHAMOD)==''))
{
	if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && $_POST['values'] && User('PROFILE')=='admin')
	{
		if($_REQUEST['new_school']!='true')
		{
			$sql = "UPDATE SCHOOLS SET ";

			foreach($_REQUEST['values'] as $column=>$value)
			{
                                $value=paramlib_validation($column,$value);
                                if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                                {

                                $sql .= $column."=\"".str_replace("\'","''",$value)."\",";
                             
                                }
                                else
                                {
				$sql .= $column."='".str_replace("\'","''",$value)."',";
                                }

                        }
			$sql = substr($sql,0,-1) . " WHERE ID='".UserSchool()."'";

			DBQuery($sql);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>'; 
			$note[] = 'This school has been modified.';
                                                      $_REQUEST['modfunc'] = '';
		}
		else
		{
			$fields = $values = '';

			foreach($_REQUEST['values'] as $column=>$value)
				if($column!='ID' && $value)
				{
                    $value=paramlib_validation($column,$value);
					$fields .= ','.$column;
					$values .= ",\" ".str_replace("\'","''",$value)." \" ";
				}

			if($fields && $values)
			{
				/*
				$id = DBGet(DBQuery("SELECT ".db_seq_nextval('SCHOOLS_SEQ')." AS ID".FROM_DUAL));
				$id = $id[1]['ID'];
				$sql = "INSERT INTO SCHOOLS (SYEAR,ID$fields) values(".UserSyear().", $id$values)";
				*/
				
				#$id = DBGet(DBQuery("SELECT ".db_seq_nextval('SCHOOLS_SEQ')." AS ID".FROM_DUAL));
				#$id = $id[1]['ID'];
				
                        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'SCHOOLS'"));
                        $id = $id[1]['AUTO_INCREMENT'];
				
				$sql = "INSERT INTO SCHOOLS (SYEAR$fields) values(".UserSyear()."$values)";
				
				DBQuery($sql);
				
		#		echo "UPDATE STAFF SET SCHOOLS=CONCAT(CONCAT(SCHOOLS,','),'$id') WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOLS IS NOT NULL";
				
				DBQuery("UPDATE STAFF SET SCHOOLS=CONCAT(SCHOOLS,'$id,') WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOLS IS NOT NULL");
				
                                                                        DBQuery("INSERT INTO SCHOOL_YEARS (MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT fn_marking_period_seq()+MARKING_PERIOD_ID,SYEAR,'$id' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY MARKING_PERIOD_ID");
				DBQuery("INSERT INTO SYSTEM_PREFERENCE(school_id, full_day_minute, half_day_minute) VALUES (".$id.", NULL, NULL)");
				
				
				$_SESSION['UserSchool'] = $id;
				
//                                                                        echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>'; 
				
				unset($_REQUEST['new_school']);
			}
	echo '<FORM action=Modules.php?modname='.$_REQUEST['modname'].' method=POST>';
	echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	echo "<br><br>";
	DrawHeaderHome('<IMG SRC=assets/check.gif> &nbsp; A new school called<strong>'.  GetSchool(UserSchool()).'</strong> has been created. To finish the operation, click OK button.','<INPUT  type=submit value=OK class="btn_medium">');
	echo '<input type="hidden" name="copy" value="done"/>';
	echo '</FORM>';
		}
	}

//	$_REQUEST['modfunc'] = '';
	unset($_SESSION['_REQUEST_vars']['values']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);
//	echo '<script language=JavaScript> document.forms[0].submit(); </script>'; 
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Delete' && User('PROFILE')=='admin')
{
	if(DeletePrompt('school'))
	{
			if(BlockDelete('school'))
			{
				DBQuery("DELETE FROM SCHOOLS WHERE ID='".UserSchool()."'");
				DBQuery("DELETE FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("DELETE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("DELETE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("DELETE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("DELETE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("DELETE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("DELETE FROM SCHOOL_PROGRESS_PERIODS WHERE SCHOOL_ID='".UserSchool()."'");
				DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID=NULL WHERE CURRENT_SCHOOL_ID='".UserSchool()."'");
				DBQuery("UPDATE STAFF SET SCHOOLS=replace(SCHOOLS,',".UserSchool().",',',')");
		
				unset($_SESSION['UserSchool']);
				echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
				unset($_REQUEST);
				$_REQUEST['modname'] = "School_Setup/Schools.php?new_school=true";
				$_REQUEST['new_school'] = true;
				unset($_REQUEST['modfunc']);
				echo '
				<SCRIPT language="JavaScript">
				window.location="Side.php?school_id=new&modcat='.$_REQUEST['modcat'].'";
				</SCRIPT>
				';
			}/*echo '<script language=JavaScript> document.head_frm.submit(); </script>'; */
	}
}
if(clean_param($_REQUEST['copy'],PARAM_ALPHAMOD)=='done'){
echo '<br><strong>School has been created successfully.</strong>';
}  else {
if(!$_REQUEST['modfunc'])
{
	if(!$_REQUEST['new_school'])
	{
		$schooldata = DBGet(DBQuery("SELECT ID,TITLE,ADDRESS,CITY,STATE,ZIPCODE,PHONE,PRINCIPAL,WWW_ADDRESS,REPORTING_GP_SCALE,E_MAIL,CEEB FROM SCHOOLS WHERE ID='".UserSchool()."'"));
		$schooldata = $schooldata[1];
		$school_name = GetSchool(UserSchool());
	}
	else
		$school_name = 'Add a School';
		
	echo "<FORM name=school  id=school  enctype='multipart/form-data'  METHOD='POST' ACTION='Modules.php?modname=".$_REQUEST['modname']."&modfunc=update&btn=".$_REQUEST['button']."&new_school=$_REQUEST[new_school]'>";
#	DrawHeader('',"<INPUT TYPE=SUBMIT name=button VALUE='Save'>".(($_REQUEST['new_school']!='true')?"<INPUT type=submit name=button value=Delete>":''));
#	echo '<BR>';
#	PopTable('header',$school_name);
	PopTable_wo_header('header');
#	echo "<FIELDSET><TABLE>";
	echo "<table border=0 align=center><tr><td>";
     

        echo "<TABLE align=center>";

	echo "<TR><TD class='label'><b>School Name:</b></td><td>".TextInput($schooldata['TITLE'],'values[TITLE]', '','class=cell_floating size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>Address:</b></TD><td>".TextInput($schooldata['ADDRESS'],'values[ADDRESS]','','class=cell_floating maxlength=100 size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>City:</b></TD><td>".TextInput($schooldata['CITY'],'values[CITY]','','maxlength=100, class=cell_floating size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>State:</b></TD><td>".TextInput($schooldata['STATE'],'values[STATE]','','maxlength=10 class=cell_floating size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>Zip/Postal Code:</b></TD><td>".TextInput($schooldata['ZIPCODE'],'values[ZIPCODE]','','maxlength=10 class=cell_floating size=24')."</td></TR>";

	echo "<TR ALIGN=LEFT><TD  class='label'><b>Telephone:</b></td><td>".TextInput($schooldata['PHONE'],'values[PHONE]','','class=cell_floating size=24')."</TD></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>Principal:</b></td><td>".TextInput($schooldata['PRINCIPAL'],'values[PRINCIPAL]','','class=cell_floating size=24')."</TD></TR>";
        echo "<TR ALIGN=LEFT><TD class='label'><b>Base Grading Scale:</b></td><td>".TextInput($schooldata['REPORTING_GP_SCALE'],'values[REPORTING_GP_SCALE]','','class=cell_floating maxlength=10 size=24')."</TD></TR>";
        echo "<TR ALIGN=LEFT><TD class='label'><b>E-Mail:</b></td><td>".TextInput($schooldata['E_MAIL'],'values[E_MAIL]','','class=cell_floating maxlength=100 size=24')."</TD></TR>";
        echo "<TR ALIGN=LEFT><TD class='label'><b>CEEB:</b></td><td>".TextInput($schooldata['CEEB'],'values[CEEB]','','class=cell_floating maxlength=100 size=24')."</TD></TR>";

	if(AllowEdit() || !$schooldata['WWW_ADDRESS'])
		echo "<TR ALIGN=LEFT><TD class='label'><b>Website:</b></td><td>".TextInput($schooldata['WWW_ADDRESS'],'values[WWW_ADDRESS]','','class=cell_floating size=24')."</TD></TR>";
	else
		//echo "<TR ALIGN=LEFT><TD colspan=3><A HREF=http://$schooldata[WWW_ADDRESS] target=_blank>$schooldata[WWW_ADDRESS]</A><BR><small><FONT color=".Preferences('TITLES').">Website</FONT></small></TD></TR>";
		echo "<TR ALIGN=LEFT><TD class='label'><b>Website:</b></td><td><A HREF=http://$schooldata[WWW_ADDRESS] target=_blank>$schooldata[WWW_ADDRESS]</A></TD></TR>";

 //echo "<TR ALIGN=LEFT><TD class='label'><b>CEEB:</b></td><td>".TextInput($schooldata['CEEB'],'values[CEEB]','','class=cell_floating maxlength=100 size=24')."</TD></TR>";
        $uploaded_sql=DBGet(DBQuery("SELECT VALUE FROM PROGRAM_CONFIG WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR IS NULL AND TITLE='PATH'"));
        $_SESSION['logo_path']=$uploaded_sql[1]['VALUE'];
        if(!$_REQUEST['new_school'] && file_exists($uploaded_sql[1]['VALUE']))
 echo "<TR ALIGN=LEFT><TD class='label'><b>School Logo: </b></td><td><a href ='Modules.php?modname=School_Setup/UploadLogo.php&modfunc=edit'><img src='".$uploaded_sql[1]['VALUE']."' height=50 width=50 />Click Here To Change Logo</a></TD></TR>";
 else if(!$_REQUEST['new_school'])
 echo "<TR ALIGN=LEFT><TD class='label'><b>School Logo: </b></td><td><a href ='Modules.php?modname=School_Setup/UploadLogo.php'>Click Here To Upload Logo</a></TD></TR>";

#	echo "</TABLE></FIELDSET>";
	echo "</TABLE>";
     
	if(User('PROFILE')=='admin' && AllowEdit())
	{
	 	if($_REQUEST['new_school'])
		{
			DrawHeader('','',"<CENTER><INPUT TYPE=submit name=button id=button class=btn_medium VALUE='Save' onclick='return formcheck_school_setup_school();'></CENTER>");
		}
		else
		{
			#DrawHeader('','',"<INPUT TYPE=BUTTON name=button id=button class=btn_medium VALUE='Update' onclick='formcheck_school_setup_school();'>&nbsp;<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Delete'></CENTER>");
			DrawHeader('','',"<CENTER><INPUT TYPE=submit name=button id=button class=btn_medium VALUE='Update' onclick='return formcheck_school_setup_school();'></CENTER>");
		}
	}
	
	echo "</td></tr></table>";
	
	PopTable('footer');
/*	if(User('PROFILE')=='admin' && AllowEdit())
		echo "<CENTER><INPUT TYPE=SUBMIT name=button VALUE='Save'></CENTER>";	*/
	echo "</FORM>";
}

}

?>