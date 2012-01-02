<?php
error_reporting(0);
include('Redirect_root.php');
include 'Warehouse.php';
include 'data.php';
$syear = $_SESSION['UserSyear'];
$flag=FALSE;
$RET=DBGet(DBQuery("SELECT SCHOOL_ID,SCHOOL_DATE,COURSE_PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID FROM MISSING_ATTENDANCE WHERE SYEAR='".  UserSyear()."' LIMIT 0,1"));
 if (count($RET))
{
     $flag=TRUE;
 }
$last_update=DBGet(DBQuery("SELECT VALUE FROM PROGRAM_CONFIG WHERE PROGRAM='MissingAttendance' AND TITLE='LAST_UPDATE'"));
$last_update=$last_update[1]['VALUE'].'<br>';
DBQuery("INSERT INTO MISSING_ATTENDANCE(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cp.PERIOD_ID,cp.TEACHER_ID,cp.SECONDARY_TEACHER_ID " .
                                    "FROM ATTENDANCE_CALENDAR acc " .
                                    "INNER JOIN MARKING_PERIODS mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID " .
                                    " AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE " .
                                    "INNER JOIN COURSE_PERIODS cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.DOES_ATTENDANCE='Y' AND cp.CALENDAR_ID=acc.CALENDAR_ID " .
                                    "INNER JOIN SCHOOL_PERIODS sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cp.PERIOD_ID " .
                                    " AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 " .
                                    "   OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) " .
                                    "INNER JOIN SCHOOLS s ON s.ID=acc.SCHOOL_ID " .
                                    "INNER JOIN SCHEDULE sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.START_DATE<=acc.SCHOOL_DATE " .
                                    "AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) ".
                                    "LEFT JOIN ATTENDANCE_COMPLETED ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=sp.PERIOD_ID " .
                                    "WHERE acc.SYEAR='".$syear."'" .
                                    " AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) " .
                                    " AND acc.SCHOOL_DATE<='".date('Y-m-d')."' " .
                                                                            " AND acc.SCHOOL_DATE> '".$last_update."'".
                                                                            " AND ac.STAFF_ID IS NULL " .
                                    "GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID ");
//
DBQuery("UPDATE PROGRAM_CONFIG SET VALUE='".date('Y-m-d')."'WHERE PROGRAM='MissingAttendance' AND TITLE='LAST_UPDATE'");
$RET=DBGet(DBQuery("SELECT SCHOOL_ID,SCHOOL_DATE,COURSE_PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID FROM MISSING_ATTENDANCE WHERE SYEAR='".  UserSyear()."' LIMIT 0,1"));
 if (count($RET) && $flag==FALSE)
{
     echo '<span style="display:none">NEW_MI_YES</span>';
 }

echo '<br/><table><tr><td width="38"><img src="assets/icon_ok.png" /></td><td valign="middle"><span style="font-size:14px;">Missing attendance data list created. Please continue with your work.</span></td></tr></table>';

?>
