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
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='save')
{
	$date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
	if(count($_REQUEST['month_values']))
	{
		foreach($_REQUEST['month_values'] as $field_name=>$month)
		{
			$_REQUEST['values'][$field_name] = $_REQUEST['day_values'][$field_name].'-'.$month.'-'.$_REQUEST['year_values'][$field_name];
			if(!VerifyDate($_REQUEST['values'][$field_name]))
			{
				if($_REQUEST['values'][$field_name]!='--')
					$note = '<IMG SRC=assets/warning_button.gif>The date you specified is not valid, so was not used.  The other data was saved.';
				unset($_REQUEST['values'][$field_name]);
			}
		}
	}

	if(count($_REQUEST['values']) && count($_REQUEST['student']))
	{
		if($_REQUEST['values']['NEXT_SCHOOL']!='')
		{
                                            $next_school = $_REQUEST['values']['NEXT_SCHOOL'];
		        unset($_REQUEST['values']['NEXT_SCHOOL']);
		}
		if($_REQUEST['values']['CALENDAR_ID'])
		{
                        $calendar = clean_param($_REQUEST['values']['CALENDAR_ID'],PARAM_INT);
			unset($_REQUEST['values']['CALENDAR_ID']);
		}
		foreach($_REQUEST['values'] as $field=>$value)
		{
			if(isset($value) && trim($value)!='')
			{       
                                $value= paramlib_validation($field,$value);
				$update .= ','.$field."='$value'";
				$values_count++;
			}
		}

		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			if($yes=='Y')
			{
				$students .= ",'$student_id'";
				$students_count++;
			}
		}
		if($values_count && $students_count)
			DBQuery('UPDATE STUDENTS SET '.substr($update,1).' WHERE STUDENT_ID IN ('.substr($students,1).')');
		elseif($note)
			$note = substr($note,0,strpos($note,'. '));
		elseif($next_school=='' && !$calendar)
			$note = '<IMG SRC=assets/warning_button.gif>No data was entered.';

		if($next_school!='')
			DBQuery("UPDATE STUDENT_ENROLLMENT SET NEXT_SCHOOL='".$next_school."' WHERE SYEAR='".UserSyear()."' AND STUDENT_ID IN (".substr($students,1).") ");
		if($calendar)
			DBQuery("UPDATE STUDENT_ENROLLMENT SET CALENDAR_ID='".$calendar."' WHERE SYEAR='".UserSyear()."' AND STUDENT_ID IN (".substr($students,1).") ");

		if(!$note)
			$note = '<IMG SRC=assets/check.gif>The specified information was applied to the selected students.';
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['values']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['values']);
	}
	else
                {
                    ShowErr('You must choose at least one field and one student');
                    for_error();
}
}

DrawBC("Students > ".ProgramTitle());

if(!$_REQUEST['modfunc'])
{
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",CAST(NULL AS CHAR(1)) AS CHECKBOX";

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save METHOD=POST>";

		if($_REQUEST['category_id'])
		{
			$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS FROM CUSTOM_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]'"),array(),array('TYPE'));
		}	
		else
		{
			$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS FROM CUSTOM_FIELDS"),array(),array('TYPE'));
		}
		$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STUDENT_FIELD_CATEGORIES WHERE ID=1 OR ID=2"));
		$tmp_REQUEST = $_REQUEST;
		unset($tmp_REQUEST['category_id']);
		echo '<CENTER><TABLE align=center ><TR><TD align=center>';
		echo '<CENTER><SELECT name=category_id onchange="document.location.href=\''.PreparePHP_SELF($tmp_REQUEST).'&amp;category_id=\'+this.form.category_id.value;"><OPTION value="">All Categories</OPTION>';
		foreach($categories_RET as $category)
			echo '<OPTION value='.$category['ID'].($_REQUEST['category_id']==$category['ID']?' SELECTED':'').'>'.$category['TITLE'].'</OPTION>';
		echo '</SELECT><div class=clear ></div>';
		PopTable_wo_header ('header','');
		echo '<TABLE align=center>';
		if(count($fields_RET['text']))
		{
			foreach($fields_RET['text'] as $field)
			 {
			 	$title=strtolower(trim($field['TITLE']));
			 	if(strpos(trim($field['TITLE']),' ')!=0)
				{
				 $p1=substr(trim($field['TITLE']),0,strpos(trim($field['TITLE']),' '));
				 $p2=substr(trim($field['TITLE']),strpos(trim($field['TITLE']),' ')+1);
				 $title=strtolower($p1.'_'.$p2);
				}
				$query=mysql_query("SELECT * FROM STUDENTS");
				$f=0;
				while($colnames=mysql_fetch_field($query))
				 {
				  if($colnames->name==$title)
				  $f=1;
				 }
				if($f==0)
				 {
				 	$title='CUSTOM_'.trim($field['ID']);
				 }
				echo '<TR><TD class=lable>'.$field['TITLE'].'</TD><TD>'._makeTextInput($title).'</TD></TR>';
			 }	
		}
		if(count($fields_RET['numeric']))
		{
			foreach($fields_RET['numeric'] as $field)
				echo '<TR><TD class=lable>'.$field['TITLE'].'</TD><TD>'._makeTextInput('CUSTOM_'.$field['ID'],true).'</TD></TR>';
		}
		if(count($fields_RET['date']))
		{
			foreach($fields_RET['date'] as $field)
				echo '<TR><TD class=lable>'.$field['TITLE'].'</TD><TD>'._makeDateInput('CUSTOM_'.$field['ID']).'</TD></TR>';
		}
		if(count($fields_RET['select']))
		{
			foreach($fields_RET['select'] as $field)
			{
				$select_options = array();
				$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$options = explode("\r",$field['SELECT_OPTIONS']);
				if(count($options))
				{
					foreach($options as $option)
						$select_options[$option] = $option;
				}

				echo "<TR><TD class=lable_right valign=top>$field[TITLE]</TD><TD>"._makeSelectInput('CUSTOM_'.$field['ID'],$select_options).'</TD></TR>';
				echo "</TD></TR>";
			}
		}
		if(count($fields_RET['textarea']))
		{
			foreach($fields_RET['textarea'] as $field)
			{
				echo '<TR><TD class=lable_right valign=top>'.$field['TITLE'].'</TD>';
				echo '<TD>';
				echo _makeTextareaInput('CUSTOM_'.$field['ID']);
				echo '</TD>';
				echo '</TR>';
			}
		}
		if(!$_REQUEST['category_id'] || $_REQUEST['category_id']=='1')
		{
			echo '<TR><TD class=lable valign=top>Rolling Retention / Options</TD>';
			echo '<TD>';
			$schools_RET = DBGet(DBQuery("SELECT ID,TITLE FROM SCHOOLS WHERE ID!='".UserSchool()."'"));
			$options = array(UserSchool()=>'Next grade at current school','0'=>'Retain','-1'=>'Do not enroll after this school year');
			if(count($schools_RET))
			{
				foreach($schools_RET as $school)
					$options[$school['ID']] = $school['TITLE'];
			}
			echo _makeSelectInput('NEXT_SCHOOL',$options);
			echo '</TD>';
			echo '</TR><TR>';

			echo '<TD class=lable_right valign=top>Calendar</TD>';
			echo '<TD>';
			$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,DEFAULT_CALENDAR,TITLE FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY DEFAULT_CALENDAR ASC"));
			$options = array();
			if(count($calendars_RET))
			{
				foreach($calendars_RET as $calendar)
					$options[$calendar['CALENDAR_ID']] = $calendar['TITLE'];
			}
			echo _makeSelectInput('CALENDAR_ID',$options);
			echo '</TD>';
			echo '</TR>';
		}
		echo '</TABLE>';
		echo '<BR>';

		$radio_count = count($fields_RET['radio']);
		if($radio_count)
		{
			echo '<TABLE cellpadding=5>';
			echo '<TR>';
			for($i=1;$i<=$radio_count;$i++)
			{
				echo '<TD>'._makeCheckboxInput('CUSTOM_'.$fields_RET['radio'][$i]['ID'],'<b>'.$fields_RET['radio'][$i]['TITLE'].'</b>').'</TD>';
				if($i%5==0 && $i!=$radio_count)
					echo '</TR><TR>';
			}
			echo '</TD></TR>';
			echo '</TABLE>';
		}
		PopTable('footer');
		echo '</TD></TR>';
		echo '</TABLE><BR>';
	}
	elseif($note)
		DrawHeader($note);

	Widgets('activity');
	Widgets('course');
	Widgets('absences');

	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Search('student_id',$extra);
	//if($_REQUEST['search_modfunc']=='list')
        if($_REQUEST['search_modfunc']=='list' && $_SESSION['count_stu']!='0')
        {
             unset ($_SESSION['count_stu']);
             echo "<BR><CENTER>".SubmitButton('Save','','class=btn_medium')."</CENTER>";
         }
         echo '</FORM>';
}

function _makeChooseCheckbox($value,$title='')
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

function _makeTextInput($column,$numeric=false)
{
	if($numeric===true)
		$options = 'size=3 maxlength=11 class=cell_floating';
	else
		$options = 'size=25 class=cell_floating';

	return TextInput('','values['.$column.']','',$options);
}

function _makeTextareaInput($column,$numeric=false)
{
	return TextAreaInput('','values['.$column.']');
}

function _makeDateInput($column)
{
	return DateInput('','values['.$column.']','');
}

function _makeSelectInput($column,$options)
{
	return SelectInput('','values['.$column.']','',$options,'N/A',"style='max-width:250;'");
}

function _makeCheckboxInput($column,$name)
{
	return CheckboxInput('','values['.$column.']',$name,'',true);
}
?>