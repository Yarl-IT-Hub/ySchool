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
echo "<FORM name=add id=add action=".PreparePHP_SELF()." method=POST>";
DrawBC("Students > ".ProgramTitle());

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	while(!VerifyDate($start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start']))
		$_REQUEST['day_start']--;
}
else
	$start_date = '01-'.strtoupper(date('M-y'));

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	while(!VerifyDate($end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end']))
		$_REQUEST['day_end']--;
}
else
	$end_date = DBDate();

DrawHeaderHome('<table><tr><td>'.PrepareDateSchedule($start_date,'_start').'</td><td> - </td><td>'.PrepareDateSchedule($end_date,'_end'),'</td><td><INPUT type=submit class=btn_medium value=Go></td></tr></table>');
echo '</FORM>';

$enrollment_RET = DBGet(DBQuery("SELECT se.START_DATE AS START_DATE,NULL AS END_DATE,se.START_DATE AS DATE,se.SCHOOL_ID,se.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM STUDENT_ENROLLMENT se,STUDENTS s WHERE s.STUDENT_ID=se.STUDENT_ID AND se.START_DATE BETWEEN '$start_date' AND '$end_date' 
								UNION SELECT NULL AS START_DATE,se.END_DATE AS END_DATE,se.END_DATE AS DATE,se.SCHOOL_ID,se.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM STUDENT_ENROLLMENT se,STUDENTS s WHERE s.STUDENT_ID=se.STUDENT_ID AND se.END_DATE BETWEEN '$start_date' AND '$end_date'
								ORDER BY DATE DESC"),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate','SCHOOL_ID'=>'GetSchool'));
$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','SCHOOL_ID'=>'School','START_DATE'=>'Enrolled','END_DATE'=>'Dropped');
ListOutput($enrollment_RET,$columns,'Enrollment Record','Enrollment Records');
?>