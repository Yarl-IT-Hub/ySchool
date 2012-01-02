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
if($_REQUEST['modfunc']=='attn')
{
    header("Location:Modules.php?modname=Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php");
}

/*
$From = $_REQUEST['From'];
 $to = $_REQUEST['to'];
 */
 
 
 if($_REQUEST['From'] && $_REQUEST['to'])
 {
 	$_SESSION['from_date'] = $_REQUEST['From'];
	$_SESSION['to_date'] = $_REQUEST['to'];
 }
 
 
 $From = $_SESSION['from_date'];
 $to = $_SESSION['to_date'];

 
 
# echo $to;
# ------------------------ Old Query It's Also Working Start ---------------------------------- #

/*
            $RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHOOLS s,STAFF st,SCHEDULE sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0 AND acc.SCHOOL_DATE>=sch.START_DATE AND acc.SCHOOL_DATE<'".DBDate()."'
        AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
        AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0
            OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
        AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));
*/

# ------------------------ Old Query It's Also Working End ---------------------------------- #



#if($_REQUEST['From'] && $_REQUEST['to'])
if($From && $to)
{
	#$queryMP = UserMP();
	#echo GetAllMP('',$queryMP);
	
	
//	$RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHOOLS s,STAFF st,SCHEDULE sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0 AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND  acc.SCHOOL_DATE>='".$From."' AND acc.SCHOOL_DATE<='".$to."' AND acc.SCHOOL_DATE>=sch.START_DATE AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE  UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE ) AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND (ac.STAFF_ID=cp.TEACHER_ID OR ac.STAFF_ID=cp.SECONDARY_TEACHER_ID) AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID AND cp.TITLE in(select cp.TITLE  FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SCHOOL_ID=".UserSchool()." AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID  AND s.SYEAR='".UserSyear()."') ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));

                    $RET = DBGET(DBQuery("SELECT s.TITLE AS SCHOOL,mi.SCHOOL_DATE,cp.TITLE, mi.COURSE_PERIOD_ID FROM MISSING_ATTENDANCE mi,COURSE_PERIODS cp,SCHOOLS s WHERE mi.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND s.ID=mi.SCHOOL_ID AND mi.SCHOOL_ID='".  UserSchool()."' AND (mi.TEACHER_ID='".  User('STAFF_ID')."' OR mi.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND mi.SCHOOL_DATE>='".$From."' AND mi.SCHOOL_DATE<'".$to."' ORDER BY cp.TITLE,mi.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));
}


if((!UserStudentID() || substr($_REQUEST['modname'],0,5)=='Users') )
        {
            $RET_Users = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
            DrawHeaderHome( 'Selected User: '.$RET_Users[1]['FIRST_NAME'].'&nbsp;'.$RET_Users[1]['LAST_NAME'].' ( <A HREF=Side.php?modname='.$_REQUEST['modname'].'&staff_id=new&From='.$From.'&to='.$to.' >Back to User List )</A>');
        }

#echo count($RET);
if (count($RET))
{
    echo '<p><center><font color=#FF0000><b>Warning!!</b></font> - Teachers have missing attendance data:</center>';

    $modname = "Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php&miss_attn=1&From=$From&to=$to";
    $link['remove']['link'] = "Modules.php?modname=$modname&modfunc=attn&username=admin";
    $link['remove']['variables'] = array('date'=>'SCHOOL_DATE','cp_id'=>'COURSE_PERIOD_ID');
	$_SESSION['miss_attn']=1;
	ListOutput_missing_attn($RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Period -Teacher','SCHOOL'=>'School'),'Period','Periods',$link,array(),array('save'=>false,'search'=>false));

    echo '</p>';
}
else
    echo '<p><center><font color=#FF0000></font>Attendance completed for this teacher:</center>';
    

?>
