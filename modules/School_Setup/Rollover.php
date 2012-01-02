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
$next_syear = UserSyear()+1;
$_SESSION['DT'] = $DatabaseType; 
$_SESSION['DS'] = $DatabaseServer; 
$_SESSION['DU'] = $DatabaseUsername; 
$_SESSION['DP'] = $DatabasePassword; 
$_SESSION['DB'] = $DatabaseName; 
$_SESSION['DBP'] = $DatabasePort; 
$_SESSION['NY'] = $next_syear;


$tables = array('STAFF'=>'Users','SCHOOL_PERIODS'=>'School Periods','SCHOOL_YEARS'=>'Marking Periods','ATTENDANCE_CALENDARS'=>'Calendars','REPORT_CARD_GRADE_SCALES'=>'Report Card Grade Codes','COURSES'=>'Courses<b>*</b>','STUDENT_ENROLLMENT'=>'Students','REPORT_CARD_COMMENTS'=>'Report Card Comment Codes','ELIGIBILITY_ACTIVITIES'=>'Eligibility Activity Codes','ATTENDANCE_CODES'=>'Attendance Codes','STUDENT_ENROLLMENT_CODES'=>'Student Enrollment Codes');
$no_school_tables = array('STUDENT_ENROLLMENT_CODES'=>true,'STAFF'=>true);

$table_list = '<TABLE align=center>';
$table_list .= '<tr><td colspan=3 class=clear></td></tr>';
$table_list .= '<tr><td colspan=3>* You <i>must</i> roll users, school periods, marking periods, calendars, and report card<br>codes at the same time or before rolling courses<BR><BR>* You <i>must</i> roll courses at the same time or before rolling report card comments<BR><BR>Red items have already have data in the next school year (They might have been rolled).<BR><BR>Rolling red items will delete already existing data in the next school year.</td></tr>';
foreach($tables as $table=>$name)
{
	$exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
	if($exists_RET[$table][1]['COUNT']>0)
		$table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.']></TD><TD width=94%>'.$name.' ('.$exists_RET[$table][1]['COUNT'].')</TD></TR>';
	else
		$table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED></TD><TD width=94%>'.$name.'</TD></TR>';
}
$table_list .= '</TABLE></CENTER><CENTER>';

DrawBC("School Setup > ".ProgramTitle());

if(Prompt_rollover('Confirm Rollover','Are you sure you want to roll the data for '.UserSyear().'-'.(UserSyear()+1).' to the next school year?',$table_list))
{
	if($_REQUEST['tables']['COURSES'] && ((!$_REQUEST['tables']['STAFF'] && $exists_RET['STAFF'][1]['COUNT']<1) || (!$_REQUEST['tables']['SCHOOL_PERIODS'] && $exists_RET['SCHOOL_PERIODS'][1]['COUNT']<1) || (!$_REQUEST['tables']['SCHOOL_YEARS'] && $exists_RET['SCHOOL_YEARS'][1]['COUNT']<1) || (!$_REQUEST['tables']['ATTENDANCE_CALENDARS'] && $exists_RET['ATTENDANCE_CALENDARS'][1]['COUNT']<1) || (!$_REQUEST['tables']['REPORT_CARD_GRADE_SCALES'] && $exists_RET['REPORT_CARD_GRADE_SCALES'][1]['COUNT']<1)))
		BackPrompt('You must roll users, school periods, marking periods, calendars, and report card codes at the same time or before rolling courses.');
	if($_REQUEST['tables']['REPORT_CARD_COMMENTS'] && ((!$_REQUEST['tables']['COURSES'] && $exists_RET['COURSES'][1]['COUNT']<1)))
		BackPrompt('You must roll  courses at the same time or before rolling report card comments.');
	if(count($_REQUEST['tables']))
	{
		foreach($_REQUEST['tables'] as $table=>$value)
		{
			
			Rollover($table);
		}
	}
	
	#echo '<FORM>';
	#DrawHeaderHome('<IMG SRC=assets/check.gif>The data have been rolled.','<input type=button value=ok onclick="parent.location.reload();"><a href=index.php?modfunc=logout target=_top>Please login again</a>');
	DrawHeaderHome('<IMG SRC=assets/check.gif>The data have been rolled.','<input type=button onclick=document.location.href="index.php?modfunc=logout" value="Please login again" class=btn_large >');
#	DrawHeader('<IMG SRC=assets/check.gif>The data have been rolled. Please login again!');
	#echo '</FORM>';
	unset($_SESSION['_REQUEST_vars']['tables']);
	unset($_SESSION['_REQUEST_vars']['delete_ok']);	
	// --------------------------------------------------------------------------------------------------------------------------------------------------------- //
	
}

function Rollover($table)
{	global $next_syear;

	switch($table)
	{
		case 'STAFF':
			$user_custom='';
			$fields_RET = DBGet(DBQuery("SELECT ID FROM STAFF_FIELDS"));
			foreach($fields_RET as $field)
				$user_custom .= ',CUSTOM_'.$field['ID'];
			DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STAFF_ID IN (SELECT STAFF_ID FROM STAFF WHERE SYEAR=$next_syear)");
			DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID IN (SELECT STAFF_ID FROM STAFF WHERE SYEAR=$next_syear)");
			DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID IN (SELECT STAFF_ID FROM STAFF WHERE SYEAR=$next_syear)");
			DBQuery("DELETE FROM STAFF WHERE SYEAR='$next_syear'");

			DBQuery("INSERT INTO STAFF (SYEAR,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,LAST_LOGIN,SCHOOLS,PROFILE_ID,ROLLOVER_ID$user_custom) SELECT SYEAR+1,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,NULL,SCHOOLS,PROFILE_ID,STAFF_ID$user_custom FROM STAFF WHERE SYEAR='".UserSyear()."'");

			DBQuery("INSERT INTO PROGRAM_USER_CONFIG (USER_ID,PROGRAM,TITLE,VALUE) SELECT s.STAFF_ID,puc.PROGRAM,puc.TITLE,puc.VALUE FROM STAFF s,PROGRAM_USER_CONFIG puc WHERE puc.USER_ID=s.ROLLOVER_ID AND puc.PROGRAM='Preferences' AND s.SYEAR='$next_syear'");

			DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT STAFF_ID,MODNAME,CAN_USE,CAN_EDIT FROM STAFF,STAFF_EXCEPTIONS WHERE USER_ID=ROLLOVER_ID AND SYEAR='$next_syear'");

			DBQuery("INSERT INTO STUDENTS_JOIN_USERS (STUDENT_ID,STAFF_ID) SELECT j.STUDENT_ID,s.STAFF_ID FROM STAFF s,STUDENTS_JOIN_USERS j WHERE j.STAFF_ID=s.ROLLOVER_ID AND s.SYEAR='$next_syear'");
		break;

		case 'SCHOOL_PERIODS':
			DBQuery("DELETE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
			DBQuery("INSERT INTO SCHOOL_PERIODS (SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,PERIOD_ID FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ATTENDANCE_CALENDARS':
			DBQuery("DELETE FROM ATTENDANCE_CALENDARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
			DBQuery("INSERT INTO ATTENDANCE_CALENDARS (SYEAR,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'SCHOOL_YEARS':
			DBQuery("DELETE FROM SCHOOL_PROGRESS_PERIODS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM SCHOOL_QUARTERS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM SCHOOL_SEMESTERS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM SCHOOL_YEARS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");

			$r = DBGet(DBQuery("select max(m.marking_period_id) as marking_period_id from (select max(marking_period_id) as marking_period_id from SCHOOL_YEARS union select max(marking_period_id) as marking_period_id from SCHOOL_SEMESTERS union select max(marking_period_id) as marking_period_id from SCHOOL_QUARTERS) m"));
			$mpi = $r[1]['MARKING_PERIOD_ID'] + 1;
		        DBQuery("ALTER TABLE MARKING_PERIOD_ID_GENERATOR AUTO_INCREMENT = $mpi");
                         // DBQuery('INSERT INTO MARKING_PERIOD_ID_GENERATOR (id)VALUES (NULL)');
                            //$MARKING_PERIOD_SEQ_VALUE_ARRAY= DBGet(DBQuery('SELECT  max(id) AS ID from MARKING_PERIOD_ID_GENERATOR' ));
                           // $MARKING_PERIOD_SEQ_VALUE=$MARKING_PERIOD_SEQ_VALUE_ARRAY[1]['ID'];
			DBQuery("INSERT INTO SCHOOL_YEARS (MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT ".db_seq_nextval('marking_period_seq').",SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,date_add(START_DATE,INTERVAL 365 DAY),date_add(END_DATE,INTERVAL 365 DAY),date_add(POST_START_DATE,INTERVAL 365 DAY),date_add(POST_END_DATE,INTERVAL 365 DAY),DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

                                                   
                        DBQuery("INSERT INTO SCHOOL_SEMESTERS (MARKING_PERIOD_ID,YEAR_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT ".db_seq_nextval('marking_period_seq').",(SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS y WHERE y.SYEAR=s.SYEAR+1 AND y.ROLLOVER_ID=s.YEAR_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,date_add(START_DATE, INTERVAL 365 DAY),date_add(END_DATE,INTERVAL 365 DAY),date_add(POST_START_DATE,INTERVAL 365 DAY),date_add(POST_END_DATE,INTERVAL 365 DAY),DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS s WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

                        DBQuery("INSERT INTO SCHOOL_QUARTERS (MARKING_PERIOD_ID,SEMESTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT ".db_seq_nextval('marking_period_seq').",(SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS s WHERE s.SYEAR=q.SYEAR+1 AND s.ROLLOVER_ID=q.SEMESTER_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE+365,END_DATE+365,POST_START_DATE+365,POST_END_DATE+365,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_QUARTERS q WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

                        DBQuery("INSERT INTO SCHOOL_PROGRESS_PERIODS (MARKING_PERIOD_ID,QUARTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT ".db_seq_nextval('marking_period_seq').",(SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS q WHERE q.SYEAR=p.SYEAR+1 AND q.ROLLOVER_ID=p.QUARTER_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,date_add(START_DATE,INTERVAL 365 DAY),date_add(END_DATE,INTERVAL 365 DAY),date_add(POST_START_DATE,INTERVAL 365 DAY),date_add(POST_END_DATE,INTERVAL 365 DAY),DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_PROGRESS_PERIODS p WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'COURSES':
			DBQuery("DELETE FROM COURSE_SUBJECTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			/*DBQuery("DELETE FROM COURSE_WEIGHTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");*/
			DBQuery("DELETE FROM COURSES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM COURSE_PERIODS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");

			// ROLL COURSE_SUBJECTS
			DBQuery("INSERT INTO COURSE_SUBJECTS (SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SUBJECT_ID FROM COURSE_SUBJECTS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			// ROLL COURSE WEIGHTS
			DBQuery("INSERT INTO COURSES (SYEAR,SUBJECT_ID,SCHOOL_ID,GRADE_LEVEL,TITLE,SHORT_NAME,ROLLOVER_ID) SELECT SYEAR+1,(SELECT SUBJECT_ID FROM COURSE_SUBJECTS s WHERE s.SYEAR=c.SYEAR+1 AND s.ROLLOVER_ID=c.SUBJECT_ID),SCHOOL_ID,GRADE_LEVEL,TITLE,SHORT_NAME,COURSE_ID FROM COURSES c WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			// ROLL COURSES
			/*DBQuery("INSERT INTO COURSE_WEIGHTS (SYEAR,SCHOOL_ID,COURSE_ID,GPA_MULTIPLIER,COURSE_WEIGHT) SELECT SYEAR+1,SCHOOL_ID,(SELECT COURSE_ID FROM COURSES c WHERE c.SYEAR=w.SYEAR+1 AND c.ROLLOVER_ID=w.COURSE_ID),GPA_MULTIPLIER,COURSE_WEIGHT FROM COURSE_WEIGHTS w WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");*/

			// ROLL COURSE_PERIODS
	
			DBQuery("INSERT INTO COURSE_PERIODS (SYEAR,SCHOOL_ID,COURSE_ID,COURSE_WEIGHT,TITLE,
SHORT_NAME,PERIOD_ID,MP,MARKING_PERIOD_ID,TEACHER_ID,ROOM,
TOTAL_SEATS,FILLED_SEATS,DOES_ATTENDANCE,GRADE_SCALE_ID,DOES_HONOR_ROLL,
DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,
AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,CALENDAR_ID,
ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,
(SELECT COURSE_ID FROM COURSES c WHERE c.SYEAR=p.SYEAR+1 AND c.ROLLOVER_ID=p.COURSE_ID),
COURSE_WEIGHT,TITLE,SHORT_NAME,(SELECT PERIOD_ID FROM SCHOOL_PERIODS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.PERIOD_ID),MP,".db_case(array('MP',"'FY'",'(SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)',"'SEM'",'(SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)',"'QTR'",'(SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)')).",(SELECT STAFF_ID FROM STAFF n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.TEACHER_ID),ROOM,TOTAL_SEATS,0 AS FILLED_SEATS,DOES_ATTENDANCE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES n WHERE n.ROLLOVER_ID=p.GRADE_SCALE_ID AND n.SCHOOL_ID=".UserSchool()."),DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS n WHERE n.ROLLOVER_ID=p.CALENDAR_ID),COURSE_PERIOD_ID FROM COURSE_PERIODS p WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");

			$rowq=DBQUERY("SELECT * FROM COURSE_PERIODS  WHERE ROLLOVER_ID=PARENT_ID");
			DBQuery("UPDATE COURSE_PERIODS SET PARENT_ID='".$rowq['course_period_id']."' WHERE PARENT_ID IS NOT NULL AND SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'STUDENT_ENROLLMENT':
			$next_start_date = DBDate();
			DBQuery("DELETE FROM STUDENT_ENROLLMENT WHERE SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
			// ROLL STUDENTS TO NEXT GRADE
			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,(SELECT NEXT_GRADE_ID FROM SCHOOL_GRADELEVELS g WHERE g.ID=e.GRADE_ID),'$next_start_date' AS START_DATE,NULL AS END_DATE,NULL AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='".UserSchool()."'");

			// ROLL STUDENTS WHO ARE TO BE RETAINED
			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,GRADE_ID,'$next_start_date' AS START_DATE,NULL AS END_DATE,NULL AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='0'");

			// ROLL STUDENTS TO NEXT SCHOOL
			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,STUDENT_ID,(SELECT g.ID FROM SCHOOL_GRADELEVELS g WHERE g.SORT_ORDER=1 AND g.SCHOOL_ID=e.NEXT_SCHOOL),'$next_start_date' AS START_DATE,NULL AS END_DATE,NULL AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL NOT IN ('".UserSchool()."','0','-1')");
		break;

		case 'REPORT_CARD_GRADE_SCALES':
			DBQuery("DELETE FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM REPORT_CARD_GRADES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			// DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (ID,SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID) SELECT ".db_seq_nextval('REPORT_CARD_GRADE_SCALES_SEQ')."+ID,SYEAR+1,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,GRADE_SCALE_ID,SORT_ORDER) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES WHERE ROLLOVER_ID=GRADE_SCALE_ID AND SCHOOL_ID=REPORT_CARD_GRADES.SCHOOL_ID),SORT_ORDER FROM REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'REPORT_CARD_COMMENTS':
			DBQuery("DELETE FROM REPORT_CARD_COMMENTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,COURSE_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SORT_ORDER,".db_case(array('COURSE_ID',"''",'NULL',"(SELECT COURSE_ID FROM COURSES WHERE ROLLOVER_ID=rc.COURSE_ID)"))." FROM REPORT_CARD_COMMENTS rc WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ELIGIBILITY_ACTIVITIES':
		case 'ATTENDANCE_CODES':
			DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SYEAR".$columns.") SELECT SYEAR+1".$columns." FROM $table WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		// DOESN'T HAVE A SCHOOL_ID
		case 'STUDENT_ENROLLMENT_CODES':
			DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear'");
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SYEAR".$columns.") SELECT SYEAR+1".$columns." FROM $table WHERE SYEAR='".UserSyear()."'");
		break;
	}
	

		// ---------------------------------------------------------------------- data write start ----------------------------------------------------------------------- //
			$string .= "<"."?php \n";
			$string .= "$"."DatabaseType = '".$_SESSION['DT']."'; \n"	;
			$string .= "$"."DatabaseServer = '".$_SESSION['DS']."'; \n"	;
			$string .= "$"."DatabaseUsername = '".$_SESSION['DU']."'; \n" ;
			$string .= "$"."DatabasePassword = '".$_SESSION['DP']."'; \n";
			$string .= "$"."DatabaseName = '".$_SESSION['DB']."'; \n";
			$string .= "$"."DatabasePort = '".$_SESSION['DBP'] ."'; \n";
			$string .= "$"."DefaultSyear = '".$_SESSION['NY']."'; \n";
			$string .="?".">";
			
			$err = "Can't write to file";
			
			$myFile = "data.php";
			$fh = fopen($myFile, 'w') or exit($err);
			fwrite($fh, $string);
			fclose($fh);
		// ---------------------------------------------------------------------- data write end ------------------------------------------------------------------------ //
		
	
}

?>