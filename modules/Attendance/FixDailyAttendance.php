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
DrawBC("Attendance > ".ProgramTitle());

$message = '<TABLE><TR><TD colspan=7 align=center>From '.PrepareDate(DBDate(),'_min').' to '.PrepareDate(DBDate(),'_max').'</TD></TR></TABLE>';
if(Prompt_Home('Confirm','When do you want to recalculate the daily attendance?',$message))
{
	$current_RET = DBGet(DBQuery("SELECT DISTINCT DATE_FORMAT(SCHOOL_DATE,'%d-%m-%Y') as SCHOOL_DATE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"),array(),array('SCHOOL_DATE'));
	$students_RET = GetStuList();

	$begin = mktime(0,0,0,MonthNWSwitch($_REQUEST['month_min'],'to_num'),$_REQUEST['day_min']*1,$_REQUEST['year_min']) + 43200;
	$end = mktime(0,0,0,MonthNWSwitch($_REQUEST['month_max'],'to_num'),$_REQUEST['day_max']*1,$_REQUEST['year_max']) + 43200;

	for($i=$begin;$i<=$end;$i+=86400)
	{
		if($current_RET[strtoupper(date('d-M-y',$i))])
		{
			foreach($students_RET as $student)
			{
				UpdateAttendanceDaily($student['STUDENT_ID'],date('d-M-y',$i));
			}
		}
	}
	
	unset($_REQUEST['modfunc']);
	DrawHeader('<table><tr><td><IMG SRC=assets/check.gif></td><td>The Daily Attendance for that timeframe has been recalculated.</td></tr></table>');
}

?>