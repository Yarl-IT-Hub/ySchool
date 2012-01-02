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
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='save')
{
	if($_SESSION['MassRequests.php'])
	{
		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE_REQUESTS WHERE COURSE_ID='".clean_param($_SESSION['MassRequests.php']['course_id'],PARAM_INT)."' AND SYEAR='".UserSyear()."'"),array(),array('STUDENT_ID'));
	$mp_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$mp_id = $mp_id[1]['MARKING_PERIOD_ID'];
	$get_teacher = DBGet(DBQuery("SELECT TEACHER_ID FROM COURSE_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND COURSE_ID='".clean_param($_SESSION['MassRequests.php']['course_id'],PARAM_INT)."' AND TEACHER_ID='".clean_param($_REQUEST['with_teacher_id'],PARAM_INT)."'"));
	$get_teacher = $get_teacher[1]['TEACHER_ID'];
	$get_period = DBGet(DBQuery("SELECT PERIOD_ID FROM COURSE_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND COURSE_ID='".clean_param($_SESSION['MassRequests.php']['course_id'],PARAM_INT)."' AND PERIOD_ID='".clean_param($_REQUEST['with_period_id'],PARAM_INT)."'"));
	$get_period = $get_period[1]['PERIOD_ID'];
	if($get_teacher)
	{
		
		# ------------------  Without Period Selection Request Entry Start  ------------------------------------ #
		
		
			foreach($_REQUEST['student'] as $student_id=>$yes)
			{
       
				$check_dup = DBGet(DBQuery("SELECT COUNT(STUDENT_ID) AS DUPLICATE FROM SCHEDULE_REQUESTS WHERE COURSE_ID='".clean_param($_SESSION['MassRequests.php']['course_id'],PARAM_INT)."' AND SYEAR='".UserSyear()."' AND STUDENT_ID='".clean_param($student_id,PARAM_INT)."' AND WITH_TEACHER_ID='".clean_param($_REQUEST['with_teacher_id'],PARAM_INT)."' AND WITH_PERIOD_ID='".clean_param($_REQUEST['with_period_id'],PARAM_INT)."'"));
				$check_dup = $check_dup[1]['DUPLICATE'];
				if($check_dup<1)
				{
					if($current_RET[$student_id]!=$student_id)
					{
						$sql = "INSERT INTO SCHEDULE_REQUESTS (SYEAR,SCHOOL_ID,STUDENT_ID,SUBJECT_ID,COURSE_ID,MARKING_PERIOD_ID,WITH_TEACHER_ID,NOT_TEACHER_ID,WITH_PERIOD_ID,NOT_PERIOD_ID)
									values('".UserSyear()."','".UserSchool()."','".$student_id."','".$_SESSION['MassRequests.php']['subject_id']."','".$_SESSION['MassRequests.php']['course_id']."','".UserMP()."','".$_REQUEST['with_teacher_id']."','".$_REQUEST['without_teacher_id']."','".$_REQUEST['with_period_id']."','".$_REQUEST['without_period_id']."')";
						DBQuery($sql);
					}
				}
				else
				{
					$duplicate = "<span class=red>Duplicate Entry.Request already exists</span>";
					unset($_REQUEST['modfunc']);
				}
			}
			if(!$duplicate)
			{
				unset($_REQUEST['modfunc']);
				$note = "That course has been added as a request for the selected students.";
			}
		
		
		# -------------------  Without Period Selection Request Entry End  ------------------------------------- #
		
		/*
		
		if($get_period)
		{
			foreach($_REQUEST['student'] as $student_id=>$yes)
			{
				$check_dup = DBGet(DBQuery("SELECT COUNT(STUDENT_ID) AS DUPLICATE FROM SCHEDULE_REQUESTS WHERE COURSE_ID='".$_SESSION['MassRequests.php']['course_id']."' AND SYEAR='".UserSyear()."' AND STUDENT_ID='".$student_id."' AND WITH_TEACHER_ID='".$_REQUEST['with_teacher_id']."' AND WITH_PERIOD_ID='".$_REQUEST['with_period_id']."'"));
				$check_dup = $check_dup[1]['DUPLICATE'];
				if($check_dup<1)
				{
					if($current_RET[$student_id]!=$student_id)
					{
						$sql = "INSERT INTO SCHEDULE_REQUESTS (SYEAR,SCHOOL_ID,STUDENT_ID,SUBJECT_ID,COURSE_ID,MARKING_PERIOD_ID,WITH_TEACHER_ID,NOT_TEACHER_ID,WITH_PERIOD_ID,NOT_PERIOD_ID)
									values('".UserSyear()."','".UserSchool()."','".$student_id."','".$_SESSION['MassRequests.php']['subject_id']."','".$_SESSION['MassRequests.php']['course_id']."','".$mp_id."','".$_REQUEST['with_teacher_id']."','".$_REQUEST['without_teacher_id']."','".$_REQUEST['with_period_id']."','".$_REQUEST['without_period_id']."')";
						DBQuery($sql);
					}
				}
				else
				{
					$duplicate = "<span class=red>Duplicate Entry.Request already exists</span>";
					unset($_REQUEST['modfunc']);
				}
			}
			if(!$duplicate)
			{
				unset($_REQUEST['modfunc']);
				$note = "That course has been added as a request for the selected students.";
			}
		}
		else
		{
			$period_error = "<span class=red>Wrong Period Selection</span>";
			unset($_REQUEST['modfunc']);
		}
		
		*/

	}
	else
	{
		$teacher_error = "<span class=red>Wrong Teacher Selection</span>";
		unset($_REQUEST['modfunc']);
	}
	}
	else
	{
	//	BackPrompt('You must choose a Course');
	ShowErr('You must choose a Course');
	for_error();
	}
}


if($_REQUEST['modfunc']!='choose_course')
{
	DrawBC("Scheduling > ".ProgramTitle());
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM name=qq id=qq action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		#DrawHeader('',SubmitButton('Add Request to Selected Students'));
		PopTable_wo_header ('header');
		echo '<TABLE><TR><TD align=right>Request to Add</TD><TD><DIV id=course_div>';
		if($_SESSION['MassRequests.php'])
		{
			$course_title = DBGet(DBQuery("SELECT TITLE,COURSE_ID FROM COURSES WHERE COURSE_ID='".$_SESSION['MassRequests.php']['course_id']."'"));
			$course_title = $course_title[1]['TITLE'];
			
		}
	//	echo '</DIV>'."<A HREF=# onclick='window.open(\"Modules.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
	//	echo '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
	
	
	//course ok//	echo '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=Scheduling/MassSchedule.php&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
		echo '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
	#	$extra['search'] .= "<TR><TD align=right width=120>Request</TD><TD><DIV id=request_div></DIV> <A HREF=# onclick='window.open(\"for_window.php?modname=misc/ChooseRequest.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><SMALL>Choose</SMALL></A></TD></TR>";		
		echo '<TR><TD align=right valign=top>With</TD><TD>';
		echo '<DIV id=WITH_TEACHER_PERIOD ><TABLE><TR><TD align=right>Teacher</TD><TD><SELECT name=with_teacher_id><OPTION value="">N/A</OPTION>';
		
		//$teachers_RET = DBGet(DBQuery("SELECT DISTINCT s.FIRST_NAME,s.LAST_NAME,s.STAFF_ID AS TEACHER_ID FROM STAFF s,COURSE_PERIODS cp WHERE s.STAFF_ID=cp.TEACHER_ID "));
		
		$teachers_RET = DBGet(DBQuery("SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM STAFF WHERE SCHOOLS LIKE '%,".UserSchool().",%' AND SYEAR='".UserSyear()."' AND PROFILE='teacher' ORDER BY LAST_NAME,FIRST_NAME"));
		foreach($teachers_RET as $teacher)
		echo '<OPTION value='.$teacher['STAFF_ID'].'>'.$teacher['LAST_NAME'].', '.$teacher['FIRST_NAME'].' '.$teacher['MIDDLE_NAME'].'</OPTION>';
		echo '</SELECT></TD></TR><TR><TD align=right>Period</TD><TD><SELECT name=with_period_id><OPTION value="">N/A</OPTION>';
		$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
		foreach($periods_RET as $period)
		echo '<OPTION value='.$period['PERIOD_ID'].'>'.$period['TITLE'].'</OPTION>';
		echo '</SELECT></TD></TR></TABLE></DIV>';
		echo '</TD></TR>';
		
		echo '<TR><TD align=right valign=top>Without</TD><TD>';
		echo '<BR><DIV ID=WITHOUT_TEACHER_PERIOD><TABLE><TR><TD align=right>Teacher</TD><TD><SELECT name=without_teacher_id><OPTION value="">N/A</OPTION>';
		foreach($teachers_RET as $teacher)
			echo '<OPTION value='.$teacher['STAFF_ID'].'>'.$teacher['LAST_NAME'].', '.$teacher['FIRST_NAME'].' '.$teacher['MIDDLE_NAME'].'</OPTION>';
		echo '</SELECT></TD></TR><TR><TD align=right>Period</TD><TD><SELECT name=without_period_id><OPTION value="">N/A</OPTION>';
		foreach($periods_RET as $period)
			echo '<OPTION value='.$period['PERIOD_ID'].'>'.$period['TITLE'].'</OPTION>';
		echo '</SELECT></TD></TR></TABLE></DIV>';
		echo '</TD></TR>';
		echo '</TABLE>';
		PopTable ('footer');
	}
	if($note)
		DrawHeaderHome('<table><tr><td><IMG SRC=assets/check.gif></td><td>'.$note.'</td></tr></table>');
	if($teacher_error)
		DrawHeaderHome('<table><tr><td><IMG SRC=assets/warning_button.gif></td><td>'.$teacher_error.'</td></tr></table>');
	if($period_error)
		DrawHeaderHome('<table><tr><td><IMG SRC=assets/warning_button.gif></td><td>'.$period_error.'</td></tr></table>');
	if($duplicate)
		DrawHeaderHome('<table><tr><td><IMG SRC=assets/warning_button.gif></td><td>'.$duplicate.'</td></tr></table>');
		
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']!='list')
		unset($_SESSION['MassRequests.php']);
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",CAST(NULL AS CHAR(1)) AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Widgets('request');
	Widgets('activity');
	
	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list'){
	if($_SESSION['count_stu']!=0)	
                  echo '<BR><CENTER>'.SubmitButton('','','class=btn_group_requests onclick=\'formload_ajax("qq");\'')."</CENTER>";
        echo '</FORM>';
            }

}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAEXT)=='choose_course')
{
//	if($_REQUEST['course_id'])
//	{
//		$weights_RET = DBGet(DBQuery("SELECT COURSE_WEIGHT,GPA_MULTIPLIER FROM COURSE_WEIGHTS WHERE COURSE_ID='$_REQUEST[course_id]'"));
//		if(count($weights_RET)==1)
//			$_REQUEST['course_weight'] = $weights_RET[1]['COURSE_WEIGHT'];
//	}

	if(!$_REQUEST['course_id'])
		include 'modules/Scheduling/CoursesforWindow.php';
	else
	{
		$_SESSION['MassRequests.php']['subject_id'] = clean_param($_REQUEST['subject_id'],PARAM_INT);
		$_SESSION['MassRequests.php']['course_id'] = clean_param($_REQUEST['course_id'],PARAM_INT);
		//$_SESSION['MassRequests.php']['course_weight'] = $_REQUEST['course_weight'];
		
		$course_title = DBGet(DBQuery("SELECT TITLE,COURSE_ID FROM COURSES WHERE COURSE_ID='".$_SESSION['MassRequests.php']['course_id']."'"));
		$course_title = $course_title[1]['TITLE'];
		$c=$_SESSION['MassRequests.php']['course_id'];
		
		//***WITH TEACHER_PERIOD*************************************************************
	    $tp_html='';
		$tp_html.='<TABLE><TR><TD align=right>Teacher</TD><TD><SELECT name=with_teacher_id><OPTION>N/A</OPTION>';
		$corr_teachers = DBGet(DBQuery("SELECT Distinct s.FIRST_NAME,s.LAST_NAME,s.STAFF_ID AS TEACHER_ID FROM STAFF s,COURSE_PERIODS cp WHERE s.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_ID='".$c."'"));
		foreach($corr_teachers as $teacher)
		{
		$tp_html.= '<OPTION value='.$teacher['TEACHER_ID'].'>'.$teacher['LAST_NAME'].', '.$teacher['FIRST_NAME'].'</OPTION>';
		}
		$tp_html.= '</SELECT></TD></TR><TR><TD align=right>Period</TD><TD><SELECT name=with_period_id><OPTION>N/A</OPTION>';
		$corr_periods = DBGet(DBQuery("SELECT Distinct p.TITLE,p.PERIOD_ID FROM SCHOOL_PERIODS p,COURSE_PERIODS cp WHERE p.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_ID='".$c."'"));
		foreach($corr_periods as $period)
		{
		$tp_html.= '<OPTION value='.$period['PERIOD_ID'].'>'.$period['TITLE'].'</OPTION>';
		}
		$tp_html.= '</SELECT></TD></TR></TABLE>';
		
		
		//***WITH TEACHER_PERIOD**********************************************************
		//***WITHOUT TEACHER_PERIOD*******************************************************
		$tp_html_w='';
		$tp_html_w.='<BR><TABLE><TR><TD align=right>Teacher</TD><TD><SELECT name=without_teacher_id><OPTION>N/A</OPTION>';
		$corr_teachers = DBGet(DBQuery("SELECT Distinct s.FIRST_NAME,s.LAST_NAME,s.STAFF_ID AS TEACHER_ID FROM STAFF s,COURSE_PERIODS cp WHERE s.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_ID='".$c."'"));
		foreach($corr_teachers as $teacher)
		{
		$tp_html_w.= '<OPTION value='.$teacher['TEACHER_ID'].'>'.$teacher['LAST_NAME'].', '.$teacher['FIRST_NAME'].'</OPTION>';
		}
		$tp_html_w.= '</SELECT></TD></TR><TR><TD align=right>Period</TD><TD><SELECT name=without_period_id><OPTION>N/A</OPTION>';
		$corr_periods = DBGet(DBQuery("SELECT Distinct p.TITLE,p.PERIOD_ID FROM SCHOOL_PERIODS p,COURSE_PERIODS cp WHERE p.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_ID='".$c."'"));
		foreach($corr_periods as $period)
		{
		$tp_html_w.= '<OPTION value='.$period['PERIOD_ID'].'>'.$period['TITLE'].'</OPTION>';
		}
		$tp_html_w.= '</SELECT></TD></TR></TABLE>';
		
		//***WITHOUT TEACHER_PERIOD*******************************************************
        echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title\";
opener.document.getElementById(\"WITH_TEACHER_PERIOD\").innerHTML = \"$tp_html\"; 
opener.document.getElementById(\"WITHOUT_TEACHER_PERIOD\").innerHTML = \"$tp_html_w\";
window.close();
</script>";
       
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>