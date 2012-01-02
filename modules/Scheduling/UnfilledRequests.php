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
if($_REQUEST['modname']=='Scheduling/UnfilledRequests.php')
	DrawBC("Scheduling > ".ProgramTitle());
else
	$extra['suppress_save'] = true;
$extra['SELECT'] = ',c.TITLE AS COURSE,sr.SUBJECT_ID,sr.COURSE_ID,sr.WITH_TEACHER_ID,sr.NOT_TEACHER_ID,sr.WITH_PERIOD_ID,sr.NOT_PERIOD_ID,(SELECT COALESCE(sum(COALESCE(cp.TOTAL_SEATS,0)-COALESCE(cp.FILLED_SEATS,0)),0) AS AVAILABLE_SEATS FROM COURSE_PERIODS cp WHERE cp.COURSE_ID=sr.COURSE_ID AND (cp.GENDER_RESTRICTION=\'N\' OR cp.GENDER_RESTRICTION=substring(s.GENDER,1,1)) AND (sr.WITH_TEACHER_ID IS NULL OR sr.WITH_TEACHER_ID=\'\' OR sr.WITH_TEACHER_ID=cp.TEACHER_ID) AND (sr.NOT_TEACHER_ID IS NULL OR sr.NOT_TEACHER_ID=\'\' OR sr.NOT_TEACHER_ID!=cp.TEACHER_ID) AND (sr.WITH_PERIOD_ID IS NULL OR sr.WITH_PERIOD_ID=\'\' OR sr.WITH_PERIOD_ID=cp.PERIOD_ID) AND (sr.NOT_PERIOD_ID IS NULL OR sr.NOT_PERIOD_ID=\'\' OR sr.NOT_PERIOD_ID!=cp.PERIOD_ID)) AS AVAILABLE_SEATS,(SELECT count(*) AS SECTIONS FROM COURSE_PERIODS cp WHERE cp.COURSE_ID=sr.COURSE_ID AND (cp.GENDER_RESTRICTION=\'N\' OR cp.GENDER_RESTRICTION=substring(s.GENDER,1,1)) AND (sr.WITH_TEACHER_ID IS NULL OR sr.WITH_TEACHER_ID=\'\' OR sr.WITH_TEACHER_ID=cp.TEACHER_ID) AND (sr.NOT_TEACHER_ID IS NULL OR sr.NOT_TEACHER_ID=\'\' OR sr.NOT_TEACHER_ID!=cp.TEACHER_ID) AND (sr.WITH_PERIOD_ID IS NULL OR sr.WITH_PERIOD_ID=\'\' OR sr.WITH_PERIOD_ID=cp.PERIOD_ID) AND (sr.NOT_PERIOD_ID IS NULL OR sr.NOT_PERIOD_ID=\'\' OR sr.NOT_PERIOD_ID!=cp.PERIOD_ID)) AS SECTIONS ';
$extra['FROM'] = ',SCHEDULE_REQUESTS sr,COURSES c';
#$extra['WHERE'] = ' AND sr.STUDENT_ID=ssm.STUDENT_ID AND sr.SYEAR=ssm.SYEAR AND sr.SCHOOL_ID=ssm.SCHOOL_ID AND sr.COURSE_ID=c.COURSE_ID AND NOT EXISTS (SELECT \'\' FROM SCHEDULE s WHERE s.STUDENT_ID=sr.STUDENT_ID AND s.COURSE_ID=sr.COURSE_ID) AND sr.STUDENT_ID='.UserStudentID();
//$extra['WHERE'] = ' AND sr.STUDENT_ID=ssm.STUDENT_ID AND sr.SYEAR=ssm.SYEAR AND sr.SCHOOL_ID=ssm.SCHOOL_ID AND sr.COURSE_ID=c.COURSE_ID AND NOT EXISTS (SELECT \'\' FROM SCHEDULE s WHERE s.STUDENT_ID=sr.STUDENT_ID AND s.COURSE_ID=sr.COURSE_ID)';
$extra['WHERE'] = ' AND sr.STUDENT_ID=ssm.STUDENT_ID AND sr.SYEAR=ssm.SYEAR AND sr.SCHOOL_ID=ssm.SCHOOL_ID AND sr.COURSE_ID=c.COURSE_ID ';

$extra['functions'] = array('WITH_TEACHER_ID'=>'_makeTeacher','WITH_PERIOD_ID'=>'_makePeriod');
$extra['columns_after'] = array('COURSE'=>'Request','AVAILABLE_SEATS'=>'Available Seats','SECTIONS'=>'Sections','WITH_TEACHER_ID'=>'Teacher','WITH_PERIOD_ID'=>'Period');
$extra['singular'] = 'Unscheduled Request';
$extra['plural'] = 'Unscheduled Requests';
if(!$extra['link']['FULL_NAME'])
{
	$extra['link']['FULL_NAME']['link'] = 'Modules.php?modname=Scheduling/Requests.php';
	//$extra['link']['FULL_NAME']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]\");'";
	$extra['link']['FULL_NAME']['variables']['student_id'] = 'STUDENT_ID';
}
$extra['new'] = true;
$extra['Redirect'] = false;

Search('student_id',$extra);

function _makeTeacher($value,$column)
{	global $THIS_RET;

	return ($value!=''?'With: '.GetTeacher($value).'<BR>':'').($THIS_RET['NOT_TEACHER_ID']!=''?'Without: '.GetTeacher($THIS_RET['NOT_TEACHER_ID']):'');
}

function _makePeriod($value,$column)
{	global $THIS_RET;

	return ($value!=''?'On: '.GetPeriod($value).'<BR>':'').($THIS_RET['NOT_PERIOD_ID']!=''?'Not on: '.GetPeriod($THIS_RET['NOT_PERIOD_ID']):'');
}

?>