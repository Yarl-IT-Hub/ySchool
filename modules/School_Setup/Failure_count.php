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
if($_REQUEST['modfunc']=='update'){

if($_REQUEST['failure']){
$TOTAL_COUNT=DBGet(DBQuery("SELECT COUNT(FAIL_COUNT) AS TOTAL_COUNT FROM SYSTEM_PREFERENCE_MISC"));
$TOTAL_COUNT=$TOTAL_COUNT[1]['TOTAL_COUNT'];
if($TOTAL_COUNT==0 && $_REQUEST['failure']['FAIL_COUNT']){
DBQuery("INSERT INTO SYSTEM_PREFERENCE_MISC (FAIL_COUNT) VALUES(".$_REQUEST['failure']['FAIL_COUNT'].")");
}else if($TOTAL_COUNT==1){
$sql="UPDATE SYSTEM_PREFERENCE_MISC SET ";
foreach($_REQUEST['failure'] as $column_name=>$value)
					{
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",$value))."',";

}
$sql= substr($sql,0,-1) ." WHERE 1=1";
DBQuery($sql);
}
}
unset($_REQUEST['failure']);
}
$failure_RET=DBGet(DBQuery("SELECT FAIL_COUNT FROM SYSTEM_PREFERENCE_MISC LIMIT 1"));
$failure=$failure_RET[1];
echo "<FORM name=failure id=failure action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&page_display=FAILURE method=POST>";
echo '<table width="330px;" cellpadding="4">';
echo '<tr><td width="92%" align="right">No. of login failures allowed before account is disabled:</td><td align="left">'.TextInput($failure['FAIL_COUNT'],'failure[FAIL_COUNT]','','class=cell_floating').'</td></tr>';
echo '<tr><td colspan="2"></td></tr>';
echo '<tr><td colspan="2"><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER></td></tr>';
echo '</table>';
echo '</FORM>';
