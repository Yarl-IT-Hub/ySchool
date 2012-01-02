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
$cp_id = $_REQUEST['cp_id'];
if(UserStaffID() || $_REQUEST['staff_id'])
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&dt=1&pr=1 method=POST>";
	DrawBC("Users > Teacher Programs");
        ###########################################
if(UserStaffID() || $_REQUEST['staff_id'])
{
    if($_REQUEST['modfunc']!='save' && $_REQUEST[modname]!='Users/TeacherPrograms.php?include=Attendance/Missing_Attendance.php' && $_REQUEST[modname]!='Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php')
    {
	//if(UserStudentID())
	//	echo '<IMG SRC=assets/pixel_trans.gif height=2>';
        if($_REQUEST['staff_id'])
                $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".$_REQUEST['staff_id']."'"));
        else
            $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
            $count_staff_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STAFF"));
            if($count_staff_RET[1]['NUM']>1){
                DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Users/User.php&ajax=true&bottom_back=true&return_session=true target=body>Back to User List</A>');
            }else{
                DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>)');
            }
    }
}
#############################################
if($_REQUEST['include'] != 'Attendance/Missing_Attendance.php')
{

	if(!UserStaffID())
		Search('staff_id','teacher');
	else
	{
		$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
		if($profile[1]['PROFILE']!='teacher')
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			Search('staff_id','teacher');
		}
	}
}
else
{
	Search_Miss_Attn('staff_id','teacher');
}

if(UserStaffID())
{
	$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND (cp.TEACHER_ID='".UserStaffID()."' OR cp.SECONDARY_TEACHER_ID='".UserStaffID()."') ORDER BY sp.SORT_ORDER ");
	$RET = DBGet($QI);
	// get the fy marking period id, there should be exactly one fy marking period
	$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	if(isset($cp_id))
		$_REQUEST['period'] = $cp_id;



        if($_REQUEST['period'])
            $_SESSION['UserCoursePeriod'] = $_REQUEST['period'];
	
        if($_REQUEST['p_id'])
                $_SESSION['UserPeriod'] = $_REQUEST['p_id'];
        if(!UserCoursePeriod())
                $_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];
        $incl_page = $_REQUEST['include'];	
        if($incl_page == 'Grades/ProgressReports.php')
        {
            if(!$_SESSION['take_mssn_attn']){
            $period_select = "Choose Period: <SELECT name=period onChange='this.form.submit();'>";
            $period_select .= "<OPTION value='na' selected>N/A</OPTION>";
            }else{
            $period_select = "<SELECT name=period onChange='document.forms[1].submit();' style='visibility:hidden;'>";
            }
            foreach($RET as $period)
            {
                    $period_select .= "<OPTION value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['SHORT_NAME'].($period['MARKING_PERIOD_ID']!=$fy_id?' '.GetMP($period['MARKING_PERIOD_ID'],'SHORT_NAME'):'').(strlen($period['DAYS'])<5?' '.$period['DAYS']:'').' - '.$period['COURSE_TITLE']."</OPTION>";
                    if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
                    {
                            $_SESSION['UserPeriod'] = $period['PERIOD_ID'];
                    }
            }
            $period_select .= "</SELECT>";
            if(!$_REQUEST['modfunc'] && !$_REQUEST['staff_id'] && !($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc']) || $_REQUEST['pr']==1)
                DrawHeader($period_select);
            
            echo '</FORM>';
            unset($_openSIS['DrawHeader']);

            $_openSIS['allow_edit'] = AllowEdit($_REQUEST['modname']);
            $_openSIS['User'] = array(1=>array('STAFF_ID'=>UserStaffID(),'NAME'=>GetTeacher(UserStaffID()),'USERNAME'=>GetTeacher(UserStaffID(),'','USERNAME'),'PROFILE'=>'teacher','SCHOOLS'=>','.UserSchool().',','SYEAR'=>UserSyear()));

            include('modules/'.$_REQUEST['include']);

        }
        else
        {
            if($incl_page != 'Attendance/Missing_Attendance.php')
            {
                if(!$_SESSION['take_mssn_attn']){
                    $period_select = "Choose Period: <SELECT name=period onChange='this.form.submit();'>";
                    $period_select .= "<OPTION value='na' selected>N/A</OPTION>";

        //            else{
        //            $period_select = "<SELECT name=period onChange='this.form.submit();' style='visibility:hidden;'>";
        //            }
                    foreach($RET as $period)
                    {
                            $period_select .= "<OPTION value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['SHORT_NAME'].($period['MARKING_PERIOD_ID']!=$fy_id?' '.GetMP($period['MARKING_PERIOD_ID'],'SHORT_NAME'):'').(strlen($period['DAYS'])<5?' '.$period['DAYS']:'').' - '.$period['COURSE_TITLE']."</OPTION>";
                            if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
                            {
                                    $_SESSION['UserPeriod'] = $period['PERIOD_ID'];
                            }
                    }
                    $period_select .= "</SELECT>";
                }
            }
            DrawHeader($period_select);
            echo '</FORM><BR>';
            unset($_openSIS['DrawHeader']);

            $_openSIS['allow_edit'] = AllowEdit($_REQUEST['modname']);
            $_openSIS['User'] = array(1=>array('STAFF_ID'=>UserStaffID(),'NAME'=>GetTeacher(UserStaffID()),'USERNAME'=>GetTeacher(UserStaffID(),'','USERNAME'),'PROFILE'=>'teacher','SCHOOLS'=>','.UserSchool().',','SYEAR'=>UserSyear()));

            echo '<CENTER><TABLE width=100% ><TR><TD>';

            include('modules/'.$_REQUEST['include']);

            echo '</TD></TR></TABLE></CENTER>';
        }
}
?>
