<?php
include('Redirect_root.php');
include('Warehouse.php');
$next_syear=$_SESSION['NY'];
$table=$_REQUEST['table_name'];
$next_start_date=$_SESSION['roll_start_date'];
$tables = array('STAFF'=>'Users','SCHOOL_PERIODS'=>'School Periods','SCHOOL_YEARS'=>'Marking Periods','ATTENDANCE_CALENDARS'=>'Calendars','REPORT_CARD_GRADE_SCALES'=>'Report Card Grade Codes','COURSES'=>'Courses','STUDENT_ENROLLMENT'=>'Students','ELIGIBILITY_ACTIVITIES'=>'Eligibility Activity Codes','ATTENDANCE_CODES'=>'Attendance Codes','STUDENT_ENROLLMENT_CODES'=>'Student Enrollment Codes','REPORT_CARD_COMMENTS'=>'Report Card Comment Codes','NONE'=>'none');
$no_school_tables = array('STUDENT_ENROLLMENT_CODES'=>true,'STAFF'=>true);
switch($table)
{
		case 'STAFF':
		
			$user_custom='';
			$fields_RET = DBGet(DBQuery("SELECT ID FROM STAFF_FIELDS"));
			foreach($fields_RET as $field)
			     $user_custom .= ',CUSTOM_'.$field['ID'];
//			DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STAFF_ID IN (SELECT STAFF_ID FROM STAFF WHERE SYEAR=$next_syear)");
//			DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID IN (SELECT STAFF_ID FROM STAFF WHERE SYEAR=$next_syear)");
//			DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID IN (SELECT STAFF_ID FROM STAFF WHERE SYEAR=$next_syear)");
//			DBQuery("DELETE FROM STAFF WHERE SYEAR='$next_syear'");
                        $staff_rollovered=DBGet(DBQuery("SELECT STAFF_ID FROM STAFF WHERE SYEAR='$next_syear'"));
                        $total_staff=count($staff_rollovered);
                        if($total_staff==0){
                            DBQuery("INSERT INTO STAFF (SYEAR,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,LAST_LOGIN,SCHOOLS,PROFILE_ID,ROLLOVER_ID$user_custom) SELECT SYEAR+1,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,NULL,SCHOOLS,PROFILE_ID,STAFF_ID$user_custom FROM STAFF WHERE SYEAR='".UserSyear()."'");
                            DBQuery("INSERT INTO PROGRAM_USER_CONFIG (USER_ID,PROGRAM,TITLE,VALUE) SELECT s.STAFF_ID,puc.PROGRAM,puc.TITLE,puc.VALUE FROM STAFF s,PROGRAM_USER_CONFIG puc WHERE puc.USER_ID=s.ROLLOVER_ID AND puc.PROGRAM='Preferences' AND s.SYEAR='$next_syear'");
                            DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT STAFF_ID,MODNAME,CAN_USE,CAN_EDIT FROM STAFF,STAFF_EXCEPTIONS WHERE USER_ID=ROLLOVER_ID AND SYEAR='$next_syear'");
                            DBQuery("INSERT INTO STUDENTS_JOIN_USERS (STUDENT_ID,STAFF_ID) SELECT j.STUDENT_ID,s.STAFF_ID FROM STAFF s,STUDENTS_JOIN_USERS j WHERE j.STAFF_ID=s.ROLLOVER_ID AND s.SYEAR='$next_syear'");                   
                        }
                        $parent=DBGet(DBQuery("SELECT * FROM STAFF WHERE PROFILE='parent' AND SYEAR='$next_syear' AND CURRENT_SCHOOL_ID='".UserSchool()."'"));
                        foreach($parent as $key)
                        { 
                            $join_students=DBGet(DBQuery("SELECT * FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".$key[ROLLOVER_ID]."'"));
                            foreach($join_students as $stu_info)
                            {   
                                  $enrollment_record=DBGet(DBQuery("SELECT * FROM STUDENT_ENROLLMENT WHERE STUDENT_ID='$stu_info[STUDENT_ID]' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
                                  
                                  foreach($enrollment_record as $enroll_next_school)
                                  {
                                          if($enroll_next_school['NEXT_SCHOOL']=='-1')
                                          {
                                              $arr[]='true';
                                          }
                                         else {
                                            $arr[]='false';
                                        }
                                  }
                            }
                            
                            
                            if(!in_array('false', $arr))
                            {
                                DBQuery("DELETE FROM STAFF WHERE STAFF_ID='".$key['STAFF_ID']."'");
                            }
                        }
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['STAFF'].'|'.'(|'.$total_rolled_data.'|)';
                    break;

		case 'SCHOOL_PERIODS':
                            
                        DBQuery("DELETE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
                        DBQuery("INSERT INTO SCHOOL_PERIODS (SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,ROLLOVER_ID,START_TIME,END_TIME) SELECT SYEAR+1,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,PERIOD_ID,START_TIME,END_TIME FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['SCHOOL_PERIODS'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
		
		case 'ATTENDANCE_CALENDARS':
                        
			DBQuery("DELETE FROM ATTENDANCE_CALENDARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
			DBQuery("INSERT INTO ATTENDANCE_CALENDARS (SYEAR,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        //------------------newly added-------------------
                        DBQuery("DELETE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");
                        DBQuery("DELETE FROM CALENDAR_EVENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='$next_syear'");

                        $calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,ROLLOVER_ID FROM ATTENDANCE_CALENDARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".$next_syear."'"));
                        foreach($calendars_RET as $calendar)
                        {
                            roll_calendar($calendar['CALENDAR_ID'],$calendar['ROLLOVER_ID']);
                        }
//                      DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,BLOCK,CALENDAR_ID) SELECT SYEAR+1,SCHOOL_ID,SCHOOL_DATE+INTERVAL '1' YEAR,MINUTES,BLOCK,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ATTENDANCE_CALENDAR.CALENDAR_ID=ATTENDANCE_CALENDARS.ROLLOVER_ID) AS CAL_ID FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        DBQuery("INSERT INTO CALENDAR_EVENTS (SYEAR,SCHOOL_ID,SCHOOL_DATE,TITLE,DESCRIPTION) SELECT SYEAR+1,SCHOOL_ID,SCHOOL_DATE+INTERVAL '1' YEAR,TITLE,DESCRIPTION FROM CALENDAR_EVENTS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['ATTENDANCE_CALENDARS'].'|'.'(|'.$total_rolled_data.'|)';                                      //-------------------end--------------------------------
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
                        DBQuery("INSERT INTO SCHOOL_QUARTERS (MARKING_PERIOD_ID,SEMESTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT ".db_seq_nextval('marking_period_seq').",(SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS s WHERE s.SYEAR=q.SYEAR+1 AND s.ROLLOVER_ID=q.SEMESTER_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE+INTERVAL '1' YEAR,END_DATE+INTERVAL '1' YEAR,POST_START_DATE+INTERVAL '1' YEAR,POST_END_DATE+INTERVAL '1' YEAR,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_QUARTERS q WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        DBQuery("INSERT INTO SCHOOL_PROGRESS_PERIODS (MARKING_PERIOD_ID,QUARTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT ".db_seq_nextval('marking_period_seq').",(SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS q WHERE q.SYEAR=p.SYEAR+1 AND q.ROLLOVER_ID=p.QUARTER_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,date_add(START_DATE,INTERVAL 365 DAY),date_add(END_DATE,INTERVAL 365 DAY),date_add(POST_START_DATE,INTERVAL 365 DAY),date_add(POST_END_DATE,INTERVAL 365 DAY),DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_PROGRESS_PERIODS p WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));             
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['SCHOOL_YEARS'].'|'.'(|'.$total_rolled_data.'|)';
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
			DBQuery("INSERT INTO COURSE_PERIODS (SYEAR,SCHOOL_ID,COURSE_ID,COURSE_WEIGHT,TITLE,SHORT_NAME,PERIOD_ID,MP,MARKING_PERIOD_ID,TEACHER_ID,ROOM,TOTAL_SEATS,FILLED_SEATS,DOES_ATTENDANCE,GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,CALENDAR_ID,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,(SELECT COURSE_ID FROM COURSES c WHERE c.SYEAR=p.SYEAR+1 AND c.ROLLOVER_ID=p.COURSE_ID),COURSE_WEIGHT,TITLE,SHORT_NAME,(SELECT PERIOD_ID FROM SCHOOL_PERIODS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.PERIOD_ID),MP,".db_case(array('MP',"'FY'",'(SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)',"'SEM'",'(SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)',"'QTR'",'(SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)')).",(SELECT STAFF_ID FROM STAFF n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.TEACHER_ID),ROOM,TOTAL_SEATS,0 AS FILLED_SEATS,DOES_ATTENDANCE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES n WHERE n.ROLLOVER_ID=p.GRADE_SCALE_ID),DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS n WHERE n.ROLLOVER_ID=p.CALENDAR_ID),COURSE_PERIOD_ID FROM COURSE_PERIODS p WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        
                        //$rowq=DBGet(DBQUERY("SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS  WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'"));
                        //rollid=DBGet(DBQUERY("SELECT ROLLOVER_ID FROM COURSE_PERIODS  WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'"));
                        
                        DBQuery("UPDATE COURSE_PERIODS SET PARENT_ID=COURSE_PERIOD_ID WHERE SYEAR='$next_syear'AND SCHOOL_ID='".UserSchool()."'");
                              

                        
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['COURSES'].'|'.'(|'.$total_rolled_data.'|)';
                        break;

		case 'STUDENT_ENROLLMENT':
                   
//                        $next_start_date = DBDate();
//			DBQuery("DELETE FROM STUDENT_ENROLLMENT WHERE SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
//			// ROLL STUDENTS TO NEXT GRADE
//			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,(SELECT NEXT_GRADE_ID FROM SCHOOL_GRADELEVELS g WHERE g.ID=e.GRADE_ID),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='".UserSchool()."'");
//			// ROLL STUDENTS WHO ARE TO BE RETAINED
//			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,GRADE_ID,'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='0'");
//			// ROLL STUDENTS TO NEXT SCHOOL
//			DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,STUDENT_ID,(SELECT g.ID FROM SCHOOL_GRADELEVELS g WHERE g.SORT_ORDER=1 AND g.SCHOOL_ID=e.NEXT_SCHOOL),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL NOT IN ('".UserSchool()."','0','-1')");
    //new                                                
                                                    //DBQuery("DELETE FROM STUDENT_ENROLLMENT WHERE SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
                                                    DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,(SELECT NEXT_GRADE_ID FROM SCHOOL_GRADELEVELS g WHERE g.ID=e.GRADE_ID),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='".UserSchool()."'");
			// ROLL STUDENTS WHO ARE TO BE RETAINED
                                                    DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,GRADE_ID,'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='0'");
			// ROLL STUDENTS TO NEXT SCHOOL
                                                    DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR,SCHOOL_ID,GRADE_ID,STUDENT_ID,START_DATE,END_DATE,NEXT_SCHOOL,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,(SELECT g.ID FROM SCHOOL_GRADELEVELS g WHERE g.SORT_ORDER=1 AND g.SCHOOL_ID=e.NEXT_SCHOOL),STUDENT_ID,'$next_start_date' AS START_DATE,NULL AS END_DATE,NEXT_SCHOOL,(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,NULL,NEXT_SCHOOL FROM STUDENT_ENROLLMENT e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL NOT IN ('".UserSchool()."','0','-1')");
                                                    
                                                    DBQuery("UPDATE STUDENT_ENROLLMENT SET NEXT_SCHOOL='-1' WHERE GRADE_ID=(SELECT MAX(NEXT_GRADE_ID)FROM SCHOOL_GRADELEVELS) AND SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
                                                    DBQuery("UPDATE STUDENT_ENROLLMENT SET END_DATE='$next_start_date'-INTERVAL 1 DAY, DROP_CODE=(SELECT ID FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR='".UserSyear()."' AND TYPE='Roll') WHERE SYEAR=".  UserSyear()." AND SCHOOL_ID=".  UserSchool());
                                                    
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['STUDENT_ENROLLMENT'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
        
		case 'REPORT_CARD_GRADE_SCALES':
                         
			DBQuery("DELETE FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("DELETE FROM REPORT_CARD_GRADES WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			// DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (ID,SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID) SELECT ".db_seq_nextval('REPORT_CARD_GRADE_SCALES_SEQ')."+ID,SYEAR+1,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID,GP_SCALE) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ID,GP_SCALE FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,GRADE_SCALE_ID,SORT_ORDER) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES WHERE ROLLOVER_ID=GRADE_SCALE_ID AND SCHOOL_ID=REPORT_CARD_GRADES.SCHOOL_ID),SORT_ORDER FROM REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['REPORT_CARD_GRADE_SCALES'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
       
		case 'REPORT_CARD_COMMENTS':
                   
			DBQuery("DELETE FROM REPORT_CARD_COMMENTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,COURSE_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SORT_ORDER,".db_case(array('COURSE_ID',"''",'NULL',"(SELECT COURSE_ID FROM COURSES WHERE ROLLOVER_ID=rc.COURSE_ID)"))." FROM REPORT_CARD_COMMENTS rc WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['REPORT_CARD_COMMENTS'].'|'.'(|'.$total_rolled_data.'|)';
                     break;

		case 'ELIGIBILITY_ACTIVITIES':
                        
			DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
                        DBQuery("INSERT INTO $table (SYEAR".$columns.") SELECT SYEAR+1".$columns." FROM $table WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['ELIGIBILITY_ACTIVITIES'].'|'.'(|'.$total_rolled_data.'|)';
                      break;

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
                        
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['ATTENDANCE_CODES'].'|'.'(|'.$total_rolled_data.'|)';
                      break;

		// DOESN'T HAVE A SCHOOL_ID
		case 'STUDENT_ENROLLMENT_CODES':
                        
			//DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear'");
                        $student_enroll_rolled=DBGet(DBQuery("SELECT ID FROM $table WHERE SYEAR='$next_syear'"));
                        $total_student_enroll_rolled=count($student_enroll_rolled);
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
                        if($total_student_enroll_rolled==0){
			DBQuery("INSERT INTO $table (SYEAR".$columns.") SELECT SYEAR+1".$columns." FROM $table WHERE SYEAR='".UserSyear()."'");
                                $roll_RET=DBGet(DBQuery("SELECT ID FROM $table WHERE TYPE='Roll' AND SYEAR=$next_syear"));
                                if(!$roll_RET){
                                    DBQuery("INSERT INTO $table (SYEAR".$columns.") VALUES('$next_syear','Rolled Over','ROLL','Roll')");
                                }
                        }
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['STUDENT_ENROLLMENT_CODES'].'|'.'(|'.$total_rolled_data.'|)';
                      break;

                    case 'NONE' :

                        echo '<div style="padding-top:90px; text-align:center;"><span style="font-size:14px; font-weight:bold;">The school year has been rolled.</span><br/><br/><input type=button onclick=document.location.href="index.php?modfunc=logout" value="Please login again" class=btn_large ></div>';
						
                        unset($_SESSION['_REQUEST_vars']['tables']);
                        unset($_SESSION['_REQUEST_vars']['delete_ok']);
                        
}

function roll_calendar($calendar_id,$rollover_id)
{
    $next_y=UserSyear()+1;
    $cal_RET=DBGet(DBQuery("SELECT DATE_FORMAT(MIN(SCHOOL_DATE),'%c') AS START_MONTH,DATE_FORMAT(MIN(SCHOOL_DATE),'%e') AS START_DAY,DATE_FORMAT(MIN(SCHOOL_DATE),'%Y') AS START_YEAR,
                                    DATE_FORMAT(MAX(SCHOOL_DATE),'%c') AS END_MONTH,DATE_FORMAT(MAX(SCHOOL_DATE),'%e') AS END_DAY,DATE_FORMAT(MAX(SCHOOL_DATE),'%Y') AS END_YEAR FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID=$rollover_id"));
    $min_month=$cal_RET[1]['START_MONTH'];
    $min_day=$cal_RET[1]['START_DAY'];
    $min_year=$cal_RET[1]['START_YEAR']+1;
    $max_month=$cal_RET[1]['END_MONTH'];
    $max_day=$cal_RET[1]['END_DAY'];
    $max_year=$cal_RET[1]['END_YEAR']+1;
    $begin=mktime(0,0,0,$min_month,$min_day,$min_year)+ 43200;
    $end=mktime(0,0,0,$max_month,$max_day,$max_year)+ 43200;
    $day_RET=DBGet(DBQuery("SELECT SCHOOL_DATE FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID='$rollover_id' ORDER BY SCHOOL_DATE LIMIT 0, 7"));
    foreach ($day_RET as $day)
    {
        $weekdays[date('w',strtotime($day['SCHOOL_DATE']))]=date('w',strtotime($day['SCHOOL_DATE']));
    }
    $weekday = date('w',$begin);
    for($i=$begin;$i<=$end;$i+=86400)
    {
            if($weekdays[$weekday]!=''){
                if(is_leap_year($next_y)){
                   $previous_year_day=$i-31622400;
                }else{
                     $previous_year_day=$i-31536000;
                }
                $previous_RET=DBGet(DBQuery("SELECT COUNT(SCHOOL_DATE) AS SCHOOL FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE='".date('Y-m-d',$previous_year_day)."' AND CALENDAR_ID='$rollover_id'"));
                if($previous_RET[1]['SCHOOL']==0){
                    $prev_weekday=date('w',$previous_year_day);
                    if($weekdays[$prev_weekday]==''){
                        DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,CALENDAR_ID) values('".$next_y."','".UserSchool()."','".date('Y-m-d',$i)."','999','".$calendar_id."')");
                    }
                }else{
                    DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,CALENDAR_ID) values('".$next_y."','".UserSchool()."','".date('Y-m-d',$i)."','999','".$calendar_id."')");
                }
            }
            $weekday++;
            if($weekday==7)
                    $weekday = 0;
    }
}

function is_leap_year($year)
{
	return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
}
?>
