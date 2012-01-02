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
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='print')
{
#echo '<pre>';print_r($_SESSION['events_RET']);echo '</pre>';
	echo '<style type="text/css">.print_wrapper{font-family:arial;font-size:12px;}.print_wrapper table table{border-right:1px solid #666;border-bottom:1px solid #666;}.print_wrapper table td{font-size:12px;}</style>';
	echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
	echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:10px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">List of Events</div></td><td align=right style=\"padding-top:10px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
	echo "<div class=print_wrapper>";
ListOutputFloat($_SESSION['events_RET'],array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description'),'Event','Events','','',array('search'=>false,'count'=>false));
	echo "</div>";
#ListOutput($_SESSION['events_RET'],array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description'),'Event','Events');
}
if(!$_REQUEST['month'])
	$_REQUEST['month'] = date("n");
else
	$_REQUEST['month'] = MonthNWSwitch($_REQUEST['month'],'tonum')*1;
if(!$_REQUEST['year'])
	$_REQUEST['year'] = date("Y");

$time = mktime(0,0,0,$_REQUEST['month'],1,$_REQUEST['year']);

DrawBC("School Setup >> ".ProgramTitle());

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='create')
{
	$fy_RET = DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
	$fy_RET = $fy_RET[1];

	$message = '<TABLE cellspacing=0 cellpadding=0 border=0 ><TR><TD colspan=7 align=center>Title <INPUT type=text name=title class=cell_floating id=title> <INPUT type=checkbox name=default value=Y> Default Calendar for this School<BR><BR></TD></TR><TR><TD colspan=7 align=center>From '.PrepareDate($fy_RET['START_DATE'],'_min').' To '.PrepareDate($fy_RET['END_DATE'],'_max').'</TD></TR><tr><td class=clear></td></tr><TR><TD><INPUT type=checkbox value=Y name=weekdays[0]>Sunday</TD><TD><INPUT type=checkbox value=Y name=weekdays[1] CHECKED>Monday</TD><TD><INPUT type=checkbox value=Y name=weekdays[2] CHECKED>Tuesday</TD><TD><INPUT type=checkbox value=Y name=weekdays[3] CHECKED>Wednesday</TD><TD><INPUT type=checkbox value=Y name=weekdays[4] CHECKED>Thursday</TD><TD><INPUT type=checkbox value=Y name=weekdays[5] CHECKED>Friday</TD><TD><INPUT type=checkbox value=Y name=weekdays[6]>Saturday</TD></TR></TABLE>';
	if(Prompt_Calender('Create a new calendar','',$message))
	{
		$begin = mktime(0,0,0,MonthNWSwitch($_REQUEST['month_min'],'to_num'),$_REQUEST['day_min']*1,$_REQUEST['year_min']) + 43200;
		$end = mktime(0,0,0,MonthNWSwitch($_REQUEST['month_max'],'to_num'),$_REQUEST['day_max']*1,$_REQUEST['year_max']) + 43200;

		$weekday = date('w',$begin);

		//$calendar_id = DBGet(DBQuery("SELECT ".db_seq_nextval('CALENDARS_SEQ')." AS CALENDAR_ID ".FROM_DUAL));
                $fetch_calendar_id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'ATTENDANCE_CALENDARS'"));
                $calendar_id[1]['CALENDAR_ID']= $fetch_calendar_id[1]['AUTO_INCREMENT'];
                $calendar_id = $calendar_id[1]['CALENDAR_ID'];

		for($i=$begin;$i<=$end;$i+=86400)
		{
			if($_REQUEST['weekdays'][$weekday]=='Y')
			{
                          
				DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,CALENDAR_ID) values('".UserSyear()."','".UserSchool()."','".date('Y-m-d',$i)."','999','".$calendar_id."')");
                        }
			$weekday++;
			if($weekday==7)
				$weekday = 0;
		}
                $col=Calender_Title;
                $cal_title=paramlib_validation($col,$_REQUEST['title']);
                if($_REQUEST['default'])
                    DBQuery("Update ATTENDANCE_CALENDARS SET DEFAULT_CALENDAR=NULL WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear ()."'");
                DBQuery("INSERT INTO ATTENDANCE_CALENDARS (SYEAR,SCHOOL_ID,TITLE,DEFAULT_CALENDAR) values('".UserSyear()."','".UserSchool()."','".str_replace("'","\'",$cal_title)."','".$_REQUEST['default']."')");

		$_REQUEST['calendar_id'] = $calendar_id;
		unset($_REQUEST['modfunc']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['weekdays']);
		unset($_SESSION['_REQUEST_vars']['title']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete_calendar')
{
        $colmn=Calender_Id;
        $cal_title=paramlib_validation($colmn,$_REQUEST[calendar_id]);
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM STUDENT_ENROLLMENT WHERE CALENDAR_ID='$cal_title'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
	UnableDeletePrompt('Cannot delete because calendars are associated.');
	}else{
	if(DeletePromptCommon('calendar'))
	{
		DBQuery("DELETE FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID='$cal_title'");
		DBQuery("DELETE FROM ATTENDANCE_CALENDARS WHERE CALENDAR_ID='$cal_title'");
		$default_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND DEFAULT_CALENDAR='Y'"));
		if(count($default_RET))
			$_REQUEST['calendar_id'] = $default_RET[1]['CALENDAR_ID'];
		else
		{
			$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
			if(count($calendars_RET))
				$_REQUEST['calendar_id'] = $calendars_RET[1]['CALENDAR_ID'];
			else
				$error = array('There are no calendars yet setup.');
		}
		unset($_REQUEST['modfunc']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
                                    unset ($_REQUEST['calendar_id']);
	}
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='edit_calendar')
{
        $colmn=Calender_Id;
        $cal_id=paramlib_validation($colmn,$_REQUEST['calendar_id']);
        $acs_RET=DBGet(DBQuery("SELECT TITLE, DEFAULT_CALENDAR FROM ATTENDANCE_CALENDARS WHERE CALENDAR_ID='$cal_id'"));
        $acs_RET=$acs_RET[1];
        $ac_RET = DBGet(DBQuery("SELECT MIN(SCHOOL_DATE) AS START_DATE,MAX(SCHOOL_DATE) AS END_DATE FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID='$cal_id'"));
	$ac_RET = $ac_RET[1];
        
        $day_RET=DBGet(DBQuery("SELECT DAYNAME(SCHOOL_DATE) AS DAY_NAME FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID='$cal_id' ORDER BY SCHOOL_DATE LIMIT 0, 7"));
        $i=0;
        foreach ($day_RET as $day)
        {
            $weekdays[$i]=$day['DAY_NAME'];
            $i++;
        }
        
        $message = '<TABLE cellspacing=0 cellpadding=0 border=0 ><TR><TD colspan=7 align=center>Title <INPUT type=text name=title class=cell_floating id=title value="'.$acs_RET['TITLE'].'"> <INPUT type=checkbox name=default value=Y '. (($acs_RET['DEFAULT_CALENDAR']=='Y')? 'checked' : '').'> Default Calendar for this School<BR><BR></TD></TR><TR><TD colspan=7 align=center>From</TD></TR><TR><TD colspan=7 align=center> '.PrepareDate($ac_RET['START_DATE'],'_min',true,array('disabled'=>true),'N').'</TD></TR><TR><TD colspan=7 align=center> To </TD></TR><TR><TD colspan=7 align=center >'.PrepareDate($ac_RET['END_DATE'],'_max',true,array('disabled'=>true),'N').'</TD></TR><TR><TD class=clear></TD></TR><TR><TD><INPUT type=checkbox value=Y name=weekdays[0] '.((in_array('Sunday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Sunday</TD><TD><INPUT type=checkbox value=Y name=weekdays[1] '.((in_array('Monday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Monday</TD><TD><INPUT type=checkbox value=Y name=weekdays[2] '.((in_array('Tuesday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Tuesday</TD><TD><INPUT type=checkbox value=Y name=weekdays[3] '.((in_array('Wednesday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Wednesday</TD><TD><INPUT type=checkbox value=Y name=weekdays[4] '.((in_array('Thursday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Thursday</TD><TD><INPUT type=checkbox value=Y name=weekdays[5] '.((in_array('Friday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Friday</TD><TD><INPUT type=checkbox value=Y name=weekdays[6] '.((in_array('Saturday', $weekdays)==true)? 'CHECKED' : '').' DISABLED>Saturday</TD></TR></TABLE>';
       
        if(Prompt_Calender('Edit this calendar','',$message))
        {
            $col=Calender_Title;
            $cal_title=paramlib_validation($col,$_REQUEST['title']);
            if(isset ($_REQUEST['default']))
                DBQuery("UPDATE ATTENDANCE_CALENDARS SET DEFAULT_CALENDAR = NULL WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
            
            DBQuery("UPDATE ATTENDANCE_CALENDARS SET TITLE = '".str_replace("'","\'",$cal_title)."', DEFAULT_CALENDAR = '".$_REQUEST['default']."' WHERE CALENDAR_ID='$cal_id'");
            $_REQUEST['calendar_id'] = $cal_id;
            unset($_REQUEST['modfunc']);
            unset($_SESSION['_REQUEST_vars']['modfunc']);
            unset($_SESSION['_REQUEST_vars']['weekdays']);
            unset($_SESSION['_REQUEST_vars']['title']);
        }

}

if(User('PROFILE')!='admin')
{
	$course_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".UserCoursePeriod()."'"));
	if($course_RET[1]['CALENDAR_ID'])
		$_REQUEST['calendar_id'] = $course_RET[1]['CALENDAR_ID'];
	else
	{
		$default_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND DEFAULT_CALENDAR='Y'"));
		$_REQUEST['calendar_id'] = $default_RET[1]['CALENDAR_ID'];
	}
}
elseif(!$_REQUEST['calendar_id'])
{
	$default_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND DEFAULT_CALENDAR='Y'"));
	if(count($default_RET))
		$_REQUEST['calendar_id'] = $default_RET[1]['CALENDAR_ID'];
	else
	{
		$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		if(count($calendars_RET))
			$_REQUEST['calendar_id'] = $calendars_RET[1]['CALENDAR_ID'];
		else
			$error = array('There are no calendars yet setup.');
	}
}
unset($_SESSION['_REQUEST_vars']['calendar_id']);

if($_REQUEST['modfunc']=='detail')
{
	if($_REQUEST['month_values'] && $_REQUEST['day_values'] && $_REQUEST['year_values'])
	{
		$_REQUEST['values']['SCHOOL_DATE'] = $_REQUEST['day_values']['SCHOOL_DATE'].'-'.$_REQUEST['month_values']['SCHOOL_DATE'].'-'.$_REQUEST['year_values']['SCHOOL_DATE'];
		if(!VerifyDate($_REQUEST['values']['SCHOOL_DATE']))
			unset($_REQUEST['values']['SCHOOL_DATE']);
	}

	if($_POST['button']=='Save' && AllowEdit() && $_REQUEST['values'])
	{
                                    if(!(isset($_REQUEST['values']['TITLE']) && trim($_REQUEST['values']['TITLE'])==''))
                                    {
                                            if($_REQUEST['event_id']!='new')
                                            {
                                                    $sql = "UPDATE CALENDAR_EVENTS SET ";

                                                    foreach($_REQUEST['values'] as $column=>$value){
                                                            $value=paramlib_validation($column,$value);
                                                            if($column=="SCHOOL_DATE"){
                                                                    $value= date('Y-m-d',strtotime($value));
                                                            }
                                                            if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                                                    $value=  mysql_real_escape_string($value);
                                                                    $value=str_replace('%u201D', "\"", $value);
                                                            }
                                                            $sql .= $column.'="'.str_replace("","",trim($value)).'",';
                                                    }
                                                    $sql = substr($sql,0,-1) . " WHERE ID='$_REQUEST[event_id]'";
                                                    DBQuery($sql);
                                            }
                                            else
                                            {
                                                    if(!$_REQUEST['values']['SCHOOL_DATE'])
                                                    $_REQUEST['values']['SCHOOL_DATE'] = $_REQUEST['dd'];

                                                    $sql = "INSERT INTO CALENDAR_EVENTS ";

                                                    $fields = 'SYEAR,SCHOOL_ID,';
                                                    $values = "'".UserSyear()."','".UserSchool()."',";

                                                    $go = 0;
                                                    foreach($_REQUEST['values'] as $column=>$value)
                                                    {
                                                            if(trim($value))
                                                            {
                                                                    $value=paramlib_validation($column,$value);
                                                                    $fields .= $column.',';
                                                                    if($column=="SCHOOL_DATE")
                                                                        $values .= "'".date('Y-m-d',strtotime($value))."',";
                                                                    else{
                                                                        if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                                                            $value=  mysql_real_escape_string($value);
                                                                        }
                                                                        $values .= '"'.str_replace("","",trim($value)).'",';
                                                                    }
                                                                    $go = true;
                                                            }
                                                    }
                                                    $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                                                    if($go)
                                                        DBQuery($sql);
                                                    
                                            }
                                            echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&year='.$_REQUEST['year'].'&month='.MonthNWSwitch($_REQUEST['month'],'tochar').'"; window.close();</script>'; 
                                            unset($_REQUEST['values']);
                                            unset($_SESSION['_REQUEST_vars']['values']);
                                    }
		else
		{
			echo '<SCRIPT language=javascript> window.close();</script>';
		}
		
	}
	elseif(clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Delete')
	{
		if(DeletePrompt('event'))
		{
			DBQuery("DELETE FROM CALENDAR_EVENTS WHERE ID='".paramlib_validation($column=EVENT_ID,$_REQUEST[event_id])."'");
			echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&year='.$_REQUEST['year'].'&month='.MonthNWSwitch($_REQUEST['month'],'tochar').'"; window.close();</script>';
			unset($_REQUEST['values']);
			unset($_SESSION['_REQUEST_vars']['values']);
			unset($_REQUEST['button']);
			unset($_SESSION['_REQUEST_vars']['button']);
		}
	}
	else
	{
		if($_REQUEST['event_id'])
		{
			if($_REQUEST['event_id']!='new')
			{
				$RET = DBGet(DBQuery("SELECT TITLE,DESCRIPTION,SCHOOL_DATE FROM CALENDAR_EVENTS WHERE ID='$_REQUEST[event_id]'"));
				$title = $RET[1]['TITLE'];
			}
			else
			{
				$title = 'New Event';
				$RET[1]['SCHOOL_DATE'] = date('Y-m-d',strtotime($_REQUEST['school_date']));
			}
			echo "<FORM name=popform id=popform action=for_window.php?modname=$_REQUEST[modname]&dd=$_REQUEST[school_date]&modfunc=detail&event_id=$_REQUEST[event_id]&month=$_REQUEST[month]&year=$_REQUEST[year] METHOD=POST>";
		}
		else
		{
			$RET = DBGet(DBQuery("SELECT TITLE,STAFF_ID,DATE_FORMAT(DUE_DATE,'%d-%b-%y') AS SCHOOL_DATE,ASSIGNED_DATE,DUE_DATE,DESCRIPTION FROM GRADEBOOK_ASSIGNMENTS WHERE ASSIGNMENT_ID='$_REQUEST[assignment_id]'"));
			$title = $RET[1]['TITLE'];
			$RET[1]['STAFF_ID'] = GetTeacher($RET[1]['STAFF_ID']);
		}

		echo '<BR>';
		PopTableforWindow('header',$title);

		echo '<TABLE>';
		echo '<TR><TD>Date</TD><TD>'.DateInput($RET[1]['SCHOOL_DATE'],'values[SCHOOL_DATE]','',true).'</TD></TR>';
		echo '<TR><TD>Title</TD><TD>'.TextInput($RET[1]['TITLE'],'values[TITLE]','','style="width:380px;"').'</TD></TR>';
		if($RET[1]['STAFF_ID'])
			echo '<TR><TD>Teacher</TD><TD>'.TextAreaInput($RET[1]['STAFF_ID'],'values[STAFF_ID]').'</TD></TR>';
                if($RET[1]['ASSIGNED_DATE'])
                echo '<TR><TD>Assigned Date</TD><TD>'.TextAreaInput($RET[1]['ASSIGNED_DATE'],'values[ASSIGNED_DATE]').'</TD></TR>';
                if($RET[1]['DUE_DATE'])
                echo '<TR><TD>Due Date </TD><TD>'.TextAreaInput($RET[1]['DUE_DATE'],'values[DUE_DATE]').'</TD></TR>';
		echo '<TR><TD>Notes</TD><TD>'.TextAreaInput($RET[1]['DESCRIPTION'],'values[DESCRIPTION]','','style="width:380px; height:200px;"').'</TD></TR>';
		if(AllowEdit())
		{
			echo '<TR><TD colspan=2 align=center><INPUT type=submit class=btn_medium name=button value=Save onclick="formload_ajax(\'popform\');">';
			echo '&nbsp;';
			if($_REQUEST['event_id']!='new')
				echo '<INPUT type=submit name=button class=btn_medium value=Delete onclick="formload_ajax(\'popform\');">';
			echo '</TD></TR>';			

		}
		echo '</TABLE>';
		PopTableWindow('footer');
		echo '</FORM>';

		unset($_REQUEST['values']);
		unset($_SESSION['_REQUEST_vars']['values']);
		unset($_REQUEST['button']);
		unset($_SESSION['_REQUEST_vars']['button']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='list_events')
{
	if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
	{
		while(!VerifyDate($start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start']))
			$_REQUEST['day_start']--;
	}
	else
	{
		$min_date = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS MIN_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		if($min_date[1]['MIN_DATE'])
			$start_date = $min_date[1]['MIN_DATE'];
		else
			$start_date = '01-'.strtoupper(date('M-y'));
	}

	if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	{
		while(!VerifyDate($end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end']))
			$_REQUEST['day_end']--;
	}
	else
	{
		$max_date = DBGet(DBQuery("SELECT max(SCHOOL_DATE) AS MAX_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		if($max_date[1]['MAX_DATE'])
			$end_date = $max_date[1]['MAX_DATE'];
		else
			$end_date = strtoupper(date('Y-m-d'));
	}
	DrawBC("School Setup >> ".ProgramTitle());
	echo '<FORM action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc='.$_REQUEST['modfunc'].'&month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].' METHOD=POST>';
	
	#DrawHeaderHome(PrepareDateSchedule($start_date,'_start').' - '.PrepareDateSchedule($end_date,'_end').' <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].'>Back to Calendar</A>','<INPUT type=submit class=btn_medium value=Go>');
	
	DrawHeaderHome(PrepareDateSchedule($start_date,'_start').' <div style="float:left;">&nbsp;-&nbsp;</div> '.PrepareDateSchedule($end_date,'_end').' <div style="float:left; padding-left:5px; padding-top:2px;"><A HREF=Modules.php?modname='.$_REQUEST['modname'].'&month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].'>Back to Calendar</A></div>','<div style="float:left;"><INPUT type=submit class=btn_medium value=Go></div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="for_export.php?modname=School_Setup/Calendar.php&modfunc=print&_openSIS_PDF=true" target=_blank ><img src="assets/print.png" alt="Print" title="Print" /> Print</a>');
	
	$functions = array('SCHOOL_DATE'=>'ProperDate');									// <A HREF=Modules.php?modname='.$_REQUEST["modname"].'&month='.$_REQUEST["month"].'&year='.$_REQUEST["year"].'>
	$events_RET = DBGet(DBQuery("SELECT ID,SCHOOL_DATE,TITLE,DESCRIPTION FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN '".$start_date."' AND '".$end_date."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SCHOOL_DATE DESC"),$functions);
	$_SESSION['events_RET']=$events_RET;

#echo "<a href=\"for_export.php?modname=$_REQUEST[modname]&modfunc=print&_openSIS_PDF=true\" target=_blank ><img src=\"assets/print_new.png\" alt=\"Print\" title=\"Print\" /></a>";

	ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description'),'Event','Events');
	echo '</FORM>';
}

if(!$_REQUEST['modfunc'])
{

	DrawBC("School Setup >> ".ProgramTitle());
	$last = 31;
	while(!checkdate($_REQUEST['month'], $last, $_REQUEST['year']))
		$last--;

	$calendar_RET = DBGet(DBQuery("SELECT DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') as SCHOOL_DATE,MINUTES,BLOCK FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'"),array(),array('SCHOOL_DATE'));
	if($_REQUEST['minutes'])
	{
		foreach($_REQUEST['minutes'] as $date=>$minutes)
		{
			if($calendar_RET[$date])
			{
				if($minutes!='0' && $minutes!='')
					DBQuery("UPDATE ATTENDANCE_CALENDAR SET MINUTES='$minutes' WHERE SCHOOL_DATE='$date' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'");
				else
					DBQuery("DELETE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE='$date' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'");
			}
			elseif($minutes!='0' && $minutes!='')
				DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,CALENDAR_ID,MINUTES) values('".UserSyear()."','".UserSchool()."','".$date."','".$_REQUEST['calendar_id']."','".$minutes."')");
		}
		$calendar_RET = DBGet(DBQuery("SELECT DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') as SCHOOL_DATE,MINUTES,BLOCK FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'"),array(),array('SCHOOL_DATE'));
		unset($_REQUEST['minutes']);
		unset($_SESSION['_REQUEST_vars']['minutes']);
	}
	if($_REQUEST['all_day'])
	{
		foreach($_REQUEST['all_day'] as $date=>$yes)
		{
			if($yes=='Y')
			{
				if($calendar_RET[$date])
					DBQuery("UPDATE ATTENDANCE_CALENDAR SET MINUTES='999' WHERE SCHOOL_DATE='$date' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'");
				else
					DBQuery("INSERT INTO ATTENDANCE_CALENDAR (SYEAR,SCHOOL_ID,SCHOOL_DATE,CALENDAR_ID,MINUTES) values('".UserSyear()."','".UserSchool()."','".$date."','".$_REQUEST['calendar_id']."','999')");
			}
			else
				DBQuery("DELETE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE='$date' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'");
		}
		$calendar_RET = DBGet(DBQuery("SELECT DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') as SCHOOL_DATE,MINUTES,BLOCK FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'"),array(),array('SCHOOL_DATE'));
		unset($_REQUEST['all_day']);
		unset($_SESSION['_REQUEST_vars']['all_day']);
	}
	if($_REQUEST['blocks'])
	{
		foreach($_REQUEST['blocks'] as $date=>$block)
		{
			if($calendar_RET[$date])
			{
				DBQuery("UPDATE ATTENDANCE_CALENDAR SET BLOCK='".$block."' WHERE SCHOOL_DATE='$date' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'");
			}
		}
		$calendar_RET = DBGet(DBQuery("SELECT DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') as SCHOOL_DATE,MINUTES,BLOCK FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='".$_REQUEST['calendar_id']."'"),array(),array('SCHOOL_DATE'));
		unset($_REQUEST['blocks']);
		unset($_SESSION['_REQUEST_vars']['blocks']);
	}

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname] METHOD=POST>";
	$link = '';
	$title_RET = DBGet(DBQuery("SELECT CALENDAR_ID,TITLE FROM ATTENDANCE_CALENDARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY DEFAULT_CALENDAR ASC"));
	foreach($title_RET as $title)
	{
		$options[$title['CALENDAR_ID']] = $title['TITLE'];
	}
	if(AllowEdit())
	{
		$tmp_REQUEST = $_REQUEST;
		unset($tmp_REQUEST['calendar_id']);
                                    $link .= '<table><tr>';                    
                                    if($_REQUEST['calendar_id'])
                                            $link .= '<td>'. SelectInput($_REQUEST['calendar_id'],'calendar_id','',$options,false," onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST).'&amp;calendar_id="+this.form.calendar_id.value;\' ',false).'</td>';
                                    $link .="<td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=create\");'>".button('add')."</a></td><td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=create\");'>Create a new calendar</a> </td>";
                                    if($_REQUEST['calendar_id'])
                                            $link .="<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td><td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete_calendar&calendar_id=$_REQUEST[calendar_id]\");'>".button('remove')."</a></td><td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete_calendar&calendar_id=$_REQUEST[calendar_id]\");'>Delete this calendar</a></td><td>&nbsp;&nbsp;|&nbsp;&nbsp;</td><td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=edit_calendar&calendar_id=$_REQUEST[calendar_id]\");'>".button('edit')."</a></td><td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=edit_calendar&calendar_id=$_REQUEST[calendar_id]\");'>Edit this calendar</a></td>";
                                    $link .='</tr></table>';
	}
        if($_REQUEST['calendar_id'])
	DrawHeaderHome(PrepareDate(strtoupper(date("d-M-y",$time)),'',false,array('M'=>1,'Y'=>1,'submit'=>true)).' <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=list_events&month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].'>List Events</A>',SubmitButton('Save','','class=btn_medium'));
	DrawHeaderHome($link);																					// <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=list_events&month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].'>List Events</A>
	if(count($error))
	{
		if($isajax!="ajax")
			echo ErrorMessage($error,'fatal');
		else
			echo ErrorMessage1($error,'fatal');
	}
	echo '<BR>';

	$events_RET = DBGet(DBQuery("SELECT ID,DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') AS SCHOOL_DATE,TITLE FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array(),array('SCHOOL_DATE'));
	if(User('PROFILE')=='parent' || User('PROFILE')=='student')
		#$assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID AS ID,DATE_FORMAT(a.DUE_DATE,'%d-%b-%y') AS SCHOOL_DATE,a.TITLE,'Y' AS ASSIGNED FROM GRADEBOOK_ASSIGNMENTS a,SCHEDULE s WHERE (a.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID OR a.COURSE_ID=s.COURSE_ID) AND s.STUDENT_ID='".UserStudentID()."' AND (a.DUE_DATE BETWEEN s.START_DATE AND s.END_DATE OR s.END_DATE IS NULL) AND (a.ASSIGNED_DATE<=CURRENT_DATE OR a.ASSIGNED_DATE IS NULL) AND a.DUE_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."'"),array(),array('SCHOOL_DATE'));
	$assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID AS ID,DATE_FORMAT(a.DUE_DATE,'%d-%b-%y') AS SCHOOL_DATE,a.TITLE,'Y' AS ASSIGNED FROM GRADEBOOK_ASSIGNMENTS a,SCHEDULE s WHERE (a.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID OR a.COURSE_ID=s.COURSE_ID) AND s.STUDENT_ID='".UserStudentID()."'AND(  CURRENT_DATE>=a.ASSIGNED_DATE OR CURRENT_DATE<=a.ASSIGNED_DATE)AND (a.DUE_DATE IS NULL OR CURRENT_DATE<=a.DUE_DATE) "),array(),array('SCHOOL_DATE'));
	elseif(User('PROFILE')=='teacher')
		$assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID AS ID,DATE_FORMAT(a.DUE_DATE,'%d-%b-%y') AS SCHOOL_DATE,a.TITLE,CASE WHEN a.ASSIGNED_DATE<=CURRENT_DATE OR a.ASSIGNED_DATE IS NULL THEN 'Y' ELSE NULL END AS ASSIGNED FROM GRADEBOOK_ASSIGNMENTS a WHERE a.STAFF_ID='".User('STAFF_ID')."' AND a.DUE_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']))."'"),array(),array('SCHOOL_DATE'));

	$skip = date("w",$time);

	echo "<CENTER><TABLE border=0 cellpadding=0 cellspacing=0 class=pixel_border><TR><TD>";
	echo "<TABLE border=0 cellpadding=3 cellspacing=1><TR class=calendar_header align=center>";
	echo "<TD class=white>Sunday</TD><TD class=white>Monday</TD><TD class=white>Tuesday</TD><TD class=white>Wednesday</TD><TD class=white>Thursday</TD><TD class=white>Friday</TD><TD width=99 class=white>Saturday</TD>";
	echo "</TR><TR>";

	if($skip)
	{
		echo "<td colspan=" . $skip . "></td>";
		$return_counter = $skip;
	}
		$blocks_RET = DBGet(DBQuery("SELECT DISTINCT BLOCK FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND BLOCK IS NOT NULL ORDER BY BLOCK"));
	for($i=1;$i<=$last;$i++)
	{
		$day_time = mktime(0,0,0,$_REQUEST['month'],$i,$_REQUEST['year']);
		$date = date('d-M-y',$day_time);

		echo "<TD width=100 class=".($calendar_RET[$date][1]['MINUTES']?$calendar_RET[$date][1]['MINUTES']=='999'?'calendar_active':'calendar_extra':'calendar_holiday')." valign=top><table width=100><tr><td width=5 valign=top>$i</td><td width=95 align=right>";
		if(AllowEdit())
		{
			if($calendar_RET[$date][1]['MINUTES']=='999')
				echo '<TABLE cellpadding=0 cellspacing=0 ><TR><TD>'.CheckboxInput($calendar_RET[$date],"all_day[$date]",'','',false,'<IMG SRC=assets/check.gif> ').'</TD></TR></TABLE>';
			else
			{
				echo "<TABLE cellpadding=0 cellspacing=0 ><TR><TD><INPUT type=checkbox name=all_day[$date] value=Y></TD>";
				echo '<TD>'.TextInput($calendar_RET[$date][1]['MINUTES'],"minutes[$date]",'','size=3 class=cell_small onkeydown="return numberOnly(event);"').'</TD></TR></TABLE>';
			}
		}
		if(count($blocks_RET)>0)
		{
			unset($options);
			foreach($blocks_RET as $block)
				$options[$block['BLOCK']] = $block['BLOCK'];

			echo SelectInput($calendar_RET[$date][1]['BLOCK'],"blocks[$date]",'',$options);
		}
		echo "</td></tr><tr><TD colspan=2 height=50 valign=top>";

		if(count($events_RET[$date]))
		{
			echo '<TABLE cellpadding=2 cellspacing=2 border=0>';
			foreach($events_RET[$date] as $event)
				echo "<TR><TD>".button('dot','0000FF','','6')."</TD><TD> <A HREF=# onclick='javascript:window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=detail&event_id=$event[ID]&year=$_REQUEST[year]&month=".MonthNWSwitch($_REQUEST['month'],'tochar')."\",\"blank\",\"width=600,height=400\"); return false;'><b>".($event['TITLE']?$event['TITLE']:'***')."</b></A></TD></TR>";
			if(count($assignments_RET[$date]))
			{
				foreach($assignments_RET[$date] as $event)
					echo "<TR><TD>".button('dot',$event['ASSIGNED']=='Y'?'00FF00':'FF0000','',6)."</TD><TD><A HREF=# onclick='javascript:window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=detail&assignment_id=$event[ID]&year=$_REQUEST[year]&month=".MonthNWSwitch($_REQUEST['month'],'tochar')."\",\"blank\",\"width=600,height=400\"); return false;'>".$event['TITLE']."</A></TD></TR>";
			}
			echo '</TABLE>';
		}
		elseif(count($assignments_RET[$date]))
		{
			echo '<TABLE cellpadding=0 cellspacing=0 border=0>';
			foreach($assignments_RET[$date] as $event)
				echo "<TR><TD>".button('dot',$event['ASSIGNED']=='Y'?'00FF00':'FF0000','',6)."</TD><TD><A HREF=# onclick='javascript:window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=detail&assignment_id=$event[ID]&year=$_REQUEST[year]&month=".MonthNWSwitch($_REQUEST['month'],'tochar')."\",\"blank\",\"width=600,height=400\"); return false;'>".$event['TITLE']."</A></TD></TR>";
			echo '</TABLE>';
		}

		echo "</td></tr>";
		if(AllowEdit())
			echo "<tr><td valign=bottom align=left>".button('add','',"# onclick='javascript:window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=detail&event_id=new&school_date=$date&year=$_REQUEST[year]&month=".MonthNWSwitch($_REQUEST['month'],'tochar')."\",\"blank\",\"width=600,height=400\"); return false;'")."</td></tr>";
		echo "</table></TD>";
		$return_counter++;

		if($return_counter%7==0)
			echo "</TR><TR>";
	}
	echo "</TR></TABLE>";

	echo "</TD></TR></TABLE>";
	echo '<BR>'.SubmitButton('Save','','class=btn_medium');
	echo "</CENTER>";
	echo '</FORM>';
}


?>