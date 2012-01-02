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
echo "<FORM name=scheaddr id=scheaddr action=".PreparePHP_SELF()." method=POST>";
DrawBC("Scheduling > ".ProgramTitle());

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
            $_REQUEST['placed_From'] =$_REQUEST['day_start'] .'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start'];
           $start_date=(date('Y-m-d',strtotime($_REQUEST['placed_From'])));
}
else
     $start_date =date("Y-m").'-01';
if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	 $_REQUEST['placed_End'] =$_REQUEST['day_end'] .'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];
          $end_date=(date('Y-m-d',strtotime($_REQUEST['placed_End'])));
}
else
	 $end_date =date("Y-m-d");

if($_REQUEST['flag']!='list')
DrawHeaderHome(PrepareDateSchedule($start_date,'_start').'<div style="padding:0px 6px; float:left;">-</div>'.PrepareDateSchedule($end_date,'_end'),'<INPUT type=submit class=btn_medium value=Go >');
echo '</FORM>';
if($_REQUEST['modfunc']=='save')
{
 $a=count($_REQUEST['st_arr']);
  if($a==0)
  {
    echo "Sorry! No Students were selected";
  }
 else {


  if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = "  se.ID IN ($st_list)";
        }
  
  $start_date = $_REQUEST['sday'];
  $end_date = $_REQUEST['eday'];

  

$enrollment_RET = DBGet(DBQuery( "SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,se.START_DATE AS START_DATE,NULL AS END_DATE,se.START_DATE AS DATE,se.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM SCHEDULE se,STUDENTS s,COURSES c,COURSE_PERIODS cp WHERE c.COURSE_ID=se.COURSE_ID AND cp.COURSE_PERIOD_ID=se.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND s.STUDENT_ID=se.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND se.START_DATE BETWEEN '$start_date' AND '$end_date' AND $extra[WHERE]
							UNION SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,NULL AS START_DATE,se.END_DATE AS END_DATE,se.END_DATE AS DATE,se.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM SCHEDULE se,STUDENTS s,COURSES c,COURSE_PERIODS cp WHERE c.COURSE_ID=se.COURSE_ID AND cp.COURSE_PERIOD_ID=se.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND s.STUDENT_ID=se.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND se.END_DATE BETWEEN '$start_date' AND '$end_date' AND $extra[WHERE]
								ORDER BY DATE DESC"),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate')) ;
$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','COURSE_TITLE'=>'Course','TITLE'=>'Course Period','START_DATE'=>'Enrolled','END_DATE'=>'Dropped');
ListOutputPrint($enrollment_RET,$columns,'Schedule Record','Schedule Records');
}
}
 else {
echo "<FORM name=addr id=addr action='for_export.php?modname=$_REQUEST[modname]&modfunc=save&sday=$start_date&eday=$end_date&include_inactive=$_REQUEST[include_inactive]&_openSIS_PDF=true&flag=list' method=POST target=_blank>";


$enrollment_RET1 = DBGet(DBQuery("SELECT se.ID CHECKBOX,c.TITLE AS COURSE_TITLE,cp.TITLE,se.START_DATE AS START_DATE,NULL AS END_DATE,se.START_DATE AS DATE,se.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM SCHEDULE se,STUDENTS s,COURSES c,COURSE_PERIODS cp WHERE c.COURSE_ID=se.COURSE_ID AND cp.COURSE_PERIOD_ID=se.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND s.STUDENT_ID=se.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND se.START_DATE BETWEEN '$start_date' AND '$end_date'
							UNION SELECT se.ID as CHECKBOX,c.TITLE AS COURSE_TITLE,cp.TITLE,NULL AS START_DATE,se.END_DATE AS END_DATE,se.END_DATE AS DATE,se.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM SCHEDULE se,STUDENTS s,COURSES c,COURSE_PERIODS cp WHERE c.COURSE_ID=se.COURSE_ID AND cp.COURSE_PERIOD_ID=se.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND s.STUDENT_ID=se.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND se.END_DATE BETWEEN '$start_date' AND '$end_date'
								ORDER BY DATE DESC"),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate','CHECKBOX'=>'_makeChooseCheckbox'));



$columns_b = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller  onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
$columns = $columns_b+ array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','COURSE_TITLE'=>'Course','TITLE'=>'Course Period','START_DATE'=>'Enrolled','END_DATE'=>'Dropped');

ListOutput($enrollment_RET1,$columns,'Schedule Record','Schedule Records');


if($_REQUEST['flag']!='list' && count($enrollment_RET1)!='0')
echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Add/Drop Report for Selected Students\'></CENTER>';

echo '</FORM>';

}
function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.'>';
}
?>
