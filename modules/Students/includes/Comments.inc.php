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
//$_openSIS['allow_edit'] = true;
include('../../../Redirect_includes.php');
include_once('modules/Students/includes/functions.php');
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && User('PROFILE')=='admin')
{
	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
		echo '</FORM>';
	if(DeletePromptCommon($_REQUEST['title']))
	{
		DBQuery("DELETE FROM $_REQUEST[table] WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
{
	//$existing_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENT_MP_COMMENTS WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."'"));
	//if(!$existing_RET)
	//	DBQuery("INSERT INTO STUDENT_MP_COMMENTS (SYEAR,STUDENT_ID,MARKING_PERIOD_ID) values('".UserSyear()."','".UserStudentID()."','".GetParentMP('SEM',UserMP())."')");
          //   SaveData(array('STUDENT_MP_COMMENTS'=>"STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."'"),'',array('COMMENT'=>'Comment','STAFF_ID'=>UserStaffID()));
	unset($_SESSION['_REQUEST_vars']['modfunc']);
	unset($_SESSION['_REQUEST_vars']['values']);
}
if(!$_REQUEST['modfunc'])
{
	//$comments_RET = DBGet(DBQuery("SELECT COMMENT FROM STUDENT_MP_COMMENTS WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."'"));
	echo '<TABLE>';
        /*
	echo '<TR>';
	echo '<TD valign=bottom>';
	echo '<b>'.$mp['TITLE'].' Comments</b><BR>';
	echo '<TEXTAREA id=textarea name=values[STUDENT_MP_COMMENTS]['.UserStudentID().'][COMMENT] cols=66 rows=22'.(AllowEdit()?'':' readonly').' onkeypress="document.getElementById(\'chars_left\').innerHTML=(1121-this.value.length); if(this.value.length>1121) {document.getElementById(\'chars_left\').innerHTML=\'Fewer than 0\'}">';
	echo $comments_RET[1]['COMMENT'];
	echo '</TEXTAREA>';
	echo '<table><tr><td><IMG SRC=assets/comment_button.gif onload="document.getElementById(\'chars_left\').innerHTML=1121-document.getElementById(\'textarea\').value.length";></td><td><small><div id=chars_left>1121</div></small></td><td><small>characters remaining.<small></td></tr></table>';
	#echo '</TD>';
	//echo '<TR><TD align=center><INPUT type=submit value=Save></TD></TR>';
	echo '</TR></TABLE>';
	echo "<br><b>*If more than one teacher will be adding comments for this student:</b><br>";
	echo "<ul><li>Type your name above the comments you enter.</li>";
	echo "<li>Leave space for other teachers to enter their comments.</li></ul>";

*/
        echo '</tr><TD valign=top>';
	$table = 'STUDENT_MP_COMMENTS';
	$functions = array('COMMENT_DATE'=>'_makeDate','COMMENT'=>'_makeCommentsn');
        if(User('PROFILE')=='admin' || User('PROFILE')=='teacher'|| User('PROFILE')=='parent')
	$comments_RET = DBGet(DBQuery("SELECT ID,COMMENT_DATE,COMMENT,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME)AS USER_NAME,STUDENT_MP_COMMENTS.STAFF_ID FROM STUDENT_MP_COMMENTS,STAFF s WHERE STUDENT_ID='".UserStudentID()."'  AND s.STAFF_ID=STUDENT_MP_COMMENTS.STAFF_ID ORDER BY ID DESC"),$functions);
      #  else
      #  $comments_RET = DBGet(DBQuery("SELECT ID,COMMENT_DATE,COMMENT,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME)AS USER_NAME FROM STUDENT_MP_COMMENTS,STAFF s WHERE STUDENT_ID='".UserStudentID()."' AND STUDENT_MP_COMMENTS.SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."' AND s.STAFF_ID=STUDENT_MP_COMMENTS.STAFF_ID"),$functions);
	$columns = array('USER_NAME'=>'Entered By','COMMENT_DATE'=>'Date','COMMENT'=>'Comments');
	$link['add']['html'] = array('COMMENT_DATE'=>_makeDate('','COMMENT_DATE'),'COMMENT'=>_makeCommentsn('','COMMENT'),'USER_NAME'=>'');
	  if(User('PROFILE')=='admin')
          {
        $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MP_COMMENTS&title=".urlencode('medical comment');
	$link['remove']['variables'] = array('id'=>'ID');
          }
        $link['USER_NAME']['link'] = "Modules.php?modname=Users/User.php";
         $link['USER_NAME']['variables'] = array('staff_id'=>'STAFF_ID');
	ListOutput($comments_RET,$columns,'Comment','Comments',$link,array(),array('search'=>false));

	echo '</TD></TR></TABLE>';

	$_REQUEST['category_id'] = '4';
	$separator = '<hr>';
	#include('modules/Students/includes/Other_Info.inc.php');
}


?>