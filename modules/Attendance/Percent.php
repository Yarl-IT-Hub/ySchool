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
DrawBC("Attendance >> ".ProgramTitle());
if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	$start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.substr($_REQUEST['year_start'],2,4);
	$start_date_mod = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start'];
}
else
{
	$start_date = '01-'.strtoupper(date('M-y'));
	$start_date_mod = strtoupper(date('Y-m')).'-01';
}

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.substr($_REQUEST['year_end'],2,4);
	$end_date_mod = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];
}
else
{
	$end_date = DBDate();
	$end_date_mod = date('Y-m-d');
}
	
	

if($_REQUEST['modfunc']=='search')
{
	echo '<BR>';
	PopTable('header','Advanced');
	echo "<FORM name=percentform action=Modules.php?modname=$_REQUEST[modname]&list_by_day=$_REQUEST[list_by_day]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end] method=POST>";
	echo '<TABLE>';
	
	Search('general_info',$extra['grades']);
	if(!isset($extra))
		$extra = array();
	Widgets('user',$extra);
	if($extra['search'])
		echo $extra['search'];
	Search('student_fields',is_array($extra['student_fields'])?$extra['student_fields']:array());
	if(User('PROFILE')=='admin')
		echo '<CENTER><INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'><font color=black>Search All Schools</font></CENTER><BR>';
	echo '<CENTER>'.Buttons('Submit').'</CENTER>';
	
	echo '</FORM>';
	PopTable('footer');
}

if(!$_REQUEST['modfunc'])
{
    
	if(!isset($extra))
		$extra = array();
	Widgets('user');
	if($_REQUEST['advanced']=='Y')
		Widgets('all');
	$extra['WHERE'] .= appendSQL('');
	$extra['WHERE'] .= CustomFields('where');

    echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&list_by_day=$_REQUEST[list_by_day] method=POST>";
	$advanced_link = " <A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=search&list_by_day=$_REQUEST[list_by_day]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]>Advanced</A>";
	DrawHeaderHome('<table><tr><td>'.PrepareDate($start_date,'_start').'</td><td> - </td><td>'.PrepareDate($end_date,'_end').'</td><td> - </td><td>'.$advanced_link,' : <INPUT type=submit value=Go class=btn_medium></td></tr></table>');
	echo '</FORM>';

	if($_REQUEST['list_by_day']=='true')
	{
            
		$cal_days = 1;

       
        $atten_possible  =  DBGet(DBQuery("SELECT DISTINCT ad.SCHOOL_DATE, ssm.GRADE_ID,ssm.CALENDAR_ID,ap.STUDENT_ID AS ST_ID FROM ATTENDANCE_DAY ad,STUDENT_ENROLLMENT ssm,STUDENTS s, ATTENDANCE_PERIOD ap WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STUDENT_ID=ssm.STUDENT_ID AND ap.STUDENT_ID=ad.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra['WHERE']." GROUP BY ad.SCHOOL_DATE,ssm.GRADE_ID"),array(''),array('SCHOOL_DATE','GRADE_ID'));
		$student_days_absent = DBGet(DBQuery("SELECT ad.SCHOOL_DATE,ssm.GRADE_ID,COALESCE(sum(ad.STATE_VALUE-1)*-1,0) AS STATE_VALUE,ad.MINUTES_PRESENT AS MINUTES_PRESENT FROM ATTENDANCE_DAY ad,STUDENT_ENROLLMENT ssm,STUDENTS s WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra['WHERE']." GROUP BY ad.SCHOOL_DATE,ssm.GRADE_ID"),array(''),array('SCHOOL_DATE','GRADE_ID'));
        $student_days_present = DBGet(DBQuery("SELECT COUNT(*) AS PRESENT_BY_GREAD,SE.GRADE_ID FROM `ATTENDANCE_PERIOD` AP,STUDENT_ENROLLMENT SE WHERE AP.ATTENDANCE_CODE IN (SELECT ID FROM ATTENDANCE_CODES WHERE STATE_CODE!='A') AND SE.SCHOOL_ID=".UserSchool()." AND AP.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."' AND AP.STUDENT_ID=SE.STUDENT_ID GROUP BY SE.GRADE_ID"));
		$student_days_possible = DBGet(DBQuery("SELECT ac.SCHOOL_DATE,ssm.GRADE_ID,'' AS DAYS_POSSIBLE,count(*) AS ATTENDANCE_POSSIBLE,count(*) AS STUDENTS,'' AS PRESENT,'' AS ABSENT,'' AS ADA,'' AS AVERAGE_ATTENDANCE,'' AS AVERAGE_ABSENT FROM STUDENT_ENROLLMENT ssm,ATTENDANCE_CALENDAR ac,STUDENTS s WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ac.SYEAR=ssm.SYEAR AND ssm.SCHOOL_ID=ac.SCHOOL_ID AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SCHOOL_ID=ac.SCHOOL_ID AND (ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ac.SCHOOL_DATE)) AND ac.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."' ".$extra['WHERE']." GROUP BY ac.SCHOOL_DATE,ssm.GRADE_ID"),array('SCHOOL_DATE'=>'ProperDate','GRADE_ID'=>'GetGrade','ATTENDANCE_POSSIBLE'=>'_makeByDay','STUDENTS'=>'_makeByDay','PRESENT'=>'_makeByDay','ABSENT'=>'_makeByDay','ADA'=>'_makeByDay','AVERAGE_ATTENDANCE'=>'_makeByDay','AVERAGE_ABSENT'=>'_makeByDay','DAYS_POSSIBLE'=>'_makeByDay'));
	


		$columns = array('SCHOOL_DATE'=>'Date','GRADE_ID'=>'Grade','STUDENTS'=>'Students','DAYS_POSSIBLE'=>'Days Possible','PRESENT'=>'Present','ABSENT'=>'Absent','ADA'=>'ADA','AVERAGE_ATTENDANCE'=>'Average Attendance','AVERAGE_ABSENT'=>'Average Absent');

		ListOutput($student_days_possible,$columns,'','',$link);
                
	}
	else
	{
		
		$cal_days = DBGet(DBQuery("SELECT count(*) AS COUNT,CALENDAR_ID FROM ATTENDANCE_CALENDAR WHERE ".($_REQUEST['_search_all_schools']!='Y'?"SCHOOL_ID='".UserSchool()."' AND ":'')." SYEAR='".UserSyear()."' AND SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' GROUP BY CALENDAR_ID"),array(),array('CALENDAR_ID'));
		$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,TITLE FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' ".($_REQUEST['_search_all_schools']!='Y'?" AND SCHOOL_ID='".UserSchool()."'":'')),array(),array('CALENDAR_ID'));
		$extra['WHERE'] .= " GROUP BY ssm.GRADE_ID,ssm.CALENDAR_ID";
		
		
		$student_days_absent = DBGet(DBQuery("SELECT ssm.GRADE_ID,ssm.CALENDAR_ID,COUNT(ad.STATE_VALUE) AS STATE_VALUE FROM ATTENDANCE_DAY ad,STUDENT_ENROLLMENT ssm,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STATE_VALUE=0.0 AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra['WHERE']),array(''),array('GRADE_ID','CALENDAR_ID'));
		$student_not_taken = DBGet(DBQuery("SELECT ssm.GRADE_ID,ac.CALENDAR_ID,COUNT(*) AS NOT_TAKEN FROM ATTENDANCE_CALENDAR ac".$extra['FROM']." INNER JOIN STUDENT_ENROLLMENT ssm ON ssm.SYEAR=ac.SYEAR AND ssm.SCHOOL_ID=ac.SCHOOL_ID AND ssm.CALENDAR_ID=ac.CALENDAR_ID AND ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND COALESCE(ssm.END_DATE,CURDATE()) LEFT JOIN ATTENDANCE_DAY ad ON ad.SYEAR=ac.SYEAR AND ad.STUDENT_ID=ssm.STUDENT_ID AND ad.SCHOOL_DATE=ac.SCHOOL_DATE WHERE ssm.SYEAR='".  UserSyear()."' AND ac.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND ad.STUDENT_ID IS NULL ".$extra['WHERE']),array(''),array('GRADE_ID','CALENDAR_ID'));
				
		$student_days_present = DBGet(DBQuery("SELECT ssm.GRADE_ID,ssm.CALENDAR_ID,COUNT(ad.STATE_VALUE) AS PRESENT_BY_GREAD FROM ATTENDANCE_DAY ad,STUDENT_ENROLLMENT ssm,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STATE_VALUE >0.0 AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra['WHERE']),array(''),array('GRADE_ID','CALENDAR_ID'));
		$student_days_possible = DBGet(DBQuery("SELECT ssm.GRADE_ID,
			
			(SELECT count(STUDENT_ID) FROM STUDENT_ENROLLMENT ec WHERE ec.GRADE_ID=ssm.GRADE_ID AND ec.CALENDAR_ID=ssm.CALENDAR_ID AND ec.SYEAR=".UserSyear()."
			AND ((ec.START_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR (ec.END_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR ((ec.START_DATE <= '".$start_date_mod."') AND ((ec.END_DATE IS NULL) OR (ec.END_DATE >= '".$start_date_mod."'))))
			) AS STUDENTS,
			
			ssm.CALENDAR_ID,'' AS DAYS_POSSIBLE,
			
			(SELECT count(STUDENT_ID) FROM STUDENT_ENROLLMENT ec WHERE ec.GRADE_ID=ssm.GRADE_ID AND ec.SYEAR=".UserSyear()." 
			AND ((ec.START_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR (ec.END_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR ((ec.START_DATE <= '".$start_date_mod."') AND ((ec.END_DATE IS NULL) OR (ec.END_DATE >= '".$start_date_mod."'))))
			) AS TOTAL_ATTENDANCE,
			
			(SELECT count(STUDENT_ID) FROM STUDENT_ENROLLMENT ec WHERE ec.GRADE_ID=ssm.GRADE_ID AND ec.SYEAR=".UserSyear()." 
			AND ((ec.START_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR (ec.END_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR ((ec.START_DATE <= '".$start_date_mod."') AND ((ec.END_DATE IS NULL) OR (ec.END_DATE >= '".$start_date_mod."'))))
			) AS NOT_TAKEN,
			
			count(*) AS ATTENDANCE_POSSIBLE,'' AS PRESENT,'' AS ABSENT,
			
			(SELECT count(STUDENT_ID) FROM STUDENT_ENROLLMENT ec WHERE ec.GRADE_ID=ssm.GRADE_ID AND ec.SYEAR=".UserSyear()." 
			AND ((ec.START_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR (ec.END_DATE BETWEEN '".$start_date_mod."' AND '".$end_date_mod."') OR ((ec.START_DATE <= '".$start_date_mod."') AND ((ec.END_DATE IS NULL) OR (ec.END_DATE >= '".$start_date_mod."'))))
			) AS ADA,
			
			'' AS AVERAGE_ATTENDANCE,'' AS AVERAGE_ABSENT FROM STUDENT_ENROLLMENT ssm,ATTENDANCE_CALENDAR ac,STUDENTS s".$extra['FROM']." WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ac.SYEAR=ssm.SYEAR AND ac.CALENDAR_ID=ssm.CALENDAR_ID 
			AND ".($_REQUEST['_search_all_schools']!='Y'?"ssm.SCHOOL_ID='".UserSchool()."' AND ":'')." ssm.SCHOOL_ID=ac.SCHOOL_ID AND (ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ac.SCHOOL_DATE)) 
			AND ac.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' ".$extra['WHERE']),array('GRADE_ID'=>'_make','STUDENTS'=>'_make','TOTAL_ATTENDANCE'=>'_make','PRESENT'=>'_make','ABSENT'=>'_make','NOT_TAKEN'=>'_make','ADA'=>'_make','AVERAGE_ATTENDANCE'=>'_make','AVERAGE_ABSENT'=>'_make','DAYS_POSSIBLE'=>'_make','ATTENDANCE_POSSIBLE'=>'_make'));
                            	
		 $columns = array('GRADE_ID'=>'Grade','STUDENTS'=>'Students','DAYS_POSSIBLE'=>'Days Possible','ATTENDANCE_POSSIBLE'=>'Attendance Possible','PRESENT'=>'Present','ABSENT'=>'Absent','NOT_TAKEN'=>'Not Taken','ADA'=>'ADA','AVERAGE_ATTENDANCE'=>'Avg Attendance','AVERAGE_ABSENT'=>'Avg Absent');
              

		$link['add']['html'] = array('GRADE_ID'=>'<b>'.'Total'.'</b>','STUDENTS'=>round($sum['STUDENTS'],1),'DAYS_POSSIBLE'=>$cal_days[key($cal_days)][1]['COUNT'],'ATTENDANCE_POSSIBLE'=>$sum['ATTENDANCE_POSSIBLE'],'PRESENT'=>$sum['PRESENT'],'ADA'=>Percent($sum['PRESENT']/$sum['ATTENDANCE_POSSIBLE']),'ABSENT'=>$sum['ABSENT'],'NOT_TAKEN'=>$sum['NOT_TAKEN'],'AVERAGE_ATTENDANCE'=>round($sum['AVERAGE_ATTENDANCE'],1),'AVERAGE_ABSENT'=>round($sum['AVERAGE_ABSENT'],1));
                
ListOutput($student_days_possible,$columns,'Grade level','Grade levels',$link);
		
		
		
	}
}

function _make($value,$column)
{	global $THIS_RET,$student_days_absent,$student_not_taken,$cal_days,$sum,$calendars_RET,$student_days_present,$attpossible;

	switch($column)
	{

		case 'STUDENTS':
			
			$sum['STUDENTS'] += $value;
			return $value;
			break;

		case 'DAYS_POSSIBLE':
			
			return $cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
			break;
		
		case 'TOTAL_ATTENDANCE':
		
			$dayespossible = $cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
			$students = $value;
			$total_attn = ($dayespossible * $students);
			$sum['TOTAL_ATTENDANCE'] += $total_attn;
			return $total_attn;
			break;
		
		case 'PRESENT':

		    $present_by_gread = 0;
		
		    $present_by_gread = $student_days_present[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['PRESENT_BY_GREAD'];
   			$sum['PRESENT'] += $present_by_gread;
			return $present_by_gread;
			break;

		case 'ABSENT':
		
           $absent = 0;
		   $absent = $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'];
		   $absent = round($absent);
	       $sum['ABSENT'] += $absent;
		   return $absent;
		       
         break;
				
				
		case 'NOT_TAKEN':
		
			$not_taken = 0;
		   	$not_taken = $student_not_taken[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['NOT_TAKEN'];
		   	$not_taken = round($not_taken);
	       	$sum['NOT_TAKEN'] += $not_taken;
			return $not_taken;
		       
            break;

		case 'ATTENDANCE_POSSIBLE':

			$attpossible = $value;
			$sum['ATTENDANCE_POSSIBLE'] += $attpossible;
			return $attpossible;
            break;
            
		case 'ADA':
			
			$present_by_gread = $student_days_present[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['PRESENT_BY_GREAD'];
			$ada = round($present_by_gread*100/$attpossible,2) . '%';
			return $ada;
		 
		 break;

		case 'AVERAGE_ATTENDANCE':
		
			$present_by_gread = 0;
			$present_by_gread = $student_days_present[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['PRESENT_BY_GREAD'];

			$present = $present_by_gread;
			$dayespossible = $cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
			$avg_attn = ($present/$dayespossible);
			$sum['AVERAGE_ATTENDANCE'] += $avg_attn;
			return $avg_attn = round($avg_attn, 1);

            break;

		case 'AVERAGE_ABSENT':
		
			$sum['AVERAGE_ABSENT'] += ($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT']);
			return round($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
			break;

		case 'GRADE_ID':
			
			return GetGrade($value).(count($cal_days)>1?' - '.$calendars_RET[$THIS_RET['CALENDAR_ID']][1]['TITLE']:'');
			break;	
	}
}

function _makeByDay($value,$column)
{	global $THIS_RET,$student_days_absent,$atten_possible,$cal_days,$sum;

	switch($column)
	{
		case 'ATTENDANCE_POSSIBLE':
			
			if($atten_possible[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['ST_ID'])
			return 1;
			else
			return 0;
		break;
		
		case 'STUDENTS':
			$sum['STUDENTS'] += $value/$cal_days;
			return round($value/$cal_days,1);
		break;

		case 'DAYS_POSSIBLE':
			return $cal_days;
		break;
		
		case 'TOTAL_ATTENDANCE':
			return $sum['TOTAL_ATTENDANCE'] += $total_attn;
		break;

		case 'PRESENT':
			
			$sum['PRESENT'] += ($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
			$PRESENT_STU = $THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'] ;
			if($atten_possible[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['ST_ID'])
			return $THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'] ;
			else
			return "";
			
		break;

		case 'ABSENT':
			$sum['ABSENT'] += ($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
			if($atten_possible[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['ST_ID'])
			return round($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
		    else
			return "";
		break;

		case 'ADA':
			if($atten_possible[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['ST_ID'])
			return Percent((($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']))/$THIS_RET['STUDENTS']);
			else
			return "";
			
		break;

		case 'AVERAGE_ATTENDANCE':
			$sum['AVERAGE_ATTENDANCE'] += (($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'])/$cal_days);
			if($atten_possible[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['ST_ID'])
			return round(($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'])/$cal_days,1);
			else
			return "";
			
		break;

		case 'AVERAGE_ABSENT':
			$sum['AVERAGE_ABSENT'] += ($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']/$cal_days);
	if($atten_possible[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['ST_ID'])
			return round($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']/$cal_days,1);
		else
			return "";
			
		break;
	}
}
?>
