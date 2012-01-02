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
DrawBC("Eligibility > ".ProgramTitle());

Widgets('activity');
Widgets('course');
Widgets('eligibility');

Search('student_id',$extra);
if($_REQUEST['modfunc']=='add' || $_REQUEST['student_id'])
{
        if($_REQUEST['student_id'])
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".$_REQUEST['student_id']."'"));
    else
        $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.clean_param($_REQUEST['modcat'],PARAM_NOTAGS).'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.clean_param($_REQUEST['modname'],PARAM_NOTAGS).'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	  /*  if($_REQUEST['modname']!='Attendance/Administration.php')
	{
		DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	}*/
        }else if($count_student_RET[1]['NUM']==1){
            DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.clean_param($_REQUEST['modcat'],PARAM_NOTAGS).'><font color=red>Remove</font></A>) ');
        }
}
if($_REQUEST['modfunc']=='add' && AllowEdit())
{
	DBQuery("INSERT INTO STUDENT_ELIGIBILITY_ACTIVITIES (STUDENT_ID,ACTIVITY_ID,SYEAR) values('".UserStudentID()."','".$_REQUEST['new_activity']."','".UserSyear()."')");
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='remove' && AllowEdit())
{
	if(DeletePrompt('activity'))
	{
		DBQuery("DELETE FROM STUDENT_ELIGIBILITY_ACTIVITIES WHERE STUDENT_ID='".UserStudentID()."' AND ACTIVITY_ID='".$_REQUEST['activity_id']."' AND SYEAR='".UserSyear()."'");
		unset($_REQUEST['modfunc']);
	}
}

if(UserStudentID() && !$_REQUEST['modfunc'])
{
	$start_end_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_CONFIG WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND PROGRAM='eligibility' AND TITLE IN ('START_DAY','END_DAY')"));
	if(count($start_end_RET))
	{
		foreach($start_end_RET as $value)
			$$value['TITLE'] = $value['VALUE'];
	}
	
	switch(date('D'))
	{
		case 'Mon':
		$today = 1;
		break;
		case 'Tue':
		$today = 2;
		break;
		case 'Wed':
		$today = 3;
		break;
		case 'Thu':
		$today = 4;
		break;
		case 'Fri':
		$today = 5;
		break;
		case 'Sat':
		$today = 6;
		break;
		case 'Sun':
		$today = 7;
		break;
	}
	
	$start = time() - ($today-$START_DAY)*60*60*24;
	$end = time();
	
	if(!$_REQUEST['start_date'])
	{
		$start_time = $start;
		$start_date = strtoupper(date('d-M-y',$start_time));
		$end_date = strtoupper(date('d-M-y',$end));
	}
	else
	{
		$start_time = $_REQUEST['start_date'];
		$start_date = strtoupper(date('d-M-y',$start_time));
		$end_date = strtoupper(date('d-M-y',$start_time+60*60*24*7));
	}

	$sql = "SELECT min(unix_timestamp(SCHOOL_DATE)) as SCHOOL_DATE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'";
	$begin_year = DBGet(DBQuery($sql));
	$begin_year = $begin_year[1]['SCHOOL_DATE'];
	
	$date_select = "<OPTION value=$start>".date('M d, Y',$start).' - '.date('M d, Y',$end).'</OPTION>';
	
	if($begin_year != "" || !begin_year)
	{
	for($i=$start-(60*60*24*7);$i>=$begin_year;$i-=(60*60*24*7))
		$date_select .= "<OPTION value=$i".(($i+86400>=$start_time && $i-86400<=$start_time)?' SELECTED':'').">".date('M d, Y',$i).' - '.date('M d, Y',($i+1+(($END_DAY-$START_DAY))*60*60*24)).'</OPTION>';
	}
	
	echo "<FORM name=elig_stud id=elig_stud action=Modules.php?modname=$_REQUEST[modname] method=POST>";
	DrawHeaderHome('<SELECT name=start_date>'.$date_select.'</SELECT>','<INPUT type=submit value=Go class=btn_medium onclick=\'formload_ajax("elig_stud");\' >');
	echo '</FORM>';

	echo '<TABLE border=0 width=100%><TR><TD width=50% valign=top>';
	
	$RET = DBGet(DBQuery("SELECT em.STUDENT_ID,em.ACTIVITY_ID,ea.TITLE,ea.START_DATE,ea.END_DATE FROM ELIGIBILITY_ACTIVITIES ea,STUDENT_ELIGIBILITY_ACTIVITIES em WHERE em.SYEAR='".UserSyear()."' AND em.STUDENT_ID='".UserStudentID()."' AND em.SYEAR=ea.SYEAR AND em.ACTIVITY_ID=ea.ID ORDER BY ea.START_DATE"),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate'));

	$activities_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ELIGIBILITY_ACTIVITIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	if(count($activities_RET))
	{
		foreach($activities_RET as $value)
			$activities[$value['ID']] = $value['TITLE'];
	}

//	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&start_date=$_REQUEST[start_date]";
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&start_date=$_REQUEST[start_date]";
//	$link['remove']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=remove&start_date=$_REQUEST[start_date]\");'";
	$link['remove']['variables'] = array('activity_id'=>'ACTIVITY_ID');
	$link['add']['html']['TITLE'] = '<TABLE border=0 cellpadding=0 cellspacing=0><TR><TD>'.SelectInput('','new_activity','',$activities).' </TD><TD>&nbsp;<INPUT type=submit value=Add class=btn_go onclick=\'formload_ajax("elig_stud");\'></TD></TR></TABLE>';
	$link['add']['html']['remove'] = button('add');

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=add&start_date=$_REQUEST[start_date] method=POST>";
	$columns = array('TITLE'=>'Activity','START_DATE'=>'Starts','END_DATE'=>'Ends');
	ListOutput($RET,$columns,'Activity','Activities',$link);
	echo '</FORM>';

	echo '</TD><TD width=50% valign=top>';
	
	$RET = DBGet(DBQuery("SELECT e.ELIGIBILITY_CODE,c.TITLE as COURSE_TITLE FROM ELIGIBILITY e,COURSES c,COURSE_PERIODS cp WHERE e.STUDENT_ID='".UserStudentID()."' AND e.SYEAR='".UserSyear()."' AND e.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND e.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."'"),array('ELIGIBILITY_CODE'=>'_makeLower'));
	$columns = array('COURSE_TITLE'=>'Course','ELIGIBILITY_CODE'=>'Grade');
	ListOutput($RET,$columns,'Course','Courses');
	
	echo '</TD></TR></TABLE>';
}

function _makeLower($word)
{
	return ucwords(strtolower($word));
}

?>