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
if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
else
{
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = strtoupper(date('M'));
	$_REQUEST['year_date'] = date('y');
	$date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date'];
}

DrawBC("Attendance > ".ProgramTitle());

//$QI = DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER ");
$QI = DBQuery("SELECT sp.PERIOD_ID,sp.TITLE FROM SCHOOL_PERIODS sp WHERE sp.SCHOOL_ID='".UserSchool()."' AND sp.SYEAR='".UserSyear()."' AND EXISTS (SELECT '' FROM COURSE_PERIODS WHERE SYEAR=sp.SYEAR AND PERIOD_ID=sp.PERIOD_ID AND DOES_ATTENDANCE='Y') ORDER BY sp.SORT_ORDER");
$periods_RET = DBGet($QI,array(),array('PERIOD_ID'));

$period_select =  "<SELECT name=period><OPTION value=''>All</OPTION>";
foreach($periods_RET as $id=>$period)
	$period_select .= "<OPTION value=$id".(($_REQUEST['period']==$id)?' SELECTED':'').">".$period[1]['TITLE']."</OPTION>";
$period_select .= "</SELECT>";

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeaderHome('<table><tr><td>'.PrepareDateSchedule($date,'_date',false,array('submit'=>true)).'</td><td> - </td><td>'.$period_select.'</td><td> : <INPUT type=submit class=btn_medium value=Go></td></tr></table>');
echo '</FORM>';

$day = date('D',strtotime($date));
switch($day)
{
	case 'Sun':
		$day = 'U';
	break;
	case 'Thu':
		$day = 'H';
	break;
	default:
		$day = substr($day,0,1);
	break;
}

/*$sql = "SELECT concat(s.LAST_NAME,',',s.FIRST_NAME, ' ') AS FULL_NAME,sp.TITLE,cp.PERIOD_ID,s.STAFF_ID
		FROM STAFF s,COURSE_PERIODS cp,SCHOOL_PERIODS sp
		WHERE
			sp.PERIOD_ID = cp.PERIOD_ID
			AND cp.TEACHER_ID=s.STAFF_ID AND cp.MARKING_PERIOD_ID IN (".GetAllMP('QTR',GetCurrentMP('QTR',$date)).")
			AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND s.PROFILE='teacher'
			AND cp.DOES_ATTENDANCE='Y' AND instr(cp.DAYS,'$day')>0".(($_REQUEST['period'])?" AND cp.PERIOD_ID='$_REQUEST[period]'":'')."
			AND NOT EXISTS (SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.STAFF_ID=cp.TEACHER_ID AND ac.SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND ac.PERIOD_ID=sp.PERIOD_ID)
		";*/
$p=optional_param('period','',PARAM_SPCL);
$current_mp = GetCurrentMP('QTR',$date);
$MP_TYPE='QTR';
if(!$current_mp){
    $current_mp = GetCurrentMP('SEM',$date);
    $MP_TYPE='SEM';
}
if(!$current_mp){
    $current_mp = GetCurrentMP('FY',$date);
    $MP_TYPE='FY';
}
$sql = "SELECT concat(s.LAST_NAME,',',s.FIRST_NAME, ' ') AS FULL_NAME,sp.TITLE,cp.PERIOD_ID,s.STAFF_ID
		FROM STAFF s,COURSE_PERIODS cp,SCHOOL_PERIODS sp
		WHERE
			sp.PERIOD_ID = cp.PERIOD_ID
			AND cp.TEACHER_ID=s.STAFF_ID AND cp.MARKING_PERIOD_ID IN (".GetAllMP($MP_TYPE,$current_mp).")
			AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND s.PROFILE='teacher'
			AND cp.DOES_ATTENDANCE='Y' AND instr(cp.DAYS,'$day')>0".(($p)?" AND cp.PERIOD_ID='$p'":'')."
			AND NOT EXISTS (SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.STAFF_ID=cp.TEACHER_ID AND ac.SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND ac.PERIOD_ID=sp.PERIOD_ID)
		";



$RET = DBGet(DBQuery($sql),array(),array('STAFF_ID','PERIOD_ID'));
$i= 0;
if(count($RET))
{
	foreach($RET as $staff_id=>$periods)
	{
		$i++;
		$staff_RET[$i]['FULL_NAME'] = $periods[key($periods)][1]['FULL_NAME'];
		foreach($periods as $period_id=>$period)
		{
			$staff_RET[$i][$period_id] = '<IMG SRC=assets/x.gif>';
		}
	}
}

$columns = array('FULL_NAME'=>'Teacher');
if(!$_REQUEST['period'])
{
	foreach($periods_RET as $id=>$period)
		$columns[$id] = $period[1]['TITLE'];
}
else
	$period_title = $periods_RET[$_REQUEST['period']][1]['TITLE'].' ';
echo '<div style="overflow:auto; overflow-x:auto; overflow-y:auto; height:400px; width:830px;">';
ListOutput($staff_RET,$columns,'Teacher who hasn\'t taken '.$period_title.'attendance','Teachers who haven\'t taken '.$period_title.'attendance');
echo '</div>';
?>
