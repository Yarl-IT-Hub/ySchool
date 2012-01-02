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
$QI = DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER ");
$periods_RET = DBGet($QI);

/*
$period_select =  "<SELECT name=period><OPTION value=''>All</OPTION>";
foreach($periods_RET as $period)
	$period_select .= "<OPTION value=$period[PERIOD_ID]".(($_REQUEST['period']==$period['PERIOD_ID'])?' SELECTED':'').">".$period['TITLE']."</OPTION>";
$period_select .= "</SELECT>";
*/

DrawBC("Scheduling > ".ProgramTitle());
echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeader($period_select);
echo '</FORM>';

if($_REQUEST['search_modfunc']=='list')
{
	
    $mp = GetAllMP('QTR',UserMP());

    if(!isset($mp))
      $mp = GetAllMP('SEM',UserMP());

    if(!isset($mp))
      $mp = GetAllMP('FY',UserMP());
    
    
        Widgets('course');
	Widgets('request');
	$extra['SELECT'] .= ',sp.PERIOD_ID';
	$extra['FROM'] .= ',SCHOOL_PERIODS sp,SCHEDULE ss,COURSE_PERIODS cp';
	$extra['WHERE'] .= ' AND (\''.DBDate().'\' BETWEEN ss.START_DATE AND ss.END_DATE OR ss.END_DATE IS NULL) AND ss.SCHOOL_ID=ssm.SCHOOL_ID AND ss.MARKING_PERIOD_ID IN ('.$mp.') AND ss.STUDENT_ID=ssm.STUDENT_ID AND ss.SYEAR=ssm.SYEAR AND ss.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.PERIOD_ID=sp.PERIOD_ID ';
	if(UserStudentID())
		$extra['WHERE'] .= " AND s.STUDENT_ID='".UserStudentID()."' ";
	$extra['group'] = array('STUDENT_ID','PERIOD_ID');
	
	$schedule_RET = GetStuList($extra);
}
unset($extra);
$extra['force_search'] = true;
$extra['new'] = true;
Widgets('course');	
Widgets('request');

foreach($periods_RET as $period)
{
	$extra['SELECT'] .= ',NULL AS PERIOD_'.$period['PERIOD_ID'];
	$extra['columns_after']['PERIOD_'.$period['PERIOD_ID']] = $period['TITLE'];
	$extra['functions']['PERIOD_'.$period['PERIOD_ID']] = '_preparePeriods';
}
if(!$_REQUEST['search_modfunc'])
	Search('student_id',$extra);
else
{
	$singular = 'Student with an incomplete schedule';
	$plural = 'Students with incomplete schedules';

	$students_RET = GetStuList($extra);
	$bad_students[0] = array();
	foreach($students_RET as $student)
	{
		if(count($schedule_RET[$student['STUDENT_ID']])!=count($periods_RET))
			$bad_students[] = $student;
	}
	if(!is_array($extra['columns_after']))
		$extra['columns_after'] = array();
	unset($bad_students[0]);
	//$link['FULL_NAME']['link'] = "Modules.php?modname=Scheduling/Schedule.php";
	#$link['FULL_NAME']['link'] = "#"." onclick='check_content(\"ajax.php?modname=Scheduling/Schedule.php\");'";
	$link['FULL_NAME']['link'] = "Modules.php?modname=Scheduling/Schedule.php";
	$link['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');
	echo '<div style=" width:800px; height:400px; padding:0px 12px 0px 12px; background-color:transparent; overflow-x:scroll; overflow-y:auto;">';
	ListOutput($bad_students,array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','GRADE_ID'=>'Grade')+$extra['columns_after'],$singular,$plural,$link);
	echo "</div>";
}
function _preparePeriods($value,$name)
{	global $THIS_RET,$schedule_RET;

	$period_id = substr($name,7);
	if(!$schedule_RET[$THIS_RET['STUDENT_ID']][$period_id])
		return '<IMG SRC=assets/x.gif>';
	else
		return '';
}
?>