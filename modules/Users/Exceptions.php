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
DrawBC("Users > ".ProgramTitle());
include 'Menu.php';
if(!UserStaffID())
	Search('staff_id');
//else
//{
//	$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
//	if(!$profile[1]['PROFILE'] || $profile[1]['PROFILE']=='none')
//	{
//		unset($_SESSION['staff_id']);
//		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;
//		Search('staff_id');
//	}
//}

$user_id = UserStaffID();
$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='$user_id'"));
$xprofile = $profile[1]['PROFILE'];
$exceptions_RET = DBGet(DBQuery("SELECT MODNAME,CAN_USE,CAN_EDIT FROM STAFF_EXCEPTIONS WHERE USER_ID='$user_id'"),array(),array('MODNAME'));
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && AllowEdit())
{
    
	$tmp_menu = $_SESSION['menu'];
	$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STUDENT_FIELD_CATEGORIES"));
	foreach($categories_RET as $category)
	{ 
		$file = 'Students/Student.php&category_id='.$category['ID'];
		$tmp_menu['Students'][$xprofile][$file] = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
	} 
	$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STAFF_FIELD_CATEGORIES"));
	foreach($categories_RET as $category)
	{
		$file = 'Users/User.php&category_id='.$category['ID'];
		$tmp_menu['Users'][$xprofile][$file] = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
	}

	foreach($tmp_menu as $modcat=>$profiles)
	{    
		$values = $profiles[$xprofile];
		foreach($values as $modname=>$title)
		{
			if(!is_numeric($modname))
			{ 
				if(!count($exceptions_RET[$modname]) && ($_REQUEST['can_edit'][str_replace('.','_',$modname)] || $_REQUEST['can_use'][str_replace('.','_',$modname)]))
                                 DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME) values('$user_id','$modname')");
				elseif(count($exceptions_RET[$modname]) && !$_REQUEST['can_edit'][str_replace('.','_',$modname)] && !$_REQUEST['can_use'][str_replace('.','_',$modname)])
                                 DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID='$user_id' AND MODNAME='$modname'");

				if($_REQUEST['can_edit'][str_replace('.','_',$modname)] || $_REQUEST['can_use'][str_replace('.','_',$modname)])
				{
					$update = "UPDATE STAFF_EXCEPTIONS SET ";
					if($_REQUEST['can_edit'][str_replace('.','_',$modname)])
						$update .= "CAN_EDIT='Y',";
					else
						$update .= "CAN_EDIT=NULL,";
					if($_REQUEST['can_use'][str_replace('.','_',$modname)])
						$update .= "CAN_USE='Y'";
					else
						$update .= "CAN_USE=NULL";                                        
					$update .= " WHERE USER_ID='$user_id' AND MODNAME='$modname'";
					DBQuery($update);
				}
			}
		}
	}
	$exceptions_RET = DBGet(DBQuery("SELECT MODNAME,CAN_USE,CAN_EDIT FROM STAFF_EXCEPTIONS WHERE USER_ID='$user_id'"),array(),array('MODNAME'));
	unset($tmp_menu);
	unset($_REQUEST['modfunc']);
	unset($_REQUEST['can_edit']);
	unset($_REQUEST['can_use']);
        $menu=$_SESSION['menu'];
        unset($_SESSION['menu']);
}

if(UserStaffID())
	$staff_RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,PROFILE,PROFILE_ID FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));

if(UserStaffID() && !$staff_RET[1]['PROFILE_ID'])
{
        $_SESSION['menu']=$menu;
	echo "<FORM name=exform id=exform action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	DrawHeaderHome('Select the programs with which this user can use and save information.',SubmitButton('Save','','class=btn_medium'));
	echo '<BR>';
	PopTable('header','Permissions');
	echo '<TABLE border=0 cellspacing=0>';
        foreach($_SESSION['menu'] as $modcat=>$profiles)
	{
		$values = $profiles[$staff_RET[1]['PROFILE']];
		echo '<TR><TD valign=top align=right>';
		echo "<BR><b><font color=gray>".str_replace('_',' ',$modcat)."</font></b></TD><TD width=3>&nbsp;</TD>";
		echo "<TH bgcolor=#FFFFFF><small><font color=gray>Can Use".(AllowEdit()?"<INPUT type=checkbox name=can_use_$modcat onclick='checkAll(this.form,this.form.can_use_$modcat.checked,\"can_use[$modcat\");'>":'')."</font></small></TH><TH bgcolor=#FFFFFF> &nbsp;<small><font color=gray>Can Edit".(AllowEdit()?"<INPUT type=checkbox name=can_edit_$modcat onclick='checkAll(this.form,this.form.can_edit_$modcat.checked,\"can_edit[$modcat\");'>":'')."</font></small></TH><TH bgcolor=#FFFFFF></TH></TR>";
		if(count($values))
		{
			foreach($values as $file=>$title)
			{
				if(!is_numeric($file))
				{
					$can_use = $exceptions_RET[$file][1]['CAN_USE'];
					$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];
					echo "<TR><TD></TD><TD></TD>";
					echo "<TD align=center bgcolor=#DDDDDD><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
					if($staff_RET[1]['PROFILE']=='admin')
						echo "<TD align=center bgcolor=#DDDDDD><INPUT type=checkbox id=can_edit[".str_replace('.','_',$file)."] name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
					else
						echo "<TD align=center bgcolor=#DDDDDD></TD>";
					echo "<TD bgcolor=#DDDDDD> &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 height=1 bgcolor=#000000></TR>";

					if($modcat=='Students' && $file=='Students/Student.php')
					{
						$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));
                                                
						foreach($categories_RET as $category)
						{
							$file = 'Students/Student.php&category_id='.$category['ID'];
							$title = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];                                                        
							$can_use = $exceptions_RET[$file][1]['CAN_USE'];
							$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];
							echo "<TR><TD></TD><TD></TD>";
							echo "<TD align=center bgcolor=#DDDDDD><INPUT type=checkbox id=can_use[".str_replace('.','_',$file)."] name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
							echo "<TD align=center bgcolor=#DDDDDD><INPUT type=checkbox id=can_edit[".str_replace('.','_',$file)."] name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
							echo "<TD bgcolor=#DDDDDD> &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 height=1 bgcolor=#000000></TR>";
						}
					}
					elseif($modcat=='Users' && $file=='Users/User.php')
					{
						$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STAFF_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));
						foreach($categories_RET as $category)
						{
							$file = 'Users/User.php&category_id='.$category['ID'];
							$title = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
							$can_use = $exceptions_RET[$file][1]['CAN_USE'];
							$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];
							echo "<TR><TD></TD><TD></TD>";
							echo "<TD align=center bgcolor=#DDDDDD><INPUT type=checkbox id=can_use[".str_replace('.','_',$file)."] name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
							echo "<TD align=center bgcolor=#DDDDDD><INPUT type=checkbox id=can_edit[".str_replace('.','_',$file)."] name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
							echo "<TD bgcolor=#DDDDDD> &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 height=1 bgcolor=#000000></TR>";
						}
					}
				}
				else
					echo '<TR><TD></TD><TD></TD><TD bgcolor=#FFFFFF colspan=3 align=center><small><b>- '.$title.' -</b></small></TD></TR>';

			}
		}
		echo '<TR><TD colspan=5 align=center height=20></TD></TR>';
	}
	echo '</TABLE>';
	PopTable('footer');
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>';

	echo '</DIV>';
	echo '</TD></TR></TABLE>';
	echo '</FORM>';
	echo '<DIV id=new_id_content style="position:absolute;visibility:hidden;">Title <INPUT type=text name=new_profile_title><BR>';
	echo 'Type <SELECT name=new_profile_type><OPTION value=admin>Administrator<OPTION value=teacher>Teacher<OPTION value=parent>Parent</SELECT></DIV>';
}
elseif(UserStaffID() && $staff_RET[1]['PROFILE_ID'])
{
	$profile_title = DBGet(DBQuery("SELECT TITLE FROM USER_PROFILES WHERE ID='".$staff_RET[1]['PROFILE_ID']."'"));
	echo '<BR>';
	PopTable('header','Error','width=50%');
	echo '<TABLE><TR><TD><IMG SRC=assets/warning_button.gif width=30></TD><TD>'.$staff_RET[1]['FIRST_NAME'].' '.$staff_RET[1]['LAST_NAME'].' is assigned to the profile '.$profile_title[1]['TITLE'].'.<BR><BR> To assign permissions to this user, either change the permissions for this profile using the '.ProgramLink('Users/Profiles.php','Profiles').' setup program or change this user to a User with custom permissions using the '.ProgramLink('Users/User.php','General Info').' program.</TD></TR></TABLE>';
	PopTable('footer');
}
?>