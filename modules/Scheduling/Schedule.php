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
// TABBED FY,SEM,QTR
// REPLACE DBDate() & date() WITH USER ENTERED VALUES
// ERROR HANDLING
include('../../Redirect_modules.php');
DrawBC("Scheduling >> ".ProgramTitle());

Widgets('activity');
Widgets('course');
Widgets('request');

if(!$_SESSION['student_id']){
Search('student_id',$extra);
}
####################

if(isset($_REQUEST['student_id']) )
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM STUDENTS,STUDENT_ENROLLMENT WHERE STUDENTS.STUDENT_ID='".$_REQUEST['student_id']."' AND STUDENT_ENROLLMENT.STUDENT_ID = STUDENTS.STUDENT_ID "));
	//$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname=Scheduling/Schedule.php&search_modfunc=list&next_modname=Scheduling/Schedule.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');



//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) ');
        }

	//echo '<div align="left" style="padding-left:16px"><b>Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'</b></div>';
}
####################


if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
else
{
	$min_date = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS MIN_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
	{
		$date = $min_date[1]['MIN_DATE'];
		$_REQUEST['day_date'] = date('d',strtotime($date));
		$_REQUEST['month_date'] = strtoupper(date('M',strtotime($date)));
		$_REQUEST['year_date'] = date('y',strtotime($date));
                 $first_visit='yes';
	}
	else
	{
		$_REQUEST['day_date'] = date('d');
		$_REQUEST['month_date'] = strtoupper(date('M'));
		$_REQUEST['year_date'] = date('y');
		$date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date'];
                $first_visit='yes';
	}
}

if($_REQUEST['month_schedule'] && ($_POST['month_schedule']||$_REQUEST['ajax']))
{
	foreach($_REQUEST['month_schedule'] as $id=>$start_dates)
	foreach($start_dates as $start_date=>$columns)
	{
		foreach($columns as $column=>$value)
		{
			$_REQUEST['schedule'][$id][$start_date][$column] = $_REQUEST['day_schedule'][$id][$start_date][$column].'-'.$value.'-'.$_REQUEST['year_schedule'][$id][$start_date][$column];
			if($_REQUEST['schedule'][$id][$start_date][$column]=='--')
				$_REQUEST['schedule'][$id][$start_date][$column] = '';
		}
	}
	unset($_REQUEST['month_schedule']);
	unset($_REQUEST['day_schedule']);
	unset($_REQUEST['year_schedule']);
	unset($_SESSION['_REQUEST_vars']['month_schedule']);
	unset($_SESSION['_REQUEST_vars']['day_schedule']);
	unset($_SESSION['_REQUEST_vars']['year_schedule']);
	$_POST['schedule'] = $_REQUEST['schedule'];
}

if($_REQUEST['schedule'] && ($_POST['schedule'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['schedule'] as $course_period_id=>$start_dates){
	foreach($start_dates as $start_date=>$columns)
	{
       	$sql = "UPDATE SCHEDULE SET ";
		foreach($columns as $column=>$value)
		{
                 $value= paramlib_validation($column,$value);
                if($column==END_DATE)
                {
$prev_scheduler_lock = DBGet(DBQuery("SELECT SCHEDULER_LOCK FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND START_DATE='".date('Y-m-d',strtotime($start_date))."'"))   ;

		 if($column==END_DATE && str_replace("\'","''",$value)=='')
			$sql .= $column."=NULL,";
                else if($column==END_DATE && str_replace("\'","''",$value)!='' && $prev_scheduler_lock[1]['SCHEDULER_LOCK']!='Y')
                $sql .= $column."='".str_replace("\'","''",$value)."',";
                else
                    echo "This Schedule is locked,dropped date can not be changed.";
                }

                else
              $sql .= $column."='".str_replace("\'","''",$value)."',";
                }
		
                if($columns['START_DATE'] || $columns['END_DATE'] || $columns['MARKING_PERIOD_ID'])
                {
                 $sql.= MODIFIED_DATE."='".DBDate()."',";
                $sql.= MODIFIED_BY."='".User('STAFF_ID')."',";
                }
               
	  $sql = substr($sql,0,-1) . " WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND START_DATE='".date('Y-m-d',strtotime($start_date))."'";
        DBQuery($sql);

	################################# Start of Filled seats update code ###############################

			$start_end_RET = DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND END_DATE<=CURRENT_DATE AND COURSE_PERIOD_ID='".$course_period_id."'"));

			if(count($start_end_RET))
			{
				$end_null_RET = DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND END_DATE IS NULL"));
					if(!count($end_null_RET)){
					/*DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS-1 WHERE COURSE_PERIOD_ID='".$course_period_id."'");*/
					DBQuery("CALL SEAT_COUNT()");
					}
			}

	################################# End of Filled seats update code ###############################

			if($columns['END_DATE'])
			DBQuery("DELETE FROM ATTENDANCE_PERIOD WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND SCHOOL_DATE > '".$columns['END_DATE']."'");
	}
                  UpdateMissingAttendance($course_period_id);
        }
	DBQuery("CALL SEAT_FILL()");
	unset($_SESSION['_REQUEST_vars']['schedule']);
	unset($_REQUEST['schedule']);
}

if(UserStudentID() && $_REQUEST['modfunc']!='choose_course' )
{
	echo "<FORM name=modify id=modify action=Modules.php?modname=$_REQUEST[modname]&modfunc=modify METHOD=POST>";

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['include_inactive']);

    ##################################################################

    $years_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));

  $semesters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));

  $uarters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));

  #$quarters_RET= DBGet(DBQuery("SELECT TITLE FROM SCHOOL_QUARTERS SQ  UNION SELECT TITLE FROM SCHOOL_SEMESTERS SS WHERE SS.SYEAR='".UserSyear()."' AND SS.SCHOOL_ID='".UserSchool()."' "));

 #  $quarters_RET= DBGet(DBQuery("SELECT SY.MARKING_PERIOD_ID,SY.TITLE, NULL AS SEMESTER_ID, SQ.MARKING_PERIOD_ID,SQ.TITLE,SQ.SEMESTER_ID AS SEMESTER_ID FROM SCHOOL_YEARS SY,SCHOOL_QUARTERS SQ WHERE SY.SYEAR='".UserSyear()."' AND SY.SCHOOL_ID='".UserSchool()."' AND SQ.SYEAR='".UserSyear()."' AND SQ.SCHOOL_ID='".UserSchool()."' "));

#$mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'2'  FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'1' FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'0' FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY 3,4"));

$mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,1 AS TBL FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,2 AS TBL FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,3 AS TBL FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY TBL,SORT_ORDER"));


  # $mp = CreateSelect($mp_RET, 'marking_period_id', 'Show All', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id=');
  
  $mp = CreateSelect($mp_RET, 'marking_period_id', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id=', $_REQUEST['marking_period_id']);



    ###################################################################3



	DrawHeaderHome(PrepareDateSchedule($date,'_date',false,array('submit'=>true)).' <INPUT type=checkbox name=include_inactive value=Y'.($_REQUEST['include_inactive']=='Y'?" CHECKED onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=\";'":" onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=Y\";'").'>Include Inactive Courses : &nbsp;  Marking Period :  '.$mp.' &nbsp;',SubmitButton('Save','','class=btn_medium onclick=\'formload_ajax("modify");\''));

	$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	$sql = "SELECT
				s.COURSE_ID,s.COURSE_PERIOD_ID,
				s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,s.MODIFIED_DATE,s.MODIFIED_BY,
				UNIX_TIMESTAMP(s.START_DATE) AS START_EPOCH,UNIX_TIMESTAMP(s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,
				cp.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
				c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
				s.STUDENT_ID,ROOM,DAYS,SCHEDULER_LOCK,CONCAT(st.LAST_NAME, ' ' ,st.FIRST_NAME) AS MODIFIED_NAME
			FROM COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHEDULE s
                        LEFT JOIN STAFF st ON s.MODIFIED_BY = st.STAFF_ID
			WHERE
			 s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
				AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID
				AND s.STUDENT_ID='".UserStudentID()."'
				AND s.SYEAR='".UserSyear()."'";
                            $sql.=" AND s.SCHOOL_ID = '".UserSchool()."'";
                               # if($_REQUEST['include_inactive']!='Y' && $first_visit!='yes')
                            if($_REQUEST['include_inactive']!='Y'){
                                $sql .= " AND ('".date('Y-m-d',strtotime($date))."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND s.START_DATE<='".date('Y-m-d',strtotime($date))."')) ";
                            }

                     if(clean_param($_REQUEST['marking_period_id'],PARAM_INT)){
                            $mp_id=$_REQUEST['marking_period_id'];
                     }

                     if(!isset($_REQUEST['marking_period_id'])){
                         $mp_id=UserMP();
                     }
                    $sql .=' AND s.MARKING_PERIOD_ID IN ('.GetAllMP(GetMPTable(GetMP($mp_id,'TABLE')),$mp_id).')'; 
                   $sql .= " ORDER BY sp.SORT_ORDER,s.MARKING_PERIOD_ID";

	$QI = DBQuery($sql);
	$schedule_RET = DBGet($QI,array('TITLE'=>'_makeTitle','PERIOD_PULLDOWN'=>'_makePeriodSelect','COURSE_MARKING_PERIOD_ID'=>'_makeMPSelect','SCHEDULER_LOCK'=>'_makeLock','START_DATE'=>'_makeDate','END_DATE'=>'_makeDate','MODIFIED_DATE'=>'_makeDate'));
    $link['add']['link'] = "# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");' ";
	$link['add']['title'] = "Add a Course";

	$columns = array('TITLE'=>'Course','PERIOD_PULLDOWN'=>'Period - Teacher','ROOM'=>'Room','DAYS'=>'Days of Week','COURSE_MARKING_PERIOD_ID'=>'Term','SCHEDULER_LOCK'=>'<IMG SRC=assets/locked.gif border=0>','START_DATE'=>'Enrolled','END_DATE'=>'Dropped','MODIFIED_NAME'=>'Modified By','MODIFIED_DATE'=>'Modified Date');
	$days_RET = DBGet(DBQuery("SELECT DISTINCT DAYS FROM COURSE_PERIODS"));
	if(count($days_RET)==1)
		unset($columns['DAYS']);
	if($_REQUEST['_openSIS_PDF'])
		unset($columns['SCHEDULER_LOCK']);

	VerifySchedule($schedule_RET);
	echo '<div style="width:820px; overflow:auto; overflow-x:scroll; padding-bottom:8px;">';
	ListOutput($schedule_RET,$columns,'Course','Courses',$link);
	echo '</div>';

	if(!$schedule_RET)
	echo '';
	else
	{
	    DrawHeader( "<table><tr><td>&nbsp;&nbsp;</td><td>". (ProgramLinkforExport('Scheduling/PrintSchedules.php','<img src=assets/print.png>','&modfunc=save&st_arr[]='.UserStudentID().'&mp_id='.$mp_id.'&include_inactive='.$_REQUEST['include_inactive'].'&_openSIS_PDF=true target=_blank'))."</td><td>". (ProgramLinkforExport('Scheduling/PrintSchedules.php','Print Schedule','&modfunc=save&st_arr[]='.UserStudentID().'&mp_id='.$mp_id.'&include_inactive='.$_REQUEST['include_inactive'].'&_openSIS_PDF=true target=_blank'))."</td></tr></table>");
	    echo '<BR><CENTER>'.SubmitButton('Save','','class=btn_medium onclick=\'formload_ajax("modify");\'').'</CENTER>';
	}

	echo '</FORM>';
	echo "<div class=break></div>";

	if(AllowEdit())
	{
		unset($_REQUEST);
		$_REQUEST['modname'] = 'Scheduling/Schedule.php';
		$_REQUEST['search_modfunc'] = 'list';
		$extra['link']['FULL_NAME']['link'] = 'Modules.php?modname=Scheduling/Requests.php';
		$extra['link']['FULL_NAME']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID');
		include('modules/Scheduling/UnfilledRequests.php');
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='choose_course')
{
    
	if(!$_REQUEST['course_period_id'])
		include "modules/Scheduling/CoursesforWindow.php";
	else
	{
                                    # ------------------------------------ GENDER RESTRICTION STARTS----------------------------------------- #
                                    $cp_RET=DBGet(DBQuery("SELECT GENDER_RESTRICTION FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                                    $stu_Gen=DBGet(DBQuery("SELECT LEFT(GENDER,1) AS GENDER FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
                                    $stu_Gender=$stu_Gen[1]['GENDER'];
                                    if($cp_RET[1]['GENDER_RESTRICTION']!="N" && $stu_Gender!=$cp_RET[1]['GENDER_RESTRICTION'])
                                    {
                                          $warnings[] = 'There is gender restriction.';
                                    }
                                    # ------------------------------------ GENDER RESTRICTION ENDS----------------------------------------- #
                                    else
                                    {
                                        # ------------------------------------ PARENT RESTRICTION STARTS----------------------------------------- #
                                    $pa_RET=DBGet(DBQuery("SELECT PARENT_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                                    if($pa_RET[1]['PARENT_ID']!=$_REQUEST['course_period_id'])
                                    {
                                        $stu_pa=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$pa_RET[1]['PARENT_ID']."' AND DROPPED='N' AND START_DATE<='".date('Y-m-d')."'"));
                                        $par_sch=count($stu_pa);
                                        if($par_sch<1 || (strtotime(DBDate())<strtotime($stu_pa[$par_sch]['START_DATE']) && $stu_pa[$par_sch]['START_DATE']!="") || (strtotime(DBDate())>strtotime($stu_pa[$par_sch]['END_DATE']) && $stu_pa[$par_sch]['END_DATE']!=""))
                                        {
                                             $warnings[] = 'Your are not scheduled in the parent course of this course period';
                                        }
                                         
                                    }
                                    
            
                                    # ------------------------------------ PARENT RESTRICTION ENDS----------------------------------------- #
               
		$min_date = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS MIN_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
			$date = $min_date[1]['MIN_DATE'];
		else
			$date = DBDate();
                                    
		$mp_RET = DBGet(DBQuery("SELECT MP,MARKING_PERIOD_ID,DAYS,PERIOD_ID,MARKING_PERIOD_ID,TOTAL_SEATS,COALESCE(FILLED_SEATS,0) AS FILLED_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
		$mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);

		if(is_numeric($mp_RET[1]['TOTAL_SEATS']) && $mp_RET[1]['TOTAL_SEATS']<=$mp_RET[1]['FILLED_SEATS'])
			$warnings[] = 'That section is already full.';

		# ------------------------------------ Same Days Conflict Start ----------------------------------------- #

		$period_RET = DBGet(DBQuery("SELECT cp.DAYS FROM SCHEDULE s,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.STUDENT_ID='".UserStudentID()."' AND cp.PERIOD_ID='".$mp_RET[1]['PERIOD_ID']."' AND s.MARKING_PERIOD_ID IN (".$mps.") AND (s.END_DATE IS NULL OR '".DBDate()."'<=s.END_DATE)"));
		$days_conflict = false;
		foreach($period_RET as $existing)
		{
			if(strlen($mp_RET[1]['DAYS'])+strlen($existing['DAYS'])>7)
			{
				$days_conflict = true;
				break;
			}
			else
				foreach(_str_split($mp_RET[1]['DAYS']) as  $i)
                                                                        if(strpos($existing['DAYS'],$i)!==false)
                                                                        {
                                                                                $days_conflict = true;
                                                                                break 2;
                                                                        }
		}
		if($days_conflict)
			$warnings[] = 'There is already a course scheduled in that period.';


		# ------------------------------------ Same Days Conflict End ------------------------------------------ #

		# ------------------------------------ Time Clash Conflict Start ----------------------------------- #

					# ------------------------ Functions Start ----------------------------- #
							function get_min($time)
							{
								$org_tm = $time;
								$stage = substr($org_tm,-2);
								$main_tm = substr($org_tm,0,5);
								$main_tm = trim($main_tm);
								$sp_time = split(':',$main_tm);
								$hr = $sp_time[0];
								$min = $sp_time[1];
								if($hr == 12)
								{
									$hr = $hr;
								}
								else
								{
									if($stage == 'AM')
										$hr = $hr;
									if($stage == 'PM')
										$hr = $hr + 12;
								}

								$time_min = (($hr * 60) + $min);
								return $time_min;
							}

							function con_date($date)
							{
								$mother_date = $date;
								$year = substr($mother_date, 2, 2);
								$temp_month = substr($mother_date, 5, 2);

									if($temp_month == '01')
										$month = 'JAN';
									elseif($temp_month == '02')
										$month = 'FEB';
									elseif($temp_month == '03')
										$month = 'MAR';
									elseif($temp_month == '04')
										$month = 'APR';
									elseif($temp_month == '05')
										$month = 'MAY';
									elseif($temp_month == '06')
										$month = 'JUN';
									elseif($temp_month == '07')
										$month = 'JUL';
									elseif($temp_month == '08')
										$month = 'AUG';
									elseif($temp_month == '09')
										$month = 'SEP';
									elseif($temp_month == '10')
										$month = 'OCT';
									elseif($temp_month == '11')
										$month = 'NOV';
									elseif($temp_month == '12')
										$month = 'DEC';

									$day = substr($mother_date, 8, 2);

									$select_date = $day.'-'.$month.'-'.$year;
									return $select_date;
							}
					# ------------------------ Functions End ----------------------------- #
                                    $full_period = DBGET(DBQuery("SELECT IGNORE_SCHEDULING,PERIOD_ID FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND IGNORE_SCHEDULING='Y'"));
                                    $FULL_PERIOD=$full_period[1]['IGNORE_SCHEDULING'];
                                    $block_period=$full_period[1]['PERIOD_ID'];

                                    $periods_list .= ",'".$block_period."'";
                                    $periods_list = '('.substr($periods_list,1).')';


		$course_per_id = clean_param($_REQUEST['course_period_id'],PARAM_INT);
		$per_id = DBGet(DBQuery("SELECT PERIOD_ID, DAYS, MARKING_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID = $course_per_id"));
		$period_id = $per_id[1]['PERIOD_ID'];
		$days = $per_id[1]['DAYS'];
		$day_st_count = strlen($days);
		$mp_id = $per_id[1]['MARKING_PERIOD_ID'];


		#$st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id"));
                                    if($FULL_PERIOD)
                                        $st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id AND PERIOD_ID NOT IN $periods_list"));    /********* for homeroom scheduling*/
                                    else
                                        $st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id "));    /********* for homeroom scheduling*/
                                    $start_time = $st_time[1]['START_TIME'];
		$min_start_time = get_min($start_time);
		$end_time = $st_time[1]['END_TIME'];
		$min_end_time = get_min($end_time);


			#$sql = "SELECT COURSE_PERIOD_ID,START_DATE FROM SCHEDULE WHERE STUDENT_ID = ".UserStudentID()." AND (END_DATE IS NULL OR END_DATE>CURRENT_DATE) AND SCHOOL_ID='".UserSchool()."' AND MARKING_PERIOD_ID='".$mp_id."'";
			// edited 7.12.2009
 $child_mpid = GetAllMP($mp_id);
 

  //$sql = "SELECT COURSE_PERIOD_ID,START_DATE FROM SCHEDULE WHERE STUDENT_ID = ".UserStudentID()." AND (END_DATE IS NULL OR END_DATE>CURRENT_DATE) AND SCHOOL_ID='".UserSchool()."' AND COURSE_PERIOD_ID NOT IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS CP,SCHOOL_PERIODS SP WHERE CP.PERIOD_ID=SP.PERIOD_ID AND SP.IGNORE_SCHEDULING != '')  AND MARKING_PERIOD_ID='".$mp_id."'";

  $sql = "SELECT COURSE_PERIOD_ID,START_DATE FROM SCHEDULE WHERE STUDENT_ID = ".UserStudentID()." AND (END_DATE IS NULL OR END_DATE>CURRENT_DATE) AND SCHOOL_ID='".UserSchool()."' AND COURSE_PERIOD_ID NOT IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS CP,SCHOOL_PERIODS SP WHERE CP.PERIOD_ID=SP.PERIOD_ID AND SP.IGNORE_SCHEDULING != '')  AND MARKING_PERIOD_ID IN($child_mpid)";
                                   $xyz = mysql_query($sql);
                                    $time_clash_conflict = false;
                                    while($coue_p_id = mysql_fetch_array($xyz))
                                    {
                                            $cp_id = $coue_p_id[0];
                                            $st_dt = $coue_p_id[1];
                                            $convdate = con_date($st_dt);
                                                                                    $sel_per_id = DBGet(DBQuery("SELECT COURSE_PERIOD_ID, PERIOD_ID, DAYS, MARKING_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID = $cp_id"));
                                                                            $sel_period_id = $sel_per_id[1]['PERIOD_ID'];
                                                                                    $sel_days = $sel_per_id[1]['DAYS'];
                                                                                    $sel_mp = $sel_per_id[1]['MARKING_PERIOD_ID'];
                                                                                    $sel_cp = $sel_per_id[1]['COURSE_PERIOD_ID'];
                                                                                    $sel_st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $sel_period_id"));
                                                                                    if($sel_st_time)
                                                                                    {
                                                                                        $sel_start_time = $sel_st_time[1]['START_TIME'];
                                                                                        $min_sel_start_time = get_min($sel_start_time);
                                                                                        $sel_end_time = $sel_st_time[1]['END_TIME'];
                                                                                        $min_sel_end_time = get_min($sel_end_time);
                                                                                        # ---------------------------- Days conflict ------------------------------------ #
                                                                                        $j = 0;
                                                                                        for($i=0; $i<$day_st_count; $i++)
                                                                                        {
                                                                                                $clip = substr($days, $i, 1);
                                                                                                $pos = strpos($sel_days, $clip);
                                                                                                if($pos !== false)
                                                                                                        $j++;
                                                                                        }
                                                                                        # ---------------------------- Days conflict ------------------------------------ #
                                                                                        if($j != 0)
                                                                                        {
                                                                                                if((($min_sel_start_time <= $min_start_time) && ($min_sel_end_time >= $min_start_time)) || (($min_sel_start_time <= $min_end_time) && ($min_sel_end_time >= $min_end_time)) || (($min_sel_start_time >= $min_start_time) && ($min_sel_end_time <= $min_end_time)))
                                                                                                {
                                                                                                        $time_clash_conflict = true;
                                                                                                        break;
                                                                                                }
                                                                                                else
                                                                                                {
                                                                                                        $time_clash_conflict = false;
                                                                                                }
                                                                                        }
                                                                                    }
                                    }
		if($time_clash_conflict)
                                        $warnings[] = 'There is a period time clash.';
		# ------------------------------------ Time Clash Conflict End ----------------------------------------- #
                                    }
		if(!$warnings)
		{
                                            DBQuery("INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".$date."','".User('STAFF_ID')."','".clean_param($_REQUEST['course_id'],PARAM_INT)."','".clean_param($_REQUEST['course_period_id'],PARAM_INT)."','".clean_param($mp_RET[1]['MP'],PARAM_ALPHA)."','".clean_param($mp_RET[1]['MARKING_PERIOD_ID'],PARAM_INT)."')");
                                            DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".clean_param($_REQUEST['course_period_id'],PARAM_INT)."'");
                                            UpdateMissingAttendance($_REQUEST['course_period_id']);
                                            echo "<script language=javascript>opener.document.location = 'Modules.php?modname=".clean_param($_REQUEST['modname'],PARAM_NOTAGS)."&time=".time()."'; window.close();</script>";
		}
		elseif($warnings)
		{
			if(PromptCourseWarning('Confirm','There is a conflict. You cannot add this course period.',ErrorMessage($warnings,'note')))
			{
				DBQuery("INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".$date."','".User('STAFF_ID')."','".clean_param($_REQUEST['course_id'],PARAM_INT)."','".clean_param($_REQUEST['course_period_id'],PARAM_INT)."','".clean_param($mp_RET[1]['MP'],PARAM_ALPHA)."','".clean_param($mp_RET[1]['MARKING_PERIOD_ID'],PARAM_INT)."')");
				DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".clean_param($_REQUEST['course_period_id'],PARAM_INT)."'");
				echo "<script language=javascript>opener.document.location = 'Modules.php?modname=".clean_param($_REQUEST['modname'],PARAM_NOTAGS)."&time=".time()."'; window.close();</script>";
			}
		}
	}
}

function _makeTitle($value,$column='')
{	global $_openSIS,$THIS_RET;

	return $value;//.' - '.$THIS_RET['COURSE_WEIGHT'];
}

function _makeLock($value,$column)
{	global $THIS_RET;

	if($value=='Y')
		$img = 'locked';
	else
		$img = 'unlocked';

	return '<IMG SRC=assets/'.$img.'.gif '.(AllowEdit()?'onclick="if(this.src.indexOf(\'assets/locked.gif\')!=-1) {this.src=\'assets/unlocked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'\';} else {this.src=\'assets/locked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'Y\';}"':'').'><INPUT type=hidden name=schedule['.$THIS_RET['COURSE_PERIOD_ID'].']['.$THIS_RET['START_DATE'].'][SCHEDULER_LOCK] id=lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].' value='.$value.'>';
}

function _makePeriodSelect($course_period_id,$column='')
{	global $_openSIS,$THIS_RET,$fy_id;

	$sql = "SELECT cp.COURSE_PERIOD_ID,cp.PARENT_ID,cp.TITLE,cp.MARKING_PERIOD_ID,COALESCE(cp.TOTAL_SEATS-cp.FILLED_SEATS,0) AS AVAILABLE_SEATS FROM COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE sp.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_ID='$THIS_RET[COURSE_ID]' ORDER BY sp.SORT_ORDER";
	$QI = DBQuery($sql);
	$orders_RET = DBGet($QI);

	foreach($orders_RET as $value)
	{
		if($value['COURSE_PERIOD_ID']!=$value['PARENT_ID'])
		{
			$parent = DBGet(DBQuery("SELECT SHORT_NAME FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$value['PARENT_ID']."'"));
			$parent = $parent[1]['SHORT_NAME'];
		}
		$periods[$value['COURSE_PERIOD_ID']] = $value['TITLE'] . (($value['MARKING_PERIOD_ID']!=$fy_id && $value['COURSE_PERIOD_ID']!=$course_period_id)?' ('.GetMP($value['MARKING_PERIOD_ID']).')':'').($value['COURSE_PERIOD_ID']!=$course_period_id?' ('.$value['AVAILABLE_SEATS'].' seats)':'').(($value['COURSE_PERIOD_ID']!=$course_period_id && $parent)?' -> '.$parent:'');
	}

	#return SelectInput($course_period_id,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][COURSE_PERIOD_ID]",'',$periods,false);
	return SelectInput_Disonclick($course_period_id,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][COURSE_PERIOD_ID]",'',$periods,false);
}

function _makeMPSelect($mp_id,$name='')
{	global $_openSIS,$THIS_RET,$fy_id;

	if(!$_openSIS['_makeMPSelect'])
	{
		$semesters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		$quarters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));

		$_openSIS['_makeMPSelect'][$fy_id][1] = array('MARKING_PERIOD_ID'=>"$fy_id",'TITLE'=>'Full Year','SEMESTER_ID'=>'');
		foreach($semesters_RET as $sem)
			$_openSIS['_makeMPSelect'][$fy_id][] = $sem;
		foreach($quarters_RET as $qtr)
			$_openSIS['_makeMPSelect'][$fy_id][] = $qtr;

		$quarters_QI = DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER");
		$quarters_indexed_RET = DBGet($quarters_QI,array(),array('SEMESTER_ID'));

		foreach($semesters_RET as $sem)
		{
			$_openSIS['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][1] = $sem;
			foreach($quarters_indexed_RET[$sem['MARKING_PERIOD_ID']] as $qtr)
				$_openSIS['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][] = $qtr;
		}

		foreach($quarters_RET as $qtr)
			$_openSIS['_makeMPSelect'][$qtr['MARKING_PERIOD_ID']][] = $qtr;
	}

	foreach($_openSIS['_makeMPSelect'][$mp_id] as $value)
		$mps[$value['MARKING_PERIOD_ID']] = $value['TITLE'];

	if($THIS_RET['MARKING_PERIOD_ID']!=$mp_id)
		$mps[$THIS_RET['MARKING_PERIOD_ID']] = '* '.$mps[$THIS_RET['MARKING_PERIOD_ID']];

	return SelectInput($THIS_RET['MARKING_PERIOD_ID'],"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][MARKING_PERIOD_ID]",'',$mps,false);
}

function _makeDate($value,$column)
{	global $THIS_RET;

	if($column=='START_DATE')
		$allow_na = false;
	else
		$allow_na = true;

	return DateInput($value,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][$column]",'',true,$allow_na);
}

function VerifySchedule(&$schedule)
{
	$conflicts = array();

	$ij = count($schedule);
	for($i=1; $i<$ij; $i++)
		for($j=$i+1; $j<=$ij; $j++)
			if(!$conflicts[$i] || !$conflicts[$j])
				if(strpos(GetAllMP(GetMPTable(GetMP($schedule[$i]['MARKING_PERIOD_ID'],'TABLE')),$schedule[$i]['MARKING_PERIOD_ID']),"'".$schedule[$j]['MARKING_PERIOD_ID']."'")!==false
				&& (!$schedule[$i]['END_EPOCH'] || $schedule[$j]['START_EPOCH']<=$schedule[$i]['END_EPOCH']) && (!$schedule[$j]['END_EPOCH'] || $schedule[$i]['START_EPOCH']<=$schedule[$j]['END_EPOCH']))
					if($schedule[$i]['COURSE_ID']==$schedule[$j]['COURSE_ID']) //&& $schedule[$i]['COURSE_WEIGHT']==$schedule[$j]['COURSE_WEIGHT'])
						$conflicts[$i] = $conflicts[$j] = true;
					else
						if($schedule[$i]['PERIOD_ID']==$schedule[$j]['PERIOD_ID'])
							if(strlen($schedule[$i]['DAYS'])+strlen($schedule[$j]['DAYS'])>7)
								$conflicts[$i] = $conflicts[$j] = true;
							else
								foreach(_str_split($schedule[$i]['DAYS']) as $k)
									if(strpos($schedule[$j]['DAYS'],$k)!==false)
									{
										$conflicts[$i] = $conflicts[$j] = true;
										break;
									}

	foreach($conflicts as $i=>$true)
		$schedule[$i]['TITLE'] = '<FONT color=red>'.$schedule[$i]['TITLE'].'</FONT>';
}

function _str_split($str)
{
	$ret = array();
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
		$ret [] = substr($str,$i,1);
	return $ret;
}



function CreateSelect($val, $name, $link='', $mpid)
	{
	 	//$html .= "<table width=600px><tr><td align=right width=45%>";
		//$html .= $cap." </td><td width=55%>";
		
		if($link!='')
		$html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
		else
		$html .= "<select name=".$name." id=".$name." >";
		
				foreach($val as $key=>$value)
				{
					
					
					if(!isset($mpid) && (UserMP() == $value[strtoupper($name)]))
						$html .= "<option selected value=".UserMP().">".$value['TITLE']."</option>";
					else
					{
						if($value[strtoupper($name)]==$_REQUEST[$name])
							$html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
						else
							$html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
					}
					
				}



		$html .= "</select>";
		return $html;
	}


?>
