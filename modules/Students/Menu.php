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
$menu['Students']['admin'] = array(
						'Students/Student.php'=>'Student Info',
						'Students/Student.php&include=General_Info&student_id=new'=>'Add a Student',
						'Students/AssignOtherInfo.php'=>'Group Assign Student Info',
						'Students/AddUsers.php'=>'Associate Parents with Students',
						1=>'Reports',
						'Students/AdvancedReport.php'=>'Advanced Report',
						'Students/AddDrop.php'=>'Add / Drop Report',
						'Students/Letters.php'=>'Print Letters',
						'Students/MailingLabels.php'=>'Print Mailing Labels',
						'Students/StudentLabels.php'=>'Print Student Labels',
						'Students/PrintStudentInfo.php'=>'Print Student Info',
                        'Students/GoalReport.php'=>'Print Goals & Progresses',
						2=>'Setup',
						'Students/StudentFields.php'=>'Student Fields',
						#'Students/AddressFields.php'=>'Address Fields',
						#'Students/PeopleFields.php'=>'Contact Fields',
						'Students/EnrollmentCodes.php'=>'Enrollment Codes',
						'Students/Upload.php'=>'Upload Student Photo',
						'Students/Upload.php?modfunc=edit'=>'Update Student Photo'
					);

$menu['Students']['teacher'] = array(
						'Students/Student.php'=>'Student Info',
						'Students/AddUsers.php'=>'Associated Parents',
						1=>'Reports',
						'Students/AdvancedReport.php'=>'Advanced Report',
						'Students/StudentLabels.php'=>'Print Student Labels'
					);

$menu['Students']['parent'] = array(
						'Students/Student.php'=>'Student Info',
						'Students/ChangePassword.php'=>'Change Password'
					);

$exceptions['Students'] = array(
						'Students/Student.php?include=General_Info?student_id=new'=>true,
						'Students/AssignOtherInfo.php'=>true
					);
?>
