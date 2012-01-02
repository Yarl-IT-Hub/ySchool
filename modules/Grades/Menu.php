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
$menu['Grades']['admin'] = array(
						'Grades/ReportCards.php'=>'Report Cards',
						'Grades/CalcGPA.php'=>'Calculate GPA',
						'Grades/Transcripts.php'=>'Transcripts',
						1=>'Reports',
						'Grades/TeacherCompletion.php'=>'Teacher Completion',
						'Grades/GradeBreakdown.php'=>'Grade Breakdown',
						'Grades/FinalGrades.php'=>'Student Final Grades',
						'Grades/GPARankList.php'=>'GPA / Class Rank List',
                        'Grades/HonorRoll.php'=>'Honor Roll',
						2=>'Setup',
						'Grades/ReportCardGrades.php'=>'Report Card Grades',
						'Grades/ReportCardComments.php'=>'Report Card Comments',
                                                'Grades/HonorRollSetup.php'=>'Honor Roll Setup',
                        'Grades/HonorRollSetup.php'=>'Honor Roll Setup',
						3=>'Utilities',
						'Grades/FixGPA.php'=>'Recalculate GPA Numbers',
                        'Grades/EditReportCardGrades.php'=>'Edit Report Card Grades',
                        'Grades/EditHistoryMarkingPeriods.php'=>'Edit Historical Marking Periods'
					);

$menu['Grades']['teacher'] = array(
						'Grades/InputFinalGrades.php'=>'Input Final Grades',
						'Grades/ReportCards.php'=>'Report Cards',
						1=>'Gradebook',
						'Grades/Grades.php'=>'Grades',
						'Grades/Assignments.php'=>'Assignments',
						'Grades/AnomalousGrades.php'=>'Anomalous Grades',
						'Grades/ProgressReports.php'=>'Progress Reports',
						2=>'Reports',
						'Grades/StudentGrades.php'=>'Student Grades',
						'Grades/FinalGrades.php'=>'Final Grades',
						3=>'Setup',
						'Grades/Configuration.php'=>'Configuration',
						'Grades/ReportCardGrades.php'=>'Report Card Grades',
						'Grades/ReportCardComments.php'=>'Report Card Comments'
					);

$menu['Grades']['parent'] = array(
						'Grades/StudentGrades.php'=>'Gradebook Grades',
						'Grades/FinalGrades.php'=>'Final Grades',
						'Grades/ReportCards.php'=>'Report Cards',
						'Grades/Transcripts.php'=>'Transcripts',
						'Grades/GPARankList.php'=>'GPA / Class Rank'
					);

$menu['Users']['admin'] += array(
						'Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php'=>'Input Final Grades',
						'Users/TeacherPrograms.php?include=Grades/Grades.php'=>'Gradebook Grades',
                                                'Users/TeacherPrograms.php?include=Grades/ProgressReports.php'=>'Progress Reports'
					);

$exceptions['Grades'] = array(
						'Grades/CalcGPA.php'=>true
					);
?>