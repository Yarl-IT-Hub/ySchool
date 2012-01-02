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
define('LANG_RECORDS_ADDED_CONFIRMATION','Absence records were added for the selected students.');
define('LANG_CHOOSE_STUDENT_ERROR','You must choose at least one period and one student.');
define('LANG_ABSENCE_CODE','Absence Code');
define('LANG_ABSENCE_REASON','Absence Reason');
//include("languages/English/$_REQUEST[modname]");
if(!$_REQUEST['month'])
	$_REQUEST['month'] = date("m");
else
	$_REQUEST['month'] = MonthNWSwitch($_REQUEST['month'],'tonum');
if(!$_REQUEST['year'])
	$_REQUEST['year'] = date("Y");
else
	$_REQUEST['year'] = ($_REQUEST['year']<1900?'20'.$_REQUEST['year']:$_REQUEST['year']);

//if($_REQUEST['modfunc']=='save')
if(optional_param('modfunc','',PARAM_NOTAGS)=='save')
{
    
	if(count($_REQUEST['period']) && count($_REQUEST['student']) && count($_REQUEST['dates']))
	{
		foreach($_REQUEST['period'] as $period_id=>$yes)
			$periods_list .= ",'".$period_id."'";
		$periods_list = '('.substr($periods_list,1).')';

		foreach($_REQUEST['student'] as $student_id=>$yes)
			$students_list .= ",'".$student_id."'";
		$students_list = '('.substr($students_list,1).')';
		

		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID,PERIOD_ID,SCHOOL_DATE,ATTENDANCE_CODE FROM ATTENDANCE_PERIOD WHERE EXTRACT(MONTH FROM SCHOOL_DATE)='".($_REQUEST['month']*1)."' AND EXTRACT(YEAR FROM SCHOOL_DATE)='$_REQUEST[year]' AND PERIOD_ID IN $periods_list AND STUDENT_ID IN $students_list"),array(),array('STUDENT_ID','SCHOOL_DATE','PERIOD_ID'));
		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			foreach($_REQUEST['dates'] as $date=>$yes)
			{
				$current_mp = GetCurrentMP('QTR',$date);
                                if(!$current_mp)
                                    $current_mp = GetCurrentMP('SEM',$date);
                                if(!$current_mp)
                                    $current_mp = GetCurrentMP('FY',$date);
                                
				$all_mp = GetAllMP(GetMPTable(GetMP($current_mp,'TABLE')),$current_mp);
				/*$course_periods_RET = DBGet(DBQuery("SELECT s.COURSE_PERIOD_ID,cp.PERIOD_ID FROM SCHEDULE s,COURSE_PERIODS cp,ATTENDANCE_CALENDAR ac,SCHOOL_PERIODS sp WHERE sp.PERIOD_ID=cp.PERIOD_ID AND ac.SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND ac.CALENDAR_ID=cp.CALENDAR_ID AND (ac.BLOCK=sp.BLOCK OR sp.BLOCK IS NULL) AND s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND s.STUDENT_ID='$student_id' AND cp.PERIOD_ID IN $periods_list AND cp.DOES_ATTENDANCE='Y' AND (ac.SCHOOL_DATE BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND ac.SCHOOL_DATE>=s.START_DATE)) AND position(substring('UMTWHFS' FROM DAYOFWEEK(ac.SCHOOL_DATE)  FOR 1) IN cp.DAYS)>0 AND cp.MARKING_PERIOD_ID IN ($all_mp) AND s.MARKING_PERIOD_ID IN ($all_mp) AND NOT (cp.HALF_DAY='Y' AND (SELECT STATE_CODE FROM ATTENDANCE_CODES WHERE ID='$_REQUEST[absence_code]')='H')"),array(),array('PERIOD_ID'));*/
				$course_periods_RET = DBGet(DBQuery("SELECT s.COURSE_PERIOD_ID,cp.PERIOD_ID FROM SCHEDULE s,COURSE_PERIODS cp,ATTENDANCE_CALENDAR ac,SCHOOL_PERIODS sp WHERE sp.PERIOD_ID=cp.PERIOD_ID AND ac.SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND ac.CALENDAR_ID=cp.CALENDAR_ID AND (ac.BLOCK=sp.BLOCK OR sp.BLOCK IS NULL) AND s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND s.STUDENT_ID='$student_id' AND cp.PERIOD_ID IN $periods_list AND cp.DOES_ATTENDANCE='Y' AND (ac.SCHOOL_DATE BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND ac.SCHOOL_DATE>=s.START_DATE)) AND position(substring('UMTWHFS' FROM DAYOFWEEK(ac.SCHOOL_DATE)  FOR 1) IN cp.DAYS)>0 AND cp.MARKING_PERIOD_ID IN ($all_mp) AND s.MARKING_PERIOD_ID IN ($all_mp) AND NOT (cp.HALF_DAY='Y' AND (SELECT STATE_CODE FROM ATTENDANCE_CODES WHERE ID='".optional_param('absence_code','',PARAM_NUMBER)."')='H')"),array(),array('PERIOD_ID'));
				
				foreach($_REQUEST['period'] as $period_id=>$yes)
				{ if(!$current_RET[$student_id][$date][$period_id])
					{
						$course_period_id = $course_periods_RET[$period_id][1]['COURSE_PERIOD_ID'];
						if($course_period_id)
						{
							/*$sql = "INSERT INTO ATTENDANCE_PERIOD (STUDENT_ID,SCHOOL_DATE,PERIOD_ID,MARKING_PERIOD_ID,COURSE_PERIOD_ID,ATTENDANCE_CODE,ATTENDANCE_TEACHER_CODE,ATTENDANCE_REASON,ADMIN)
										values('$student_id','$date','$period_id','$current_mp','$course_period_id','$_REQUEST[absence_code]','$_REQUEST[absence_code]','$_REQUEST[absence_reason]','Y')";*/
												
							$sql = "INSERT INTO ATTENDANCE_PERIOD (STUDENT_ID,SCHOOL_DATE,PERIOD_ID,MARKING_PERIOD_ID,COURSE_PERIOD_ID,ATTENDANCE_CODE,ATTENDANCE_TEACHER_CODE,ATTENDANCE_REASON,ADMIN)values('$student_id','$date','$period_id','$current_mp','$course_period_id','".optional_param('absence_code','',PARAM_NUMBER)."','".optional_param('absence_code','',PARAM_NUMBER)."','".optional_param('absence_reason','',PARAM_SPCL)."','Y')";
							DBQuery($sql);
						}
					}
					else
					{
						/*$sql = "UPDATE ATTENDANCE_PERIOD SET ATTENDANCE_CODE='$_REQUEST[absence_code]',ATTENDANCE_TEACHER_CODE='$_REQUEST[absence_code]',ATTENDANCE_REASON='$_REQUEST[absence_reason]',ADMIN='Y'
								WHERE STUDENT_ID='$student_id' AND SCHOOL_DATE='$date' AND PERIOD_ID='$period_id'";
							*/	
						$sql = "UPDATE ATTENDANCE_PERIOD SET ATTENDANCE_CODE='".optional_param('absence_code','',PARAM_NUMBER)."',ATTENDANCE_TEACHER_CODE='".optional_param('absence_code','',PARAM_NUMBER)."',ATTENDANCE_REASON='".optional_param('absence_reason','',PARAM_SPCL)."',ADMIN='Y'
								WHERE STUDENT_ID='$student_id' AND SCHOOL_DATE='$date' AND PERIOD_ID='$period_id'";
						DBQuery($sql);
					}
				}
				$val=optional_param('absence_reason','',PARAM_SPCL);
				//UpdateAttendanceDaily($student_id,$date,($_REQUEST['absence_reason']?$_REQUEST['absence_reason']:false));
				UpdateAttendanceDaily($student_id,$date,($val?$val:false));
				
			}
		}
                //-----------------------For update attendance_completed----------------------------------------
                $current_RET = DBGet(DBQuery("SELECT STUDENT_ID,PERIOD_ID,SCHOOL_DATE,ATTENDANCE_CODE FROM ATTENDANCE_PERIOD WHERE EXTRACT(MONTH FROM SCHOOL_DATE)='".($_REQUEST['month']*1)."' AND EXTRACT(YEAR FROM SCHOOL_DATE)='$_REQUEST[year]'"),array(),array('SCHOOL_DATE','PERIOD_ID'));
                foreach($_REQUEST['dates'] as $date=>$yes)
                {
                    $course_periods_RET = DBGet(DBQuery("SELECT s.COURSE_PERIOD_ID,cp.PERIOD_ID,cp.TEACHER_ID FROM SCHEDULE s,COURSE_PERIODS cp,ATTENDANCE_CALENDAR ac,SCHOOL_PERIODS sp WHERE sp.PERIOD_ID=cp.PERIOD_ID AND ac.SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND ac.CALENDAR_ID=cp.CALENDAR_ID AND (ac.BLOCK=sp.BLOCK OR sp.BLOCK IS NULL) AND s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.DOES_ATTENDANCE='Y' AND (ac.SCHOOL_DATE BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND ac.SCHOOL_DATE>=s.START_DATE)) AND position(substring('UMTWHFS' FROM DAYOFWEEK(ac.SCHOOL_DATE)  FOR 1) IN cp.DAYS)>0 AND cp.MARKING_PERIOD_ID IN ($all_mp) AND s.MARKING_PERIOD_ID IN ($all_mp) AND NOT (cp.HALF_DAY='Y' AND (SELECT STATE_CODE FROM ATTENDANCE_CODES WHERE ID='".optional_param('absence_code','',PARAM_NUMBER)."')='H')"),array(),array('PERIOD_ID'));
                    foreach($_REQUEST['period'] as $period_id=>$yes)
                    {
                        $attn_taken=count($current_RET[$date][$period_id]);
                        $attn_possible=count($course_periods_RET[$period_id]);
                        if($attn_possible==$attn_taken && count($attn_possible)>0)
                        {
                            $RET = DBGet(DBQuery("SELECT 'completed' AS COMPLETED FROM ATTENDANCE_COMPLETED WHERE STAFF_ID='".$course_periods_RET[$period_id][1]['TEACHER_ID']."' AND SCHOOL_DATE='".$date."' AND PERIOD_ID='".$period_id."'"));
                            if(!count($RET))
                                DBQuery("INSERT INTO ATTENDANCE_COMPLETED (STAFF_ID,SCHOOL_DATE,PERIOD_ID) values('".$course_periods_RET[$period_id][1]['TEACHER_ID']."','$date','".$period_id."')");
                        }
                    }
                }
                //---------------------------------------------------------------
		unset($_REQUEST['modfunc']);
		$note = LANG_RECORDS_ADDED_CONFIRMATION;
	}
	else{
                        echo '<font color=red>'.LANG_CHOOSE_STUDENT_ERROR.'</font>';
                        for_error_sch();
}
}



if(!$_REQUEST['modfunc'])
{
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",NULL AS CHECKBOX";

	//if($_REQUEST['search_modfunc']=='list')
	if(optional_param('search_modfunc','',PARAM_NOTAGS)=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save METHOD=POST name=addAbsences>";
		
		PopTable_wo_header ('header');

		echo '<BR>';

		echo '<CENTER><TABLE><TR><TD align=right>Add absence to periods</TD>';
		echo '<TD><TABLE><TR>';
	
		$periods_RET = DBGet(DBQuery("SELECT SHORT_NAME,PERIOD_ID FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND EXISTS (SELECT * FROM COURSE_PERIODS WHERE PERIOD_ID=SCHOOL_PERIODS.PERIOD_ID AND DOES_ATTENDANCE='Y') ORDER BY SORT_ORDER"));
		foreach($periods_RET as $period)
			echo '<TD><INPUT type=CHECKBOX value=Y name=period['.$period['PERIOD_ID'].']>'.$period['SHORT_NAME'].'</TD>';
		echo '</TR></TABLE></TD>';
		echo '<TR><TD align=right>Absence code</TD><TD><SELECT name=absence_code>';
		$codes_RET = DBGet(DBQuery("SELECT TITLE,ID FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='0'"));
		foreach($codes_RET as $code)
			echo '<OPTION value='.$code['ID'].'>'.$code['TITLE'].'</OPTION>';
		echo '</SELECT></TD></TR>';
		echo '<TR><TD align=right>Absence reason</TD><TD><INPUT type=text name=absence_reason></TD></TR>';
	
		echo '<TR><TD colspan=2 align=center>';
		$time = mktime(0,0,0,$_REQUEST['month']*1,1,substr($_REQUEST['year'],2));
		echo PrepareDate(strtoupper(date("d-M-y",$time)),'',false,array('M'=>1,'Y'=>1,'submit'=>true));

		$skip = date("w",$time);
		$last = 31;
		while(!checkdate($_REQUEST['month']*1, $last, substr($_REQUEST['year'],2)))
			$last--;

		echo '<TABLE><TR>';
		echo '<TH>S</TH><TH>M</TH><TH>T</TH><TH>W</TH><TH>Th</TH><TH>F</TH><TH>S</TH></TR><TR>';
		$calendar_RET = DBGet(DBQuery("SELECT SCHOOL_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND MINUTES!='0' AND EXTRACT(MONTH FROM SCHOOL_DATE)='".($_REQUEST['month']*1)."'"),array(),array('SCHOOL_DATE'));
		for($i=1;$i<=$skip;$i++)
			echo '<TD></TD>';

		for($i=1;$i<=$last;$i++)
		{
			$this_date = $_REQUEST['year'].'-'.$_REQUEST['month'].'-'.($i<10?'0'.$i:$i);
			if(!$calendar_RET[$this_date])
				$disabled = ' DISABLED';
			elseif(date('Y-m-d')==$this_date)
				$disabled = ' CHECKED';
			else
				$disabled = '';

			echo '<TD align=right>'.$i.'<INPUT type=checkbox name=dates['.$this_date.'] value=Y'.$disabled.'></TD>';
			$skip++;
			if($skip%7==0 && $i!=$last)
				echo '</TR><TR>';
		}
		echo '</TR></TABLE>';
		echo '</TD></TR></TABLE>';
		Poptable ('footer');
	}
	elseif($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);

	Widgets('activity');
	Widgets('course');
	Widgets('absences');

	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Search('student_id',$extra);
	//if($_REQUEST['search_modfunc']=='list')
	if(optional_param('search_modfunc','',PARAM_ALPHA)=='list')
		echo '<BR><CENTER>'.SubmitButton(Save,'','class=btn_medium onclick="formload_ajax(\'addAbsences\');"')."</CENTER></FORM>";
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}
?>
