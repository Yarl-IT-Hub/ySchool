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
unset($_SESSION['student_id']);

if(!$_REQUEST['modfunc'] && $_REQUEST['search_modfunc']!='list')
	unset($_SESSION['MassSchedule.php']);

if(isset($_REQUEST['per']))
	$per_status = $_REQUEST['per'];


if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='save')
{
	if($_SESSION['MassSchedule.php'])
	{
		$start_date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
		if(!VerifyDate($start_date))
			BackPrompt('The date you entered is not valid');
		$course_mp = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
		$course_mp = $course_mp[1]['MARKING_PERIOD_ID'];
		$course_mp_table = GetMPTable(GetMP($course_mp,'TABLE'));

		if($course_mp_table!='FY' && $course_mp!=$_REQUEST['marking_period_id'] && strpos(GetChildrenMP($course_mp_table,$course_mp),"'".$_REQUEST['marking_period_id']."'")===false)
		{
		ShowErr("You cannot schedule a student into that course during the marking period that you chose.  This course meets on ".GetMP($course_mp).'.');
		#for_error();
		for_error_sch();
		}
		$mp_table = GetMPTable(GetMP($_REQUEST['marking_period_id'],'TABLE'));

		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."' AND SYEAR='".UserSyear()."' AND (('".$start_date."' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL) AND '".$start_date."'>=START_DATE)"),array(),array('STUDENT_ID'));
		$request_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE_REQUESTS WHERE WITH_PERIOD_ID=(SELECT PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."') AND SYEAR='".UserSyear()."' AND COURSE_ID='".$_SESSION['MassSchedule.php']['course_id']."'"),array(),array('STUDENT_ID'));
		
		
// ----------------------------------------- Time Clash Logic Start ---------------------------------------------------------- //
		
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
			$year = substr($mother_date, 7, 4);
			$temp_month = substr($mother_date, 3, 3);
			
				if($temp_month == 'JAN')
					$month = '-01-';
				elseif($temp_month == 'FEB')
					$month = '-02-';
				elseif($temp_month == 'MAR')
					$month = '-03-';
				elseif($temp_month == 'APR')
					$month = '-04-';
				elseif($temp_month == 'MAY')
					$month = '-05-';
				elseif($temp_month == 'JUN')
					$month = '-06-';
				elseif($temp_month == 'JUL')
					$month = '-07-';
				elseif($temp_month == 'AUG')
					$month = '-08-';
				elseif($temp_month == 'SEP')
					$month = '-09-';
				elseif($temp_month == 'OCT')
					$month = '-10-';
				elseif($temp_month == 'NOV')
					$month = '-11-';
				elseif($temp_month == 'DEC')
					$month = '-12-';
					
				$day = substr($mother_date, 0, 2);
				
				$select_date = $year.$month.$day;
				return $select_date;
			
		}
                $convdate = con_date($start_date);
		$course_per_id = $_SESSION['MassSchedule.php']['course_period_id'];
		$per_id = DBGet(DBQuery("SELECT PERIOD_ID, DAYS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID = $course_per_id"));
		$period_id = $per_id[1]['PERIOD_ID'];
		$days = $per_id[1]['DAYS'];
		$day_st_count = strlen($days);
		
                        $st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id AND (IGNORE_SCHEDULING IS NULL OR IGNORE_SCHEDULING!='Y')"));    /********* for homeroom scheduling*/
                        if($st_time)
                        {
                            $start_time = $st_time[1]['START_TIME'];
                            $min_start_time = get_min($start_time);
                            $end_time = $st_time[1]['END_TIME'];
                            $min_end_time = get_min($end_time);
                        }
// ----------------------------------------- Time Clash Logic End ---------------------------------------------------------- //		
		
		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
                    
                     # ------------------------------------ PARENT RESTRICTION STARTS----------------------------------------- #
                                    $pa_RET=DBGet(DBQuery("SELECT PARENT_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
                                    if($pa_RET[1]['PARENT_ID']!=$_SESSION['MassSchedule.php']['course_period_id'])
                                    {
                                        $stu_pa=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHEDULE WHERE STUDENT_ID='$student_id' AND COURSE_PERIOD_ID='".$pa_RET[1]['PARENT_ID']."' AND DROPPED='N' AND START_DATE<='".date('Y-m-d',strtotime($start_date))."'"));
                                        $par_sch=count($stu_pa);
                                        if($par_sch<1 || (strtotime(DBDate())<strtotime($stu_pa[$par_sch]['START_DATE']) && $stu_pa[$par_sch]['START_DATE']!="") || (strtotime(DBDate())>strtotime($stu_pa[$par_sch]['END_DATE']) && $stu_pa[$par_sch]['END_DATE']!=""))
                                        {
                                             $select_stu_RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
                                                                $select_stu = $select_stu_RET[1]['FIRST_NAME']."&nbsp;".$select_stu_RET[1]['LAST_NAME'];
                                                                $parent_res .= $select_stu."<br>";
                                                                continue;
                                        }
                                         
                                    }
                                    
            
                                    # ------------------------------------ PARENT RESTRICTION ENDS----------------------------------------- #
			if($_SESSION['MassSchedule.php']['gender']!='N')
                                                     {
                                                            $select_stu_RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,LEFT(GENDER,1) AS GENDER FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
                                                            if($_SESSION['MassSchedule.php']['gender']!=$select_stu_RET[1]['GENDER']){
                                                                $select_stu = $select_stu_RET[1]['FIRST_NAME']."&nbsp;".$select_stu_RET[1]['LAST_NAME'];
                                                                $gender_conflict .= $select_stu."<br>";
                                                                continue;
                                                            }
                                                            #$clash = true;
                                                     }
                         # ------------------------------------ Same Days Conflict Start ------------------------------------------ #
			$mp_RET = DBGet(DBQuery("SELECT MP,MARKING_PERIOD_ID,DAYS,PERIOD_ID,MARKING_PERIOD_ID,TOTAL_SEATS,COALESCE(FILLED_SEATS,0) AS FILLED_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
                        $mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);
                        $period_RET = DBGet(DBQuery("SELECT cp.DAYS FROM SCHEDULE s,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.STUDENT_ID='".$student_id."' AND cp.PERIOD_ID='".$mp_RET[1]['PERIOD_ID']."' AND s.MARKING_PERIOD_ID IN (".$mps.") AND (s.END_DATE IS NULL OR '".$convdate."'<=s.END_DATE)"));
                        $ig_scheld=DBGet(DBQuery("SELECT IGNORE_SCHEDULING FROM SCHOOL_PERIODS WHERE PERIOD_ID='".$mp_RET[1]['PERIOD_ID']."' AND SCHOOL_ID='".UserSchool()."'"));
                        $sql_dupl = "SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE STUDENT_ID = ".$student_id." AND COURSE_PERIOD_ID = ".$_SESSION['MassSchedule.php']['course_period_id']." AND (END_DATE IS NULL OR ('".$convdate."' BETWEEN START_DATE AND END_DATE)) AND SCHOOL_ID='".UserSchool()."'";
                        $rit_dupl = DBQuery($sql_dupl);
                        $count_entry = mysql_num_rows($rit_dupl);
                        $days_conflict = false;
                        if($count_entry<1 && $ig_scheld[1]['IGNORE_SCHEDULING']!='Y')
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
                        if($count_entry>=1)
                            $days_conflict = true;
                        if($days_conflict)
                        {
                            $select_stu_RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
                            $select_stu = $select_stu_RET[1]['FIRST_NAME']."&nbsp;".$select_stu_RET[1]['LAST_NAME'];
                            $period_res .= $select_stu."<br>";
                            continue;
			
                        }
                        # ------------------------------------ Same Days Conflict End ------------------------------------------ #
			$sql = "SELECT COURSE_PERIOD_ID,START_DATE, MARKING_PERIOD_ID FROM SCHEDULE WHERE STUDENT_ID = ".$student_id." AND SCHOOL_ID='".UserSchool()."' AND MARKING_PERIOD_ID IN (".$mps.") AND (END_DATE IS NULL OR END_DATE >'".$convdate."')";
			$xyz = mysql_query($sql);
			$coue_p_id = mysql_num_rows($xyz);
//			$cp_id = $coue_p_id[0];
//			$st_dt = $coue_p_id[1];
//			$mp_id_stu = $coue_p_id[2];
//			$start_date;
//			$convdate = con_date($start_date);
			if($coue_p_id>=1)
			{
                            while($coue_p_id = mysql_fetch_array($xyz))
                            {
                                $min_sel_start_time="";
                                $min_sel_end_time="";
                                $cp_id = $coue_p_id[0];
                                $st_dt = $coue_p_id[1];
                                $mp_id_stu = $coue_p_id[2];
                                
			# --------------------------------- For Duplicate Entry Start -------------------------------------- #
			#$sql_dupl = "SELECT * FROM SCHEDULE WHERE STUDENT_ID = ".$student_id." AND COURSE_PERIOD_ID = ".$_SESSION['MassSchedule.php']['course_period_id']." AND END_DATE IS NULL OR ('".$convdate."' BETWEEN START_DATE AND END_DATE) AND SCHOOL_ID='".UserSchool()."'";
//			$sql_dupl = "SELECT course_period_id FROM SCHEDULE WHERE STUDENT_ID = ".$student_id." AND COURSE_PERIOD_ID = ".$_SESSION['MassSchedule.php']['course_period_id']." AND (END_DATE IS NULL OR ('".$convdate."' BETWEEN START_DATE AND END_DATE)) AND SCHOOL_ID='".UserSchool()."'";
//			$rit_dupl = mysql_query($sql_dupl);
//			$course_fetch = mysql_fetch_array($rit_dupl);
//			$course_p_id = $course_fetch['course_period_id'];
//			$count_entry = mysql_num_rows($rit_dupl);
			
			
			# --------------------------------- For Duplicate Entry Start -------------------------------------- #
			
//				if($count_entry >= 1)
//				{
//					$select_stu = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
//					$select_stu = $select_stu[1]['FIRST_NAME']."&nbsp;".$select_stu[1]['LAST_NAME'];
//					$clash .= $select_stu."<br>";
//					#$clash = true;
//				}
//				else
//				{
					# -------------------------------------- #
                                
//					$sql_drop = "SELECT END_DATE FROM SCHEDULE WHERE STUDENT_ID = ".$student_id." AND COURSE_PERIOD_ID = ".$_SESSION['MassSchedule.php']['course_period_id']." ORDER BY START_DATE DESC LIMIT 0,1" ;
//					$rit_drop = mysql_query($sql_drop);
//					$course_fetch_drop = mysql_fetch_array($rit_drop);
//					$course_p_end_dt = $course_fetch_drop['END_DATE'];
//					if(isset($course_p_end_dt) && ($convdate > $course_p_end_dt))
//					{
//							// ------------------------------------------------------- //
//
//							# -------------------- Manual OverRide -------------------------- #
//
//							$check_seats = DBGet(DBQuery("SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
//							$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
//
//							# -------------------- Manual OverRide -------------------------- #
//							if($check_seats > 0)
//							{
//								$sql = "INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE)
//													values('".UserSyear()."','".UserSchool()."','".clean_param($student_id,PARAM_INT)."','".$_SESSION['MassSchedule.php']['course_id']."','".$_SESSION['MassSchedule.php']['course_period_id']."','".$mp_table."','".clean_param($_REQUEST['marking_period_id'],PARAM_INT)."','".$start_date."')";
//								DBQuery($sql);
//								DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'");
//								$request_exists = false;
//								$note = "That course has been added to the selected students' schedules.";
//                                                                break;
//                                                         }
//                                                         else
//                                                         {
//
//								$no_seat = 'There is no available seats in this period.<br>';
//
//								$no_seat .= '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=seats&course_period_id=".$_SESSION['MassSchedule.php']['course_period_id']."\",\"\",\"scrollbars=no,status=no,screenX=500,screenY=500,resizable=no,width=500,height=200\");'style=\"text-decoration:none;\"><strong><input type=button class=btn_large value='Manual Override'></strong></A></TD></TR>";
//
//							 }
//
//							// ------------------------------------------------------- //
//					}
//					else
//					{
                                            
					# -------------------------------------- #
					$sel_per_id = DBGet(DBQuery("SELECT PERIOD_ID, DAYS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID = $cp_id"));
					$sel_period_id = $sel_per_id[1]['PERIOD_ID'];
					$sel_days = $sel_per_id[1]['DAYS'];
					
					$ignore_existing_period_ret=DBGet(DBQuery("SELECT IGNORE_SCHEDULING FROM SCHOOL_PERIODS WHERE PERIOD_ID=".$sel_period_id));
                                        $ignore_existing_period=$ignore_existing_period_ret[1]['IGNORE_SCHEDULING'];
                                        if($ignore_existing_period =='Y')
                                            $sel_period_id='';
                                         if($sel_period_id)
                                         {
                                            $sel_st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $sel_period_id"));
                                            $sel_start_time = $sel_st_time[1]['START_TIME'];
                                            $min_sel_start_time = get_min($sel_start_time);
                                            $sel_end_time = $sel_st_time[1]['END_TIME'];
                                            $min_sel_end_time = get_min($sel_end_time);
                                         }
					# ---------------------------- Days conflict ------------------------------------ #
//					if($course_mp == $mp_id_stu)
//					{
						$j = 0;
						for($i=0; $i<$day_st_count; $i++)
						{
							$clip = substr($days, $i, 1);
							$pos = strpos($sel_days, $clip);
							if($pos !== false)
								$j++;
						}
//					}
					
					# ---------------------------- Days conflict ------------------------------------ #
					if($j != 0)
					{				
                                            $time_clash_found = 0;
                                            if((($min_sel_start_time <= $min_start_time) && ($min_sel_end_time >= $min_start_time)) && isset ($min_sel_start_time) || (($min_sel_start_time <= $min_end_time) && ($min_sel_end_time >= $min_end_time) && isset ($min_sel_start_time)) || (($min_sel_start_time >= $min_start_time) && ($min_sel_end_time <= $min_end_time) && isset ($min_start_time)))
						{
							$select_stu = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
							$select_stu = $select_stu[1]['FIRST_NAME']."&nbsp;".$select_stu[1]['LAST_NAME'];
							$time_clash .= $select_stu."<br>";
							$time_clash_found = 1;
							break;
						}
//						else
//						{
//			// ------------------------------------------------------- //
//
//			# -------------------- Manual OverRide -------------------------- #
//
//							$check_seats = DBGet(DBQuery("SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
//							$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
//
//							# -------------------- Manual OverRide -------------------------- #
//
//
//							if($check_seats > 0)
//							{
//								$sql = "INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE)
//													values('".UserSyear()."','".UserSchool()."','".clean_param($student_id,PARAM_INT)."','".$_SESSION['MassSchedule.php']['course_id']."','".$_SESSION['MassSchedule.php']['course_period_id']."','".$mp_table."','".clean_param($_REQUEST['marking_period_id'],PARAM_INT)."','".$start_date."')";
//								DBQuery($sql);
//								DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'");
//								$request_exists = false;
//								$note = "That course has been added to the selected students' schedules.";
//                                                                break;
//
//                                                        }
//							else
//							{
//
//								$no_seat = 'There is no available seats in this period.<br>';
//
//								$no_seat .= '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=seats&course_period_id=".$_SESSION['MassSchedule.php']['course_period_id']."\",\"\",\"scrollbars=no,status=no,screenX=500,screenY=500,resizable=no,width=500,height=200\");'style=\"text-decoration:none;\"><strong><input type=button class=btn_large value='Manual Override'></strong></A></TD></TR>";
//
//							}
//
//							// ------------------------------------------------------- //
//						}
						
					}
                                        //}
                                        }
					if($j==0 || $time_clash_found==0)
					{
					
							// ------------------------------------------------------- //
							
							# -------------------- Manual OverRide -------------------------- #
							
							$check_seats = DBGet(DBQuery("SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
							$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
							
							# -------------------- Manual OverRide -------------------------- #
							
							
							if($check_seats > 0)
							{
								$sql = "INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY)
													values('".UserSyear()."','".UserSchool()."','".clean_param($student_id,PARAM_INT)."','".$_SESSION['MassSchedule.php']['course_id']."','".$_SESSION['MassSchedule.php']['course_period_id']."','".$mp_table."','".clean_param($_REQUEST['marking_period_id'],PARAM_INT)."','".$start_date."','".date('Y-m-d')."','".User('STAFF_ID')."')";
								DBQuery($sql);
								DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'");
								$request_exists = false;
								$note = "That course has been added to the selected students' schedules.";	
                                                                //break;
                                                                
                                                        }
							else
							{
							
								$no_seat = 'There is no available seats in this period.<br>';
								
								$no_seat .= '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=seats&course_period_id=".$_SESSION['MassSchedule.php']['course_period_id']."\",\"\",\"scrollbars=no,status=no,screenX=500,screenY=500,resizable=no,width=500,height=200\");'style=\"text-decoration:none;\"><strong><input type=button class=btn_large value='Manual Override'></strong></A></TD></TR>";
							
							}						
							
							// ------------------------------------------------------- //
					
					}
					
				//}
				
                        
			}
			else
			{
				
						# -------------------- Manual OverRide -------------------------- #
						
					$check_seats = DBGet(DBQuery("SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
					$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
						
						# -------------------- Manual OverRide -------------------------- #
				if($check_seats > 0)
				{
					$sql = "INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY)
											values('".UserSyear()."','".UserSchool()."','".clean_param($student_id,PARAM_INT)."','".$_SESSION['MassSchedule.php']['course_id']."','".$_SESSION['MassSchedule.php']['course_period_id']."','".$mp_table."','".clean_param($_REQUEST['marking_period_id'],PARAM_INT)."','".$start_date."','".date('Y-m-d')."','".User('STAFF_ID')."')";
					DBQuery($sql);
					DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'");
					$request_exists = false;
					$note = "That course has been added to the selected students' schedules.";
				}
				else
				{
					
					$no_seat = 'There is no available seats in this period.<br>';
							
					$no_seat .= '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=seats&course_period_id=".$_SESSION['MassSchedule.php']['course_period_id']."\",\"\",\"scrollbars=no,status=no,screenX=500,screenY=500,resizable=no,width=500,height=200\");'style=\"text-decoration:none;\"><strong><input type=button class=btn_large value='Manual Override'></strong></A></TD></TR>";
						
				}						
			}
			
		
		
		
		
		}
                UpdateMissingAttendance($_SESSION['MassSchedule.php']['course_period_id']);
		unset($_REQUEST['modfunc']);
		unset($_SESSION['MassSchedule.php']);
	}
	else
	{
		//BackPrompt('You must choose a Course');
		ShowErr('You must choose a Course');
		#for_error();
		for_error_sch();
	}
		
}
if($_REQUEST['modfunc']!='choose_course')
{
	DrawBC("Scheduling > ".ProgramTitle());
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM name=sav id=sav action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		#DrawHeader('',SubmitButton('Add Course to Selected Students'));
		PopTable_wo_header ('header');
		echo '<TABLE><TR><TD>Course to Add</TD><TD><DIV id=course_div>';
		if($_SESSION['MassSchedule.php'])
		{
			$course_title = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='".$_SESSION['MassSchedule.php']['course_id']."'"));
			$course_title = $course_title[1]['TITLE'];
			$period_title = DBGet(DBQuery("SELECT TITLE FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
			$period_title = $period_title[1]['TITLE'];

			echo "$course_title - $_REQUEST[course_weight]<BR>$period_title";
		}
		echo '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
		echo '<TR><TD>Start Date</TD><TD>'.PrepareDate(DBDate(),'').'</TD></TR>';

		echo '<TR><TD>Marking Period</TD><TD>';
		//echo '<SELECT name=marking_period_id><OPTION value=0>Full Year</OPTION>';
		$years_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		$semesters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		$quarters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		echo '<SELECT name=marking_period_id id=marking_period><OPTION value='.$years_RET[1]['MARKING_PERIOD_ID'].'>'.$years_RET[1]['TITLE'].'</OPTION>';
		foreach($semesters_RET as $mp)
			echo "<OPTION value=$mp[MARKING_PERIOD_ID]>".$mp['TITLE'].'</OPTION>';
		foreach($quarters_RET as $mp)
			echo "<OPTION value=$mp[MARKING_PERIOD_ID]>".$mp['TITLE'].'</OPTION>';
		echo '</SELECT>';
		echo '</TD></TR>';
		echo '</TABLE>';
		PopTable ('footer');
	}

	if($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);
        if($gender_conflict)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$gender_conflict.' have Gender Clash');
        if($parent_res)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$parent_res.' have Parent course restriction');
	if($period_res)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$period_res.' have already scheduled in that period');
        if($time_clash)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$time_clash.' have a period time clash');
        if($check_seats<=0 && $no_seat)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$no_seat);
//	elseif($clash)
//		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$clash." is already in the schedule");
        elseif($request_exists)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$request_clash.' already have unscheduled requests');
	
                  
#for_error_sch();
}
if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']!='list')
		unset($_SESSION['MassSchedule.php']);
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Widgets('course');
	Widgets('request');
	Widgets('activity');

	Search_GroupSchedule('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
            if($_SESSION['count_stu']!=0)
		echo '<BR><CENTER>'.SubmitButton('','','class=btn_group_schedule onclick=\'formload_ajax("sav");\'').'</CENTER>';
		echo "</FORM>";
	}

}

if($_REQUEST['modfunc']=='choose_course')
{

	if(!$_REQUEST['course_period_id'])
		include 'modules/Scheduling/CoursesforWindow.php';
	else
	{
		$_SESSION['MassSchedule.php']['subject_id'] = $_REQUEST['subject_id'];
		$_SESSION['MassSchedule.php']['course_id'] = $_REQUEST['course_id'];
		//$_SESSION['MassSchedule.php']['course_weight'] = $_REQUEST['course_weight'];
		$_SESSION['MassSchedule.php']['course_period_id'] = $_REQUEST['course_period_id'];

		$course_title = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='".$_SESSION['MassSchedule.php']['course_id']."'"));
		$course_title = $course_title[1]['TITLE'];
		$period_title_RET = DBGet(DBQuery("SELECT TITLE,MARKING_PERIOD_ID,GENDER_RESTRICTION FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
		$period_title = $period_title_RET[1]['TITLE'];
		$mperiod = $period_title_RET[1]['MARKING_PERIOD_ID'];
                                    $gender_res=$period_title_RET[1]['GENDER_RESTRICTION'];
                                    $_SESSION['MassSchedule.php']['gender']=$gender_res;
                                    if($gender_res=='N')
		echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title<BR>$period_title\";opener.document.getElementById(\"marking_period\").value=\"$mperiod\"; window.close();</script>";
                                    else
                                        echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title <BR>$period_title <br>Gender : ".($gender_res=='M'?'Male':'Female')." \";opener.document.getElementById(\"marking_period\").value=\"$mperiod\"; window.close();</script>";
               
	}
}

if($_REQUEST['modfunc']=='seats')
	{
	
	if($_REQUEST['tables']['COURSE_PERIODS'][$_REQUEST['course_period_id']]['TOTAL_SEATS']!='' && $_REQUEST['update'])
		{
			DBQuery("UPDATE COURSE_PERIODS SET TOTAL_SEATS='".$_REQUEST['tables']['COURSE_PERIODS'][$_REQUEST['course_period_id']]['TOTAL_SEATS']."' WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'");
		}
	if($_REQUEST['course_period_id'])
		{			
				$sql = DBGet(DBQuery("SELECT TOTAL_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
				$RET = $sql[1]; 
		}
	echo '<div align=center>'; 
	echo '<form name=update_seats id=update_seats method=POST action=for_window.php?modname='.$_REQUEST['modname'].'&modfunc=seats&course_period_id='.$_REQUEST['course_period_id'].'&update=true>';
 	echo '<TABLE><TR>';
	echo '<td colspan=2 align=center><b>Click on the number of seats to edit and click update</b></td></tr><tr><td colspan=2 align=center><table><tr><td>Total Seats</td><td>:</td><td>' . TextInput($RET['TOTAL_SEATS'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][TOTAL_SEATS]','','size=10 class=cell_floating maxlength=5').'</td></tr></table>';
    echo '</TR><tr><td colspan=2 align=center><input type=submit value=Update class=btn_medium></td></tr></TABLE>';
	echo '</form>';
	echo '</div>';
	}



function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

		return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}
function _str_split($str)
{
	$ret = array();
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
		$ret [] = substr($str,$i,1);
	return $ret;
}



?>
