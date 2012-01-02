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
DrawBC("Users >> ".ProgramTitle());

include 'Menu.php';

if(is_numeric(clean_param($_REQUEST['profile_id'],PARAM_INT)))
{
	$exceptions_RET = DBGet(DBQuery("SELECT PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='$_REQUEST[profile_id]'"),array(),array('MODNAME'));
	$profile_RET = DBGet(DBQuery("SELECT PROFILE FROM USER_PROFILES WHERE ID='$_REQUEST[profile_id]'"));
	$xprofile = $profile_RET[1]['PROFILE'];
	if($xprofile=='student')
	{
		$xprofile = 'parent';
		unset($menu['Users']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && AllowEdit())
{
	$profile_RET = DBGet(DBQuery("SELECT TITLE FROM USER_PROFILES WHERE ID='$_REQUEST[profile_id]'"));

	if(Prompt('Confirm Delete','Are you sure you want to delete the user profile <i>'.$profile_RET[1]['TITLE'].'</i>?','Users of that profile will retain their permissions as a custom set which can be modified on a per-user basis through the User Permissions program.'))
	{
		DBQuery("DELETE FROM USER_PROFILES WHERE ID='".$_REQUEST['profile_id']."'");
		DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID IN (SELECT STAFF_ID FROM STAFF WHERE PROFILE_ID='".$_REQUEST['profile_id']."')");
		DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT s.STAFF_ID,e.MODNAME,e.CAN_USE,e.CAN_EDIT FROM STAFF s,PROFILE_EXCEPTIONS e WHERE s.PROFILE_ID='$_REQUEST[profile_id]' AND s.PROFILE_ID=e.PROFILE_ID");
		DBQuery("DELETE FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".$_REQUEST['profile_id']."'");
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['profile_id']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && AllowEdit() && !$_REQUEST['new_profile_title'])
{
	$tmp_menu = $menuprof;
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
					DBQuery("INSERT INTO PROFILE_EXCEPTIONS (PROFILE_ID,MODNAME) values('$_REQUEST[profile_id]','$modname')");
				elseif(count($exceptions_RET[$modname]) && !$_REQUEST['can_edit'][str_replace('.','_',$modname)] && !$_REQUEST['can_use'][str_replace('.','_',$modname)])
					DBQuery("DELETE FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='$_REQUEST[profile_id]' AND MODNAME='$modname'");

				if($_REQUEST['can_edit'][str_replace('.','_',$modname)] || $_REQUEST['can_use'][str_replace('.','_',$modname)])
				{
					$update = "UPDATE PROFILE_EXCEPTIONS SET ";
					if($_REQUEST['can_edit'][str_replace('.','_',$modname)])
						$update .= "CAN_EDIT='Y',";
					else
						$update .= "CAN_EDIT=NULL,";
					if($_REQUEST['can_use'][str_replace('.','_',$modname)])
						$update .= "CAN_USE='Y'";
					else
						$update .= "CAN_USE=NULL";
					$update .= " WHERE PROFILE_ID='$_REQUEST[profile_id]' AND MODNAME='$modname';";
					DBQuery($update);
				}
			}
		}
	}
	$exceptions_RET = DBGet(DBQuery("SELECT MODNAME,CAN_USE,CAN_EDIT FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='$_REQUEST[profile_id]'"),array(),array('MODNAME'));
	unset($tmp_menu);
	unset($_REQUEST['modfunc']);
	unset($_REQUEST['can_edit']);
	unset($_REQUEST['can_use']);
}

if(clean_param($_REQUEST['new_profile_title'],PARAM_NOTAGS) && AllowEdit())
{     
	// $id = DBGet(DBQuery("SELECT ".db_seq_nextval('USER_PROFILES_SEQ')." AS ID".FROM_DUAL));
        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'USER_PROFILES'"));
        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
	$id = $id[1]['ID'];
	$exceptions_RET = array();
	DBQuery("INSERT INTO USER_PROFILES (TITLE,PROFILE) values('".clean_param($_REQUEST['new_profile_title'],PARAM_NOTAGS)."','".clean_param($_REQUEST['new_profile_type'],PARAM_ALPHA)."')");
	$_REQUEST['profile_id'] = $id;
	$xprofile = $_REQUEST['new_profile_type'];
	unset($_REQUEST['new_profile_title']);
	unset($_REQUEST['new_profile_type']);
	unset($_SESSION['_REQUEST_vars']['new_profile_title']);
	unset($_SESSION['_REQUEST_vars']['new_profile_type']);
}

if($_REQUEST['modfunc']!='delete')
{
PopTable('header','Permissions');
	echo "<FORM name=pref_form id=pref_form action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&profile_id=$_REQUEST[profile_id] method=POST>";
	DrawHeaderHome('Select the programs that users of this profile can use and which programs those users can use to save information.');
	echo '<BR>';
	echo '<TABLE width=100%><TR><TD valign=top width=26%>';
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>';
	$style = ' style="border:1px; border-style: none none none none; padding:4px;"';
	$style1 = ' style="border:1px; border-style: solid none none none;"';
	//$profiles_RET = DBGet(DBQuery("SELECT ID,TITLE,PROFILE FROM USER_PROFILES"));
	$profiles_RET = DBGet(DBQuery("SELECT ID,TITLE,PROFILE FROM USER_PROFILES ORDER BY ID"),array(),array('PROFILE','ID'));
	echo '<TR><TD colspan=3 style="border:1px; border-style: none none solid none;"><b>Profiles</b></TD></TR>';
	foreach(array('admin','teacher','parent','student') as $profiles)
	{
	foreach($profiles_RET[$profiles] as $id=>$profile)
	{
		if($_REQUEST['profile_id']!='' && $id==$_REQUEST['profile_id'])
			echo '<TR id=selected_tr onmouseover="" onmouseout="" bgcolor="'.Preferences('HIGHLIGHT').'"; this.style.color="white";\'><TD width=20 align=right'.$style.'>'.(AllowEdit()&&$id>3?button('remove','',"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&profile_id=$id",20):'').'</TD><TD '.$style.' onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&profile_id='.$id.'\';">';
		else
			echo '<TR onmouseover=\'this.style.backgroundColor="'.Preferences('HIGHLIGHT').'"; this.style.color="white";\' onmouseout=\'this.style.cssText="background-color:transparent; color:black;";\'><TD width=20 align=right'.$style.'>'.(AllowEdit()&&$id>3?button('remove','',"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&profile_id=$id",15):'').'</TD><TD'.$style.' onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&profile_id='.$id.'\';">';
		echo '<b><a style="cursor: pointer; cursor:hand; text-decoration:none;">'.($id>3?'':'<b>').$profile[1]['TITLE'].($id>3?'':'</b>').'</a></b> &nbsp;';
		echo '</TD>';
		echo '<TD'.$style.'><A style="cursor: pointer;"><IMG SRC=assets/arrow_right.gif></A></TD>';
		echo '</TR>';
	}
	}
	if($_REQUEST['profile_id']=='')
		echo '<TR id=selected_tr><TD height=0></TD></TR>';

	if(AllowEdit())
	{
	echo '<TR id=new_tr><TD colspan=3'.$style1.'>';
	echo '<a style="cursor: pointer;" onclick=\'document.getElementById("selected_tr").onmouseover="this.style.backgroundColor=\"'.Preferences('HIGHLIGHT').'\"; this.style.color=\"white\";"; document.getElementById("selected_tr").onmouseout="this.style.cssText=\"background-color:transparent; color:black;\";"; document.getElementById("selected_tr").style.cssText="background-color:transparent; color:black;"; changeHTML({"new_id_div":"new_id_content"},["main_div"]);document.getElementById("new_tr").onmouseover="";document.getElementById("new_tr").onmouseout="";this.onclick="";\'><b> Add a User Profile<BR></a><br><DIV id=new_id_div></DIV> </b>';
	echo '</TD>';
	//echo '<TD'.$style.'><A style="cursor: pointer; cursor:hand;"><IMG SRC=assets/arrow_right.gif></A>&nbsp;</TD>';
	#echo "<td></td>";
	echo '</TR>';
	}

	echo '</TABLE>';
	echo '</TD><TD width=20 class=vbreak></TD><TD>';
	echo '<DIV id=main_div>';
	if($_REQUEST['profile_id']!='')
	{
		#PopTable('header','Permissions');
		echo '<TABLE border=0 cellspacing=0>';
		echo '<TR><TD colspan=5 style="border:1px; border-style: none none solid none;"><b>Permissions</b></TD></TR>';
		foreach($menuprof as $modcat=>$profiles)
		{
			$values = $profiles[$xprofile];

			echo '<TR><TD valign=top class=grid align=right style="white-space: nowrap; padding:6px 2px 2px 6px" >';
			echo "<b>".str_replace('_',' ',$modcat)."</b></TD><TD width=3 class=grid>&nbsp;</TD>";
			echo "<td class=grid style='white-space: nowrap;  padding:2px 2px 2px 6px;'>Can Use".(AllowEdit()?"<INPUT type=checkbox name=can_use_$modcat onclick='checkAll(this.form,this.form.can_use_$modcat.checked,\"can_use[$modcat\");'>":'')."</td>";
			if($xprofile=='admin' || $modcat=='Students')
				echo"<td class=grid style='white-space: nowrap; padding:2px 2px 2px 6px;' > &nbsp;Can Edit".(AllowEdit()?"<INPUT type=checkbox name=can_edit_$modcat onclick='checkAll(this.form,this.form.can_edit_$modcat.checked,\"can_edit[$modcat\");'>":'')."</td>";
			else
				echo"<td class=grid></td>";
			echo "<td class=grid></td></TR>";
			if(count($values))
			{
				foreach($values as $file=>$title)
				{
					if(!is_numeric($file))
					{
						$can_use = $exceptions_RET[$file][1]['CAN_USE'];
						$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];

						echo "<TR><TD></TD><TD ></TD>";

						echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
						if($xprofile=='admin')
                            echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
						elseif($xprofile=='parent' && $file=='Scheduling/Requests.php')
                            echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
                        else
                            echo "<TD align=center></TD>";
						echo "<TD > &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break></TR>";

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
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD >$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break_headers></TR>";
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
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD style='white-space: nowrap;'> &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break_headers></TR>";
							}
						}
					}
					else
						echo '<TR><TD></TD><TD></TD><TD colspan=3 style=background-color:#bee6f2 align=right><b> '.$title.' </b></TD></TR>';

				}
			}
			echo '<TR><TD colspan=5 align=center height=20></TD></TR>';
		}
		echo '</TABLE>';
		#PopTable('footer');
	//	echo '<CENTER>'.SubmitButton('Save', '', 'class=btn_medium onclick=\'formload_ajax("pref_form");\'').'</CENTER>';
		echo '<CENTER>'.SubmitButton('Save', '', 'class=btn_medium').'</CENTER>';
		// pref_form
	}
	echo '</DIV>';
	echo '</TD></TR></TABLE>';
	echo '</FORM>';
	PopTable('footer');
	echo '<DIV id=new_id_content style="position:absolute;visibility:hidden;"><fieldset><legend>Add a User Profile</legend><table><tr><td width=30>Title </td><td><INPUT type=text name=new_profile_title></td></tr>';
	echo '<tr><td width=30>Type </td><td><SELECT name=new_profile_type><OPTION value=admin>Administrator<OPTION value=teacher>Teacher<OPTION value=parent>Parent</SELECT>
	<br></td></tr><tr><td colspan=2 align=center><input type=submit value=save class=btn_medium></td></tr></table></fieldset></DIV>';
}
?>