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
include 'modules/Attendance/config.inc.php';

$temp_date = $_REQUEST['date'];
$From = $_REQUEST['From'];
 $to = $_REQUEST['to'];
# --------------------------------------------- Date Convertion Start ----------------------------------------------- #

        function con_date($date)
        {
            $dt_arr = explode('/',$date);
            $temp_month = $dt_arr[0];
            if($temp_month == 'Jan' || $temp_month == 'January')
                $dt_arr[0] =1;
            elseif($temp_month == 'Feb' || $temp_month == 'February')
                $dt_arr[0] =2;
            elseif($temp_month == 'Mar' || $temp_month == 'March')
                $dt_arr[0] =3;
            elseif($temp_month == 'Apr' || $temp_month == 'April')
                $dt_arr[0] =4;
            elseif($temp_month == 'May' || $temp_month == 'May')
                $dt_arr[0] =5;
            elseif($temp_month == 'Jun' || $temp_month == 'June')
                $dt_arr[0] =6;
            elseif($temp_month == 'Jul' || $temp_month == 'July')
                $dt_arr[0] =7;
            elseif($temp_month == 'Aug' || $temp_month == 'August')
                $dt_arr[0] =8;
            elseif($temp_month == 'Sep' || $temp_month == 'September')
                $dt_arr[0] =9;
            elseif($temp_month == 'Oct' || $temp_month == 'October')
                $dt_arr[0] =10;
            elseif($temp_month == 'Nov' || $temp_month == 'November')
                $dt_arr[0] =11;
            elseif($temp_month == 'Dec' || $temp_month == 'December')
                $dt_arr[0] =12;
            return implode('/', $dt_arr);
        }
# --------------------------------------------- date Convertion End ------------------------------------------------- #
               
$final_date = con_date($temp_date);
if($_REQUEST['dt']==1){
    $final_date=$_SESSION['date_attn'];
}
$_REQUEST['dt']=0;	
if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
{
        while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
            $_REQUEST['day_date']--;
    
        $posted_date=ucfirst(strtolower($_REQUEST['month_date'])).'/'. $_REQUEST['day_date'].'/'.$_REQUEST['year_date'];
        $final_date=con_date($posted_date);
        unset($_SESSION['date_attn']);
        $_SESSION['date_attn']=$final_date;
}
else
{
        if(!$temp_date){
            $final_date=date('n/j/Y');
            $_REQUEST['month_date']=strtoupper(date('M'));
            $_REQUEST['day_date']=date('j');
            $_REQUEST['year_date']=date('y');
        }
        else{
            $temp_arr=explode('/',$temp_date);
            $_REQUEST['month_date']=$temp_arr[0];
            $_REQUEST['day_date']=$temp_arr[1];
            $_REQUEST['year_date']=$temp_arr[2];
        }
}

DrawBC("Attendance > ".ProgramTitle());

if(!isset($_REQUEST['table']))
	$_REQUEST['table'] = '0';

if($_REQUEST['table']=='0')
	$table = 'ATTENDANCE_PERIOD';
else
	$table = 'LUNCH_PERIOD';

/*$course_RET = DBGET(DBQuery("SELECT cp.HALF_DAY FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE acc.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND acc.SCHOOL_DATE='$date' AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM cast(extract(DOW FROM acc.SCHOOL_DATE) AS INT)+1 FOR 1) IN cp.DAYS)>0
	OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
".($_REQUEST['table']=='0'?"AND cp.DOES_ATTENDANCE='Y'":''))); */

$date=date('Y-m-d',strtotime($final_date));
$course_RET = DBGET(DBQuery("SELECT cp.HALF_DAY FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE acc.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND acc.SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0
	OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
".($_REQUEST['table']=='0'?"AND cp.DOES_ATTENDANCE='Y'":'')));

$mp_id = GetCurrentMP('QTR',$date,false);
if(!$mp_id)
    $mp_id = GetCurrentMP('SEM',$date,false);
if(!$mp_id)
    $mp_id = GetCurrentMP('FY',$date,false);
// if running as a teacher program then openSIS[allow_edit] will already be set according to admin permissions

if(!isset($_openSIS['allow_edit']))
{
	// allow teacher edit if selected date is in the current quarter or in the corresponding grade posting period
	
	#$current_qtr_id = GetCurrentMP('QTR',DBDate(),false);
	$current_qtr_id = $mp_id;
	$time = strtotime(DBDate('postgres'));
	
	
	if(($current_qtr_id || GetMP($mp_id,'POST_START_DATE') && ($time<=strtotime(GetMP($mp_id,'POST_END_DATE')))) && ($edit_days_before=='' || strtotime($date)<=$time+$edit_days_before*86400) && ($edit_days_after=='' || strtotime($date)>=$time-$edit_days_after*86400))
	{
		$_openSIS['allow_edit'] = true;
	}
}

/*$current_Q = "SELECT ATTENDANCE_TEACHER_CODE,STUDENT_ID,ADMIN,COMMENT FROM $table WHERE SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND COURSE_PERIOD_ID='".UserCoursePeriod()."'".($table=='LUNCH_PERIOD'?" AND TABLE_NAME='$_REQUEST[table]'":'');
$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID'));*/

$tabl=optional_param('table','',PARAM_ALPHANUM);
$current_Q = "SELECT ATTENDANCE_TEACHER_CODE,STUDENT_ID,ADMIN,COMMENT FROM $table WHERE SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND COURSE_PERIOD_ID='".UserCoursePeriod()."'".($table=='LUNCH_PERIOD'?" AND TABLE_NAME='$tabl'":'');
$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID'));
if($_REQUEST['attendance'] && ($_POST['attendance'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['attendance'] as $student_id=>$value)
	{
            if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
            if(isset($_REQUEST['comment'][$student_id])){
	$c= str_replace("'","\'",$_REQUEST['comment'][$student_id]);
         $_REQUEST['comment'][$student_id]=clean_param($c,PARAM_SPCL);
                }
            }
		if($current_RET[$student_id])
		{
			$sql = "UPDATE ".$table." SET ATTENDANCE_TEACHER_CODE='".substr($value,5)."' ";
			if($current_RET[$student_id][1]['ADMIN']!='Y')
				$sql .= ",ATTENDANCE_CODE='".substr($value,5)."'";
			if(isset($_REQUEST['comment'][$student_id]))
				{ $cmnt=trim($_REQUEST['comment'][$student_id]);
				  $cmnt=clean_param($cmnt,PARAM_SPCL);
					$sql .= ",COMMENT='".str_replace("'", "\'", $cmnt)."'";}
			$sql .= " WHERE SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND COURSE_PERIOD_ID='".UserCoursePeriod()."' AND STUDENT_ID='$student_id'";
		}
		else
		{ $cmnt=trim($_REQUEST['comment'][$student_id]);
				  $cmnt=clean_param($cmnt,PARAM_SPCL);
			/*$sql = "INSERT INTO ".$table." (STUDENT_ID,SCHOOL_DATE,MARKING_PERIOD_ID,PERIOD_ID,COURSE_PERIOD_ID,ATTENDANCE_CODE,ATTENDANCE_TEACHER_CODE,COMMENT".($table=='LUNCH_PERIOD'?',TABLE_NAME':'').") values('$student_id','$date','$mp_id','".UserPeriod()."','".UserCoursePeriod()."','".substr($value,5)."','".substr($value,5)."','$cmnt'".($table=='LUNCH_PERIOD'?",'$_REQUEST[table]'":'').")";
*/			$sql = "INSERT INTO ".$table." (STUDENT_ID,SCHOOL_DATE,MARKING_PERIOD_ID,PERIOD_ID,COURSE_PERIOD_ID,ATTENDANCE_CODE,ATTENDANCE_TEACHER_CODE,COMMENT".($table=='LUNCH_PERIOD'?',TABLE_NAME':'').") values('$student_id','$date','$mp_id','".UserPeriod()."','".UserCoursePeriod()."','".substr($value,5)."','".substr($value,5)."','".str_replace("'", "\'", $cmnt)."'".($table=='LUNCH_PERIOD'?",'".optional_param('table','',PARAM_ALPHANUM)."'":'').")";
		}
		DBQuery($sql);
		if($_REQUEST['table']=='0')
		
			UpdateAttendanceDaily($student_id,$date);
	}
	if($_REQUEST['table']=='0')
	{
		$RET = DBGet(DBQuery("SELECT 'completed' AS COMPLETED FROM ATTENDANCE_COMPLETED WHERE (STAFF_ID='".User('STAFF_ID')."' OR SUBSTITUTE_STAFF_ID='".  User('STAFF_ID')."') AND SCHOOL_DATE='".date('Y-m-d',strtotime($date))."' AND PERIOD_ID='".UserPeriod()."'"));
		if(!count($RET)){
                                                $teacher_type=DBGet(DBQuery("SELECT TEACHER_ID,SECONDARY_TEACHER_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".UserCoursePeriod()."'"));
                                                
                                                $secondary_teacher_id=$teacher_type[1]['SECONDARY_TEACHER_ID'];
                                                if($secondary_teacher_id==  User('STAFF_ID'))
                                                    DBQuery("INSERT INTO ATTENDANCE_COMPLETED (STAFF_ID,SCHOOL_DATE,PERIOD_ID,SUBSTITUTE_STAFF_ID,IS_TAKEN_BY_SUBSTITUTE_STAFF) values('".$teacher_type[1]['TEACHER_ID']."','$date','".UserPeriod()."','".$secondary_teacher_id."','Y')");
                                                else
                                                    DBQuery("INSERT INTO ATTENDANCE_COMPLETED (STAFF_ID,SCHOOL_DATE,PERIOD_ID,SUBSTITUTE_STAFF_ID) values('".$teacher_type[1]['TEACHER_ID']."','$date','".UserPeriod()."','".$secondary_teacher_id."')");
                                    
                                                DBQuery("DELETE FROM MISSING_ATTENDANCE WHERE TEACHER_ID='".$teacher_type[1]['TEACHER_ID']."' AND SCHOOL_DATE='".$date."' AND PERIOD_ID='".  UserPeriod()."'");
	}
	}

	$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID'));
	unset($_SESSION['_REQUEST_vars']['attendance']);
}

$codes_RET = DBGet(DBQuery("SELECT ID,TITLE,DEFAULT_CODE,STATE_CODE FROM ATTENDANCE_CODES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND TYPE = 'teacher' AND TABLE_NAME='".$_REQUEST['table']."'".($course_RET[1]['HALF_DAY'] ? " AND STATE_CODE!='H'" : '')." ORDER BY SORT_ORDER"));
if(count($codes_RET))
{
	foreach($codes_RET as $code)
	{
		$extra['SELECT'] .= ",'$code[STATE_CODE]' AS CODE_".$code['ID'];
		if($code['DEFAULT_CODE']=='Y')
			$extra['functions']['CODE_'.$code['ID']] = '_makeRadioSelected';
		else
			$extra['functions']['CODE_'.$code['ID']] = '_makeRadio';
		$columns['CODE_'.$code['ID']] = $code['TITLE'];
	}
}
else
	$columns = array();
$extra['SELECT'] .= ',s.STUDENT_ID AS COMMENT';
$columns += array('COMMENT'=>'Comment');
if(!is_array($extra['functions']))
	$extra['functions'] = array();
$extra['functions'] += array('FULL_NAME'=>'_makeTipMessage','COMMENT'=>'makeCommentInput');
$extra['DATE'] = date("Y-m-d",strtotime($date));


$stu_RET = GetStuListAttn($extra);

$date_note = $date!=date('Y-m-d') ? ' <span class=red>The selected date is not today</span>' : '';

#$date_note .= AllowEdit() ? ' <FONT COLOR=green>You can edit this attendance</FONT>':' <FONT COLOR=red>You can not edit this attendance</FONT>';
# commented as requested


if($_REQUEST['table']=='0')
{
	$completed_RET = DBGet(DBQuery("SELECT 'Y' as COMPLETED,STAFF_ID,SUBSTITUTE_STAFF_ID,IS_TAKEN_BY_SUBSTITUTE_STAFF FROM ATTENDANCE_COMPLETED WHERE (STAFF_ID='".User('STAFF_ID')."' OR SUBSTITUTE_STAFF_ID='".User('STAFF_ID')."') AND SCHOOL_DATE='$date' AND PERIOD_ID='".UserPeriod()."'"));
	 if($completed_RET){
                        if($completed_RET[1]['IS_TAKEN_BY_SUBSTITUTE_STAFF']!='Y' && User('STAFF_ID')==$completed_RET[1]['SUBSTITUTE_STAFF_ID'])
                            $note = ErrorMessage(array('<IMG SRC=assets/check.gif>Primary teacher has taken attendance today for this period.'),'note');
                        elseif($completed_RET[1]['IS_TAKEN_BY_SUBSTITUTE_STAFF']=='Y' && User('STAFF_ID')==$completed_RET[1]['STAFF_ID'])
                            $note = ErrorMessage(array('<IMG SRC=assets/check.gif>Secondary teacher has taken attendance today for this period.'),'note');
                        else
            $note = ErrorMessage(array('<IMG SRC=assets/check.gif>You have taken attendance today for this period.'),'note');
                   }
	if($_SESSION['miss_attn']==1)
	{
	   
            if($_REQUEST['username']=='admin')
            $note1 ='<a href=Modules.php?modname=Users/TeacherPrograms.php?include=Attendance/Missing_Attendance.php&From='.$From.'&to='.$to.'><< Back to Missing Attendance List </a>';
            #else
            #$note1 ='<A HREF="Modules.php?modname=misc/Portal.php?back_mssn_attn_list=Y">Back to Missing Attendance List</A>';
        }

}
//if($_REQUEST['attn']=='miss')
if(optional_param('attn',PARAM_NOTGAS)=='miss')
{
DrawHeaderHome('<A HREF="Modules.php?modname=misc/Portal.php?back_mssn_attn_list=Y">Back to Missing Attendance List</A>');
}
echo "<FORM ACTION=Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]&username=$_REQUEST[username]&From=$From&to=$to&attn=$_REQUEST[attn] method=POST>";
//echo "<FORM ACTION=Modules.php?modname=".optional_param('modname','',PARAM_NOTAGS)."&table=".optional_param('table','',PARAM_NOTAGS)."&username=".optional_param('username','',PARAM_SPCL)."&From=$From&to=$to&attn=".optional_param('attn','',PARAM_NOTAGS)." method=POST>";

#if(isset($temp_date))
	#$date = '23-FEB-09';
	
#echo '<div style="padding-left:10px;"><A HREF=Modules.php?modname=Users/TeacherPrograms.php?include=Attendance/Missing_Attendance.php&flag=bktms&bottom_back=true target=body><strong>&laquo; Back to Missing attendance teacher\'s List</strong></A></div>';

if(count($course_RET)!=0){
DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true),'Y').$date_note,SubmitButton('Save','','class=btn_medium'));
}else{
echo '<div style="float:left;">';
DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true),'Y').$date_note);
echo '</div>';
}


echo "<div style='padding-left:10px; padding-top:8px; float:left;'><input type='button' value='Go' class='btn_medium' onClick='document.location.href=\"Modules.php?modname=Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php&amp;period=$_REQUEST[period]&amp;include=Attendance/TakeAttendance.php&amp;day_date=\"+this.form.day_date.value+\"&amp;year_date=\"+this.form.year_date.value+\"&amp;table=0&amp;month_date=\"+this.form.month_date.value;' /></div><div style='clear:both;'></div>";


DrawHeader($note,$note1);

$LO_columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','GRADE_ID'=>'Grade') + $columns;

$tabs[] = array('title'=>'Attendance','link'=>"Modules.php?modname=$_REQUEST[modname]&table=0&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]");
$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ATTENDANCE_CODE_CATEGORIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
foreach($categories_RET as $category)
	$tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&table=$category[ID]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]");

echo '<BR>';

if(count($categories_RET))
{

	echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]").'</CENTER>';
	$extra = array('download'=>false,'search'=>false);
}
else
{
	$extra = array();
	$singular = 'Student';
	$plural = 'Students';
}
if(!$mp_id){
	echo "<table align=center><tr><td class=note></td><td class=note_msg>The selected date is not in a school quarter.</td></tr></table>";
}else
{
	if(count($course_RET)!=0)
	{
	    echo '<div style="overflow:auto; width:840px;">';
		
		ListOutput($stu_RET,$LO_columns,$singular,$plural,array(),array(),$extra);
		echo '</div>';

		echo '<CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>';
	}
	else
		echo "<table align=center><tr><td class=note></td><td class=note_msg>You cannot take attendance for this period on this day</td></tr></table>";
}
	
echo '</FORM>';

function _makeRadio($value,$title)
{	global $THIS_RET,$current_RET;

	$colors = array('P'=>'#00FF00','A'=>'#FF0000','H'=>'#FFCC00','T'=>'#0000FF');
	if($current_RET[$THIS_RET['STUDENT_ID']][1]['ATTENDANCE_TEACHER_CODE']==substr($title,5))
		return "<TABLE align=center".($colors[$value]?' bgcolor='.$colors[$value]:'')."><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title' CHECKED></TD></TR></TABLE>";
	else
		return "<TABLE align=center><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title'".(AllowEdit()?'':' ')."></TD></TR></TABLE>";
}

function _makeRadioSelected($value,$title)
{	global $THIS_RET,$current_RET;

	$colors = array('P'=>'#00FF00','A'=>'#FF0000','H'=>'#FFCC00','T'=>'#0000FF');
	$colors1 = array('P'=>'#DDFFDD','A'=>'#FFDDDD','H'=>'#FFEEDD','T'=>'#DDDDFF');
	if($current_RET[$THIS_RET['STUDENT_ID']][1]['ATTENDANCE_TEACHER_CODE']!='')
		if($current_RET[$THIS_RET['STUDENT_ID']][1]['ATTENDANCE_TEACHER_CODE']!=substr($title,5))
			return "<TABLE align=center><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title'".(AllowEdit()?'':' ')."></TD></TR></TABLE>";
		else
			return "<TABLE align=center".($colors[$value]?' bgcolor='.$colors[$value]:'')."><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title' CHECKED></TD></TR></TABLE>";
	else
		return "<TABLE align=center".($colors1[$value]?' bgcolor='.$colors1[$value]:'')."><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title' CHECKED></TD></TR></TABLE>";
}

function _makeTipMessage($value,$title)
{	global $THIS_RET,$StudentPicturesPath;

	if($StudentPicturesPath && ($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.$THIS_RET['STUDENT_ID'].'.JPG','r') || $file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.$THIS_RET['STUDENT_ID'].'.JPG','r')))
		return '<DIV onMouseOver=\'stm(["'.str_replace("'",'&#39;',$THIS_RET['FULL_NAME']).'","<IMG SRC='.str_replace('\\','\\\\',$picture_path).'>"],["white","#333366","","","",,"black","#e8e8ff","","","",,,,2,"#333366",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'>'.$value.'</DIV>';
	else
		return $value;
}

function makeCommentInput($student_id,$column)
{	global $current_RET;

	return TextInput($current_RET[$student_id][1]['COMMENT'],'comment['.$student_id.']','','maxlength=80',true,true);
}
?>
