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
DrawBC("Gradebook > ".ProgramTitle());

$sem = GetParentMP('SEM',UserMP());
$fy = GetParentMP('FY',$sem);
$pros = GetChildrenMP('PRO',UserMP());

// if the UserMP has been changed, the REQUESTed MP may not work
if(!$_REQUEST['mp'] || strpos($str="'".UserMP()."','".$sem."','".$fy."',".$pros,"'".ltrim($_REQUEST['mp'],'E')."'")===false)
	$_REQUEST['mp'] = UserMP();

$QI = DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER ");
$period_RET = DBGet($QI);

$period_select =  "<SELECT name=period onChange='this.form.submit();'><OPTION value=''>All</OPTION>";
foreach($period_RET as $period)
	$period_select .= "<OPTION value=$period[PERIOD_ID]".(($_REQUEST['period']==$period['PERIOD_ID'])?' SELECTED':'').">".$period['TITLE']."</OPTION>";
$period_select .= "</SELECT>";

$mp_select = "<SELECT name=mp onChange='this.form.submit();'>";
if($pros!='')
	foreach(explode(',',str_replace("'",'',$pros)) as $pro)
		if(GetMP($pro,'DOES_GRADES')=='Y')
			$mp_select .= "<OPTION value=".$pro.(($pro==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($pro)."</OPTION>";

$mp_select .= "<OPTION value=".UserMP().((UserMP()==$_REQUEST['mp'])?' SELECTED':'').">".GetMP(UserMP())."</OPTION>";

if(GetMP($sem,'DOES_GRADES')=='Y')
	$mp_select .= "<OPTION value=$sem".(($sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)."</OPTION>";
if(GetMP($sem,'DOES_EXAM')=='Y')
	$mp_select .= "<OPTION value=E$sem".(('E'.$sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)." Exam</OPTION>";

if(GetMP($fy,'DOES_GRADES')=='Y')
	$mp_select .= "<OPTION value=".$fy.(($fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)."</OPTION>";
if(GetMP($fy,'DOES_EXAM')=='Y')
	$mp_select .= "<OPTION value=E".$fy.(('E'.$fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)." Exam</OPTION>";
$mp_select .= '</SELECT>';

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeaderHome($mp_select.' - '.$period_select);
echo '</FORM>';

$sql = "SELECT s.STAFF_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,sp.TITLE,cp.PERIOD_ID
		FROM STAFF s,COURSE_PERIODS cp,SCHOOL_PERIODS sp, ATTENDANCE_CALENDAR acc
		WHERE
			sp.PERIOD_ID = cp.PERIOD_ID AND cp.GRADE_SCALE_ID IS NOT NULL
			AND cp.TEACHER_ID=s.STAFF_ID AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE )
			AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND s.PROFILE='teacher'
			".(($_REQUEST['period'])?" AND cp.PERIOD_ID='$_REQUEST[period]'":'')."
			AND NOT EXISTS (SELECT '' FROM GRADES_COMPLETED ac WHERE ac.STAFF_ID=cp.TEACHER_ID AND ac.MARKING_PERIOD_ID='$_REQUEST[mp]' AND ac.PERIOD_ID=sp.PERIOD_ID)
		";
		
		
$RET = DBGet(DBQuery($sql),array(),array('STAFF_ID','PERIOD_ID'));

if(count($RET))
{unset($i);
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

	foreach($period_RET as $period)
		$columns[$period['PERIOD_ID']] = $period['TITLE'];

echo '<div style=" width:800px; background-color:transparent; overflow-x:scroll; overflow-y:hidden;">';
ListOutput($staff_RET,$columns,'Teacher who hasn\'t entered grades','Teachers who haven\'t entered grades');
echo "</div>";
?>
