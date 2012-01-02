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
$_REQUEST['modfunc'] = 'choose_course';

//if($_REQUEST['course_id'])
//{
//	$weights_RET = DBGet(DBQuery("SELECT COURSE_WEIGHT,GPA_MULTIPLIER FROM COURSE_WEIGHTS WHERE COURSE_ID='$_REQUEST[course_id]'"));
//	if(count($weights_RET)==1)
//		$_REQUEST['course_weight'] = $weights_RET[1]['COURSE_WEIGHT'];
//}

if(!$_REQUEST['course_id'])
	include 'modules/Scheduling/CoursesforWindow.php';
else
{
	$course_title = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='".$_REQUEST['course_id']."'"));
	$course_title = $course_title[1]['TITLE']. '<INPUT type=hidden name=request_course_id value='.$_REQUEST['course_id'].'>';

	echo "<script language=javascript>opener.document.getElementById(\"request_div\").innerHTML = \"$course_title<BR><small><INPUT type=checkbox name=not_request_course value=Y>Not Requested</small>\"; window.close();</script>";
}

?>