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

while(!UserSyear())
{
    session_write_close();
    session_start();
}

$current_hour = date('H');

if($_SESSION['LAST_LOGIN'])

$welcome .= 'User: '. User('NAME').' | Last login: '.ProperDate(substr($_SESSION['LAST_LOGIN'],0,10)).' at ' .substr($_SESSION['LAST_LOGIN'],10);
//if($_REQUEST['failed_login'])
if($_SESSION['FAILED_LOGIN'])
$welcome .= ' | <span class=red >'.$_SESSION['FAILED_LOGIN'].'</b> failed login attempts</span>';

//----------------------------------------Update Missing Attendance_________________________________-

echo '<div id="calculating" style="display: none; padding-top:20px; padding-bottom:15px;"><img src="assets/missing_attn_loader.gif" /><br/><br/><br/><span style="color:#c90000;"><span style=" font-size:15px; font-weight:bold;">Please wait.</span><br /><span style=" font-size:12px;">Compiling missing attendance data. Do not click anywhere.</span></span></div>
<div id="resp" style="font-size:14px"></div>';

//-----------------------------------------Update missing attendance ends--------------------------------------------------


switch (User('PROFILE'))
{
    case 'admin':
        DrawBC ($welcome.' | Role: Administrator');
        
            $last_update=DBGet(DBQuery("SELECT VALUE FROM PROGRAM_CONFIG WHERE PROGRAM='MissingAttendance' AND TITLE='LAST_UPDATE'"));
            if($last_update[1]['VALUE']!='')
            {
                if($last_update[1]['VALUE'] < date('Y-m-d'))
                {
                    echo '<script type=text/javascript>calculate_missing_atten();</script>';
                }
            }
        
        $notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,CONCAT('<B>',pn.TITLE,'</B>') AS TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s,STAFF st WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND st.STAFF_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR FIND_IN_SET(pn.SCHOOL_ID,st.SCHOOLS)>0) AND (st.PROFILE_ID IS NULL AND FIND_IN_SET('admin', pn.PUBLISHED_PROFILES)>0 OR st.PROFILE_ID IS NOT NULL AND FIND_IN_SET(st.PROFILE_ID,pn.PUBLISHED_PROFILES)>0) AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

        if(count($notes_RET))
        {
            echo '<div>';
            ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note','SCHOOL'=>'School'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
            echo '</div>';
        }

        $events_RET = DBGet(DBQuery("SELECT ce.TITLE,ce.DESCRIPTION,ce.SCHOOL_DATE,s.TITLE AS SCHOOL FROM CALENDAR_EVENTS ce,SCHOOLS s,STAFF st WHERE ce.SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL 30 DAY AND ce.SYEAR='".UserSyear()."' AND st.STAFF_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR FIND_IN_SET(ce.SCHOOL_ID,st.SCHOOLS)>0) AND s.ID=ce.SCHOOL_ID ORDER BY ce.SCHOOL_DATE,s.TITLE"),array('SCHOOL_DATE'=>'ProperDate'));

        if(count($events_RET))
        {
            echo '<p>';
            ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description','SCHOOL'=>'School'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }

        # ------------------------------------ Original Raw Query Start ------------------------------------------------ #

               if(Preferences('HIDE_ALERTS')!='Y')
        {
            // warn if missing attendance
	$RET=DBGet(DBQuery("SELECT SCHOOL_ID,SCHOOL_DATE,COURSE_PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID FROM MISSING_ATTENDANCE WHERE SYEAR='".  UserSyear()."' AND SCHOOL_DATE<'".date('Y-m-d')."' LIMIT 0,1 "));

          if (count($RET))
          {
                echo '<p><font color=#FF0000><b>Warning!! - Teachers have missing attendance. Go to : Users -> Teacher Programs -> Missing Attendance</b></font></p>';
          }
        }
        echo '<div id="attn_alert" style="display: none" ><p><font color=#FF0000><b>Warning!! - Teachers have missing attendance. Go to : Users -> Teacher Programs -> Missing Attendance</b></font></p></div>';
        //-------------------------------------------------------------------------------ROLLOVER NOTIFICATION STARTS----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        
       $notice_date=DBGet(DBQuery("SELECT END_DATE FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
        $notice_roll_date=DBGet(DBQuery("SELECT SYEAR FROM SCHOOL_YEARS WHERE SYEAR>'".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
        $rolled=count($notice_roll_date);
        $last_date=strtotime($notice_date[1]['END_DATE'])-strtotime(DBDate());
        $last_date=$last_date/(60*60*24);
        if($last_date<=15 && $rolled==0 )
        {
            echo '<p><font color=#FF0000><b>Rollover required...</b></font></p>';
        }
        //-------------------------------------------------------------------------------ROLLOVER NOTIFICATION ENDS----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

       /* if($openSISModules['Food_Service'] && Preferences('HIDE_ALERTS')!='Y')
        {
            // warn if negative food service balance
            $staff = DBGet(DBQuery('SELECT (SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.User('STAFF_ID')));
            $staff = $staff[1];
            if($staff['BALANCE'] && $staff['BALANCE']<0)
            echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have a <b>negative</b> food service balance of <font color=#FF0000>'.$staff['BALANCE'].'</font></p>';

            // warn if students with way low food service balances
            $extra['SELECT'] = ',fssa.STATUS,fsa.BALANCE';
            $extra['FROM'] = ',FOOD_SERVICE_ACCOUNTS fsa,FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
            $extra['WHERE'] = ' AND fssa.STUDENT_ID=s.STUDENT_ID AND fsa.ACCOUNT_ID=fssa.ACCOUNT_ID AND fssa.STATUS IS NULL AND fsa.BALANCE<\'-10\'';
            $_REQUEST['_search_all_schools'] = 'Y';
            $RET = GetStuList($extra);
            if (count($RET))
            {
                echo '<p><font color=#FF0000><b>Warning!!</b></font> - Students have food service balances below -$10.00:';
                ListOutput($RET,array('FULL_NAME'=>'Student','GRADE_ID'=>'Grade','BALANCE'=>'Balance'),'Student','Students',array(),array(),array('save'=>false,'search'=>false));
                echo '</p>';
            }
        }
*/
        break;

    case 'teacher':
        DrawBC ($welcome.' | Role: Teacher');

            $last_update=DBGet(DBQuery("SELECT VALUE FROM PROGRAM_CONFIG WHERE PROGRAM='MissingAttendance' AND TITLE='LAST_UPDATE'"));
            if($last_update[1]['VALUE']!='')
            {
                if($last_update[1]['VALUE'] < date('Y-m-d'))
                {
                    echo '<script type=text/javascript>calculate_missing_atten();</script>';
                }
            }
        
       // echo '<table width="100%" cellpadding="4" cellspacing="3"><tr><td>';
        $notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,pn.TITLE AS TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s,STAFF st WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND st.STAFF_ID='".User('STAFF_ID')."' AND position(pn.SCHOOL_ID IN st.SCHOOLS)>0 AND (st.SCHOOLS IS NULL OR position(pn.SCHOOL_ID IN st.SCHOOLS)>0) AND (st.PROFILE_ID IS NULL AND position(',teacher,' IN pn.PUBLISHED_PROFILES)>0 OR st.PROFILE_ID IS NOT NULL AND position(st.PROFILE_ID IN pn.PUBLISHED_PROFILES)>0) AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

        if(count($notes_RET))
        {
            echo '<p>';
            ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note','SCHOOL'=>'School'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }

        $events_RET = DBGet(DBQuery("SELECT ce.TITLE,ce.DESCRIPTION,ce.SCHOOL_DATE,s.TITLE AS SCHOOL FROM CALENDAR_EVENTS ce,SCHOOLS s WHERE ce.SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND ce.SYEAR='".UserSyear()."' AND position(ce.SCHOOL_ID IN (SELECT SCHOOLS FROM STAFF WHERE STAFF_ID='".User('STAFF_ID')."'))>0 AND s.ID=ce.SCHOOL_ID ORDER BY ce.SCHOOL_DATE,s.TITLE"),array('SCHOOL_DATE'=>'ProperDate'));

        if(count($events_RET))
        {
            echo '<p>';
            ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description','SCHOOL'=>'School'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }
        echo '<div id="attn_alert" style="display: none" ><p><font color=#FF0000><b>Warning!! - Teachers have missing attendance. Go to : Users -> Teacher Programs -> Missing Attendance</b></font></p></div>';
        if(Preferences('HIDE_ALERTS')!='Y')
        {
            // warn if missing attendance
            /*$RET = DBGET(DBQuery("SELECT DISTINCT acc.SCHOOL_DATE,cp.TITLE FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHEDULE sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND acc.SCHOOL_DATE<'".DBDate()."' AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0 AND acc.SCHOOL_DATE>=sch.START_DATE AND acc.SCHOOL_DATE<'".DBDate()."' AND cp.TEACHER_ID='".User('STAFF_ID')."'
        AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
        AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0
            OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
        AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));
            if (count($RET))
            {
                echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have missing attendance data:';
               $modname = 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php';
    $link['remove']['link'] = "Modules.php?modname=$modname&modfunc=attn";
    $link['remove']['variables'] = array('date'=>'SCHOOL_DATE','cp_id'=>'COURSE_PERIOD_ID');
	
   ListOutput_missing_attn_teach_port($RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Period -Teacher'),'Period','Periods',$link,array(),array('save'=>false,'search'=>false));
                echo '</p>';
            }*/
			if($_REQUEST['modfunc']=='attn')
{
    header("Location:Modules.php?modname=Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php");
}

/*$RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHOOLS s,STAFF st,SCHEDULE sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND cp.TEACHER_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0 AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND acc.SCHOOL_DATE>=sch.START_DATE AND acc.SCHOOL_DATE<'".DBDate()."' AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID and cp.TITLE in(select cp.TITLE  FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID  AND s.SYEAR='".UserSyear()."') ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));*/

//$RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.PERIOD_ID FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHOOLS s,STAFF st,SCHEDULE sch WHERE acc.SYEAR='".UserSyear()."' AND acc.SCHOOL_ID='".UserSchool()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0 AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND acc.SCHOOL_DATE>=sch.START_DATE AND acc.SCHOOL_DATE<'".DBDate()."' AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND (ac.STAFF_ID=cp.TEACHER_ID OR ac.STAFF_ID=cp.SECONDARY_TEACHER_ID) AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID and cp.TITLE in(select cp.TITLE  FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID  AND s.SYEAR='".UserSyear()."') ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));

$RET=DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,mi.SCHOOL_DATE,cp.TITLE AS TITLE,mi.COURSE_PERIOD_ID,mi.PERIOD_ID 
    FROM MISSING_ATTENDANCE mi,SCHOOLS s,COURSE_PERIODS cp WHERE s.ID=mi.SCHOOL_ID AND  cp.COURSE_PERIOD_ID=mi.COURSE_PERIOD_ID AND (mi.TEACHER_ID='".User('STAFF_ID')."' OR mi.SECONDARY_TEACHER_ID='".  User('STAFF_ID')."' ) AND mi.SCHOOL_DATE < '".DBDate()."' ORDER BY cp.TITLE,mi.SCHOOL_DATE "),array('SCHOOL_DATE'=>'ProperDate'));

#echo count($RET);
if (count($RET))
{
    echo '<p><center><font color=#FF0000><b>Warning!!</b></font> - Teachers have missing attendance data:</center>';

    $modname = 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php';
    $link['remove']['link'] = "Modules.php?modname=$modname&modfunc=attn&attn=miss";
    $link['remove']['variables'] = array('date'=>'SCHOOL_DATE','cp_id'=>'COURSE_PERIOD_ID','p_id'=>'PERIOD_ID');
	$_SESSION['take_mssn_attn']=true;
   ListOutput_missing_attn_teach_port($RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Period -Teacher','SCHOOL'=>'School'),'Period','Periods',$link,array(),array('save'=>false,'search'=>false));
   

    echo '</p>';
}
        }

    /*    if($openSISModules['Food_Service'] && Preferences('HIDE_ALERTS')!='Y')
        {
            // warn if negative food service balance
            $staff = DBGet(DBQuery('SELECT (SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.User('STAFF_ID')));
            $staff = $staff[1];
            if($staff['BALANCE'] && $staff['BALANCE']<0)
            echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have a <b>negative</b> food service balance of <font color=#FF0000>'.$staff['BALANCE'].'</font></p>';
        }

*/

	break;

    case 'parent':
        DrawBC ($welcome.' | Role: Parent');

        $notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,pn.TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s,STAFF st WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND st.STAFF_ID='".User('STAFF_ID')."' AND pn.SCHOOL_ID IN (SELECT DISTINCT SCHOOL_ID FROM STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=pn.SYEAR AND se.STUDENT_ID=sju.STUDENT_ID AND se.START_DATE<=CURRENT_DATE AND (se.END_DATE>=CURRENT_DATE OR se.END_DATE IS NULL)) AND (st.SCHOOLS IS NULL OR position(concat(',', pn.SCHOOL_ID, ',') IN st.SCHOOLS)>0) AND (st.PROFILE_ID IS NULL AND position(',parent,' IN pn.PUBLISHED_PROFILES)>0 OR st.PROFILE_ID IS NOT NULL AND position(concat(',', st.PROFILE_ID, ',') IN pn.PUBLISHED_PROFILES)>0) AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

        if(count($notes_RET))
        {
            echo '<p>';
            ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note','SCHOOL'=>'School'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }

        $events_RET = DBGet(DBQuery("SELECT ce.TITLE,ce.SCHOOL_DATE,ce.DESCRIPTION,s.TITLE AS SCHOOL FROM CALENDAR_EVENTS ce,SCHOOLS s WHERE ce.SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND ce.SYEAR='".UserSyear()."' AND ce.SCHOOL_ID IN (SELECT DISTINCT SCHOOL_ID FROM STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=ce.SYEAR AND se.STUDENT_ID=sju.STUDENT_ID AND se.START_DATE<=CURRENT_DATE AND (se.END_DATE>=CURRENT_DATE OR se.END_DATE IS NULL)) AND s.ID=ce.SCHOOL_ID ORDER BY ce.SCHOOL_DATE,s.TITLE"),array('SCHOOL_DATE'=>'ProperDate'));

        if(count($events_RET))
        {
            echo '<p>';
            ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description','SCHOOL'=>'School'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }

       /* if($openSISModules['Food_Service'] && Preferences('HIDE_ALERTS')!='Y')
        {
            // warn if students with low food service balances
            $extra['SELECT'] = ',fssa.STATUS,fsa.ACCOUNT_ID, concat(\'$\', fsa.BALANCE) AS BALANCE, concat(\'$\', 16.5-fsa.BALANCE) AS DEPOSIT';
            $extra['FROM'] = ',FOOD_SERVICE_ACCOUNTS fsa,FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
            $extra['WHERE'] = 'AND fssa.STUDENT_ID=s.STUDENT_ID AND fsa.ACCOUNT_ID=fssa.ACCOUNT_ID AND fssa.STATUS IS NULL AND fsa.BALANCE<\'5\'';
            $extra['ASSOCIATED'] = User('STAFF_ID');
            $RET = GetStuList($extra);
            if (count($RET))
            {
                echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have students with food service balance below $5.00 - please deposit at least the Minimum Deposit into you children\'s accounts.';
                ListOutput($RET,array('FULL_NAME'=>'Student','GRADE_ID'=>'Grade','ACCOUNT_ID'=>'AccountID','BALANCE'=>'Balance','DEPOSIT'=>'Minimum Deposit'),'Student','Students',array(),array(),array('save'=>false,'search'=>false));
                echo '</p>';
            }

            // warn if negative food service balance
            $staff = DBGet(DBQuery('SELECT (SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.User('STAFF_ID')));
            $staff = $staff[1];
            if($staff['BALANCE'] && $staff['BALANCE']<0)
            echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have a <b>negative</b> food service balance of <font color=#FF0000>'.$staff['BALANCE'].'</font></p>';
        }
*/
      

#$courses_RET=  DBGet(DBQuery("SELECT c.TITLE ,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c,ATTENDANCE_CALENDAR acc WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"));
	  

$courses_RET=  DBGet(DBQuery("SELECT DISTINCT c.TITLE ,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c,ATTENDANCE_CALENDAR acc WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"));


foreach($courses_RET as $course)
	{
            $staff_id = $course['STAFF_ID'];
            $assignments_Graded = DBGet(DBQuery( "SELECT gg.STUDENT_ID,ga.ASSIGNMENT_ID,gg.POINTS,gg.COMMENT,ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg
                                                  ON (gg.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND (gg.POINTS IS NOT NULL) AND (ga.POINTS!='0' OR gg.POINTS IS NOT NULL AND gg.POINTS!='-1') ORDER BY ga.ASSIGNMENT_ID DESC"));
          
            foreach($assignments_Graded AS $assignments_Graded)
            $GRADED_ASSIGNMENT_ID[]= $assignments_Graded['ASSIGNMENT_ID'];
            $ASSIGNMENT_ID_GRADED = implode(",", $GRADED_ASSIGNMENT_ID);
           
           $GRADED_ASSIGNMENT = '( '.$ASSIGNMENT_ID_GRADED.' )';
		   
          if(count($assignments_Graded))
		  {
         $assignments_RET = DBGet(DBQuery( "SELECT ga.ASSIGNMENT_ID,ga.TITLE,ga.DESCRIPTION as COMMENT,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga
                                                 ,GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE ga.ASSIGNMENT_ID NOT IN ".$GRADED_ASSIGNMENT." AND (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND(  CURRENT_DATE>=ga.ASSIGNED_DATE OR CURRENT_DATE<=ga.ASSIGNED_DATE )AND ga.DUE_DATE IS NOT NULL AND CURRENT_DATE<=ga.DUE_DATE
                                                   AND (ga.POINTS!='0') ORDER BY ga.ASSIGNMENT_ID DESC"));
		   }
         else
		 {
          $assignments_RET = DBGet(DBQuery( "SELECT ga.ASSIGNMENT_ID,ga.TITLE,ga.DESCRIPTION as COMMENT,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga
                                                 ,GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND( CURRENT_DATE>=ga.ASSIGNED_DATE OR CURRENT_DATE<=ga.ASSIGNED_DATE)AND ga.DUE_DATE IS NOT NULL AND CURRENT_DATE<=ga.DUE_DATE
                                                   AND (ga.POINTS!='0') ORDER BY ga.ASSIGNMENT_ID DESC"));
												   
			}
     
	
               if(count($assignments_RET))
		{
			
			
			$LO_columns = array('TITLE'=>'Title','CATEGORY'=>'Category','ASSIGNED_DATE'=>'Assigned Date','DUE_DATE'=>'Due Date','COMMENT'=>'Description');

			$LO_ret = array(0=>array());
                        foreach($assignments_RET as $assignment)
			{
                        $LO_ret[] = array('TITLE'=>$assignment['TITLE'],'CATEGORY'=>$assignment['CATEGORY'],'ASSIGNED_DATE'=>$assignment['ASSIGNED_DATE'],'DUE_DATE'=>$assignment['DUE_DATE'],'COMMENT'=>$assignment['COMMENT']);
			}
                        DrawHeader('Subject - '.substr($course['TITLE'],strrpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')));
			
			unset($LO_ret[0]);
			ListOutput($LO_ret,$LO_columns,'Assignment','Assignments',array(),array(),array('center'=>false,'save'=>$_REQUEST['id']!='all','search'=>false));
		}
        }

 break;

    case 'student':
        DrawBC ($welcome.' | Role: Student');

        $notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,pn.TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND pn.SCHOOL_ID='".UserSchool()."' AND  position(',0,' IN pn.PUBLISHED_PROFILES)>0 AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

        if(count($notes_RET))
        {
            echo '<p>';
            ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }

        $events_RET = DBGet(DBQuery("SELECT TITLE,SCHOOL_DATE,DESCRIPTION FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array('SCHOOL_DATE'=>'ProperDate'));

        if(count($events_RET))
        {
            echo '<p>';
            ListOutput($events_RET,array('TITLE'=>'Event','SCHOOL_DATE'=>'Date','DESCRIPTION'=>'Description'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
            echo '</p>';
        }
		
		
$sql = "SELECT s.STAFF_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,sp.TITLE,cp.PERIOD_ID
		FROM STAFF s,COURSE_PERIODS cp,SCHOOL_PERIODS sp, ATTENDANCE_CALENDAR acc
		WHERE
			sp.PERIOD_ID = cp.PERIOD_ID AND cp.GRADE_SCALE_ID IS NOT NULL
			AND cp.TEACHER_ID=s.STAFF_ID AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE )
			AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND s.PROFILE='teacher'
			".(($_REQUEST['period'])?" AND cp.PERIOD_ID='$_REQUEST[period]'":'')."
			AND NOT EXISTS (SELECT '' FROM GRADES_COMPLETED ac WHERE ac.STAFF_ID=cp.TEACHER_ID AND ac.MARKING_PERIOD_ID='$_REQUEST[mp]' AND ac.PERIOD_ID=sp.PERIOD_ID)
		";
	  

#$courses_RET=  DBGet(DBQuery("SELECT c.TITLE ,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c,ATTENDANCE_CALENDAR acc WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"));


$courses_RET=  DBGet(DBQuery("SELECT DISTINCT c.TITLE ,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c,ATTENDANCE_CALENDAR acc WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"));




foreach($courses_RET as $course)
	{
            $staff_id = $course['STAFF_ID'];
            $assignments_Graded = DBGet(DBQuery( "SELECT gg.STUDENT_ID,ga.ASSIGNMENT_ID,gg.POINTS,gg.COMMENT,ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg
                                                  ON (gg.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND (gg.POINTS IS NOT NULL) AND (ga.POINTS!='0' OR gg.POINTS IS NOT NULL AND gg.POINTS!='-1') ORDER BY ga.ASSIGNMENT_ID DESC"));

            foreach($assignments_Graded AS $assignments_Graded)
            $GRADED_ASSIGNMENT_ID[]= $assignments_Graded['ASSIGNMENT_ID'];
            $ASSIGNMENT_ID_GRADED = implode(",", $GRADED_ASSIGNMENT_ID);

           $GRADED_ASSIGNMENT = '( '.$ASSIGNMENT_ID_GRADED.' )';
		   
		   
		   
		   
        if(count($assignments_Graded))
		{
         $assignments_RET = DBGet(DBQuery( "SELECT ga.ASSIGNMENT_ID,ga.TITLE,ga.DESCRIPTION as COMMENT,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY FROM GRADEBOOK_ASSIGNMENTS ga, GRADEBOOK_ASSIGNMENT_TYPES at                                                  WHERE ga.ASSIGNMENT_ID NOT IN ".$GRADED_ASSIGNMENT." AND (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND( CURRENT_DATE>=ga.ASSIGNED_DATE OR CURRENT_DATE<=ga.ASSIGNED_DATE)AND ga.DUE_DATE IS NOT NULL AND CURRENT_DATE<=ga.DUE_DATE
                                                   AND (ga.POINTS!='0') ORDER BY ga.ASSIGNMENT_ID DESC"));
		}
        else
		 {
          $assignments_RET = DBGet(DBQuery( "SELECT ga.ASSIGNMENT_ID,ga.TITLE,ga.DESCRIPTION as COMMENT,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga
                                                 ,GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND( CURRENT_DATE>=ga.ASSIGNED_DATE OR CURRENT_DATE<=ga.ASSIGNED_DATE)AND ga.DUE_DATE IS NOT NULL AND CURRENT_DATE<=ga.DUE_DATE
                                                   AND (ga.POINTS!='0') ORDER BY ga.ASSIGNMENT_ID DESC"));
		}


			if(count($assignments_RET))
			{
	
				
				$LO_columns = array('TITLE'=>'Title','CATEGORY'=>'Category','ASSIGNED_DATE'=>'Assigned Date','DUE_DATE'=>'Due Date','COMMENT'=>'Description');
	
				$LO_ret = array(0=>array());
				foreach($assignments_RET as $assignment)
				{
							$LO_ret[] = array('TITLE'=>$assignment['TITLE'],'CATEGORY'=>$assignment['CATEGORY'],'ASSIGNED_DATE'=>$assignment['ASSIGNED_DATE'],'DUE_DATE'=>$assignment['DUE_DATE'],'COMMENT'=>$assignment['COMMENT']);
				}
				DrawHeader('Subject - '.substr($course['TITLE'],strrpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')));
	
				unset($LO_ret[0]);
				ListOutput($LO_ret,$LO_columns,'Assignment','Assignments',array(),array(),array('center'=>false,'save'=>$_REQUEST['id']!='all','search'=>false));
			}
			
        }
		

        break;
}

function _nl2br($value,$column)
{
    return nl2br($value);
}
?>
