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
if($_openSIS['modules_search'] && $extra['force_search'])
	$_REQUEST['search_modfunc'] = '';

if(Preferences('SEARCH')!='Y' && !$extra['force_search'])
	$_REQUEST['search_modfunc'] = 'list';
if($extra['skip_search']=='Y')
    $_REQUEST['search_modfunc']='list';

if($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc'])
{       unset($_SESSION['new_sql']);
        unset($_SESSION['newsql']);
        unset($_SESSION['newsql1']);
	if($_SESSION['student_id'] && User('PROFILE')=='admin' && $_REQUEST['student_id']=='new')
	{
		unset($_SESSION['student_id']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}

	switch(User('PROFILE'))
	{
		case 'admin':
		case 'teacher':
		if(isset($_SESSION['stu_search']['sql']) && $search_from_grade!='true'){
		unset($_SESSION['smc']);
		unset($_SESSION['g']);
		unset($_SESSION['p']);
		unset($_SESSION['smn']);
		unset($_SESSION['sm']);
		unset($_SESSION['sma']);
		unset($_SESSION['smv']);
		unset($_SESSION['s']);
		}
			echo '<BR>';
			$_SESSION['Search_PHP_SELF'] = PreparePHP_SELF($_SESSION['_REQUEST_vars']);
			echo '<script language=JavaScript>parent.help.location.reload();</script>';
			if(isset($_SESSION['stu_search']['sql']) && $search_from_grade!='true'){
			unset($_SESSION['stu_search']);
			}else if($search_from_grade=='true'){
			$_SESSION['stu_search']['search_from_grade']='true';
			}
			PopTable('header','Find a Student');
			if($extra['pdf']!=true)
			echo "<FORM name=search id=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]".$extra['action']." method=POST>";
			else
			echo "<FORM name=search id=search action=for_export.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]".$extra['action']." method=POST target=_blank>";
			echo '<TABLE border=0>';
			Search_absence_summary('general_info');
			if($extra['search'])
				echo $extra['search'];
			Search_absence_summary('student_fields');
			
			
			
			
			# ---   Advanced Search Start ---------------------------------------------------------- #
			echo '<div style="height:10px;"></div>';
			echo '<input type=hidden name=sql_save_session value=true />';
			echo '<div id="addiv">';
			echo '<div><label onclick="show_search_div();" style="cursor:pointer; font-weight:bold; font-size:12px;  text-decoration:underline;">Advanced Search</label></div>';
			echo '</div>';
			
			echo '<div id="searchdiv" style="display:none; width:410px;">';
			echo '<div>&laquo;&nbsp;<label onclick="hide_search_div();" style="cursor:pointer; font-weight:bold; font-size:12px;  text-decoration:underline;">Back to Basic Search</label></div>';
			echo '<div style="height:14px;"></div>';
			echo '<div style=text-align:left;padding-left:74px;padding-bottom:14px;>Comments&nbsp;<input type=text name="mp_comment" size=30 class="cell_floating"></div>';
			echo '<div style="margin-left:22px; padding-bottom:5px;"><b>Birthday</b></div>';
			echo '<div style=text-align:left;padding-left:98px;padding-bottom:4px;>From: '.SearchDateInput('day_from_birthdate','month_from_birthdate','','Y','Y','').'</div>';
			echo '<div style=text-align:left;padding-left:110px;padding-bottom:20px;>To: '.SearchDateInput('day_to_birthdate','month_to_birthdate','','Y','Y','').'</div>';
			echo '<div style="margin-left:22px; padding-bottom:5px;"><b>Goal and Progress</b></div>';
			echo '<div style=text-align:left;padding-left:80px;padding-bottom:4px;>Goal Title <input type=text name="goal_title" size=30 class="cell_floating"></div>';
			echo '<div style=text-align:left;padding-left:45px;padding-bottom:4px;>Goal Description <input type=text name="goal_description" size=30 class="cell_floating"></div>';
			echo '<div style=text-align:left;padding-left:47px;padding-bottom:4px;>Progress Period <input type=text name="progress_name" size=30 class="cell_floating"></div>';
			echo '<div style=text-align:left;padding-left:16px;padding-bottom:14px;>Progress Assessment <input type=text name="progress_description" size=30 class="cell_floating"></div>';
			
			echo '<div style="margin-left:22px; padding-bottom:5px;"><b>Medical</b></div>';
			echo '<div style=text-align:left;padding-left:103px;padding-bottom:4px;>Date '.SearchDateInput('med_day','med_month','med_year','Y','Y','Y').'</div>';
			echo '<div style=text-align:left;padding-left:60px;padding-bottom:14px;>Doctor\'s Note <input type=text name="doctors_note_comments" size=30 class="cell_floating"></div>';
			
			echo '<div style="margin-left:22px; padding-bottom:5px;"><b>Immunization</b></div>';
			echo '<div style=text-align:left;padding-left:102px;padding-bottom:4px;>Type <input type=text name="type" size=30 class="cell_floating"></div>';
			echo '<div style=text-align:left;padding-left:104px;padding-bottom:4px;>Date '.SearchDateInput('imm_day','imm_month','imm_year','Y','Y','Y').'</div>';
			echo '<div style=text-align:left;padding-left:76px;padding-bottom:14px;>Comments <input type=text name="imm_comments" size=30 class="cell_floating"></div>';
			
			echo '<div style="margin-left:22px; padding-bottom:5px;"><b>Medical Alert</b></div>';
			echo '<div style=text-align:left;padding-left:104px;padding-bottom:4px;>Date '.SearchDateInput('ma_day','ma_month','ma_year','Y','Y','Y').'</div>';
			echo '<div style=text-align:left;padding-left:102px;padding-bottom:14px;>Alert <input type=text name="med_alrt_title" size=30 class="cell_floating"></div>';
			
			echo '<div style="margin-left:22px; padding-bottom:5px;"><b>Nurse Visit</b></div>';
			echo '<div style=text-align:left;padding-left:104px;padding-bottom:4px;>Date '.SearchDateInput('nv_day','nv_month','nv_year','Y','Y','Y').'</div>';
			echo '<div style=text-align:left;padding-left:89px;padding-bottom:4px;>Reason <input type=text name="reason" size=30 class="cell_floating"></div>';
			echo '<div style=text-align:left;padding-left:96px;padding-bottom:4px;>Result <input type=text name="result" size=30 class="cell_floating"></div>';
			echo '<div style=text-align:left;padding-left:76px;padding-bottom:4px;>Comments <input type=text name="med_vist_comments" size=30 class="cell_floating"></div>';
			
			
			echo '</div>';
			
			
			
			# ---   Advanced Search End ----------------------------------------------------------- #
			
			
			
			
			echo '<TABLE width=100%><TR><TD align=center><BR>';
			if(User('PROFILE')=='admin')
			{
				echo '<INPUT type=checkbox name=address_group value=Y'.(Preferences('DEFAULT_FAMILIES')=='Y'?' CHECKED':'').'>Group by Family<BR>';
				echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'>Search All Schools<BR>';
			}
			echo '<INPUT type=checkbox name=include_inactive value=Y>Include Inactive Students<BR>';
			echo '<BR>';
			//echo Buttons('Submit','Reset');
			if($extra['pdf']!=true)
			echo "<INPUT type=SUBMIT class=btn_medium value='Submit' onclick='return formcheck_student_advnc_srch();formload_ajax(\"search\");'>&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
			else
			echo "<INPUT type=SUBMIT class=btn_medium value='Submit' onclick='return formcheck_student_advnc_srch();'>&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
			
			echo '</TD></TR>';
			echo '</TABLE>';
			echo '</FORM>';
			// set focus to last name text box
			echo '<script type="text/javascript"><!--
				document.search.last.focus();
				--></script>';
			PopTable('footer');
		break;

		case 'parent':
		case 'student':
			echo '<BR>';
			PopTable('header','Search');
			if($extra['pdf']!=true)
			echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]".$extra['action']." method=POST>";
			else
			echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]".$extra['action']." method=POST target=_blank>";
			echo '<TABLE border=0>';
			if($extra['search'])
				echo $extra['search'];
			echo '<TR><TD colspan=2 align=center>';
			echo '<BR>';
			echo Buttons('Submit','Reset');
			echo '</TD></TR>';
			echo '</TABLE>';
			echo '</FORM>';
			PopTable('footer');
		break;
	}
}
//if($_REQUEST['search_modfunc']=='list')
else
{
	if(!$_REQUEST['next_modname'])
		$_REQUEST['next_modname'] = 'Students/Student.php';

	if($_REQUEST['address_group'])
	{
		$extra['SELECT'] .= ',sam.ADDRESS_ID';
		if(!($_REQUEST['expanded_view']=='true' || $_REQUEST['addr'] || $extra['addr']))
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (sam.STUDENT_ID=ssm.STUDENT_ID AND sam.RESIDENCE='Y')".$extra['FROM'];
		$extra['group'] = array('ADDRESS_ID');
	}
	
$students_RET = GetStuList_Absence_Summary($extra);
	if($_REQUEST['address_group'])
	{
		// if address_group specified but only one address returned then convert to ungrouped
		if(count($students_RET)==1)
		{
			$students_RET = $students_RET[key($students_RET)];
			unset($_REQUEST['address_group']);
		}
		else
			$extra['LO_group'] = array('ADDRESS_ID');
	}
	if($extra['array_function'] && function_exists($extra['array_function']))
		if($_REQUEST['address_group'])
			foreach($students_RET as $id=>$student_RET)
				$students_RET[$id] = $extra['array_function']($student_RET);
		else
			$students_RET = $extra['array_function']($students_RET);

	$LO_columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','ALT_ID'=>'Alternate ID','GRADE_ID'=>'Grade','PHONE'=>'Phone');
	$name_link['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[next_modname]";
	$name_link['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');
	if($_REQUEST['_search_all_schools'])
		$name_link['FULL_NAME']['variables'] += array('school_id'=>'SCHOOL_ID');

	if(is_array($extra['link']))
		$link = $extra['link'] + $name_link;
	else
		$link = $name_link;
	if(is_array($extra['columns_before']))
	{
		$columns = $extra['columns_before'] + $LO_columns;
		$LO_columns = $columns;
	}

	if(is_array($extra['columns_after']))
		$columns = $LO_columns + $extra['columns_after'];
	if(!$extra['columns_before'] && !$extra['columns_after'])
		$columns = $LO_columns;
	
	if(count($students_RET) > 1 || $link['add'] || !$link['FULL_NAME'] || $extra['columns_before'] || $extra['columns_after'] || ($extra['BackPrompt']==false && count($students_RET)==0) || ($extra['Redirect']===false && count($students_RET)==1))
                  {
                        $tmp_REQUEST = $_REQUEST;
                        unset($tmp_REQUEST['expanded_view']);
                        if($_REQUEST['expanded_view']!='true' && !UserStudentID() && count($students_RET)!=0)
                        {
                            DrawHeader("<div><A HREF=".PreparePHP_SELF($tmp_REQUEST) . "&expanded_view=true class=big_font ><img src=\"themes/Blue/expanded_view.png\" />Expanded View</A></div><div class=break ></div>",$extra['header_right']);
                            DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_openSIS['SearchTerms'],0,-4)));
                        }
                        elseif(!UserStudentID() && count($students_RET)!=0)
                        {
                            DrawHeader("<div><A HREF=".PreparePHP_SELF($tmp_REQUEST) . "&expanded_view=false class=big_font><img src=\"themes/Blue/expanded_view.png\" />Original View</A></div><div class=break ></div>",$extra['header_right']);
                            DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_openSIS['Search'],0,-4)));
                        }
                        DrawHeader($extra['extra_header_left'],$extra['extra_header_right']);
                        if($_REQUEST['LO_save']!='1' && !$extra['suppress_save'])
                        {
                            $_SESSION['List_PHP_SELF'] = PreparePHP_SELF($_SESSION['_REQUEST_vars']);
                            echo '<script language=JavaScript>parent.help.location.reload();</script>';
                        }
                        if(!$extra['singular'] || !$extra['plural'])
                        if($_REQUEST['address_group'])
                        {
                            $extra['singular'] = 'Family';
                            $extra['plural'] = 'Families';
                        }
                        else
                        {
                            $extra['singular'] = 'Student';
                            $extra['plural'] = 'Students';
                        }
                        
echo '<div style="overflow:auto; width:820px; overflow-x:scroll;">';
                        echo "<div id='students' >";
                        ListOutput($students_RET,$columns,$extra['singular'],$extra['plural'],$link,$extra['LO_group'],$extra['options']);
                        echo "</div>";
                         echo "</div>";
	}
	elseif(count($students_RET)==1)
	{
		if(count($link['FULL_NAME']['variables']))
		{
			foreach($link['FULL_NAME']['variables'] as $var=>$val)
				$_REQUEST[$var] = $students_RET['1'][$val];
		}
		if(!is_array($students_RET[1]['STUDENT_ID']))
		{
			$_SESSION['student_id'] = $students_RET[1]['STUDENT_ID'];
			#$_SESSION['UserSchool'] = $students_RET[1]['LIST_SCHOOL_ID'];
			
			
			if(User('PROFILE')=='admin')
				$_SESSION['UserSchool'] = $students_RET[1]['LIST_SCHOOL_ID'];
		    if(User('PROFILE')=='teacher')
				$_SESSION['UserSchool'] = $students_RET[1]['SCHOOL_ID'];
			
			
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			unset($_REQUEST['search_modfunc']);
		}
		if($_REQUEST['modname']!=$_REQUEST['next_modname'])
		{
			$modname = $_REQUEST['next_modname'];
			if(strpos($modname,'?'))
				$modname = substr($_REQUEST['next_modname'],0,strpos($_REQUEST['next_modname'],'?'));
			if(strpos($modname,'&'))
				$modname = substr($_REQUEST['next_modname'],0,strpos($_REQUEST['next_modname'],'&'));
			if($_REQUEST['modname'])
				$_REQUEST['modname'] = $modname;
			include('modules/'.$modname);
		}
	}
	else
		BackPrompt('No Students were found.');
}
?>