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
if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['cp_arr']))
	{
	$cp_list = '\''.implode('\',\'',$_REQUEST['cp_arr']).'\'';

	#$extra['DATE'] = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS START_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	#$extra['DATE'] = $extra['DATE'][1]['START_DATE'];
	#if(!$extra['DATE'] || DBDate()>$extra['DATE'])
	$extra['DATE'] = GetMP();

	// get the fy marking period id, there should be exactly one fy marking period
	$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	$course_periods_RET = DBGet(DBQuery("SELECT cp.TITLE,cp.COURSE_PERIOD_ID,cp.PERIOD_ID,cp.MARKING_PERIOD_ID,cp.DAYS,c.TITLE AS COURSE_TITLE,cp.TEACHER_ID,(SELECT CONCAT(LAST_NAME,', ',FIRST_NAME) FROM STAFF WHERE STAFF_ID=cp.TEACHER_ID) AS TEACHER FROM COURSE_PERIODS cp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID IN ($cp_list) ORDER BY TEACHER"));

	$first_extra = $extra;
	$handle = PDFStart();
	$PCL_UserCoursePeriod = $_SESSION['UserCoursePeriod']; // save/restore for teachers
	foreach($course_periods_RET as $teacher_id=>$course_period)
	{
		unset($_openSIS['DrawHeader']);
		//DrawHeader(Config('TITLE').' Class List');
		//DrawHeader($course_period['TEACHER'],$course_period['COURSE_TITLE'].' '.GetPeriod($course_period['PERIOD_ID']).($course_period['MARKING_PERIOD_ID']!="$fy_id"?' - '.GetMP($course_period['MARKING_PERIOD_ID']):'').(strlen($course_period['DAYS'])<5?' - '.$course_period['DAYS']:''));
		//DrawHeader(GetSchool(UserSchool()),ProperDate(DBDate()));

		$_openSIS['User'] = array(1=>array('STAFF_ID'=>$course_period['TEACHER_ID'],'NAME'=>'name','PROFILE'=>'teacher','SCHOOLS'=>','.UserSchool().',','SYEAR'=>UserSyear()));
		$_SESSION['UserCoursePeriod'] = $course_period['COURSE_PERIOD_ID'];
		echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
		echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Teacher Class List</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
		echo "<table >";
		echo '<table border=0>';
		echo '<tr><td>Teacher Name:</td>';
		echo '<td>'.$course_period['TEACHER'].'</td></tr>';
		echo '<tr><td>Course Program Name:</td>';
		echo '<td>'.$course_period['COURSE_TITLE'].'</td></tr>';
		echo '<tr><td>Course Period Name:</td>';
		echo '<td>'.GetPeriod($course_period['PERIOD_ID']).'</td></tr>';
		echo '<tr><td>Marking Period:</td>';
		echo '<td>'.GetMP($course_period['MARKING_PERIOD_ID']).'</td></tr>';
		
		echo '</table>';
		$extra = $first_extra;
		$extra['MP'] = $course_period['MARKING_PERIOD_ID'];
		
		
		include('modules/misc/Export.php');


		echo "<div style=\"page-break-before: always;\"></div>";
	}
	$_SESSION['UserCoursePeriod'] = $PCL_UserCoursePeriod;
	PDFStop($handle);
	}
	else
		BackPrompt('You must choose at least one course period.');
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Scheduling > ".ProgramTitle());

	if(User('PROFILE')!='admin')
		$_REQUEST['search_modfunc'] = 'list';

	if($_REQUEST['search_modfunc']=='list' || $_REQUEST['search_modfunc']=='select')
	{
		$_REQUEST['search_modfunc'] = 'select';
		#$extra['header_right'] = '<INPUT type=submit value=\'Create Class Lists for Selected Course Periods\'>';

		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=include_inactive value=Y>Include Inactive Students</TD></TR>';
		$extra['extra_header_left'] .= '</TABLE>';

		$Search = 'mySearch';
		include('modules/misc/Export.php');
	}
	else
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname] method=POST>";
		echo '<BR>';
		PopTable('header','Search');
		echo '<TABLE border=0>';

		$RET = DBGet(DBQuery("SELECT STAFF_ID,CONCAT(LAST_NAME,', ',FIRST_NAME) AS FULL_NAME FROM STAFF WHERE PROFILE='teacher' AND FIND_IN_SET('".UserSchool()."',SCHOOLS)>0 AND SYEAR='".UserSyear()."' ORDER BY FULL_NAME"));
		echo '<TR><TD align=right>Teacher</TD><TD>';
		echo "<SELECT name=teacher_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $teacher)
			echo "<OPTION value=$teacher[STAFF_ID]>$teacher[FULL_NAME]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';

		$RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY TITLE"));
		echo '<TR><TD align=right>Subject</TD><TD>';
		echo "<SELECT name=subject_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $subject)
			echo "<OPTION value=$subject[SUBJECT_ID]>$subject[TITLE]</OPTION>";
		echo '</SELECT>';

		$RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		echo '<TR><TD align=right>Period</TD><TD>';
		echo "<SELECT name=period_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $period)
			echo "<OPTION value=$period[PERIOD_ID]>$period[TITLE]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';

		Widgets('course');

		echo '<TR><TD colspan=2 align=center>';
		echo '<BR>';
		echo Buttons('Submit','Reset');
		echo '</TD></TR>';
		echo '</TABLE>';
		echo '</FORM>';
		PopTable('footer');
	}
}

function mySearch($extra)
{
//	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&search_modfunc=list&_openSIS_PDF=true onsubmit=document.forms[0].relation.value=document.getElementById(\"relation\").value; method=POST>";
//	echo "<FORM name=exp id=exp action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&search_modfunc=list&_openSIS_PDF=true onsubmit=document.forms[0].relation.value=document.getElementById(\"relation\").value; method=POST target=_blank>";
	echo "<FORM name=exp id=exp action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&search_modfunc=list&_openSIS_PDF=true onsubmit=document.forms[0].relation.value=document.getElementById(\"relation\").value; method=POST target=_blank>";
	echo '<DIV id=fields_div></DIV>';
	DrawHeader('',$extra['header_right']);
	DrawHeader($extra['extra_header_left'],$extra['extra_header_right']);

	if(User('PROFILE')=='admin')
	{
		if($_REQUEST['teacher_id'])
			$where .= " AND cp.TEACHER_ID='$_REQUEST[teacher_id]'";
		if($_REQUEST['first'])
			$where .= " AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%'";
		if($_REQUEST['w_course_period_id'] && $_REQUEST['w_course_period_id_which']!='course')
			$where .= " AND cp.COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."'";
		if($_REQUEST['subject_id'])
		{
			$from .= ",COURSES c";
			$where .= " AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID='".$_REQUEST['subject_id']."'";
		}
		if($_REQUEST['period_id'])
			$where .= " AND cp.PERIOD_ID='".$_REQUEST['period_id']."'";
			$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE FROM COURSE_PERIODS cp$from WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.SYEAR='".UserSyear()."'$where";
	}
	else // teacher
	{
		$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE FROM COURSE_PERIODS cp WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."'";
	}
	$sql .= ' ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)';

	$course_periods_RET = DBGet(DBQuery($sql),array('COURSE_PERIOD_ID'=>'_makeChooseCheckbox'));
	$LO_columns = array('COURSE_PERIOD_ID'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'cp_arr\');"><A>','TITLE'=>'Course Period');

	echo '<INPUT type=hidden name=relation>';
	ListOutput($course_periods_RET,$LO_columns,'Course Period','Course Periods');
//	echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Class Lists for Selected Course Periods\' onclick=\'formload_ajax("exp");\'></CENTER>';
	if(count($course_periods_RET)!=0)
	echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Class Lists for Selected Course Periods\'></CENTER>';
	echo "</FORM>";
}

function _makeChooseCheckbox($value,$title)
{
	return "<INPUT type=checkbox name=cp_arr[] value=$value checked>";
}
?>