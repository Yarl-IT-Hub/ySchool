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
function Search($type,$extra=array(),$search_from_grade='')
{	global $_openSIS;

	switch($type)
	{
		case 'student_id':
			if($_REQUEST['bottom_back'])
			{
				unset($_SESSION['student_id']);
				echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			if($_SESSION['unset_student'])
			{
				unset($_REQUEST['student_id']);
				unset($_SESSION['unset_student']);
			}

			if($_REQUEST['student_id'])
			{
				if($_REQUEST['student_id']!='new')
				{
					$_SESSION['student_id'] = $_REQUEST['student_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['student_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}

			if(!UserStudentID() && $_REQUEST['student_id']!='new' || $extra['new']==true)
			{
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				include('modules/Students/Search.inc.php');
				/*$b = $_REQUEST['modname'];
			        $a = substr($b,0,strpos($b,'/'));
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				if($a=='Scheduling')
				{
				include('modules/'.$a.'/Search.inc.php');
				}
				else
				{*/
				//include('modules/Students/Search.inc.php');
				//}
			}
		break;

		case 'staff_id':
			// convert profile string to array for legacy compatibility
			if (!is_array($extra)) $extra = array('profile'=>$extra);
			if(!$_REQUEST['staff_id'] && User('PROFILE')!='admin')
				$_REQUEST['staff_id'] = User('STAFF_ID');

			if($_REQUEST['staff_id'])
			{
				if($_REQUEST['staff_id']!='new')
				{
					$_SESSION['staff_id'] = $_REQUEST['staff_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['staff_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			//elseif(!UserStaffID() || $extra['new']==true)
			if(!UserStaffID() && $_REQUEST['staff_id']!='new' || $extra['new']==true)
			{
				if(!$_REQUEST['modfunc']) $_REQUEST['modfunc'] = 'search_fnc';
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				if(!$_REQUEST['modname']) $_REQUEST['modname'] = 'Users/Search.php';
				include('modules/Users/Search.inc.php');
			}
		break;

		case 'general_info':
			echo '<tr><td align=right width=120>Last Name</td><td><input type=text name="last" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>First Name</td><td><input type=text name="first" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>Student ID</td><td><input type=text name="stuid" size=30 class="cell_floating"></td></tr>';
		        echo '<tr><td align=right width=120>Alt ID</td><td><input type=text name="altid" size=30 class="cell_floating"></td></tr>';
                        echo '<tr><td align=right width=120>Address</td><td><input type=text name="addr" size=30 class="cell_floating"></td></tr>';

			$list = DBGet(DBQuery("SELECT DISTINCT TITLE,ID,SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
			echo '<TR><TD align=right width=120>Grade</TD><TD><SELECT name=grade><OPTION value="" class="cell_floating">Not Specified</OPTION>';
			foreach($list as $value)
				echo "<OPTION value=$value[ID]>$value[TITLE]</OPTION>";
			echo '</SELECT></TD></TR>';
		break;

		case 'student_fields':
			$search_fields_RET = DBGet(DBQuery("SELECT CONCAT('CUSTOM_',cf.ID) AS COLUMN_NAME,cf.TYPE,cf.TITLE,cf.SELECT_OPTIONS FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsSearch' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y' ORDER BY cf.SORT_ORDER,cf.TITLE"),array(),array('TYPE'));
			if(!$search_fields_RET)
				$search_fields_RET = DBGet(DBQuery("SELECT CONCAT('CUSTOM_',cf.ID) AS COLUMN_NAME,cf.TYPE,cf.TITLE,cf.SELECT_OPTIONS FROM CUSTOM_FIELDS cf WHERE cf.ID IN ('200000000','200000001')"),array(),array('TYPE'));
                                // edit needed
			if(count($search_fields_RET['text']))
			{
				foreach($search_fields_RET['text'] as $column)
					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD><INPUT type=text name=cust[{$column[COLUMN_NAME]}] size=30 class=\"cell_floating\"></TD></TR>";
			}
			if(count($search_fields_RET['numeric']))
			{
				foreach($search_fields_RET['numeric'] as $column)
					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>Between <INPUT type=text name=cust_begin[{$column[COLUMN_NAME]}] size=3 maxlength=11 class=\"cell_floating\"> &amp; <INPUT type=text name=cust_end[{$column[COLUMN_NAME]}] size=3 maxlength=11 class=\"cell_small\"></TD></TR>";
			}
			echo '</TABLE><TABLE>';
			if(count($search_fields_RET['codeds']))
			{
				foreach($search_fields_RET['codeds'] as $column)
				{
					$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
					$options = explode("\r",$column['SELECT_OPTIONS']);

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					foreach($options as $option)
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							echo "<OPTION value=\"$option[0]\">$option[1]</OPTION>";
					}
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['select']))
			{
				foreach($search_fields_RET['select'] as $column)
				{
					$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
					$options = explode("\r",$column['SELECT_OPTIONS']);

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					foreach($options as $option)
						echo "<OPTION value=\"$option\">$option</OPTION>";
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['autos']))
			{
				foreach($search_fields_RET['autos'] as $column)
				{
					if($column['SELECT_OPTIONS'])
					{
						$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
						$options_RET = explode("\r",$column['SELECT_OPTIONS']);
					}
					else
						$options_RET = array();

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					$options = array();
					foreach($options_RET as $option)
					{
						echo "<OPTION value=\"$option\">$option</OPTION>";
						$options[$option] = true;
					}
					echo "<OPTION value=\"---\">---</OPTION>";
					$options['---'] = true;
					// add values found in current and previous year
					$options_RET = DBGet(DBQuery("SELECT DISTINCT s.$column[COLUMN_NAME],upper(s.$column[COLUMN_NAME]) AS KEEY FROM STUDENTS s,STUDENT_ENROLLMENT sse WHERE sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND $column[COLUMN_NAME] IS NOT NULL ORDER BY KEEY"));
					foreach($options_RET as $option)
						if($option[$column['COLUMN_NAME']]!='' && !$options[$option[$column['COLUMN_NAME']]])
						{
							echo "<OPTION value=\"".$option[$column['COLUMN_NAME']]."\">".$option[$column['COLUMN_NAME']]."</OPTION>";
							$options[$option[$column['COLUMN_NAME']]] = true;
						}
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['edits']))
			{
				foreach($search_fields_RET['edits'] as $column)
				{
					if($column['SELECT_OPTIONS'])
					{
						$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
						$options_RET = explode("\r",$column['SELECT_OPTIONS']);
					}
					else
						$options_RET = array();

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					$options = array();
					foreach($options_RET as $option)
						echo "<OPTION value=\"$option\">$option</OPTION>";
					echo "<OPTION value=\"---\">---</OPTION>";
					echo "<OPTION value=\"~\">Other Value</OPTION>";
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			echo '</TABLE><TABLE>';
			if(count($search_fields_RET['date']))
			{
				foreach($search_fields_RET['date'] as $column)
					echo "<TR><TD colspan=2>$column[TITLE]<BR> &nbsp; &nbsp; Between ".PrepareDate('','_cust_begin['.$column['COLUMN_NAME'].']',true,array('short'=>true)).' & '.PrepareDate('','_cust_end['.$column['COLUMN_NAME'].']',true,array('short'=>true))."</TD></TR>";
			}
			if(count($search_fields_RET['radio']))
			{
				echo '<TR><TD colspan=2><BR></TD></TR>';
				echo "<TR><TD colspan=2><TABLE>";

				echo "<TR><TD></TD><TD><table border=0 cellpadding=0 cellspacing=0><tr><td width=25><b>All</b></td><td width=30><b>Yes</b></td><td width=25><b>No</b></td></tr></table></TD><TD></TD><TD></TD><TD>";
				if(count($search_fields_RET['radio'])>1)
					echo "<table border=0 cellpadding=0 cellspacing=0><tr><td width=25><b>All</b></td><td width=30><b>Yes</b></td><td width=25><b>No</b></td></tr></table>";
				echo "</TD></TR>";

				$side = 1;
				foreach($search_fields_RET['radio'] as $cust)
				{
					if($side%2!=0)
						echo '<TR>';
					echo "<TD ALIGN=RIGHT>$cust[TITLE]</TD><TD>
						<table border=0 cellpadding=0 cellspacing=0><tr><td width=25 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='' checked='checked' />
						</td><td width=30 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='Y' />
						</td><td width=25 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='N' />
						</td></tr></table>
						</TD><TD>&nbsp; &nbsp; &nbsp; &nbsp;</TD>";
					if($side%2==0)
						echo '</TR>';
					$side++;
				}
				echo "</TABLE></TD></TR>";
			}
			echo '</TABLE>';
		break;
	}
}




# -------------------------------- SEARCH FOR MISSING ATTENDANCE START ----------------------------------------- #


function Search_Miss_Attn($type,$extra=array())
{	global $_openSIS;

	switch($type)
	{

		case 'staff_id':
			// convert profile string to array for legacy compatibility
			if (!is_array($extra)) $extra = array('profile'=>$extra);
			if(!$_REQUEST['staff_id'] && User('PROFILE')!='admin')
				$_REQUEST['staff_id'] = User('STAFF_ID');

			if($_REQUEST['staff_id'])
			{
				if($_REQUEST['staff_id']!='new')
				{
					$_SESSION['staff_id'] = $_REQUEST['staff_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['staff_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			//elseif(!UserStaffID() || $extra['new']==true)
			if(!UserStaffID() && $_REQUEST['staff_id']!='new' || $extra['new']==true)
			{
				if(!$_REQUEST['modfunc']) $_REQUEST['modfunc'] = 'search_fnc';
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				if(!$_REQUEST['modname']) $_REQUEST['modname'] = 'Users/Search.php';
				include('modules/Users/Search_Miss_Attn.inc.php');
			}
		break;

	}
}


# ---------------------------------------- SEARCH FOR MISSING ATTENDANCE END ----------------------------------------- #

#---------------------------------SEARCH FOR GROUP SCHEDULING ---------------------------------------------------------------#

function Search_GroupSchedule($type,$extra=array())
{	global $_openSIS;
unset($_SESSION['student_id']);
	switch($type)
	{
		case 'student_id':
			if($_REQUEST['bottom_back'])
			{
				unset($_SESSION['student_id']);
				echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			if($_SESSION['unset_student'])
			{
				unset($_REQUEST['student_id']);
				unset($_SESSION['unset_student']);
			}

			if($_REQUEST['student_id'])
			{
				if($_REQUEST['student_id']!='new')
				{
					$_SESSION['student_id'] = $_REQUEST['student_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['student_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}

			if(!UserStudentID() && $_REQUEST['student_id']!='new' || $extra['new']==true)
			{
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				include('modules/Scheduling/Search.inc.php');
			}
		break;

		case 'staff_id':
			// convert profile string to array for legacy compatibility
			if (!is_array($extra)) $extra = array('profile'=>$extra);
			if(!$_REQUEST['staff_id'] && User('PROFILE')!='admin')
				$_REQUEST['staff_id'] = User('STAFF_ID');

			if($_REQUEST['staff_id'])
			{
				if($_REQUEST['staff_id']!='new')
				{
					$_SESSION['staff_id'] = $_REQUEST['staff_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['staff_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			//elseif(!UserStaffID() || $extra['new']==true)
			if(!UserStaffID() && $_REQUEST['staff_id']!='new' || $extra['new']==true)
			{
				if(!$_REQUEST['modfunc']) $_REQUEST['modfunc'] = 'search_fnc';
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				if(!$_REQUEST['modname']) $_REQUEST['modname'] = 'Users/Search.php';
				include('modules/Users/Search.inc.php');
			}
		break;

		case 'general_info':
			echo '<tr><td align=right width=120>Last Name</td><td><input type=text name="last" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>First Name</td><td><input type=text name="first" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>Student ID</td><td><input type=text name="stuid" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>Alt ID</td><td><input type=text name="altid" size=30 class="cell_floating"></td></tr>';
                        echo '<tr><td align=right width=120>Address</td><td><input type=text name="addr" size=30 class="cell_floating"></td></tr>';

			$list = DBGet(DBQuery("SELECT DISTINCT TITLE,ID,SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
			echo '<TR><TD align=right width=120>Grade</TD><TD><SELECT name=grade><OPTION value="" class="cell_floating">Not Specified</OPTION>';
			foreach($list as $value)
				echo "<OPTION value=$value[ID]>$value[TITLE]</OPTION>";
			echo '</SELECT></TD></TR>';
		break;

		case 'student_fields':
			$search_fields_RET = DBGet(DBQuery("SELECT CONCAT('CUSTOM_',cf.ID) AS COLUMN_NAME,cf.TYPE,cf.TITLE,cf.SELECT_OPTIONS FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsSearch' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y' ORDER BY cf.SORT_ORDER,cf.TITLE"),array(),array('TYPE'));
			if(!$search_fields_RET)
				$search_fields_RET = DBGet(DBQuery("SELECT CONCAT('CUSTOM_',cf.ID) AS COLUMN_NAME,cf.TYPE,cf.TITLE,cf.SELECT_OPTIONS FROM CUSTOM_FIELDS cf WHERE cf.ID IN ('200000000','200000001')"),array(),array('TYPE'));
                                   // edit needed
			if(count($search_fields_RET['text']))
			{
				foreach($search_fields_RET['text'] as $column)
					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD><INPUT type=text name=cust[{$column[COLUMN_NAME]}] size=30 class=\"cell_floating\"></TD></TR>";
			}
			if(count($search_fields_RET['numeric']))
			{
				foreach($search_fields_RET['numeric'] as $column)
					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>Between <INPUT type=text name=cust_begin[{$column[COLUMN_NAME]}] size=3 maxlength=11 class=\"cell_floating\"> &amp; <INPUT type=text name=cust_end[{$column[COLUMN_NAME]}] size=3 maxlength=11 class=\"cell_small\"></TD></TR>";
			}
			echo '</TABLE><TABLE>';
			if(count($search_fields_RET['codeds']))
			{
				foreach($search_fields_RET['codeds'] as $column)
				{
					$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
					$options = explode("\r",$column['SELECT_OPTIONS']);

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					foreach($options as $option)
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							echo "<OPTION value=\"$option[0]\">$option[1]</OPTION>";
					}
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['select']))
			{
				foreach($search_fields_RET['select'] as $column)
				{
					$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
					$options = explode("\r",$column['SELECT_OPTIONS']);

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					foreach($options as $option)
						echo "<OPTION value=\"$option\">$option</OPTION>";
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['autos']))
			{
				foreach($search_fields_RET['autos'] as $column)
				{
					if($column['SELECT_OPTIONS'])
					{
						$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
						$options_RET = explode("\r",$column['SELECT_OPTIONS']);
					}
					else
						$options_RET = array();

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					$options = array();
					foreach($options_RET as $option)
					{
						echo "<OPTION value=\"$option\">$option</OPTION>";
						$options[$option] = true;
					}
					echo "<OPTION value=\"---\">---</OPTION>";
					$options['---'] = true;
					// add values found in current and previous year
					$options_RET = DBGet(DBQuery("SELECT DISTINCT s.$column[COLUMN_NAME],upper(s.$column[COLUMN_NAME]) AS KEEY FROM STUDENTS s,STUDENT_ENROLLMENT sse WHERE sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND $column[COLUMN_NAME] IS NOT NULL ORDER BY KEEY"));
					foreach($options_RET as $option)
						if($option[$column['COLUMN_NAME']]!='' && !$options[$option[$column['COLUMN_NAME']]])
						{
							echo "<OPTION value=\"".$option[$column['COLUMN_NAME']]."\">".$option[$column['COLUMN_NAME']]."</OPTION>";
							$options[$option[$column['COLUMN_NAME']]] = true;
						}
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['edits']))
			{
				foreach($search_fields_RET['edits'] as $column)
				{
					if($column['SELECT_OPTIONS'])
					{
						$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
						$options_RET = explode("\r",$column['SELECT_OPTIONS']);
					}
					else
						$options_RET = array();

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					$options = array();
					foreach($options_RET as $option)
						echo "<OPTION value=\"$option\">$option</OPTION>";
					echo "<OPTION value=\"---\">---</OPTION>";
					echo "<OPTION value=\"~\">Other Value</OPTION>";
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			echo '</TABLE><TABLE>';
			if(count($search_fields_RET['date']))
			{
				foreach($search_fields_RET['date'] as $column)
					echo "<TR><TD colspan=2>$column[TITLE]<BR> &nbsp; &nbsp; Between ".PrepareDate('','_cust_begin['.$column['COLUMN_NAME'].']',true,array('short'=>true)).' & '.PrepareDate('','_cust_end['.$column['COLUMN_NAME'].']',true,array('short'=>true))."</TD></TR>";
			}
			if(count($search_fields_RET['radio']))
			{
				echo '<TR><TD colspan=2><BR></TD></TR>';
				echo "<TR><TD colspan=2><TABLE>";

				echo "<TR><TD></TD><TD><table border=0 cellpadding=0 cellspacing=0><tr><td width=25><b>All</b></td><td width=30><b>Yes</b></td><td width=25><b>No</b></td></tr></table></TD><TD></TD><TD></TD><TD>";
				if(count($search_fields_RET['radio'])>1)
					echo "<table border=0 cellpadding=0 cellspacing=0><tr><td width=25><b>All</b></td><td width=30><b>Yes</b></td><td width=25><b>No</b></td></tr></table>";
				echo "</TD></TR>";

				$side = 1;
				foreach($search_fields_RET['radio'] as $cust)
				{
					if($side%2!=0)
						echo '<TR>';
					echo "<TD ALIGN=RIGHT>$cust[TITLE]</TD><TD>
						<table border=0 cellpadding=0 cellspacing=0><tr><td width=25 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='' checked='checked' />
						</td><td width=30 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='Y' />
						</td><td width=25 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='N' />
						</td></tr></table>
						</TD><TD>&nbsp; &nbsp; &nbsp; &nbsp;</TD>";
					if($side%2==0)
						echo '</TR>';
					$side++;
				}
				echo "</TABLE></TD></TR>";
			}
			echo '</TABLE>';
		break;
	}
}



#----------------------------SEARCH FOR GROUP SCHEDULING ENDS HERE -----------------------------------------------------------#


function Search_absence_summary($type,$extra=array(),$search_from_grade='')
{	global $_openSIS;

	switch($type)
	{
		case 'student_id':
			if($_REQUEST['bottom_back'])
			{
				unset($_SESSION['student_id']);
				echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			if($_SESSION['unset_student'])
			{
				unset($_REQUEST['student_id']);
				unset($_SESSION['unset_student']);
			}

			if($_REQUEST['student_id'])
			{
				if($_REQUEST['student_id']!='new')
				{
					$_SESSION['student_id'] = $_REQUEST['student_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['student_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}

			if(!UserStudentID() && $_REQUEST['student_id']!='new' || $extra['new']==true)
			{
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				include('modules/Attendance/Search.inc.php');
				/*$b = $_REQUEST['modname'];
			        $a = substr($b,0,strpos($b,'/'));
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				if($a=='Scheduling')
				{
				include('modules/'.$a.'/Search.inc.php');
				}
				else
				{*/
				//include('modules/Students/Search.inc.php');
				//}
			}
		break;

		case 'staff_id':
			// convert profile string to array for legacy compatibility
			if (!is_array($extra)) $extra = array('profile'=>$extra);
			if(!$_REQUEST['staff_id'] && User('PROFILE')!='admin')
				$_REQUEST['staff_id'] = User('STAFF_ID');

			if($_REQUEST['staff_id'])
			{
				if($_REQUEST['staff_id']!='new')
				{
					$_SESSION['staff_id'] = $_REQUEST['staff_id'];
					if($_REQUEST['school_id'])
						$_SESSION['UserSchool'] = $_REQUEST['school_id'];
				}
				else
					unset($_SESSION['staff_id']);
				if(!$_REQUEST['_openSIS_PDF'])
					echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}
			//elseif(!UserStaffID() || $extra['new']==true)
			if(!UserStaffID() && $_REQUEST['staff_id']!='new' || $extra['new']==true)
			{
				if(!$_REQUEST['modfunc']) $_REQUEST['modfunc'] = 'search_fnc';
				$_REQUEST['next_modname'] = $_REQUEST['modname'];
				if(!$_REQUEST['modname']) $_REQUEST['modname'] = 'Users/Search.php';
				include('modules/Users/Search.inc.php');
			}
		break;

		case 'general_info':
			echo '<tr><td align=right width=120>Last Name</td><td><input type=text name="last" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>First Name</td><td><input type=text name="first" size=30 class="cell_floating"></td></tr>';
			echo '<tr><td align=right width=120>Student ID</td><td><input type=text name="stuid" size=30 class="cell_floating"></td></tr>';
		        echo '<tr><td align=right width=120>Alt ID</td><td><input type=text name="altid" size=30 class="cell_floating"></td></tr>';
                        echo '<tr><td align=right width=120>Address</td><td><input type=text name="addr" size=30 class="cell_floating"></td></tr>';

			$list = DBGet(DBQuery("SELECT DISTINCT TITLE,ID,SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
			echo '<TR><TD align=right width=120>Grade</TD><TD><SELECT name=grade><OPTION value="" class="cell_floating">Not Specified</OPTION>';
			foreach($list as $value)
				echo "<OPTION value=$value[ID]>$value[TITLE]</OPTION>";
			echo '</SELECT></TD></TR>';
		break;

		case 'student_fields':
			$search_fields_RET = DBGet(DBQuery("SELECT CONCAT('CUSTOM_',cf.ID) AS COLUMN_NAME,cf.TYPE,cf.TITLE,cf.SELECT_OPTIONS FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsSearch' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y' ORDER BY cf.SORT_ORDER,cf.TITLE"),array(),array('TYPE'));
			if(!$search_fields_RET)
				$search_fields_RET = DBGet(DBQuery("SELECT CONCAT('CUSTOM_',cf.ID) AS COLUMN_NAME,cf.TYPE,cf.TITLE,cf.SELECT_OPTIONS FROM CUSTOM_FIELDS cf WHERE cf.ID IN ('200000000','200000001')"),array(),array('TYPE'));
                                // edit needed
			if(count($search_fields_RET['text']))
			{
				foreach($search_fields_RET['text'] as $column)
					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD><INPUT type=text name=cust[{$column[COLUMN_NAME]}] size=30 class=\"cell_floating\"></TD></TR>";
			}
			if(count($search_fields_RET['numeric']))
			{
				foreach($search_fields_RET['numeric'] as $column)
					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>Between <INPUT type=text name=cust_begin[{$column[COLUMN_NAME]}] size=3 maxlength=11 class=\"cell_floating\"> &amp; <INPUT type=text name=cust_end[{$column[COLUMN_NAME]}] size=3 maxlength=11 class=\"cell_small\"></TD></TR>";
			}
			echo '</TABLE><TABLE>';
			if(count($search_fields_RET['codeds']))
			{
				foreach($search_fields_RET['codeds'] as $column)
				{
					$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
					$options = explode("\r",$column['SELECT_OPTIONS']);

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					foreach($options as $option)
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							echo "<OPTION value=\"$option[0]\">$option[1]</OPTION>";
					}
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['select']))
			{
				foreach($search_fields_RET['select'] as $column)
				{
					$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
					$options = explode("\r",$column['SELECT_OPTIONS']);

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					foreach($options as $option)
						echo "<OPTION value=\"$option\">$option</OPTION>";
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['autos']))
			{
				foreach($search_fields_RET['autos'] as $column)
				{
					if($column['SELECT_OPTIONS'])
					{
						$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
						$options_RET = explode("\r",$column['SELECT_OPTIONS']);
					}
					else
						$options_RET = array();

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					$options = array();
					foreach($options_RET as $option)
					{
						echo "<OPTION value=\"$option\">$option</OPTION>";
						$options[$option] = true;
					}
					echo "<OPTION value=\"---\">---</OPTION>";
					$options['---'] = true;
					// add values found in current and previous year
					$options_RET = DBGet(DBQuery("SELECT DISTINCT s.$column[COLUMN_NAME],upper(s.$column[COLUMN_NAME]) AS KEEY FROM STUDENTS s,STUDENT_ENROLLMENT sse WHERE sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND $column[COLUMN_NAME] IS NOT NULL ORDER BY KEEY"));
					foreach($options_RET as $option)
						if($option[$column['COLUMN_NAME']]!='' && !$options[$option[$column['COLUMN_NAME']]])
						{
							echo "<OPTION value=\"".$option[$column['COLUMN_NAME']]."\">".$option[$column['COLUMN_NAME']]."</OPTION>";
							$options[$option[$column['COLUMN_NAME']]] = true;
						}
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			if(count($search_fields_RET['edits']))
			{
				foreach($search_fields_RET['edits'] as $column)
				{
					if($column['SELECT_OPTIONS'])
					{
						$column['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$column['SELECT_OPTIONS']));
						$options_RET = explode("\r",$column['SELECT_OPTIONS']);
					}
					else
						$options_RET = array();

					echo "<TR><TD align=right width=120>$column[TITLE]</TD><TD>";
					echo "<SELECT name=cust[{$column[COLUMN_NAME]}] style='max-width:250;'><OPTION value=''>N/A</OPTION><OPTION value='!'>No Value</OPTION>";
					$options = array();
					foreach($options_RET as $option)
						echo "<OPTION value=\"$option\">$option</OPTION>";
					echo "<OPTION value=\"---\">---</OPTION>";
					echo "<OPTION value=\"~\">Other Value</OPTION>";
					echo '</SELECT>';
					echo "</TD></TR>";
				}
			}
			echo '</TABLE><TABLE>';
			if(count($search_fields_RET['date']))
			{
				foreach($search_fields_RET['date'] as $column)
					echo "<TR><TD colspan=2>$column[TITLE]<BR> &nbsp; &nbsp; Between ".PrepareDate('','_cust_begin['.$column['COLUMN_NAME'].']',true,array('short'=>true)).' & '.PrepareDate('','_cust_end['.$column['COLUMN_NAME'].']',true,array('short'=>true))."</TD></TR>";
			}
			if(count($search_fields_RET['radio']))
			{
				echo '<TR><TD colspan=2><BR></TD></TR>';
				echo "<TR><TD colspan=2><TABLE>";

				echo "<TR><TD></TD><TD><table border=0 cellpadding=0 cellspacing=0><tr><td width=25><b>All</b></td><td width=30><b>Yes</b></td><td width=25><b>No</b></td></tr></table></TD><TD></TD><TD></TD><TD>";
				if(count($search_fields_RET['radio'])>1)
					echo "<table border=0 cellpadding=0 cellspacing=0><tr><td width=25><b>All</b></td><td width=30><b>Yes</b></td><td width=25><b>No</b></td></tr></table>";
				echo "</TD></TR>";

				$side = 1;
				foreach($search_fields_RET['radio'] as $cust)
				{
					if($side%2!=0)
						echo '<TR>';
					echo "<TD ALIGN=RIGHT>$cust[TITLE]</TD><TD>
						<table border=0 cellpadding=0 cellspacing=0><tr><td width=25 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='' checked='checked' />
						</td><td width=30 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='Y' />
						</td><td width=25 align=center>
						<input name='cust[{$cust[COLUMN_NAME]}]' type='radio' value='N' />
						</td></tr></table>
						</TD><TD>&nbsp; &nbsp; &nbsp; &nbsp;</TD>";
					if($side%2==0)
						echo '</TR>';
					$side++;
				}
				echo "</TABLE></TD></TR>";
			}
			echo '</TABLE>';
		break;
	}
}

?>
