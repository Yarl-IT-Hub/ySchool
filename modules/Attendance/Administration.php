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
//include("languages/English/$_REQUEST[modname]");
DrawBC("Attendance >> ".ProgramTitle());

if($_REQUEST['codes'])
    $_SESSION['code']=$_REQUEST['codes'];
if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
{
	//echo $_REQUEST['year_date'];
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
	/*if($_SESSION['Administration.php']['date'] && $_SESSION['Administration.php']['date']!=$date)
	{
		unset($_REQUEST['attendance']);
		unset($_REQUEST['attendance_day']);
	}*/
}
else
{
	$date = DBDate();
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = strtoupper(date('M'));
	$_REQUEST['year_date'] = date('y');
}

if(!$_REQUEST['table'])
	$_REQUEST['table'] = '0';

if($_REQUEST['table']==0)
{
	$table = 'ATTENDANCE_PERIOD';
	$extra_sql = '';
}
else
{
	$table = 'LUNCH_PERIOD';
	$extra_sql = " AND TABLE_NAME='$_REQUEST[table]'";
}
$_SESSION['Administration.php']['date'] = $date;

$current_RET = DBGet(DBQuery("SELECT ATTENDANCE_TEACHER_CODE,ATTENDANCE_CODE,ATTENDANCE_REASON,STUDENT_ID,ADMIN,COURSE_PERIOD_ID FROM $table WHERE SCHOOL_DATE='".$date."'".$extra_sql),array(),array('STUDENT_ID','COURSE_PERIOD_ID'));

    $current_mp = GetCurrentMP('QTR',$date);
    $MP_TYPE='QTR';
if(!$current_mp){
    $current_mp = GetCurrentMP('SEM',$date);
    $MP_TYPE='SEM';
}
if(!$current_mp){
    $current_mp = GetCurrentMP('FY',$date);
    $MP_TYPE='FY';
}

if($_REQUEST['attendance'] && ($_POST['attendance'] || $_REQUEST['ajax']) && AllowEdit())
{
	foreach($_REQUEST['attendance'] as $student_id=>$values)
	{
		foreach($values as $period=>$columns)
		{
			if($current_RET[$student_id][$period])
			{
				$sql = "UPDATE $table SET ADMIN='Y',";

				foreach($columns as $column=>$value)
					$sql .= $column."='".str_replace("\'","''",$value)."',";

				$sql = substr($sql,0,-1) . " WHERE SCHOOL_DATE='".$date."' AND COURSE_PERIOD_ID='".$period."' AND STUDENT_ID='".$student_id."'".$extra_sql;
				DBQuery($sql);
			}
			else
			{
				$period_id = DBGet(DBQuery("SELECT PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='$period'"));
				$period_id = $period_id[1]['PERIOD_ID'];

				$sql = "INSERT INTO $table ";

				$fields = 'STUDENT_ID,SCHOOL_DATE,PERIOD_ID,MARKING_PERIOD_ID,COURSE_PERIOD_ID,ADMIN,';
				$values = "'".$student_id."','".$date."','".$period_id."','".$current_mp."','".$period."','Y',";
				if($table=='LUNCH_PERIOD')
				{
					$fields .= 'TABLE_NAME,';
					$values .= "'".$_REQUEST['table']."',";
				}

				$go = 0;
				foreach($columns as $column=>$value)
				{
					if($value)
					{
						$fields .= $column.',';
						$values .= "'".str_replace("\'","''",$value)."',";
						$go = true;
					}
				}
				$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

				if($go)
					DBQuery($sql);
			}
		}
		/*UpdateAttendanceDaily($student_id,$date,($_REQUEST['attendance_day'][$student_id]['COMMENT']?$_REQUEST['attendance_day'][$student_id]['COMMENT']:false));*/
		$val=clean_param($_REQUEST['attendance_day'][$student_id]['COMMENT'],PARAM_SPCL);
		UpdateAttendanceDaily($student_id,$date,($val?$val:false));
		unset($_REQUEST['attendance_day'][$student_id]);
	}
	echo $_REQUEST['attendance_day'][$student_id]['COMMENT'];
	$current_RET = DBGet(DBQuery("SELECT ATTENDANCE_TEACHER_CODE,ATTENDANCE_CODE,ATTENDANCE_REASON,STUDENT_ID,ADMIN,COURSE_PERIOD_ID FROM $table WHERE SCHOOL_DATE='".$date."'".$extra_sql),array(),array('STUDENT_ID','COURSE_PERIOD_ID'));
	unset($_REQUEST['attendance']);
	unset($_SESSION['_REQUEST_vars']['attendance']);
	unset($_SESSION['_REQUEST_vars']['attendance_day']);
}

if(count($_REQUEST['attendance_day']))
{
	foreach($_REQUEST['attendance_day'] as $student_id=>$comment)

		{$val=clean_param($comment['COMMENT'],PARAM_SPCL);
		//UpdateAttendanceDaily($student_id,$date,$comment['COMMENT']);
		UpdateAttendanceDaily($student_id,$date,$val);
		}
	unset($_REQUEST['attendance_day']);
}

$codes_RET = DBGet(DBQuery("SELECT ID,SHORT_NAME,TITLE,STATE_CODE FROM ATTENDANCE_CODES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND TABLE_NAME='$_REQUEST[table]'"));
$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,SHORT_NAME,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND EXISTS (SELECT * FROM COURSE_PERIODS WHERE PERIOD_ID=SCHOOL_PERIODS.PERIOD_ID AND DOES_ATTENDANCE='Y') ORDER BY SORT_ORDER"));

//if(isset($_REQUEST['student_id']) && $_REQUEST['student_id']!='new')
if(isset($_REQUEST['student_id']) && optional_param('student_id','',PARAM_ALPHANUM)!='new')
{
    //if(UserStudentID() != $_REQUEST['student_id'])
	if(UserStudentID() != optional_param('student_id','',PARAM_ALPHANUM))
	{
		//$_SESSION['student_id'] = $_REQUEST['student_id'];
		$_SESSION['student_id'] = optional_param('student_id','',PARAM_ALPHANUM);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}

	$functions = array('ATTENDANCE_CODE'=>'_makeCodePulldown','ATTENDANCE_TEACHER_CODE'=>'_makeCode','ATTENDANCE_REASON'=>'_makeReasonInput');
	/*$schedule_RET = DBGet(DBQuery("SELECT
										s.STUDENT_ID,concat(c.TITLE) AS COURSE,cp.PERIOD_ID,cp.COURSE_PERIOD_ID,p.TITLE AS PERIOD_TITLE,
										'' AS ATTENDANCE_CODE,'' AS ATTENDANCE_TEACHER_CODE,'' AS ATTENDANCE_REASON
									FROM
										SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS p,ATTENDANCE_CALENDAR ac
									WHERE
										s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID='".UserSchool()."' AND s.MARKING_PERIOD_ID IN (".GetAllMP('QTR',GetCurrentMP('QTR',$date)).")
										AND s.COURSE_ID=c.COURSE_ID
										AND s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.PERIOD_ID=p.PERIOD_ID AND cp.DOES_ATTENDANCE='Y'
										AND s.STUDENT_ID='".$_REQUEST['student_id']."' AND ('$date' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND '$date'>=s.START_DATE))
										AND position(substring('UMTWHFS' FROM DAYOFWEEK(cast('$date' AS DATE)) FOR 1) IN cp.DAYS)>0
										AND ac.CALENDAR_ID=cp.CALENDAR_ID AND ac.SCHOOL_DATE='$date' AND ac.MINUTES!='0'
									ORDER BY p.SORT_ORDER"),$functions);
	$columns = array('PERIOD_TITLE'=>'Period','COURSE'=>'Course','ATTENDANCE_CODE'=>'Attendance Code','ATTENDANCE_TEACHER_CODE'=>'Teacher\'s Entry','ATTENDANCE_REASON'=>'Comment');*/
	
	$schedule_RET = DBGet(DBQuery("SELECT
										s.STUDENT_ID,concat(c.TITLE) AS COURSE,cp.PERIOD_ID,cp.COURSE_PERIOD_ID,p.TITLE AS PERIOD_TITLE,
										'' AS ATTENDANCE_CODE,'' AS ATTENDANCE_TEACHER_CODE,'' AS ATTENDANCE_REASON
									FROM
										SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS p,ATTENDANCE_CALENDAR ac
									WHERE
										s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID='".UserSchool()."' AND s.MARKING_PERIOD_ID IN (".GetAllMP($MP_TYPE,$current_mp).")
										AND s.COURSE_ID=c.COURSE_ID
										AND s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.PERIOD_ID=p.PERIOD_ID AND cp.DOES_ATTENDANCE='Y'
										AND s.STUDENT_ID='".optional_param('student_id','',PARAM_ALPHANUM)."' AND ('$date' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND '$date'>=s.START_DATE))
										AND position(substring('UMTWHFS' FROM DAYOFWEEK(cast('$date' AS DATE)) FOR 1) IN cp.DAYS)>0
										AND ac.CALENDAR_ID=cp.CALENDAR_ID AND ac.SCHOOL_DATE='$date' AND ac.MINUTES!='0'
									ORDER BY p.SORT_ORDER"),$functions);
	$columns = array('PERIOD_TITLE'=>'Period','COURSE'=>'Course','ATTENDANCE_CODE'=>'Attendance Code','ATTENDANCE_TEACHER_CODE'=>'Teacher\'s Entry','ATTENDANCE_REASON'=>'Comment');
        $tmp_req=$_REQUEST;
        $action = PreparePHP_SELF($tmp_req);
        echo "<FORM action=$action&modfunc=student method=POST>";
	#DrawHeaderHome('<A HREF=Modules.php?modname=Students/Student.php&search_modfunc=list&next_modname=Students%2FStudent.php&ajax=true&bottom_back=true target=body  style="text-decoration:none"><strong>Back to Student List</strong></A>');
	if(isset($_REQUEST['student_id']) )
{
        $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
        #-----------------------------------------------------newly added attendance code and the date in back to list--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	DrawHeaderHome('Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&codes[]='.$_SESSION[code][0].'&ajax=true&bottom_back=true&month_date='.$_REQUEST[month_date].'&day_date='.$_REQUEST[day_date].'&year_date='.$_REQUEST[year_date].' target=body  style="text-decoration:none"><strong>Back to Student List</strong></A>');
	#-----------------------------------------------------newly added attendance code and the date in back to list--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	//DrawHeaderHome('Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].' target=body  style="text-decoration:none"><strong>Back to Student List</strong></A>');
	//DrawHeaderHome('Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.optional_param('modcat','',PARAM_NOTAGS).'><font color=red>Remove</font></A>) | <A HREF=Side.php?student_id=new&modcat='.optional_param('modcat','',PARAM_NOTAGS).' target=body  style="text-decoration:none"><strong>Back to Student List</strong></A>');
	}
}
	ListOutput($schedule_RET,$columns,'Course','Courses');
                  echo '<BR><CENTER>'.SubmitButton('UPDATE','','class=btn_wide').'</CENTER>';
	echo '</FORM>';
}
else
{

	
	if($_REQUEST['expanded_view']!='true')
		$extra['WHERE'] = $extra2['WHERE'] = " AND EXISTS (SELECT '' FROM $table ap,ATTENDANCE_CODES ac WHERE ap.SCHOOL_DATE='".$date."' AND ap.STUDENT_ID=ssm.STUDENT_ID AND ap.ATTENDANCE_CODE=ac.ID AND ac.SCHOOL_ID=ssm.SCHOOL_ID AND ac.SYEAR=ssm.SYEAR ".str_replace('TABLE_NAME','ac.TABLE_NAME',$extra_sql);
	else
		$extra['WHERE'] = " AND EXISTS (SELECT '' FROM $table ap,ATTENDANCE_CODES ac WHERE ap.SCHOOL_DATE='".$date."' AND ap.STUDENT_ID=ssm.STUDENT_ID AND ap.ATTENDANCE_CODE=ac.ID AND ac.SCHOOL_ID=ssm.SCHOOL_ID AND ac.SYEAR=ssm.SYEAR ".str_replace('TABLE_NAME','ac.TABLE_NAME',$extra_sql);
																																																														

	if(count($_REQUEST['codes']))
	{
		$REQ_codes = $_REQUEST['codes'];
		foreach($REQ_codes as $key=>$value)
		{
			if(!$value)
				unset($REQ_codes[$key]);
			elseif($value=='A')
				$abs = true;
		}
	}
	else
		$abs = true;
	if(count($REQ_codes) && !$abs)
	{
		$extra['WHERE'] .= "AND ac.ID IN (";
		foreach($REQ_codes as $code)
			$extra['WHERE'] .= "'".$code."',";
		if($_REQUEST['expanded_view']!='true')
			$extra2['WHERE'] = $extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
		else
			$extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
	}
	elseif($abs)
	{
		$RET = DBGet(DBQuery("SELECT ID FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='$_REQUEST[table]'"));
		if(count($RET))
		{
			$extra['WHERE'] .= "AND ac.ID IN (";
			foreach($RET as $code)
				$extra['WHERE'] .= "'".$code['ID']."',";

			if($_REQUEST['expanded_view']!='true')
				$extra2['WHERE'] = $extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
			else
				$extra['WHERE'] = substr($extra['WHERE'],0,-1) . ')';
		}
	}
	$extra['WHERE'] .= ')'; 

	// EXPANDED VIEW BREAKS THIS QUERY.  PLUS, PHONE IS ALREADY AN OPTION IN EXPANDED VIEW
	if($_REQUEST['expanded_view']!='true' && $_REQUEST['_openSIS_PDF']!='true')
	{
		$extra2['WHERE'] .= ')';
		$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
		$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
		$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
		$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
		$extra2['group'] = array('STUDENT_ID','PERSON_ID');
		$contacts_RET = GetStuList($extra2);
		//$extra['columns_before']['PHONE'] = 'Phone';
	}
	$columns = array();
	#$extra['SELECT'] .= ',NULL AS STATE_VALUE,NULL AS DAILY_COMMENT,NULL AS PHONE';
	$extra['SELECT'] .= ',NULL AS STATE_VALUE,NULL AS DAILY_COMMENT,s.PHONE AS PHONE';
	//$extra['functions']['PHONE'] = '_makePhone';
	$extra['functions']['STATE_VALUE'] = '_makeStateValue';
	$extra['functions']['DAILY_COMMENT'] = '_makeStateValue';
	$extra['columns_after']['STATE_VALUE'] = 'Present';
	$extra['columns_after']['DAILY_COMMENT'] = 'Comment';
	$extra['columns_after']['PHONE'] = 'Phone';
	$extra['link']['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]";
	$extra['link']['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');
	$extra['BackPrompt'] = false;
	$extra['Redirect'] = false;
	$extra['new'] = true;
	
	foreach($periods_RET as $period)
	{
		$extra['SELECT'] .= ",'' AS PERIOD_".$period['PERIOD_ID'];
		$extra['functions']['PERIOD_'.$period['PERIOD_ID']] = '_makeCodePulldown';
		$extra['columns_after']['PERIOD_'.$period['PERIOD_ID']] = $period['SHORT_NAME'];
	}

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['day_date']);unset($tmp_REQUEST['month_date']);unset($tmp_REQUEST['year_date']);unset($tmp_REQUEST['codes']);
	$action = PreparePHP_SELF($tmp_REQUEST);
	echo "<FORM action=$action method=POST>";
	if($REQ_codes)
	{
		foreach($REQ_codes as $code)
			$code_pulldowns .= _makeCodeSearch($code);
	}
	elseif($abs)
	{
		$code_pulldowns = _makeCodeSearch('A');
	}	
	else
	{
		$code_pulldowns = _makeCodeSearch();
	}	
		
	if(UserStudentID())
	{
		#$current_student_link = "<A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=student&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]&student_id=".UserStudentID().">".LANG_CURRRENT_STUDENT."</A></TD><TD>";
	}
		
		//----------------------------- Date Edit Start ------------------------------------------//
		
		//-------- if start -------------//
		if(strlen($date)==11)
		{
			$mother_date = $date;
			$date_edit = explode("-", $mother_date);
			
			$day = $date_edit[0];
			$month = $date_edit[1];
			$year = $date_edit[2];
			
			if($month=='JAN')
				$month = '01';
			elseif($month=='FEB')
				$month = '02';
			elseif($month=='MAR')
				$month = '03';
			elseif($month=='APR')
				$month = '04';
			elseif($month=='MAY')
				$month = '05';
			elseif($month=='JUN')
				$month = '06';
			elseif($month=='JUL')
				$month = '07';
			elseif($month=='AUG')
				$month = '08';
			elseif($month=='SEP')
				$month = '09';
			elseif($month=='OCT')
				$month = '10';
			elseif($month=='NOV')
				$month = '11';
			elseif($month=='DEC')
				$month = '12';
				
		$final_date = $year."-".$month."-".$day;
		$date = $final_date;
		} 
		//echo $date;
		//--------- if end --------------//
		
		
		//------------------------------ Date Edit End -------------------------------------------//
		
	DrawHeader('<TABLE><TR><TD>'.PrepareDate($date,'_date',false,array('submit'=>true)).'</TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>'.SubmitButton('Go','','class=btn_medium').'</TD><TR></TABLE>','<TABLE><TR><TD>'.$current_student_link.button('add','',"# onclick='javascript:addHTML(\"".str_replace('"','\"',_makeCodeSearch())."\",\"code_pulldowns\"); return false;'").'</TD><TD><DIV id=code_pulldowns>'.$code_pulldowns.'</DIV></TD></TR></TABLE>');
	
	$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ATTENDANCE_CODE_CATEGORIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['table']);unset($tmp_REQUEST['codes']);

	$tmp_PHP_SELF = PreparePHP_SELF($tmp_REQUEST);
	if(count($categories_RET))
	{
		echo '<center><div style="margin-bottom:-25px;"><TABLE border=0 cellpadding=0 cellspacing=0 style="border:1;border-style: none none solid none;"><TR><TD>';
		echo '<TABLE height=1><TR><TD height=1></TD></TR></TABLE>';
		$header = '<TABLE border=0 cellpadding=0 cellspacing=0 height=14><TR>';
		if($_REQUEST['table']!=='0')
		{
			$tabcolor = '#DFDFDF';
			$textcolor = '#999999';
		}
		else
		{
			$tabcolor = Preferences('HIGHLIGHT');
			$textcolor = '#000000';
		}

		$header .= '<TD width=10></TD><TD>'.DrawTab('Attendance',$tmp_PHP_SELF.'&amp;table=0',$tabcolor,$textcolor,'_circle',array('textcolor'=>'#000000')).'</TD>';
		foreach($categories_RET as $category)
		{
			if($_REQUEST['table']!==$category['ID'])
			{
				$tabcolor = '#DFDFDF';
				$textcolor = '#999999';
			}
			else
			{
				$tabcolor = Preferences('HIGHLIGHT');
				$textcolor = '#000000';
			}

			$header .= '<TD>'.DrawTab($category['TITLE'],$tmp_PHP_SELF.'&amp;table='.$category['ID'],$tabcolor,$textcolor,'_circle',array('textcolor'=>'#000000')).'</TD>';
		}
		$header .= '</TR></TABLE>';
		echo $header;
		echo '<TABLE height=1><TR><TD height=1></TD></TR></TABLE>';
		echo '</TD></TR></TABLE></div></center>';
	}
	
	#echo SubmitButton(LANG_UPDATE,'','class=btn_medium');
	$_REQUEST['search_modfunc'] = 'list';
	$extra['DEBUG']=true;
	
	PopTable_wo_header('header');
	//if($_REQUEST['expanded_view']==true)
//	{
//	echo '<div style="width:820px; overflow-x:scroll;">';
//	}
//	else
//	{
//	echo '<div style="width:820px; overflow-x:scroll;">';
//	}
	Search('student_id',$extra);
//	echo '</div>';
	echo '<BR><CENTER>'.SubmitButton('UPDATE','','class=btn_wide').'</CENTER>';
	PopTable ('footer');
	
	echo "</FORM>";
}

function _makePhone($value,$column)
{	global $THIS_RET,$contacts_RET;

	if(count($contacts_RET[$THIS_RET['STUDENT_ID']]))
	{
		foreach($contacts_RET[$THIS_RET['STUDENT_ID']] as $person)
		{
			if($person[1]['FIRST_NAME'] || $person[1]['LAST_NAME'])
				$tipmessage .= ''.$person[1]['STUDENT_RELATION'].': '.$person[1]['FIRST_NAME'].' '.$person[1]['LAST_NAME'].' | ';
			$tipmessage .= '';
			if($person[1]['PHONE'])
				$tipmessage .= ' '.$person[1]['PHONE'].'';
			foreach($person as $info)
			{
				if($info['TITLE'] || $info['VALUE'])
					$tipmessage .= ''.$info['TITLE'].''.$info['VALUE'].'';
			}
			$tipmessage .= '';
		}
	}
	else
	$tipmessage = 'This student has no contact information.';
		return button('phone','','# alt="'.$tipmessage.'" title="'.$tipmessage.'"');
}

function _makeCodePulldown($value,$title)
{	global $THIS_RET,$codes_RET,$current_RET,$current_schedule_RET,$date;

	if(!is_array($current_schedule_RET[$THIS_RET['STUDENT_ID']]))
	{
		$current_mp = GetCurrentMP('QTR',$date);
                if(!$current_mp){
                    $current_mp = GetCurrentMP('SEM',$date);
                }
                if(!$current_mp){
                    $current_mp = GetCurrentMP('FY',$date);
                }
		$all_mp = GetAllMP(GetMPTable(GetMP($current_mp,'TABLE')),$current_mp);
		$current_schedule_RET[$THIS_RET['STUDENT_ID']] = DBGet(DBQuery("SELECT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,cp.HALF_DAY FROM SCHEDULE s,COURSE_PERIODS cp WHERE s.STUDENT_ID='".$THIS_RET['STUDENT_ID']."' AND s.SYEAR='".UserSyear()."' AND s.SCHOOL_ID='".UserSchool()."' AND cp.COURSE_PERIOD_ID = s.COURSE_PERIOD_ID AND cp.DOES_ATTENDANCE='Y' AND ('$date' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND '$date'>=s.START_DATE)) AND s.MARKING_PERIOD_ID IN ($all_mp) ORDER BY s.START_DATE ASC"),array(),array('PERIOD_ID'));
		if(!$current_schedule_RET[$THIS_RET['STUDENT_ID']])
			$current_schedule_RET[$THIS_RET['STUDENT_ID']] = array();
	}
	if($THIS_RET['COURSE'])
	{
		$period = $THIS_RET['COURSE_PERIOD_ID'];
		$period_id = $THIS_RET['PERIOD_ID'];
		$code_title = 'TITLE';
	}
	else
	{
		$period_id = substr($title,7);
		$period = $current_schedule_RET[$THIS_RET['STUDENT_ID']][$period_id][1]['COURSE_PERIOD_ID'];
		#$code_title = 'SHORT_NAME';
		$code_title = 'TITLE';
	}

	if($current_schedule_RET[$THIS_RET['STUDENT_ID']][$period_id])
	{
		foreach($codes_RET as $code)
			if($current_schedule_RET[$THIS_RET['STUDENT_ID']][$period_id][1]['HALF_DAY']!='Y' || $code['STATE_CODE']!='H') // prune half day codes for half day courses
				$options[$code['ID']] = $code[$code_title];

		$val = $current_RET[$THIS_RET['STUDENT_ID']][$period][1]['ATTENDANCE_CODE'];

		return SelectInput($val,'attendance['.$THIS_RET['STUDENT_ID'].']['.$period.'][ATTENDANCE_CODE]','',$options);
	}
	else
		return false;
}

function _makeCode($value,$title)
{	global $THIS_RET,$codes_RET,$current_RET;

	foreach($codes_RET as $code)
	{
		if($current_RET[$THIS_RET['STUDENT_ID']][$THIS_RET['COURSE_PERIOD_ID']][1]['ATTENDANCE_TEACHER_CODE']==$code['ID'])
			return $code['TITLE'];
	}
}

function _makeReasonInput($value,$title)
{	global $THIS_RET,$codes_RET,$current_RET;

	$val = $current_RET[$THIS_RET['STUDENT_ID']][$THIS_RET['COURSE_PERIOD_ID']][1]['ATTENDANCE_REASON'];

	return TextInput($val,'attendance['.$THIS_RET['STUDENT_ID'].']['.$THIS_RET['COURSE_PERIOD_ID'].'][ATTENDANCE_REASON]','',$options);
}

function _makeCodeSearch($value='')
{	global $codes_RET,$code_search_selected;

	$return = '<SELECT name=codes[]><OPTION value="A"'.(($value=='A')?' SELECTED':'').'>Not Present</OPTION>';
	if(count($codes_RET))
	{
		foreach($codes_RET as $code)
		{
				
			if($value==$code['ID'])
				$return .= "<OPTION value=$code[ID] SELECTED>$code[TITLE]</OPTION>";
			else
				$return .= "<OPTION value=$code[ID]>$code[TITLE]</OPTION>";
		}
	}
	$return .= '</SELECT>';

	return $return;
}

function _makeStateValue($value,$name)
{	global $THIS_RET,$date;

	$value = DBGet(DBQuery("SELECT STATE_VALUE,COMMENT FROM ATTENDANCE_DAY WHERE STUDENT_ID='$THIS_RET[STUDENT_ID]' AND SCHOOL_DATE='$date'"));
	if($name=='STATE_VALUE')
	{
		$value  = $value[1]['STATE_VALUE'];

		if($value=='0.0')
			return 'None';
		elseif($value=='.5')
			return 'Half-Day';
		else
			return 'Full-Day';
	}
	else
		return TextInput($value[1]['COMMENT'],'attendance_day['.$THIS_RET['STUDENT_ID'].'][COMMENT]');
}

?>
