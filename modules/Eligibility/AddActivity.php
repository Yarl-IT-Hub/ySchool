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
if(User('PROFILE')=='admin')
{
if(!$_REQUEST['student_id'])
{
if(!$_REQUEST['include'])
{
unset($_SESSION['student_id']);
	unset($_SESSION['_REQUEST_vars']['student_id']);
//	unset($_SESSION['_REQUEST_vars']['search_modfunc']);
}
}
}

//if($_REQUEST['modfunc']=='save')
if(optional_param('modfunc','',PARAM_NOTAGS)=='save')
{
	if($_REQUEST['activity_id'])
	{
		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENT_ELIGIBILITY_ACTIVITIES WHERE ACTIVITY_ID='".$_SESSION['activity_id']."' AND SYEAR='".UserSyear()."'"),array(),array('STUDENT_ID'));
		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			if(!$current_RET[$student_id])
			{
				/*$sql = "INSERT INTO STUDENT_ELIGIBILITY_ACTIVITIES (SYEAR,STUDENT_ID,ACTIVITY_ID)
							values('".UserSyear()."','".$student_id."','".$_REQUEST['activity_id']."')";*/
							
				$sql = "INSERT INTO STUDENT_ELIGIBILITY_ACTIVITIES (SYEAR,STUDENT_ID,ACTIVITY_ID)
							values('".UserSyear()."','".$student_id."','".optional_param('activity_id','',PARAM_SPCL)."')";
				DBQuery($sql);
			}
		}
		unset($_REQUEST['modfunc']);
		$note = "That activity has been added to the selected students.";
	}
	else
		BackPrompt('You must choose an activity.');
}

DrawBC("Eligibility > ".ProgramTitle());

if($note)
	DrawHeader('<table><tr><td><IMG SRC=assets/check.gif></td><td class=notice_msg>'.$note.'</td></tr></table>');

if($_REQUEST['search_modfunc']=='list')
{
	echo "<FORM name=addact id=addact action=Modules.php?modname=$_REQUEST[modname]&modfunc=save METHOD=POST>";
	#DrawHeader('',SubmitButton('Add Activity to Selected Students'));
	#echo '<BR>';

	echo '<CENTER><TABLE cellpadding=6><TR><TD align=right><b>Activity</b></TD>';
	echo '<TD>';
	$activities_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ELIGIBILITY_ACTIVITIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	echo '<SELECT name=activity_id><OPTION value="">N/A</OPTION>';
	if(count($activities_RET))
	{
		foreach($activities_RET as $activity)
			echo "<OPTION value=$activity[ID]>$activity[TITLE]</OPTION>";
	}
	echo '</SELECT>';
	echo '</TD>';
	echo '</TR></TABLE><BR>';

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",NULL AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;
}
	Widgets('activity');
	Widgets('course');

Search('student_id',$extra);
if($_REQUEST['search_modfunc']=='list')
//	echo '<BR><CENTER>'.SubmitButton('Add Activity to Selected Students','','class=btn_xlarge onclick=\'formload_ajax("addact");\'')."</CENTER></FORM>";
	echo '<BR><CENTER>'.SubmitButton('Add Activity to Selected Students','','class=btn_xlarge')."</CENTER></FORM>";

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>