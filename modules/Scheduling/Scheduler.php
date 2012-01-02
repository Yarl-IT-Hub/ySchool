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

if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
{$name=$_REQUEST['year_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['day_date'];
   $date = date('Y-m-d',strtotime($name));
	
}
else
{
	$date = DBDate();
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = strtoupper(date('M'));
	$_REQUEST['year_date'] = date('y');
}
if($_REQUEST['modname']=='Scheduling/Scheduler.php' && !$_REQUEST['run'])

{

	#$function = 'Prompt';
	$function = 'Prompt_Home_Schedule';

	DrawBC("Scheduling > ".ProgramTitle());

}

else

	$function = '_returnTrue';


if($function('Confirm Scheduler Run','Are you sure you want to run the scheduler?',
    '<TABLE>
        
    <TR><TD><INPUT type=checkbox name=test_mode   value=Y onclick=showhidediv("div1",this);></TD><TD>Schedule Unscheduled Requests</TD></TR>


    <TR><TD></TD><TD><div id=div1 style=display:none>'.PrepareDateSchedule($date,'_date',false,'').'</div></TD></TR>
 
    <TR><TD><INPUT type=checkbox name=delete_mode value=Y></TD><TD>Delete Current Schedules</TD></TR>
    </TABLE>'))

{

	#echo '<BR>';

	PopTable('header','Scheduler Progress');

	echo '<CENTER><TABLE cellpadding=0 cellspacing=0><TR><TD><TABLE cellspacing=0 border=0><TR>';

	for($i=1;$i<=100;$i++)

		echo '<TD id=cell'.$i.' width=3 ></TD>';

	echo '</TR></TABLE></TD></TR></TABLE><BR><DIV id=percentDIV><IMG SRC=assets/spinning.gif> Processing Requests ... </DIV></CENTER>';

	PopTable('footer');

	ob_flush();

	flush();

	ini_set('MAX_EXECUTION_TIME',0);



	// get the fy marking period id, there should be exactly one fy marking period

	$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));

	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];



	$sql = "SELECT r.REQUEST_ID,r.STUDENT_ID,s.GENDER as GENDER,r.SUBJECT_ID,r.COURSE_ID,MARKING_PERIOD_ID,WITH_TEACHER_ID,NOT_TEACHER_ID,WITH_PERIOD_ID,NOT_PERIOD_ID,(SELECT COUNT(*) FROM COURSE_PERIODS cp2 WHERE cp2.COURSE_ID=r.COURSE_ID) AS SECTIONS

	FROM SCHEDULE_REQUESTS r,STUDENTS s,STUDENT_ENROLLMENT ssm

	WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR=r.SYEAR

	AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR ssm.END_DATE IS NULL)

	AND s.STUDENT_ID=r.STUDENT_ID AND r.SYEAR='".UserSyear()."' AND r.SCHOOL_ID='".UserSchool()."' ORDER BY SECTIONS";

	$requests_RET = DBGet(DBQuery($sql),array(),array('REQUEST_ID'));

	//print_r($requests_RET);

	if($_REQUEST['delete_mode']=='Y')

	{
	$not_delete=DBGet(DBQuery("SELECT DISTINCT SC.ID AS NOT_DEL FROM SCHEDULE SC,ATTENDANCE_PERIOD AP,STUDENT_REPORT_CARD_GRADES SRCG WHERE (SC.STUDENT_ID=AP.STUDENT_ID AND SC.COURSE_PERIOD_ID=AP.COURSE_PERIOD_ID) OR (SC.STUDENT_ID=SRCG.STUDENT_ID AND SC.COURSE_PERIOD_ID=SRCG.COURSE_PERIOD_ID)"));
	
	$notin='';
	foreach($not_delete as $value){
	$notin.=$value['NOT_DEL'].",";
	}
	if($notin!=''){
	$notin=substr($notin, 0, -1); 
                        DBQuery("DELETE FROM MISSING_ATTENDANCE WHERE COURSE_PERIOD_ID IN(SELECT COURSE_PERIOD_ID  FROM SCHEDULE WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND (SCHEDULER_LOCK!='Y' OR SCHEDULER_LOCK IS NULL OR SCHEDULER_LOCK='') AND ID NOT IN ($notin)))");
		DBQuery("DELETE FROM SCHEDULE WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND (SCHEDULER_LOCK!='Y' OR SCHEDULER_LOCK IS NULL OR SCHEDULER_LOCK='') AND ID NOT IN ($notin)");
		}else{
                        DBQuery("DELETE FROM MISSING_ATTENDANCE WHERE COURSE_PERIOD_ID IN(SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND (SCHEDULER_LOCK!='Y' OR SCHEDULER_LOCK IS NULL OR SCHEDULER_LOCK=''))");
		DBQuery("DELETE FROM SCHEDULE WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND (SCHEDULER_LOCK!='Y' OR SCHEDULER_LOCK IS NULL OR SCHEDULER_LOCK='')");
		}
                DBQuery("UPDATE PROGRAM_CONFIG SET VALUE='".date('Y-m-d')."' WHERE PROGRAM='MISSING_ATTENDANCE' AND TITLE='LAST_UPDATE'");
		// FIX THIS

		DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=0 WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'");

	}

	$count = DBGet(DBQuery("SELECT COUNT(*) AS COUNT FROM SCHEDULE WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));

//$sql = "SELECT COALESCE(PARENT_ID,COURSE_PERIOD_ID) AS PARENT_ID,COURSE_PERIOD_ID,COURSE_ID,COURSE_ID AS COURSE,GENDER_RESTRICTION,PERIOD_ID,DAYS,TEACHER_ID,MARKING_PERIOD_ID,MP,COALESCE(TOTAL_SEATS,0)-COALESCE(FILLED_SEATS,0) AS AVAILABLE_SEATS,(SELECT COUNT(*) FROM COURSE_PERIODS cp2 WHERE cp2.COURSE_ID=cp.COURSE_ID) AS SECTIONS FROM COURSE_PERIODS cp WHERE cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' ORDER BY SECTIONS,AVAILABLE_SEATS";

   $sql = "SELECT mp.PARENT_ID,mp.GRANDPARENT_ID FROM MARKING_PERIODS mp,SCHEDULE_REQUESTS sr WHERE mp.MARKING_PERIOD_ID=sr.MARKING_PERIOD_ID";
   $get_parent_id = DBGet(DBQuery($sql));
   $parent_id = $get_parent_id[1]['PARENT_ID'];
   $grand_pid = $get_parent_id[1]['GRANDPARENT_ID'];

 $sql = "SELECT COALESCE(cp.PARENT_ID,cp.COURSE_PERIOD_ID) AS PARENT_ID,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.COURSE_ID AS COURSE,cp.GENDER_RESTRICTION,cp.PERIOD_ID,cp.DAYS,cp.TEACHER_ID,cp.MARKING_PERIOD_ID,cp.MP,COALESCE(cp.TOTAL_SEATS,0)-COALESCE(cp.FILLED_SEATS,0) AS AVAILABLE_SEATS,(SELECT COUNT(*) FROM COURSE_PERIODS cp2 WHERE cp2.COURSE_ID=cp.COURSE_ID) AS SECTIONS FROM COURSE_PERIODS cp,SCHEDULE_REQUESTS sr WHERE cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND cp.MARKING_PERIOD_ID IN (sr.MARKING_PERIOD_ID,'".$parent_id."','".$grand_pid."') ORDER BY SECTIONS,AVAILABLE_SEATS";

	$cp_parent_RET = DBGet(DBQuery($sql),array(),array('PARENT_ID'));
	$sql = "SELECT COALESCE(PARENT_ID,COURSE_PERIOD_ID) AS PARENT_ID,COURSE_PERIOD_ID,COURSE_ID,COURSE_ID AS COURSE,GENDER_RESTRICTION,PERIOD_ID,DAYS,TEACHER_ID,MARKING_PERIOD_ID,MP,COALESCE(TOTAL_SEATS,0)-COALESCE(FILLED_SEATS,0) AS AVAILABLE_SEATS,(SELECT COUNT(*) FROM COURSE_PERIODS cp2 WHERE cp2.COURSE_ID=cp.COURSE_ID) AS SECTIONS FROM COURSE_PERIODS cp WHERE cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND (PARENT_ID IS NULL OR PARENT_ID=COURSE_PERIOD_ID) ORDER BY SECTIONS,AVAILABLE_SEATS";

	$cp_course_RET = DBGet(DBQuery($sql),array(),array('COURSE'));



	$mps_RET = DBGet(DBQuery("SELECT SEMESTER_ID,MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array(),array('SEMESTER_ID','MARKING_PERIOD_ID'));



	// GET FILLED/LOCKED REQUESTS

	$sql = "SELECT s.STUDENT_ID,r.REQUEST_ID,s.COURSE_PERIOD_ID,cp.PARENT_ID,s.COURSE_ID,cp.PERIOD_ID,cp.TEACHER_ID FROM SCHEDULE_REQUESTS r,SCHEDULE s,COURSE_PERIODS cp WHERE

				s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.PARENT_ID=cp.COURSE_PERIOD_ID AND r.WITH_TEACHER_ID=cp.TEACHER_ID AND

				r.SYEAR='".UserSyear()."' AND r.SCHOOL_ID='".UserSchool()."' AND s.SYEAR=r.SYEAR AND s.SCHOOL_ID=r.SCHOOL_ID

				AND s.COURSE_ID=r.COURSE_ID AND r.STUDENT_ID = s.STUDENT_ID

				AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR s.END_DATE IS NULL)";

	$QI = DBQuery($sql);

	$locked_RET = DBGet($QI,array(),array('STUDENT_ID','REQUEST_ID'));

	foreach($locked_RET as $student_id=>$courses)

	{

		foreach($courses as $request_id=>$course)

		{

			$course = $course[1];

			foreach($cp_parent_RET[$course['PARENT_ID']] as $slice)

			{

				$schedule[$student_id][$slice['PERIOD_ID']][] = $slice + array('REQUEST_ID'=>$request_id);

				$filled[$request_id] = true;

			}

		}

	}

	if(ob_get_level() == 0)

		ob_start();



	$last_percent = 0;

	$requests_count = count($requests_RET);

	foreach($requests_RET as $request_id=>$request)

	{

		// EXISTING / LOCKED COURSE

		if($locked_RET[$request[1]['STUDENT_ID']][$request[1]['REQUEST_ID']])

		{

			$completed++;

			continue;

		}



		$scheduled = _scheduleRequest($request[1]);



		if(!$scheduled)

		{

			$not_request = array();

			foreach($locked_RET[$request[1]['STUDENT_ID']] as $request_id=>$requests)

				$not_request[] = $request_id;



			$moved = _moveRequest($request[1],$not_request);



			if(!$moved)

				$unfilled[] = $request[1];

			else

				$filled[$request[1]['REQUEST_ID']] = true;

		}

		else

			$filled[$request[1]['REQUEST_ID']] = true;



		$completed++;



		$percent = round($completed*100/$requests_count,0);

		if($percent>$last_percent)

		{

			echo '<script language="javascript">'."\r";

			for($i=$last_percent+1;$i<=$percent;$i++)

				echo 'cell'.$i.'.bgColor="#'.Preferences('HIGHLIGHT').'";'."\r";

			echo 'addHTML("'.$percent.'% Done","percentDIV",true);'."\r";

			echo '</script>';

			ob_flush();

			flush();

			$last_percent = $percent;

		}

	}



	echo '<!-- '.count($unfilled).' -->';

	foreach($unfilled as $key=>$request)

	{

		$scheduled = _scheduleRequest($request[1]);



		if(!$scheduled)

		{

			$not_request = array();

			foreach($locked_RET[$request[1]['STUDENT_ID']] as $request_id=>$requests)

				$not_request[] = $request_id;



			$moved = _moveRequest($request[1],$not_request);



			if($moved)

				unset($unfilled[$key]);

		}

		else

			unset($unfilled[$key]);

	}

	echo '<!-- '.count($unfilled).' -->';



	if($_REQUEST['test_mode']=='Y')

	{

		echo '<script language="javascript">'."\r";

		echo 'addHTML("<IMG SRC=assets/spinning.gif> Saving Schedules ... ","percentDIV",true);'."\r";

		echo '</script>';

		echo str_pad(' ',4096);

		ob_flush();

		flush();



		$connection = db_start();

		//db_trans_start($connection);


		//$date = DBDate();
                $date =$date;
		foreach($schedule as $student_id=>$periods)

		{

			foreach($periods as $course_periods)

			{

				foreach($course_periods as $period_id=>$course_period)

				{

					$scount++;
                                               if(!$locked_RET[$student_id][$course_period['REQUEST_ID']] && $course_period['AVAILABLE_SEATS']!=0)

						{
                                                   
                                                    $mp_RET = DBGet(DBQuery("SELECT MP,MARKING_PERIOD_ID,DAYS,PERIOD_ID,MARKING_PERIOD_ID,TOTAL_SEATS,COALESCE(FILLED_SEATS,0) AS FILLED_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$course_period['COURSE_PERIOD_ID']."'"));
                                                    $mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);
                                                    $period_RET = DBGet(DBQuery("SELECT cp.DAYS FROM SCHEDULE s,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.STUDENT_ID='$student_id' AND cp.PERIOD_ID='".$mp_RET[1]['PERIOD_ID']."' AND s.MARKING_PERIOD_ID IN (".$mps.") AND (s.END_DATE IS NULL OR '".DBDate()."'<=s.END_DATE)"));
                                                    $days_conflict = false;
                                                    
                                                    foreach($period_RET as $existing)
                                                    {
                                                        if(strlen($mp_RET[1]['DAYS'])+strlen($existing['DAYS'])>7)
                                                        {
                                                            $days_conflict = true;
                                                            break;
                                                        }
                                                        else
                                                            foreach(str_split($mp_RET[1]['DAYS']) as  $i)
                                                                if(strpos($existing['DAYS'],$i)!==false)
                                                                {
                                                                    $days_conflict = true;                                                         
                                                                    break 2;
                                                                }
                                                    }
                                                    if(!$days_conflict)
                                                    {
							DBQuery("INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".$student_id."','".$date."','".$course_period['COURSE_ID']."','".$course_period['COURSE_PERIOD_ID']."','".$course_period['MP']."','".$course_period['MARKING_PERIOD_ID']."');");
                                                        DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$course_period['COURSE_PERIOD_ID']."'");
							DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE REQUEST_ID='".$course_period['REQUEST_ID']."'");
     
                                                     }
                                                    
						 }
                                                else
						{
                                                        echo '<!-- Bad Locked -->';
                                                        
                                                       // if($course_period['AVAILABLE_SEATS']!=0)
                                                        //DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE REQUEST_ID='".$course_period['REQUEST_ID']."'");
                                               //if($locked_RET[$student_id][$course_period['REQUEST_ID']] && $course_period['AVAILABLE_SEATS']==0)
                                                 //DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE REQUEST_ID='".$course_period['REQUEST_ID']."'");


                                                }

				}

			}

		}

		echo '<!-- Schedule Count() '.$scount.'-->';

		//echo 'Empty Courses:';
		//foreach($cp_parent_RET as $parent_id=>$course_period)

		//{

			//$course_period = $course_period[1];

			//if($course_period['AVAILABLE_SEATS']<='0')

			//	echo $course_period['COURSE_ID'].'-'.$course_period['COURSE_WEIGHT'].': '.$course_period['COURSE_PERIOD_ID'].'<BR>';

			

			//db_trans_query($connection,"UPDATE COURSE_PERIODS SET FILLED_SEATS=TOTAL_SEATS-'".$course_period['AVAILABLE_SEATS']."' WHERE PARENT_ID='".$parent_id."'");
			//DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=TOTAL_SEATS-'".$course_period['AVAILABLE_SEATS']."' WHERE PARENT_ID='".$parent_id."'");

		//}
//print_r($course_period);
		//db_trans_commit($connection);

	}



	if($_REQUEST['test_mode']!='Y' || $_REQUEST['delete_mode']=='Y')

	{

		echo '<script language="javascript">'."\r";

		echo 'addHTML("<IMG SRC=assets/spinning.gif> Optimizing ... ","percentDIV",true);'."\r";

		echo '</script>';

		echo str_pad(' ',4096);

		ob_flush();

		flush();

	}

	

	$check_request = DBGet(DBQuery("SELECT REQUEST_ID FROM SCHEDULE_REQUESTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));

	$check_request = $check_request[1]['REQUEST_ID'];

	if(count($check_request)>0)
		$warn = 'Following Students cannot be accomodated as No More Seats Available or Periods Conflict';

	//if($_REQUEST['delete_mode']=='Y' || count($check_request)==0 || $slice['AVAILABLE_SEATS']>0)
        if($_REQUEST['delete_mode']=='Y' || count($check_request)==0)
	{

		echo '<script language="javascript">'."\r";

		echo 'addHTML("<IMG SRC=assets/check.gif> <B>Done.</B>","percentDIV",true);'."\r";

		echo '</script>';

		ob_end_flush();

	}

	elseif($warn)

	{

		echo '<script language="javascript">'."\r";

		echo 'addHTML("<B><font color=red>Warning</font><br>'.$warn.'</B>","percentDIV",true);'."\r";
		echo '</script>';

		ob_end_flush();

	}

	else

	{

		echo '<script language="javascript">'."\r";

		echo 'addHTML("<B><font color=red>Error</font><br>No Seats Available</B>","percentDIV",true);'."\r";

		echo '</script>';

		ob_end_flush();

	}



	$_REQUEST['modname'] = 'Scheduling/UnfilledRequests.php';

	$_REQUEST['search_modfunc']='list';

	include('modules/Scheduling/UnfilledRequests.php');

}



function _scheduleRequest($request,$not_parent_id=false)

{	global $requests_RET,$cp_parent_RET,$cp_course_RET,$mps_RET,$schedule,$filled,$unfilled;

	$possible = array();

	if(count($cp_course_RET[$request['COURSE_ID']]))

	{

		foreach($cp_course_RET[$request['COURSE_ID']] as $course_period)

		{

			foreach($cp_parent_RET[$course_period['COURSE_PERIOD_ID']] as $slice)

			{

				// ALREADY SCHEDULED HERE

				if($slice['PARENT_ID']==$not_parent_id) {

					continue 2;

				}

					

				// NO SEATS

				if($slice['AVAILABLE_SEATS']<=0) {

				continue 2;

				}



				// SLICE VIOLATES GENDER RESTRICTION

				if($slice['GENDER_RESTRICTION']!='N' && $slice['GENDER_RESTRICTION']!=substr($request['GENDER'],0,1)) {

					continue 2;

				}

				// PARENT VIOLATES TEACHER / PERIOD REQUESTS

				if($slice['PARENT_ID']==$slice['COURSE_PERIOD_ID'] && (($request['WITH_TEACHER_ID']!='' && $slice['TEACHER_ID']!=$request['WITH_TEACHER_ID']) || ($request['WITH_PERIOD_ID'] && $slice['PERIOD_ID']!=$request['WITH_PERIOD_ID']) || ($request['NOT_TEACHER_ID'] && $slice['TEACHER_ID']==$request['NOT_TEACHER_ID']) || ($request['NOT_PERIOD_ID'] && $slice['PERIOD_ID']==$request['NOT_PERIOD_ID']))) {

					continue 2;

				}

				if(count($schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']]))

				{

					// SHOULD LOOK FOR COMPATIBLE CP's IF NOT THE COMPLETE WEEK/YEAR

					foreach($schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']] as $existing_slice)

					{

						if($existing_slice['PARENT_ID']!=$not_parent_id && _isConflict($existing_slice,$slice)) {

							continue 3;

						}

					}

				}

			}

			// No conflict

			$possible[] = $course_period;

		}

	}

	if(count($possible))

	{

		// IF THIS COURSE IS BEING SCHEDULED A SECOND TIME, DELETE THE ORIGINAL ONE

		if($not_parent_id)

		{

			foreach($cp_parent_RET[$not_parent_id] as $key=>$slice)

			{

				foreach($schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']] as $key2=>$item)

				{

					if($item['COURSE_PERIOD_ID']==$slice['COURSE_PERIOD_ID'])

					{

						$filled[$schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']][$key2]['REQUEST_ID']] = false;

						unset($schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']][$key2]);

						$cp_parent_RET[$not_parent_id][$key]['AVAILABLE_SEATS']++;

					}

				}

			}

		}



		// CHOOSE THE BEST CP

		_scheduleBest($request,$possible);

		return true;

	}

	else

		return false; // if this point is reached, the request could not be scheduled

}



function _moveRequest($request,$not_request=false,$not_parent_id=false)

{	global $requests_RET,$cp_parent_RET,$cp_course_RET,$mps_RET,$schedule,$filled,$unfilled;



	if(!$not_request && !is_array($not_request))

		$not_request = array();



	if(count($cp_course_RET[$request['COURSE_ID']]))

	{

		foreach($cp_course_RET[$request['COURSE_ID']] as $course_period)

		{

			// CLEAR OUT A SLOT FOR EACH $slice

			foreach($cp_parent_RET[$course_period['PARENT_ID']] as $slice)

			{

				/* Don't bother to move courses around if request can't be scheduled here anyway. */

				// SEAT COUNTS

				if($slice['AVAILABLE_SEATS']<=0)

					continue 2;



				// SLICE VIOLATES GENDER RESTRICTION

				if($slice['GENDER_RESTRICTION']!='N' && $slice['GENDER_RESTRICTION']!=substr($request['GENDER'],0,1))

					continue 2;



				// PARENT VIOLATES TEACHER / PERIOD REQUESTS

				if($slice['PARENT_ID']==$slice['COURSE_PERIOD_ID'] && (($request['WITH_TEACHER_ID']!='' && $slice['TEACHER_ID']!=$request['WITH_TEACHER_ID']) || ($request['WITH_PERIOD_ID'] && $slice['PERIOD_ID']!=$request['WITH_PERIOD_ID']) || ($request['NOT_TEACHER_ID'] && $slice['TEACHER_ID']==$request['NOT_TEACHER_ID']) || ($request['NOT_PERIOD_ID'] && $slice['PERIOD_ID']==$request['NOT_PERIOD_ID'])))

					continue 2;



				if(count($schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']]))

				{

					foreach($schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']] as $existing_slice)

					{

						if(in_array($existing_slice['REQUEST_ID'],$not_request))

							continue 3;



						if(true)

						{

							$not_request_temp = $not_request;

							$not_request_temp[] = $existing_slice['REQUEST_ID'];

							if(!$scheduled = _scheduleRequest($requests_RET[$existing_slice['REQUEST_ID']][1],$existing_slice['PARENT_ID']))

							{

								if(!$moved = _moveRequest($requests_RET[$existing_slice['REQUEST_ID']][1],$not_request_temp,$existing_slice['PARENT_ID']))

									continue 3;

							}

						}

					}

				}

				else

				{

					// WTF???

				}

			}

			if(_scheduleRequest($request,$not_parent_id))

				return true;

		}

	}



	return false; // if this point is reached, the request could not be scheduled

}



function _isConflict($existing_slice,$slice)

{	global $requests_RET,$cp_parent_RET,$cp_course_RET,$mps_RET,$schedule,$filled,$unfilled,$fy_id;



	 $mp_conflict = $days_conflict = false;

	// LOOK FOR CONFLICT IN SCHEDULED SLICE -- CONFLICT == SEATS,MP,DAYS,PERIOD TIMES



	// MARKING PERIOD CONFLICTS

	if($existing_slice['MARKING_PERIOD_ID']=="$fy_id" || ($slice['MARKING_PERIOD_ID']=="$fy_id" && (!$request['MARKING_PERIOD_ID'] || $request['MARKING_PERIOD_ID']==$slice['MARKING_PERIOD_ID'])))

		$mp_conflict = true; // if either course is full year

	elseif($existing_slice['MARKING_PERIOD_ID']==$slice['MARKING_PERIOD_ID'])

		$mp_conflict = true; // if both fall in the same QTR or SEM

	elseif($existing_slice['MP']==$slice['MP'])

		$mp_conflict = false; // both are SEM's or QTR's, but not the same

	elseif($existing_slice['MP']=='SEM' && $mps_RET[$existing_slice['MARKING_PERIOD_ID']][$slice['MARKING_PERIOD_ID']])

		$mp_conflict = true; // the new course is a quarter in the existing semester

	elseif($mps_RET[$slice['MARKING_PERIOD_ID']][$existing_slice['MARKING_PERIOD_ID']])

		$mp_conflict = true; // the existing course is a quarter in the new semester

	else

		$mp_conflict = false; // not the same MP, but no conflict



	if($mp_conflict) // only look for a day conflict if there's already an MP conflict

	{

		if(strlen($slice['DAYS'])+strlen($existing_slice['DAYS'])>7)

			$days_conflict = true;

		else

		{

			$days_len = strlen($slice['DAYS']);

			for($i=0;$i<$days_len;$i++)

			{

				if(strpos($existing_slice['DAYS'],substr($slice['DAYS'],$i,1))!==false)

				{

					$days_conflict = true;

					break;

				}

			}

		}

		if($days_conflict)

			return true; // Go to the next available section

	}



	return false; // There is no conflict

}



function _scheduleBest($request,$possible)

{	global $cp_parent_RET,$schedule,$filled;

	$best = $possible[0];

	if(count($possible)>1)

	{

		foreach($possible as $course_period)

		{

			if($cp_parent_RET[$course_period['COURSE_PERIOD_ID']][1]['AVAILABLE_SEATS']>$cp_parent_RET[$best['COURSE_PERIOD_ID']][1]['AVAILABLE_SEATS'])

			{

				$best = $course_period;

			}

		}

	}

	foreach($cp_parent_RET[$best['COURSE_PERIOD_ID']] as $key=>$slice)

	{

		$schedule[$request['STUDENT_ID']][$slice['PERIOD_ID']][] = $slice + array('REQUEST_ID'=>$request['REQUEST_ID']);

		$cp_parent_RET[$best['COURSE_PERIOD_ID']][$key]['AVAILABLE_SEATS']--;

	}

}



function _returnTrue($arg1,$arg2='',$arg3='')

{

	return true;

}
function Prompt_Home_Schedule($title='Confirm',$question='',$message='',$pdf='')
{

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;

	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);
   	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
          
                    echo "<CENTER><h4>$question</h4><FORM name=run_schedule action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST onSubmit='return confirmAction();'>$message<BR><BR><INPUT type=submit class=btn_medium value=OK >&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=misc/Portal.php\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}
?>
