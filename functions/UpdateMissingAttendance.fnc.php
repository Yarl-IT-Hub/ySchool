<?php
function UpdateMissingAttendance($course_period_id)
{
    DBQuery("DELETE FROM MISSING_ATTENDANCE WHERE COURSE_PERIOD_ID='".$course_period_id."'");
    
    DBQuery("INSERT INTO MISSING_ATTENDANCE(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cp.PERIOD_ID,cp.TEACHER_ID,cp.SECONDARY_TEACHER_ID " .
				"FROM ATTENDANCE_CALENDAR acc " .
				"INNER JOIN MARKING_PERIODS mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID " .
				" AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE " .
				"INNER JOIN COURSE_PERIODS cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.DOES_ATTENDANCE='Y' AND cp.CALENDAR_ID=acc.CALENDAR_ID " .
                                                                        " AND cp.COURSE_PERIOD_ID='".$course_period_id."'".
				"INNER JOIN SCHOOL_PERIODS sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cp.PERIOD_ID " .
				" AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 " .
				"   OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) " .
				"INNER JOIN SCHOOLS s ON s.ID=acc.SCHOOL_ID " .
				"INNER JOIN STAFF st ON (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) " .
				"INNER JOIN SCHEDULE sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.START_DATE<=acc.SCHOOL_DATE " .
                                                                        "AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) ".
				"LEFT JOIN ATTENDANCE_COMPLETED ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=sp.PERIOD_ID " .
				"WHERE acc.SYEAR='".UserSyear()."' AND acc.SCHOOL_ID='".  UserSchool()."'" .
				" AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) " .
				" AND st.STAFF_ID='".User('STAFF_ID')."' " .
				" AND acc.SCHOOL_DATE<='".date('Y-m-d',strtotime(DBDate()))."' " .
                                                                        " AND ac.STAFF_ID IS NULL " .
				"GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID");

}

?>
