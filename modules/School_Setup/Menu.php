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
$menu['School_Setup']['admin'] = array(
						'School_Setup/PortalNotes.php'=>'Portal Notes',
						'School_Setup/MarkingPeriods.php'=>'Marking Periods',
						'School_Setup/Calendar.php'=>'Calendars',
						'School_Setup/Periods.php'=>'Periods',
						'School_Setup/GradeLevels.php'=>'Grade Levels',
                         1=>'School',
                        'School_Setup/Schools.php'=>'School Information',
    'School_Setup/UploadLogo.php'=>'Upload School Logo',
						'School_Setup/Schools.php?new_school=true'=>'Add a School',
						'School_Setup/CopySchool.php'=>'Copy School',
						'School_Setup/system_preference.php'=>'System Preference',
                         2=>'Courses',
                        'School_Setup/Courses.php'=>'Course Manager',
                        'School_Setup/CourseCatalog.php'=>'Course Catalog',
                        'School_Setup/PrintCatalog.php'=>'Print Catalog by Term', 
                        'School_Setup/PrintAllCourses.php'=>'Print all Courses'  
					);

$menu['School_Setup']['teacher'] = array(
						'School_Setup/Schools.php'=>'School Information',
						'School_Setup/MarkingPeriods.php'=>'Marking Periods',
						'School_Setup/Calendar.php'=>'Calendar',
						1=>'Courses',
                        'School_Setup/Courses.php'=>'Course Manager',
                        'School_Setup/CourseCatalog.php'=>'Course Catalog',
                        'School_Setup/PrintCatalog.php'=>'Print Catalog by Term', 
                        'School_Setup/PrintAllCourses.php'=>'Print all Courses'
					);

$menu['School_Setup']['parent'] = array(
						'School_Setup/Schools.php'=>'School Information',
						'School_Setup/Calendar.php'=>'Calendar'
					);

$exceptions['School_Setup'] = array(
						'School_Setup/PortalNotes.php'=>true,
						'School_Setup/Schools.php?new_school=true'=>true,
						'School_Setup/Rollover.php'=>true
					);
?>
