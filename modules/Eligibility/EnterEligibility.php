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
$start_end_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_CONFIG WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND PROGRAM='eligibility'"));
if(count($start_end_RET))
{
	foreach($start_end_RET as $value)
		$$value['TITLE'] = $value['VALUE'];
}

switch(date('D'))
{
	case 'Mon':
	$today = 1;
	break;
	case 'Tue':
	$today = 2;
	break;
	case 'Wed':
	$today = 3;
	break;
	case 'Thu':
	$today = 4;
	break;
	case 'Fri':
	$today = 5;
	break;
	case 'Sat':
	$today = 6;
	break;
	case 'Sun':
	$today = 7;
	break;
}

$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

if(strlen($START_MINUTE)==1)
	$START_MIN = '0'.$START_MINUTE;
if(strlen($END_MINUTE)==1)
	$END_MINUTE = '0'.$END_MINUTE;

$start_date = strtoupper(date('d-M-y',mktime()-($today-$START_DAY)*60*60*24));
$end_date = strtoupper(date('d-M-y',mktime()+($END_DAY-$today)*60*60*24));

$current_RET = DBGet(DBQuery("SELECT ELIGIBILITY_CODE,STUDENT_ID FROM ELIGIBILITY WHERE SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND COURSE_PERIOD_ID='".UserCoursePeriod()."'"),array(),array('STUDENT_ID'));

//if($_REQUEST['modfunc']=='gradebook')
if(optional_param('modfunc','',PARAM_NOTAGS)=='gradebook')
{
    
	$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
	if(count($config_RET))
		foreach($config_RET as $title=>$value)
			$programconfig[User('STAFF_ID')][$title] = $value[1]['VALUE'];
	else
		$programconfig[User('STAFF_ID')] = true;
	include_once 'functions/_makeLetterGrade.fnc.php';

	$course_period_id = UserCoursePeriod();
	$course_id = DBGet(DBQuery("SELECT COURSE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".UserCoursePeriod()."'"));
	$course_id = $course_id[1]['COURSE_ID'];

	$grades_RET = DBGet(DBQuery("SELECT ID,TITLE,GPA_VALUE FROM REPORT_CARD_GRADES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"),array(),array('ID'));

	if($programconfig[User('STAFF_ID')]['WEIGHT']=='Y')
		$points_RET = DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID,gt.ASSIGNMENT_TYPE_ID,     gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID".($programconfig[User('STAFF_ID')]['ELIGIBILITY_CUMULITIVE']=='Y'?" IN (".GetChildrenMP('SEM',UserMP()).")":"='".UserMP()."'").") LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
			else
		$points_RET = DBGet(DBQuery("SELECT s.STUDENT_ID,'-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID".($programconfig[User('STAFF_ID')]['ELIGIBILITY_CUMULITIVE']=='Y'?" IN (".GetChildrenMP('SEM',UserMP()).")":"='".UserMP()."'").") LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID)                               WHERE                                                                               ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE                                             "),array(),array('STUDENT_ID'));

	if(count($points_RET))
	{
		foreach($points_RET as $student_id=>$student)
		{
			$total = $total_percent = 0;
			foreach($student as $partial_points)
				if($partial_points['PARTIAL_TOTAL']!=0)
				{
					$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
					$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
                                }
			if($total_percent!=0)
				$total /= $total_percent;

			$grade = $grades_RET[_makeLetterGrade($total,0,0,'ID')][1];
			if($grade['GPA_VALUE']=='0' || !$grade['GPA_VALUE'])
				$code = 'FAILING';
			elseif(strpos($grade['TITLE'],'D')!==false || $grade['GPA_VALUE']<2)
				$code = 'BORDERLINE';
			else
				$code = 'PASSING';

			if($current_RET[$student_id])
				$sql = "UPDATE ELIGIBILITY SET ELIGIBILITY_CODE='".$code."' WHERE SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND COURSE_PERIOD_ID='".UserCoursePeriod()."' AND STUDENT_ID='".$student_id."'";
			else
				$sql = "INSERT INTO ELIGIBILITY (STUDENT_ID,SCHOOL_DATE,SYEAR,PERIOD_ID,COURSE_PERIOD_ID,ELIGIBILITY_CODE) values('$student_id','".DBDate()."','".UserSyear()."','".UserPeriod()."','".$course_period_id."','".$code."')";
			DBQuery($sql);
		}
		$current_RET = DBGet(DBQuery("SELECT ELIGIBILITY_CODE,STUDENT_ID FROM ELIGIBILITY WHERE SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND COURSE_PERIOD_ID='".UserCoursePeriod()."'"),array(),array('STUDENT_ID'));
	}

}
if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']))
{
	$course_period_id = UserCoursePeriod();
	foreach($_REQUEST['values'] as $student_id=>$value)
	{
		if($current_RET[$student_id])
			$sql = "UPDATE ELIGIBILITY SET ELIGIBILITY_CODE='".$value."' WHERE SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND PERIOD_ID='".UserPeriod()."' AND STUDENT_ID='".$student_id."'";
		else
			$sql = "INSERT INTO ELIGIBILITY (STUDENT_ID,SCHOOL_DATE,SYEAR,PERIOD_ID,COURSE_PERIOD_ID,ELIGIBILITY_CODE) values('$student_id','".DBDate()."','".UserSyear()."','".UserPeriod()."','".$course_period_id."','".$value."')";
		DBQuery($sql);
	}
	$RET = DBGet(DBQuery("SELECT 'completed' AS COMPLETED FROM ELIGIBILITY_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND PERIOD_ID='".UserPeriod()."'"));
	if(!count($RET))
		DBQuery("INSERT INTO ELIGIBILITY_COMPLETED (STAFF_ID,SCHOOL_DATE,PERIOD_ID) values('".User('STAFF_ID')."','".DBDate()."','".UserPeriod()."')");

	$current_RET = DBGet(DBQuery("SELECT ELIGIBILITY_CODE,STUDENT_ID FROM ELIGIBILITY WHERE SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND PERIOD_ID='".UserPeriod()."'"),array(),array('STUDENT_ID'));
}

$extra['SELECT'] .= ",'' AS PASSING,'' AS BORDERLINE,'' AS FAILING,'' AS INCOMPLETE";
$extra['functions'] = array('PASSING'=>'makeRadio','BORDERLINE'=>'makeRadio','FAILING'=>'makeRadio','INCOMPLETE'=>'makeRadio');
$columns = array('PASSING'=>'Passing','BORDERLINE'=>'Borderline','FAILING'=>'Failing','INCOMPLETE'=>'Incomplete');

$stu_RET = GetStuList($extra);

echo "<FORM ACTION=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeaderHome(ProgramTitle());

if($today>$END_DAY || $today<$START_DAY || ($today==$START_DAY && date('Gi')<($START_HOUR.$START_MINUTE)) || ($today==$END_DAY && date('Gi')>($END_HOUR.$END_MINUTE)))
{
	if($START_HOUR>12)
	{
		$START_HOUR-=12;
		$START_M = 'PM';
	}
	else
		$START_M = 'AM';

	if($END_HOUR>12)
	{
		$END_HOUR-=12;
		$END_M = 'PM';
	}
	else
		$END_M = 'AM';

	echo ErrorMessage(array('You can only enter eligibility from '.$days[$START_DAY].' '.$START_HOUR.':'.$START_MINUTE.' '.$START_M.' to '.$days[$END_DAY].' '.$END_HOUR.':'.$END_MINUTE.' '.$END_M),'error');
}
else
{
    if(count($stu_RET) !=0)
	DrawHeader("<A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=gradebook>Use Gradebook Grades</A>",'<INPUT type=submit class=btn_medium value=Save>');
        

	$LO_columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','GRADE_ID'=>'Grade') + $columns;
	ListOutput($stu_RET,$LO_columns,'Student','Students');
if(count($stu_RET) !=0)	
        echo '<br><CENTER><INPUT type=submit class=btn_medium value=Save></CENTER>';
}
echo "</FORM>";

function makeRadio($value,$title)
{	global $THIS_RET,$current_RET;

	if((isset($current_RET[$THIS_RET['STUDENT_ID']][1]['ELIGIBILITY_CODE']) && $current_RET[$THIS_RET['STUDENT_ID']][1]['ELIGIBILITY_CODE']==$title) || ($title=='PASSING' && !$current_RET[$THIS_RET['STUDENT_ID']][1]['ELIGIBILITY_CODE']))
		return "<INPUT type=radio name=values[".$THIS_RET['STUDENT_ID']."] value='$title' CHECKED>";
	else
		return "<INPUT type=radio name=values[".$THIS_RET['STUDENT_ID']."] value='$title'>";
}

?>
