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
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='save' && AllowEdit())
{
	$current_RET = DBGet(DBQuery("SELECT STAFF_ID FROM STUDENTS_JOIN_USERS WHERE STUDENT_ID='".UserStudentID()."'"),array(),array('STAFF_ID'));
	foreach($_REQUEST['staff'] as $staff_id=>$yes)
	{
		if(!$current_RET[$staff_id])
		{
			$sql = "INSERT INTO STUDENTS_JOIN_USERS (STAFF_ID,STUDENT_ID) values('".$staff_id."','".UserStudentID()."')";
			DBQuery($sql);
		}
	}
	unset($_REQUEST['modfunc']);
	$note = "The selected user's profile now includes access to the selected students.";
}
DrawBC("Students > ".ProgramTitle());

if(isset($_REQUEST['student_id']) && $_REQUEST['student_id']!='new' || UserStudentID())
{
        if($_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
            $stu_id=$_REQUEST['student_id'];
        else
            $stu_id=UserStudentID ();
        $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM STUDENTS,STUDENT_ENROLLMENT WHERE STUDENTS.STUDENT_ID='".$stu_id."' AND STUDENT_ENROLLMENT.STUDENT_ID = STUDENTS.STUDENT_ID "));
        //$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) ');
        }
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && AllowEdit())
{
	if(DeletePromptCommon('student from that user','remove access to'))
	{
		DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='$_REQUEST[staff_id]' AND STUDENT_ID='".UserStudentID()."'");
		unset($_REQUEST['modfunc']);
	}
}

if($note)
	DrawHeader('<IMG SRC=assets/check.gif>'.$note);

if($_REQUEST['modfunc']!='delete')
{
	$extra['SELECT'] = ",(SELECT count(u.STAFF_ID) FROM STUDENTS_JOIN_USERS u,STAFF st WHERE u.STUDENT_ID=s.STUDENT_ID AND st.STAFF_ID=u.STAFF_ID AND st.SYEAR=ssm.SYEAR) AS ASSOCIATED";
	$extra['columns_after'] = array('ASSOCIATED'=>'# Associated');
	Search('student_id',$extra);

	if(UserStudentID())
	{
		if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='list')
		{
			echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
			#DrawHeader('',SubmitButton('Add Selected Parents'));
		}

		echo '<CENTER><TABLE><TR><TD valign=top>';
		$current_RET = DBGet(DBQuery("SELECT u.STAFF_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,s.LAST_LOGIN FROM STUDENTS_JOIN_USERS u,STAFF s WHERE s.STAFF_ID=u.STAFF_ID AND u.STUDENT_ID='".UserStudentID()."' AND s.SYEAR='".UserSyear()."'"),array('LAST_LOGIN'=>'_makeLogin'));
		$link['remove'] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=delete",'variables'=>array('staff_id'=>'STAFF_ID'));
		#$link['remove'] = array('link'=>"#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=delete\");'",'variables'=>array('staff_id'=>'STAFF_ID'));
		ListOutput($current_RET,array('FULL_NAME'=>'Parents','LAST_LOGIN'=>'Last Login'),'','',$link,array(),array('search'=>false));
		echo '</TD><TD valign=top>';

		if(AllowEdit())
		{
			unset($extra);
			$extra['link'] = array('FULL_NAME'=>false);
			$extra['SELECT'] = ",CAST(NULL AS CHAR(1)) AS CHECKBOX";
			$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
			$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
			$extra['new'] = true;
			$extra['options']['search'] = false;
			$extra['profile'] = 'parent';
			$_openSIS['DrawHeader'] = 'bgcolor=#ff8040';

		Search('staff_id',$extra);
		}

		echo '</TD></TR></TABLE></CENTER>';

		if($_REQUEST['modfunc']=='list')
			echo "<BR><CENTER>".SubmitButton('Add Selected Parents','','class=btn_large')."</CENTER></FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=staff[".$THIS_RET['STAFF_ID']."] value=Y>";
}

function _makeLogin($value)
{
	if($value)
		return ProperDate(substr($value,0,10)).substr($value,10);
	else
		return '-';
}
?>