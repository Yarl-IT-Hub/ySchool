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
//if($_REQUEST['modfunc']!='XMLHttpRequest')
include('../../Redirect_modules.php');
	DrawBC("Scheduling -> ".ProgramTitle());

Widgets('request');
Search('student_id',$extra);


if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA) =='remove')
{
	if(DeletePrompt('request'))
	{
		DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE REQUEST_ID='".paramlib_validation($colmn=PERIOD_ID,$_REQUEST['id'])."'");
		unset($_REQUEST['modfunc']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['id']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA) =='update')
{
    foreach($_REQUEST['values'] as $request_id=>$columns)
	{
		$sql = "UPDATE SCHEDULE_REQUESTS SET ";

		foreach($columns as $column=>$value)
		{
                    $value=paramlib_validation($column,$value);
                    if(str_replace("\'","''",$value)=='' || str_replace("\'","''",$value)==0)
                        $sql .=$column."=NULL,";
                    else
                        $sql .= $column."='".str_replace("\'","''",$value)."',";
		}
		$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='".UserStudentID()."' AND REQUEST_ID='".$request_id."'";
		//echo $sql;
		DBQuery($sql);
	}
	unset($_REQUEST['modfunc']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA) =='add')
{
    if($_REQUEST['subject_id']==0)
    {
        echo "<font color='red'>"."Please select a subject"."</font>";
        unset($_REQUEST['modfunc']);
	
    }
    else{
            if($_REQUEST['course_id']==0)
            {
                echo "<font color='red'>"."Please select a course"."</font>";
        unset($_REQUEST['modfunc']);
            }
            else{
        $course_id = paramlib_validation($colmn=PERIOD_ID,$_REQUEST['course_id']);
	$course_weight = substr($_REQUEST['course'],strpos($_REQUEST['course'],'-')+1);
	//$subject_id = DBGet(DBQuery("SELECT SUBJECT_ID FROM COURSES WHERE COURSE_ID='".$course_id."'"));
	$subject_id =$_REQUEST['subject_id'];
	$mp_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$mp_id = UserMP();
        
	DBQuery("INSERT INTO SCHEDULE_REQUESTS (SYEAR,SCHOOL_ID,STUDENT_ID,SUBJECT_ID,COURSE_ID,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$subject_id."','".$course_id."','".$mp_id."')");
	unset($_REQUEST['modfunc']);
            }
        }
}

/*
if($_REQUEST['modfunc']=='XMLHttpRequest')
{
	header("Content-Type: text/xml\n\n");
	$courses_RET = DBGet(DBQuery("SELECT c.COURSE_ID,c.TITLE FROM COURSES c WHERE ".($_REQUEST['subject_id']?"c.SUBJECT_ID='".$_REQUEST['subject_id']."' AND ":'')."UPPER(c.TITLE) LIKE '".strtoupper($_REQUEST['course_title'])."%' AND c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'"));
	echo '<?phpxml version="1.0" standalone="yes"?><courses>';
	if(count($courses_RET))
	{
		foreach($courses_RET as $course)
			echo '<course><id>'.$course['COURSE_ID'].'</id><title>'.str_replace('&','&amp;',$course['TITLE']).'</title></course>';
	}
	echo '</courses>';
}
*/
if(!$_REQUEST['modfunc'] && UserStudentID())
{
	

        if(User('PROFILE')!='admin')
        {
            if(User('PROFILE')!='student')
            if(User('PROFILE_ID'))
            $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Scheduling/Requests.php' AND CAN_EDIT='Y'"));
            else
            $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Scheduling/Requests.php' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
            else
            $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND MODNAME='Scheduling/Requests.php' AND CAN_EDIT='Y'"));
            if($can_edit_RET)
            $_openSIS['allow_edit'] = true;
        }

        $functions = array('COURSE'=>'_makeCourse','WITH_TEACHER_ID'=>'_makeTeacher','WITH_PERIOD_ID'=>'_makePeriod');
	$requests_RET = DBGet(DBQuery("SELECT r.REQUEST_ID,c.TITLE as COURSE,r.COURSE_ID,r.COURSE_WEIGHT,r.MARKING_PERIOD_ID,r.WITH_TEACHER_ID,r.NOT_TEACHER_ID,r.WITH_PERIOD_ID,r.NOT_PERIOD_ID FROM SCHEDULE_REQUESTS r,COURSES c WHERE r.COURSE_ID=c.COURSE_ID AND r.SYEAR='".UserSyear()."' AND r.STUDENT_ID='".UserStudentID()."'"),$functions);
	$columns = array('COURSE'=>'Course','WITH_TEACHER_ID'=>'Teacher','WITH_PERIOD_ID'=>'Period');

	//$link['add']['html'] = array('COURSE_ID'=>_makeCourse('','COURSE_ID'),'WITH_TEACHER_ID'=>_makeTeacher('','WITH_TEACHER_ID'),'WITH_PERIOD_ID'=>_makePeriod('','WITH_PERIOD_ID'),'MARKING_PERIOD_ID'=>_makeMP('','MARKING_PERIOD_ID'));
	$subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$subjects= CreateSelect($subjects_RET, 'subject_id', 'Select Subject', 'Modules.php?modname='.$_REQUEST['modname'].'&subject_id=');
	
	if($_REQUEST['subject_id'])
	{

	$courses_RET = DBGet(DBQuery("SELECT c.COURSE_ID,c.TITLE FROM COURSES c WHERE ".($_REQUEST['subject_id']?"c.SUBJECT_ID='".$_REQUEST['subject_id']."' AND ":'')."UPPER(c.TITLE) LIKE '".strtoupper($_REQUEST['course_title'])."%' AND c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'"));
	$courses = CreateSelect($courses_RET, 'course_id', 'Select Course', 'Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST['subject_id'].'&course_id=');
			
	}
	if($_REQUEST['course_id'])
	{
	//$periods_RET = DBGet(DBQuery("SELECT c.COURSE_PERIOD_ID,c.TITLE FROM COURSE_PERIODS c, COURSE_SUBJECTS cp WHERE ".($_REQUEST['course_id']?"c.COURSE_ID='".$_REQUEST['course_id']."' AND ":'')."UPPER(c.TITLE) LIKE '".strtoupper($_REQUEST['title'])."%' AND c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'"));
	//include("modules/Scheduling/RequestsReport.php");
	}
    if(User('PROFILE')=='admin' || (User('PROFILE')=='student' && AllowEdit()) || (User('PROFILE')=='parent' && AllowEdit()))
    {
	echo '<br><br><FORM name=ad id=ad action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=add method=POST>';
	DrawHeaderHome('Add a Request : &nbsp; Subject '.$subjects.' &nbsp; '.$courses,SubmitButton('Add','','class=btn_medium onclick=\'formload_ajax("ad");\''));
	#echo '<small>Add a Request : </small> &nbsp; Subject '.$subjects.' &nbsp; '.$courses;
	#echo '<br><br><CENTER>'.SubmitButton('Add','','class=btn_medium onclick=\'formload_ajax("ad");\'').'</CENTER>';
	echo '</FORM>';
		$link['remove'] = array('link'=>'Modules.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	//$link['remove'] = array('link'=>'"#"." onclick='check_content(\"ajax.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	echo '<br><br><FORM name=up id=up action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=update method=POST>';
	//DrawHeaderHome('',SubmitButton('Update'));
	//$link['add']['span'] = '<small>Add a Request : </small> &nbsp; Subject '.$subjects.' &nbsp; '.$courses;
        ListOutput($requests_RET,$columns,'Request','Requests',$link);
	if(!$requests_RET)
	echo '';
	else
	echo '<br><CENTER>'.SubmitButton('Update','','class=btn_medium onclick=\'formload_ajax("up");\'').'</CENTER>';
	echo '</FORM>';

    }
    else
    {
	$link['remove'] = array('link'=>'Modules.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	//$link['remove'] = array('link'=>'"#"." onclick='check_content(\"ajax.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	echo '<br><br><FORM name=up id=up action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=update method=POST>';
	//DrawHeaderHome('',SubmitButton('Update'));
	//$link['add']['span'] = '<small>Add a Request : </small> &nbsp; Subject '.$subjects.' &nbsp; '.$courses;
	ListOutput($requests_RET,$columns,'Request','Requests',$link);
	echo '<br><CENTER>'.SubmitButton('Update','','class=btn_medium onclick=\'formload_ajax("up");\'').'</CENTER>';
	echo '</FORM>';
    }
    $_openSIS['allow_edit'] = false;
}



function _makeCourse($value,$column)
{	global $THIS_RET;

	return $value.' - '.$THIS_RET['COURSE_WEIGHT'];	

}

function _makeTeacher($value,$column)
{	global $THIS_RET;

	$teachers_RET = DBGet(DBQuery("SELECT s.FIRST_NAME,s.LAST_NAME,s.STAFF_ID AS TEACHER_ID FROM STAFF s,COURSE_PERIODS cp WHERE s.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_ID='".$THIS_RET['COURSE_ID']."'"));
	foreach($teachers_RET as $teacher)
		$options[$teacher['TEACHER_ID']] = $teacher['FIRST_NAME'].' '.$teacher['LAST_NAME'];
	
	return 'With: '.SelectInput($value,'values['.$THIS_RET['REQUEST_ID'].'][WITH_TEACHER_ID]','',$options).' Without: '.SelectInput($THIS_RET['NOT_TEACHER_ID'],'values['.$THIS_RET['REQUEST_ID'].'][NOT_TEACHER_ID]','',$options);
}

function _makePeriod($value,$column)
{	global $THIS_RET;

	$periods_RET = DBGet(DBQuery("SELECT p.TITLE,p.PERIOD_ID FROM SCHOOL_PERIODS p,COURSE_PERIODS cp WHERE p.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_ID='".$THIS_RET['COURSE_ID']."'"));
	foreach($periods_RET as $period)
		$options[$period['PERIOD_ID']] = $period['TITLE'];
	
	return 'On: '.SelectInput($value,'values['.$THIS_RET['REQUEST_ID'].'][WITH_PERIOD_ID]','',$options).' Not on: '.SelectInput($THIS_RET['NOT_PERIOD_ID'],'values['.$THIS_RET['REQUEST_ID'].'][NOT_PERIOD_ID]','',$options);
}

// DOESN'T SUPPORT MP REQUEST
function _makeMP($value,$column)
{	global $THIS_RET;

	return SelectInput($value,'values['.$THIS_RET['REQUEST_ID'].'][MARKING_PERIOD_ID]','',$options);
}

	function CreateSelect($val, $name, $opt, $link='')
	{
	 	//$html .= "<table width=600px><tr><td align=right width=45%>";
		//$html .= $cap." </td><td width=55%>";
		if($link!='')
		$html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
		else
		$html .= "<select name=".$name." id=".$name." >";
		$html .= "<option value=''>".$opt."</option>";
		
				foreach($val as $key=>$value)
				{
					if($value[strtoupper($name)]==$_REQUEST[$name])
						$html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
					else
						$html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";	
				}
		
		
				
		$html .= "</select>";
		return $html;
	}

?>