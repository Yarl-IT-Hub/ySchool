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
DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='remove')
{
	if(DeletePrompt('request'))
	{
		DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE STUDENT_ID='$_REQUEST[student_id]' AND COURSE_ID='$_REQUEST[course_id]' AND COURSE_WEIGHT='$_REQUEST[course_weight]' AND SYEAR='".UserSyear()."'");
		unset($_REQUEST['modfunc']);
	}
}
	
if(!$_REQUEST['modfunc'])
{
	$sql = "SELECT 
				concat(s.FIRST_NAME, ' ', s.LAST_NAME) AS FULL_NAME,r.STUDENT_ID, concat(c.TITLE, ' - ', r.COURSE_WEIGHT) as COURSE,r.COURSE_ID,r.COURSE_WEIGHT
			FROM
				SCHEDULE_REQUESTS r,COURSES c,STUDENTS s
			WHERE
				s.STUDENT_ID = r.STUDENT_ID AND r.COURSE_ID = c.COURSE_ID
				AND r.SYEAR = '".UserSyear()."' AND r.SCHOOL_ID = '".UserSchool()."'
				AND NOT EXISTS (SELECT '' FROM SCHEDULE ss WHERE ss.STUDENT_ID=r.STUDENT_ID AND ss.COURSE_ID=r.COURSE_ID AND ss.COURSE_WEIGHT=r.COURSE_WEIGHT)
			";
	$RET = DBGet(DBQuery($sql),array(),array('STUDENT_ID'));
	$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','COURSE'=>'Course');
	$link['remove']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=remove\");'";
	$link['remove']['variables'] = array('student_id'=>'STUDENT_ID','course_id'=>'COURSE_ID','course_weight'=>'COURSE_WEIGHT');
	ListOutput($RET,$columns,'Unscheduled Request','Unscheduled Requests',$link,array(array('FULL_NAME','STUDENT_ID')));
}
?>