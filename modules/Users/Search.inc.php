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
	//if($_REQUEST['modfunc']=='search_fnc' || !$_REQUEST['modfunc'])
	if(($_REQUEST['modfunc']=='search_fnc' || !$_REQUEST['modfunc']) &&  !$_REQUEST['search_modfunc'])
	{
		if($_SESSION['staff_id'])
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		}

		echo '<BR>';
		PopTable('header','Find a User');

		echo "<FORM name=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=list&next_modname=$_REQUEST[next_modname] method=POST>";
		echo '<TABLE>';
		echo '<TR><TD align=right>Last Name</TD><TD><INPUT type=text class=cell_floating name=last></TD></TR>';
		echo '<TR><TD align=right>First Name</TD><TD><INPUT type=text class=cell_floating name=first></TD></TR>';
		echo '<TR><TD align=right>Username</TD><TD><INPUT type=text class=cell_floating name=username></TD></TR>';
		$options = array(''=>'N/A','admin'=>'Administrator','teacher'=>'Teacher','parent'=>'Parent','none'=>'No Access');
		if($extra['profile'])
			$options = array($extra['profile']=>$options[$extra['profile']]);
		echo '<TR><TD align=right>Profile</TD><TD><SELECT name=profile>';
		foreach($options as $key=>$val)
			echo '<OPTION value="'.$key.'">'.$val;
		echo '</SELECT></TD></TR>';
		if($extra['search'])
			echo $extra['search'];
		echo '<TR><TD colspan=2 align=center>';
		
		if(User('PROFILE')=='admin')
			echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'>Search All Schools<BR>';
			echo '<INPUT type=checkbox name=_dis_user value=Y>Include Disabled User<BR><br>';
		//echo Buttons('Submit','Reset');
		echo "<INPUT type=SUBMIT class=btn_medium value='Submit' >&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
		echo '</TD></TR>';
		echo '</TABLE>';
		/********************for Back to user***************************/
                    echo '<input type=hidden name=sql_save_session_staf value=true />';
                /************************************************/
                echo '</FORM>';
		// set focus to last name text box
		echo '<script type="text/javascript"><!--
			document.search.last.focus();
			--></script>';
		PopTable('footer');
	}

	//if($_REQUEST['modfunc']=='list')
	else
	{
		if(!$_REQUEST['next_modname'])
			$_REQUEST['next_modname'] = 'Users/User.php';

		if(!isset($_openSIS['DrawHeader'])) DrawHeaderHome('Please select a user');
		$staff_RET = GetStaffList($extra);
		if($extra['profile'])
		{
			$options = array('admin'=>'Administrator','teacher'=>'Teacher','parent'=>'Parent','none'=>'No Access');
			$singular = $options[$extra['profile']];
			$plural = $singular.($options[$extra['profile']]=='none'?'es':'s');
			$columns = array('FULL_NAME'=>$singular,'STAFF_ID'=>'Staff ID');
		}
		else
		{
			$singular = 'User';
			$plural = 'Users';
			$columns = array('FULL_NAME'=>'Staff Member','PROFILE'=>'Profile','STAFF_ID'=>'Staff ID');
		}
		if(is_array($extra['columns_before']))
			$columns = $extra['columns_before'] + $columns;
		if(is_array($extra['columns_after']))
			$columns += $extra['columns_after'];
		if(is_array($extra['link']))
			$link = $extra['link'];
		else
		{
			$link['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[next_modname]";
		//	$link['FULL_NAME']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[next_modname]\");'";
		//	$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=FY\");'";	
			$link['FULL_NAME']['variables'] = array('staff_id'=>'STAFF_ID');
		}
		ListOutput($staff_RET,$columns,$singular,$plural,$link,false,$extra['options']);
	}
}

function makeLogin($value)
{
	return ProperDate(substr($value,0,10)).substr($value,10);
}
?>