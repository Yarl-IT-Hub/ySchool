<?php
include('../../Redirect_modules.php');
DrawBC("Scheduling >> ".ProgramTitle());

Widgets('activity');
Widgets('course');
Widgets('request');
if(!$_SESSION['student_id']){
Search('student_id',$extra);
}
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

if(UserStudentID())
{
                    echo "<FORM name=modify id=modify action=Modules.php?modname=$_REQUEST[modname]&modfunc=modify METHOD=POST>";

                    $tmp_REQUEST = $_REQUEST;

                    if(clean_param($_REQUEST['marking_period_id'],PARAM_INT)){
                    $mp_id=$_REQUEST['marking_period_id'];
                    }

                    if(!isset($_REQUEST['marking_period_id'])){
                    $mp_id=UserMP();
                    $_REQUEST['marking_period_id']=$mp_id;
                    }
                    if($_REQUEST['modfunc']!='detail')
                    {
                        if(!isset ($_REQUEST['view_mode']))
                            $_REQUEST['view_mode']='day_view';
                    }
                    ##################################################################

                    $mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,1 AS TBL FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,2 AS TBL FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,3 AS TBL FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY TBL,SORT_ORDER"));

                    $mp = CreateSelect($mp_RET, 'marking_period_id', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id=', $_REQUEST['marking_period_id']);

                    $view_mode=create_view_mode('Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id='.$_REQUEST['marking_period_id'].'&view_mode=');
                    ###################################################################3

        switch ($_REQUEST['view_mode'])
        {
            case 'day_view':
                DrawHeaderHome('<table width="100%" cellpadding="2" cellspacing="2"><tr><td align="left" width="175">'.PrepareDateSchedule($date,'_date',false,array('submit'=>true)).'&nbsp;&nbsp;&nbsp;<INPUT type=submit class=btn_medium value=Go></td><td align="left">Marking Period :  '.$mp.'</td><td align="right">Calendar View
 : '.$view_mode).'</td></tr></table>';
                $full_day=date('l',strtotime($date));
                $day=get_db_day($full_day);
                $fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	$sql = "SELECT
				s.COURSE_ID,s.COURSE_PERIOD_ID,
				s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,
				UNIX_TIMESTAMP(s.START_DATE) AS START_EPOCH,UNIX_TIMESTAMP(s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,CONCAT(sp.START_TIME,' - ',sp.END_TIME) AS TIME_PERIOD,
				cp.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
				c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
				s.STUDENT_ID,ROOM,DAYS,SCHEDULER_LOCK
			FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp,MARKING_PERIODS mp
			WHERE
				s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
				AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID
                                                                        AND cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID
                                                                        AND POSITION('".$day."' IN cp.days)>0
				AND s.STUDENT_ID='".UserStudentID()."'
				AND s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID = '".UserSchool()."' 
                                                                        AND ('".date('Y-m-d',strtotime($date))."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND s.START_DATE<='".date('Y-m-d',strtotime($date))."')) AND '".date('Y-m-d',strtotime($date))."' BETWEEN mp.START_DATE AND mp.END_DATE
                                                                        AND s.MARKING_PERIOD_ID IN (".GetAllMP(GetMPTable(GetMP($mp_id,'TABLE')),$mp_id).")
                                                                        ORDER BY sp.SORT_ORDER,s.MARKING_PERIOD_ID";

	$QI = DBQuery($sql);
	$schedule_RET = DBGet($QI,array('TITLE'=>'_makeTitle','PERIOD_PULLDOWN'=>'_makePeriodSelect','COURSE_MARKING_PERIOD_ID'=>'_makeMPSelect'));

	$columns = array('TIME_PERIOD'=>'Period','TITLE'=>'Course','PERIOD_PULLDOWN'=>'Period - Teacher','ROOM'=>'Room','DAYS'=>'Days of Week','COURSE_MARKING_PERIOD_ID'=>'Term');
	$days_RET = DBGet(DBQuery("SELECT DISTINCT DAYS FROM COURSE_PERIODS"));
	if(count($days_RET)==1)
                        unset($columns['DAYS']);
	if($_REQUEST['_openSIS_PDF'])
                        unset($columns['SCHEDULER_LOCK']);
                    break;

        case 'week_view':
            $cal_RET = DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));

            $week_range=_makeWeeks($cal_RET[1]['START_DATE'],$cal_RET[1]['END_DATE'],'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id='.$_REQUEST['marking_period_id'].'&view_mode='.$_REQUEST['view_mode'].'&week_range=');
            DrawHeaderHome('<table width="100%" cellpadding="3" cellspacing="2"><tr><td style="padding-right:20px;">Marking Period : '.$mp.'</td><td style="padding-right:20px;" align="left">'.$week_range.'</td><td align="right">Calendar View : '.$view_mode.'</td></tr></table>');
            $one_day=60*60*24;
            $today=strtotime($_REQUEST['week_range']);
            $week_start=date('Y-m-d',$today);
            $week_end=date('Y-m-d',$today+$one_day*6);

            $fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
            $fy_id = $fy_id[1]['MARKING_PERIOD_ID'];
            
	$QI = ($sql);
	$wk_schedule_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,CONCAT(sp.START_TIME,' - ',sp.END_TIME) AS TIME_PERIOD,sp.TITLE FROM SCHOOL_PERIODS sp WHERE sp.SYEAR='".UserSyear()."' AND sp.SCHOOL_ID = '".UserSchool()."' ORDER BY sp.SORT_ORDER"));

                  $week_RET=DBGet(DBQuery("SELECT acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID,cp.PERIOD_ID
				FROM ATTENDANCE_CALENDAR acc
				INNER JOIN MARKING_PERIODS mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID
				AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE
				INNER JOIN COURSE_PERIODS cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID
				INNER JOIN SCHOOL_PERIODS sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cp.PERIOD_ID
                                                                        INNER JOIN SCHEDULE sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.START_DATE<=acc.SCHOOL_DATE AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE) AND acc.SCHOOL_DATE BETWEEN '".$week_start."' AND '".$week_end."'
                                                                        AND sch.STUDENT_ID='".UserStudentID()."'"),array(),array('SCHOOL_DATE','PERIOD_ID'));

                  $columns = array('TIME_PERIOD'=>'Period');
                
                $i = 0;
                 if(count($week_RET))
                {
                    foreach($wk_schedule_RET as $course)
                    {
                        $i++;
                        $schedule_RET[$i]['TIME_PERIOD'] ='<span title="'.$course['TITLE'].'">'. $course['TIME_PERIOD'].'</span>';
                        for($j=$today;$j<=$today+$one_day*6;$j=$j+$one_day){
                            if(in_array(date('Y-m-d',$j),$week_RET[date('Y-m-d',$j)][$course['PERIOD_ID']][1])){
                            $day=date('l',strtotime($week_RET[date('Y-m-d',$j)][$course['PERIOD_ID']][1]['SCHOOL_DATE']));
                            $day_RET=DBGet(DBQuery("SELECT DISTINCT cp.COURSE_PERIOD_ID,cp.TITLE,DAYS,cp.ROOM FROM COURSE_PERIODS cp,MARKING_PERIODS mp,SCHEDULE sch WHERE cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.COURSE_PERIOD_ID=sch.COURSE_PERIOD_ID AND sch.START_DATE<=  '". date('Y-m-d',$j)."' AND (sch.END_DATE>='". date('Y-m-d',$j)."' OR sch.END_DATE IS NULL) AND '". date('Y-m-d',$j)."' BETWEEN mp.START_DATE AND mp.END_DATE AND  cp.PERIOD_ID ='$course[PERIOD_ID]' AND POSITION('".get_db_day($day)."' IN cp.days)>0"));
                            if(!$day_RET)
                                $schedule_RET[$i][date('y-m-d',$j)] ='<div align=center title="Schedule not available">--</div>';
                            else
                                $schedule_RET[$i][date('y-m-d',$j)] =(count($day_RET)>1?'<font title="Conflict schedule ('.count($day_RET).')" color="red">'.$day_RET[1]['TITLE'].'<br />Room :'.$day_RET[1]['ROOM'].'</font>' : '<spna title='.date("l",$j).'>'.$day_RET[1]['TITLE'].'<br />Room :'.$day_RET[1]['ROOM'].'</span>');
                            }
                        }
                    }
                }
                    for($i=$today;$i<=$today+$one_day*6;$i=$i+$one_day)
                    $columns[date('y-m-d',$i)] = weekDate(date('Y-m-d',$i)).' '.ShortDate(date('Y-m-d',$i));
            break;
            
            case 'month_view':
                $month_str=_makeMonths('Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id='.$_REQUEST['marking_period_id'].'&view_mode='.$_REQUEST['view_mode'].'&month=');
                DrawHeaderHome('<table cellpadding="2" cellspacing="2" width="100%"><tr><td style="padding-right:20px;">Marking Period :  '.$mp.'</td><td>'.$month_str.'</td><td align="right">Calendar View : '.$view_mode.'</td></tr></table>');
                $fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
                $fy_id = $fy_id[1]['MARKING_PERIOD_ID'];
                
                $month=date('m',$_REQUEST['month']);
                $year=date('Y',$_REQUEST['month']);
                
//                  ++++++++++++++++++++++++++++++++++++++++++++++++++++++
                  $time = mktime(0,0,0,$month,1,$year);
                  $last = 31;
	while(!checkdate($month, $last, $year))
		$last--;

	$calendar_RET = DBGet(DBQuery("SELECT SCHOOL_DATE,MINUTES,BLOCK FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE BETWEEN '".date('Y-m-d',$time)."' AND '".date('Y-m-d',mktime(0,0,0,$month,$last,$year))."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array(),array('SCHOOL_DATE'));

	$skip = date("N",$time)-1;

	echo "<CENTER><TABLE border=0 cellpadding=0 cellspacing=0 class=pixel_border><TR><TD>";
	echo "<TABLE border=0 cellpadding=3 cellspacing=1><TR class=calendar_header align=center>";
	echo "<TD class=white>Monday</TD><TD class=white>Tuesday</TD><TD class=white>Wednesday</TD><TD class=white>Thursday</TD><TD class=white>Friday</TD><TD class=white>Saturday</TD><TD width=99 class=white>Sunday</TD>";
	echo "</TR><TR>";

	if($skip)
	{
		echo "<td colspan=" . $skip . "></td>";
		$return_counter = $skip;
	}
	for($i=1;$i<=$last;$i++)
	{
		$day_time = mktime(0,0,0,$month,$i,$year);
		$date = date('Y-m-d',$day_time);
                                    
                //------------------------------------------------------------------------------------------------------------------------------------------------------------
                $full_day=date('l',strtotime($date));
                $day=get_db_day($full_day);
                $sql = "SELECT
				s.COURSE_ID,s.COURSE_PERIOD_ID,
				s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,
				UNIX_TIMESTAMP(s.START_DATE) AS START_EPOCH,UNIX_TIMESTAMP(s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,CONCAT(sp.START_TIME,' - ',sp.END_TIME) AS TIME_PERIOD,sp.START_TIME,c.TITLE,
				cp.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
				c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
				s.STUDENT_ID,ROOM,DAYS,SCHEDULER_LOCK
			FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp
			WHERE
				s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
				AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID
                                                                        AND POSITION('".$day."' IN cp.days)>0
				AND s.STUDENT_ID='".UserStudentID()."'
				AND s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID = '".UserSchool()."'
                                                                        AND ('".date('Y-m-d',strtotime($date))."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND s.START_DATE<='".date('Y-m-d',strtotime($date))."'))
                                                                        AND s.MARKING_PERIOD_ID IN (".GetAllMP(GetMPTable(GetMP($mp_id,'TABLE')),$mp_id).") 
                                                                        ORDER BY sp.SORT_ORDER,s.MARKING_PERIOD_ID";

	$QI = DBQuery($sql);
	$schedule_RET = DBGet($QI);

                //-------------------------------------------------------------------------------------------------------------------------------------------------------
                
//		echo "<TD width=100 class=".($calendar_RET[$date][1]['MINUTES']?$calendar_RET[$date][1]['MINUTES']=='999'?'calendar_active':'calendar_extra':'calendar_holiday')." valign=top><table width=100><tr><td width=5 valign=top>$i</td><td width=95 align=right>";
							if($calendar_RET[$date][1]['MINUTES']){ $cssclass="class=calendar_active "; } else { $cssclass="class=calendar_holiday "; }
                                    echo "<TD width=100 $cssclass valign=top><table width=100><tr><td width=5 valign=top>$i</td><td width=95 align=right>";
		echo "</td></tr><tr><TD colspan=2 height=50 valign=top>";
                                    if($calendar_RET[$date][1]['MINUTES']){
                                        if(count($schedule_RET)>0){
                                            foreach($schedule_RET as $cp_link){
                                                echo "<a HREF=# title=Details onclick='javascript:window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=detail&date=$date&marking_period_id=$_REQUEST[marking_period_id]&period=$cp_link[PERIOD_ID]\",\"blank\",\"width=500,height=450,scrollbars=1\"); return false;'>$cp_link[START_TIME] &nbsp;&nbsp;$cp_link[TITLE]<a><br />";
                                            }
                                        }
                                        else
                                            echo 'Schedule not available';
                                    }
                                    else
                                        echo '<font color=red>Holiday</font>';
                                    echo "</td></tr>";
		echo "</table></TD>";
		$return_counter++;

		if($return_counter%7==0)
			echo "</TR><TR>";
	}
	echo "</TR></TABLE>";

	echo "</TD></TR></TABLE>";
	echo "</CENTER>";
//                  +++++++++++++++++++++++++++++++++++++++++++++++++++++++
                  break;
            
        }
        
        if($_REQUEST['modfunc']=='detail')
        {
            $date=$_REQUEST['date'];
            $mp_id=$_REQUEST['marking_period_id'];
            $full_day=date('l',strtotime($date));
                $day=get_db_day($full_day);
                $fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	$sql = "SELECT
				s.COURSE_ID,s.COURSE_PERIOD_ID,
				s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,
				UNIX_TIMESTAMP(s.START_DATE) AS START_EPOCH,UNIX_TIMESTAMP(s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,CONCAT(sp.START_TIME,' - ',sp.END_TIME) AS TIME_PERIOD,
				cp.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
				c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
				s.STUDENT_ID,ROOM,DAYS,SCHEDULER_LOCK
			FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp
			WHERE
				s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
				AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID
                                                                        AND POSITION('".$day."' IN cp.days)>0
                                                                        AND sp.PERIOD_ID='$_REQUEST[period]'
				AND s.STUDENT_ID='".UserStudentID()."'
				AND s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID = '".UserSchool()."'
                                                                        AND ('".date('Y-m-d',strtotime($date))."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND s.START_DATE<='".date('Y-m-d',strtotime($date))."'))
                                                                        AND s.MARKING_PERIOD_ID IN (".GetAllMP(GetMPTable(GetMP($mp_id,'TABLE')),$mp_id).") 
                                                                        ORDER BY sp.SORT_ORDER,s.MARKING_PERIOD_ID";

	$QI = DBQuery($sql);
	$schedule_RET = DBGet($QI,array('TITLE'=>'_makeTitle','PERIOD_PULLDOWN'=>'_makePeriodSelect','COURSE_MARKING_PERIOD_ID'=>'_makeMPSelect'));

	$columns = array('TIME_PERIOD'=>'Period','TITLE'=>'Course','PERIOD_PULLDOWN'=>'Period - Teacher','ROOM'=>'Room','DAYS'=>'Days of Week','COURSE_MARKING_PERIOD_ID'=>'Term');
	
        }
            if($_REQUEST['view_mode']!='month_view'){
	ListOutput($schedule_RET,$columns,'Period','Periods',$link,array(),array('search'=>false));
            }

            if($schedule_RET && $_REQUEST['view_mode']=='day_view')
                        DrawHeader( "<table><tr><td>&nbsp;&nbsp;</td><td>". (ProgramLinkforExport('Scheduling/PrintSchedules.php','<img src=assets/print.png>','&modfunc=save&st_arr[]='.UserStudentID().'&mp_id='.$mp_id.'&include_inactive='.$_REQUEST['include_inactive'].'&_openSIS_PDF=true target=_blank'))."</td><td>". (ProgramLinkforExport('Scheduling/PrintSchedules.php','Print Schedule','&modfunc=save&st_arr[]='.UserStudentID().'&mp_id='.$mp_id.'&include_inactive='.$_REQUEST['include_inactive'].'&_openSIS_PDF=true target=_blank'))."</td></tr></table>");
	echo '</FORM>';
	echo "<div class=break></div>";

}
        
//==============================================Function start============================================
        
function _makeTitle($value,$column='')
{	
    global $_openSIS,$THIS_RET;
    return $value;//.' - '.$THIS_RET['COURSE_WEIGHT'];
}

function _makeLock($value,$column)
{
                  global $THIS_RET;
	if($value=='Y')
		$img = 'locked';
	else
		$img = 'unlocked';

	return '<IMG SRC=assets/'.$img.'.gif '.(AllowEdit()?'onclick="if(this.src.indexOf(\'assets/locked.gif\')!=-1) {this.src=\'assets/unlocked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'\';} else {this.src=\'assets/locked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'Y\';}"':'').'><INPUT type=hidden name=schedule['.$THIS_RET['COURSE_PERIOD_ID'].']['.$THIS_RET['START_DATE'].'][SCHEDULER_LOCK] id=lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].' value='.$value.'>';
}

function _makePeriodSelect($course_period_id,$column='')
{
                  global $_openSIS,$THIS_RET,$fy_id;
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
{
    return GetMP($mp_id);
//                  global $_openSIS,$THIS_RET,$fy_id;
//
//	if(!$_openSIS['_makeMPSelect'])
//	{
//		$semesters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
//		$quarters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
//
//		$_openSIS['_makeMPSelect'][$fy_id][1] = array('MARKING_PERIOD_ID'=>"$fy_id",'TITLE'=>'Full Year','SEMESTER_ID'=>'');
//		foreach($semesters_RET as $sem)
//			$_openSIS['_makeMPSelect'][$fy_id][] = $sem;
//		foreach($quarters_RET as $qtr)
//			$_openSIS['_makeMPSelect'][$fy_id][] = $qtr;
//
//		$quarters_QI = DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER");
//		$quarters_indexed_RET = DBGet($quarters_QI,array(),array('SEMESTER_ID'));
//
//		foreach($semesters_RET as $sem)
//		{
//			$_openSIS['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][1] = $sem;
//			foreach($quarters_indexed_RET[$sem['MARKING_PERIOD_ID']] as $qtr)
//				$_openSIS['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][] = $qtr;
//		}
//
//		foreach($quarters_RET as $qtr)
//			$_openSIS['_makeMPSelect'][$qtr['MARKING_PERIOD_ID']][] = $qtr;
//	}
//	foreach($_openSIS['_makeMPSelect'][$mp_id] as $value)
//		$mps[$value['MARKING_PERIOD_ID']] = $value['TITLE'];
//
//	if($THIS_RET['MARKING_PERIOD_ID']!=$mp_id)
//		$mps[$THIS_RET['MARKING_PERIOD_ID']] = '* '.$mps[$THIS_RET['MARKING_PERIOD_ID']];
//	return SelectInput($THIS_RET['MARKING_PERIOD_ID'],"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][MARKING_PERIOD_ID]",'',$mps,false);
		}

function _makeDate($value,$column)
{
    global $THIS_RET;

	if($column=='START_DATE')
		$allow_na = false;
	else
		$allow_na = true;

	return DateInput($value,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][$column]",'',true,$allow_na);
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
		$html .= "<select title='Marking periods' name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
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
        
        function create_view_mode($link)
        {
            if($link!='')
                $html .= "<select title='View mode' name='view_mode' id='view_mode' onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
            else
                $html .= "<select name='view_mode' id='view_mode'>";
            
            $html .= '<option value="day_view" '.($_REQUEST['view_mode']=='day_view'? 'selected' : '').' >Day</option>';
            $html .= '<option value="week_view" '.($_REQUEST['view_mode']=='week_view'? 'selected' : '').'>Week</option>';
            $html .= '<option value="month_view" '.($_REQUEST['view_mode']=='month_view'? 'selected' : '').'>Month</option>';
            $html .= "</select>";
            return $html;
        }
        
        function get_db_day($day)
        {
            switch ($day)
            {
                case 'Sunday':
                    $return='U';
                    break;
                case 'Monday':
                    $return='M';
                    break;
                case 'Tuesday':
                    $return='T';
                    break;
                case 'Wednesday':
                    $return='W';
                    break;
                case 'Thursday':
                    $return='H';
                    break;
                case 'Friday':
                    $return='F';
                    break;
                case 'Saturday':
                    $return='S';
                    break;
            }
            return $return;
        }
        
        
function  weekDate($date)
{
    return date('l',strtotime($date));
}

function _makeWeeks($start,$end,$link)
{
    $one_day=60*60*24;
    $start_time=strtotime($start);
    $end_time=strtotime($end);
    if(!$_REQUEST['week_range'])
    {
            $start_time_cur=strtotime(date('Y-m-d'));
            while (date('N',$start_time_cur)!=1)
            {
                    $start_time_cur=$start_time_cur-$one_day;
            }
        $_REQUEST['week_range']=date('Y-m-d',$start_time_cur);
    }

//    if($link!='')
//        $html .= "<select name='week_range' id='week_range' onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
//    else
//        $html .= "<select name='week_range' id='week_range'>";
//    for($i=$start_time;$i<=$end_time;$i=$i+$one_day*7)
//    {
//        if(date('Y-m-d',$i)==$_REQUEST['week_range'])
//            $html .= "<option selected value=".date('Y-m-d',$i).">".date('Y-m-d',$i)."--".date('Y-m-d',$i+$one_day*6)."</option>";
//        else
//            $html .= "<option value=".date('Y-m-d',$i).">".date('Y-m-d',$i)."--".date('Y-m-d',$i+$one_day*6)."</option>";
//            
//    }
//    $html .= "</select>";
//    
//    return $html;
    
    $prev=date('Y-m-d',strtotime($_REQUEST['week_range'])-$one_day*7);
    $next=date('Y-m-d',strtotime($_REQUEST['week_range'])+$one_day*7);
    $upper=date('Y-m-d',strtotime($_REQUEST['week_range'])+$one_day*6);
    if($link!=''){
      $html .= "<strong><a href='javascript:void(0);' title=Previous onClick=\"window.location='".$link.$prev."';\" style=\"font-size:12px;\">&lt;&lt; Prev</a> &nbsp; &nbsp; <span style=\"font-size:12px;\">".preg_replace('/\//', ' ',properDate($_REQUEST[week_range]),1)."</span>&nbsp; - &nbsp;<span style=\"font-size:12px;\">".preg_replace('/\//', ' ',properDate($upper),1)."</span> &nbsp; &nbsp; <a href='javascript:void(0);' title=Next onClick=\"window.location='".$link.$next."';\" style=\"font-size:12px;\">Next &gt;&gt;</a></strong>";
    }
    
    return $html;
}

function _makeMonths($link)
{
    $one_day=60*60*24;
    if(!$_REQUEST['month'])
    {
        $_REQUEST['month']=date(strtotime(date('Y-m-d')));
    }
    $prev=$_REQUEST['month']-$one_day*30;
    $next=$_REQUEST['month']+$one_day*30;
    if($link!=''){
      $html .= "<strong><a href='javascript:void(0);' title=Previous onClick=\"window.location='".$link.$prev."';\" style=\"font-size:12px;\">&lt;&lt; Prev</a> &nbsp; &nbsp; <span style=\"font-size:12px;\">".date('F', $_REQUEST['month'])."&nbsp;".date('Y', $_REQUEST['month'])."</span> &nbsp; &nbsp; <a href='javascript:void(0);' title=Next onClick=\"window.location='".$link.$next."';\" style=\"font-size:12px;\">Next &gt;&gt;</a></strong>";
    }
    
    return $html;
}

?>
