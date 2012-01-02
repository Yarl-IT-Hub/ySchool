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
include('../../../Redirect_includes.php');
function _makeTextInput($column,$name,$size,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
	}

	if($field['TYPE']=='numeric')
		$value[$column] = str_replace('.00','',$value[$column]);

	return TextInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$size,$div);
}

function _makeDateInput($column,$name,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
	}

	return DateInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$div);
}

function _makeSelectInput($column,$name,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{   $field_err=false; 
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
	}

	$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
	$select_options = explode("\r",$field['SELECT_OPTIONS']);
	if(count($select_options))
	{
		foreach($select_options as $option)
			if($field['TYPE']=='codeds')
			{
				$option = explode('|',$option);
				if($option[0]!='' && $option[1]!='')
					$options[$option[0]] = $option[1];
			}
			else
				$options[$option] = $option;
	}

	$extra = 'class=cell_medium';
	return SelectInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$options,'N/A',$extra,$div);
}

function _makeAutoSelectInput($column,$name,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && ($value[$column]=='' || $value[$column]=='---') ? array('<FONT color=red>','</FONT>') : array('','');
	}

	// build the select list...
	// get the standard selects
	if($field['SELECT_OPTIONS'])
	{
		$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
		$select_options = explode("\r",$field['SELECT_OPTIONS']);
	}
	else
		$select_options = array();
	if(count($select_options))
	{
		foreach($select_options as $option)
			if($option!='')
				$options[$option] = $option;
	}
	// add the 'new' option, is also the separator
	$options['---'] = '---';

	if($field['TYPE']=='autos')
	{
		// add values found in current and previous year
		if($request=='values[ADDRESS]')
			$options_RET = DBGet(DBQuery("SELECT DISTINCT a.CUSTOM_$field[ID],upper(a.CUSTOM_$field[ID]) AS KEEY FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja,STUDENTS s,STUDENT_ENROLLMENT sse WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND s.STUDENT_ID=sja.STUDENT_ID AND sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND a.CUSTOM_$field[ID] IS NOT NULL ORDER BY KEEY"));
		elseif($request=='values[PEOPLE]')
			$options_RET = DBGet(DBQuery("SELECT DISTINCT p.CUSTOM_$field[ID],upper(p.CUSTOM_$field[ID]) AS KEEY FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp,STUDENTS s,STUDENT_ENROLLMENT sse WHERE p.PERSON_ID=sjp.PERSON_ID AND s.STUDENT_ID=sjp.STUDENT_ID AND sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND p.CUSTOM_$field[ID] IS NOT NULL ORDER BY KEEY"));
		else // students
			$options_RET = DBGet(DBQuery("SELECT DISTINCT s.CUSTOM_$field[ID],upper(s.CUSTOM_$field[ID]) AS KEEY FROM STUDENTS s,STUDENT_ENROLLMENT sse WHERE sse.STUDENT_ID=s.STUDENT_ID AND (sse.SYEAR='".UserSyear()."' OR sse.SYEAR='".(UserSyear()-1)."') AND s.CUSTOM_$field[ID] IS NOT NULL ORDER BY KEEY"));
		if(count($options_RET))
		{
			foreach($options_RET as $option)
				if($option['CUSTOM_'.$field['ID']]!='' && !$options[$option['CUSTOM_'.$field['ID']]])
					$options[$option['CUSTOM_'.$field['ID']]] = array($option['CUSTOM_'.$field['ID']],'<FONT color=blue>'.$option['CUSTOM_'.$field['ID']].'</FONT>');
		}
	}
	// make sure the current value is in the list
	if($value[$column]!='' && !$options[$value[$column]])
		$options[$value[$column]] = array($value[$column],'<FONT color='.($field['TYPE']=='autos'?'blue':'green').'>'.$value[$column].'</FONT>');

	if($value[$column]!='---' && count($options)>1)
	{    
		
		if(isset($num_of_cus_field)){
		$generated=true;
		}
		$extra = 'style="max-width:250;"';
		return SelectInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$options,'N/A',$extra,$div);
	}
	else
		return TextInput($value[$column]=='---'?array('---','<FONT color=red>---</FONT>'):''.$value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$size,$div);
}

function _makeCheckboxInput($column,$name,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
	}
	else
		$div = true;

	return CheckboxInput($value[$column],$request.'['.$column.']',$name,'',($_REQUEST['student_id']=='new'));
}

function _makeTextareaInput($column,$name,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
	}
	else
		$div = true;

	return TextAreaInput($value[$column],$request.'['.$column.']',$name,'',$div);
}

function _makeMultipleInput($column,$name,$request='students')
{	global $value,$field,$_openSIS;
	
	if((AllowEdit() || $_openSIS['allow_edit']) && !$_REQUEST['_openSIS_PDF'])
	{
		$field['SELECT_OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
		$select_options = explode("\r",$field['SELECT_OPTIONS']);
		if(count($select_options))
		{
			foreach($select_options as $option)
				$options[$option] = $option;
		}
		//print_r($options);
		if($value[$column]!='')
			$m_input.="<DIV id='div".$request."[".$column."]'><div onclick='javascript:addHTML(\"";
		$m_input.='<TABLE border=0 cellpadding=3>';
		if(count($options)>12)
		{
			$m_input.='<TR><TD colspan=2>';
			$m_input.='<small><FONT color='.Preferences('TITLES').'>'.$name.'</FONT></small>';
			if($value[$column]!='')
				$m_input.='<TABLE width=100% height=7 style=\"border:1;border-style: solid solid none solid;\"><TR><TD></TD></TR></TABLE>';
			else
				$m_input.='<TABLE width=100% height=7 style="border:1;border-style: solid solid none solid;"><TR><TD></TD></TR></TABLE>';

			$m_input.='</TD></TR>';
		}
		$m_input.='<TR>';
		$i = 0;
		foreach($options as $option)
		{
			if($i%2==0)
				$m_input.='</TR><TR>';
			if($value[$column]!=''){
			
				$m_input.='<TD><INPUT type=checkbox name='.$request.'['.$column.'][] value=\"'.str_replace('"','&quot;',$option).'\"'.(strpos($value[$column],'||'.$option.'||')!==false?' CHECKED':'').'><small>'.$option.'</small></TD>';
			}else{
				$m_input.='<TD><INPUT type=checkbox name='.$request.'['.$column.'][] value="'.str_replace('"','&quot;',$option).'"'.(strpos($value[$column],'||'.$option.'||')!==false?' CHECKED':'').'><small>'.$option.'</small></TD>';
				}
			$i++;
		}
		$m_input.='</TR><TR><TD colspan=2>';
		if($value[$column]!='')
			$m_input.='<TABLE width=100% height=7 style=\"border:1;border-style: none solid solid solid;\"><TR><TD></TD></TR></TABLE>';
		else
			$m_input.='<TABLE width=100% height=7 style="border:1;border-style: none solid solid solid;"><TR><TD></TD></TR></TABLE>';

		$m_input.='</TD></TR></TABLE>';
		if($value[$column]!='')
			$m_input.="\",\"div".$request."[".$column."]"."\",true);' >".(($value[$column]!='')?str_replace('"','&rdquo;',str_replace('||',', ',substr($value[$column],2,-2))):'-')."</div></DIV>";
	}
	else
		$m_input.=(($value[$column]!='')?str_replace('"','&rdquo;',str_replace('||',', ',substr($value[$column],2,-2))):'-<BR>');

	$m_input.='<small><FONT color='.Preferences('TITLES').'>'.$name.'</FONT></small>';
return $m_input;
}

// MEDICAL ----
function _makeType($value,$column)
{	global $THIS_RET;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';

	if($value != '---')
		if($value != '')
		return SelectInput($value,'values[STUDENT_MEDICAL]['.$THIS_RET['ID'].'][TYPE]','',array('Immunization'=>'Immunization','Physical'=>'Physical', '---'=>'---', $value=>$value));
		else
		return SelectInput($value,'values[STUDENT_MEDICAL]['.$THIS_RET['ID'].'][TYPE]','',array('Immunization'=>'Immunization','Physical'=>'Physical', '---'=>'---'));
	else
	return TextInput($value,'values[STUDENT_MEDICAL]['.$THIS_RET['ID'].'][TYPE]');
}

function _makeDate($value,$column='MEDICAL_DATE')
{	global $THIS_RET,$table;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';

	return DateInput($value,'values['.$table.']['.$THIS_RET['ID'].']['.$column.']');
}

//-------------------- Edit Start --------------------------//

function _makeDate_mod($value,$column='MEDICAL_DATE')
{	global $THIS_RET,$table;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';
	return DateInput($value,'values['.$table.']['.$THIS_RET['ID'].']['.$column.']');
}

function _makeDateInput_mod($column,$name,$request='students')
{	global $value,$field;

	if($_REQUEST['student_id']=='new' && $field['DEFAULT_SELECTION'])
	{
		$value[$column] = $field['DEFAULT_SELECTION'];
		$div = false;
		$req = $field['REQUIRED']=='Y' ? array('<FONT color=red>','</FONT>') : array('','');
	}
	else
	{
		$div = true;
		$req = $field['REQUIRED']=='Y' && $value[$column]=='' ? array('<FONT color=red>','</FONT>') : array('','');
		
		//-------- if start -------------//
		if(strlen($value[$column])==11)
		{
			$mother_date = $value[$column];
			$date = explode("-", $mother_date);
			
			$day = $date[0];
			$month = $date[1];
			$year = $date[2];
			
			if($month=='JAN')
				$month = '01';
			elseif($month=='FEB')
				$month = '02';
			elseif($month=='MAR')
				$month = '03';
			elseif($month=='APR')
				$month = '04';
			elseif($month=='MAY')
				$month = '05';
			elseif($month=='JUN')
				$month = '06';
			elseif($month=='JUL')
				$month = '07';
			elseif($month=='AUG')
				$month = '08';
			elseif($month=='SEP')
				$month = '09';
			elseif($month=='OCT')
				$month = '10';
			elseif($month=='NOV')
				$month = '11';
			elseif($month=='DEC')
				$month = '12';
				
		$final_date = $year."-".$month."-".$day;
		$value[$column] = $final_date;
		} 
		//--------- if end --------------//
	}

	return DateInput($value[$column],$request.'['.$column.']',$req[0].$name.$req[1],$div);
}

//--------------------- Edit End ---------------------------//

function _makeCommentsn($value,$column)
{	global $THIS_RET,$table;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';

	return TextAreaInput($value,'values['.$table.']['.$THIS_RET['ID'].']['.$column.']','','rows=8 cols=50');
	//return "<textarea rows='10' cols='45'  id=".'values['.$table.']['.$THIS_RET['ID'].']['.$column.']'." name=".'values['.$table.']['.$THIS_RET['ID'].']['.$column.']'.">$value</textarea>";
}
function _makeLongComments($value,$column)
{	global $THIS_RET,$table;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';

	//return TextInput($value,'values['.$table.']['.$THIS_RET['ID'].']['.$column.']');
	return "<textarea rows='1' cols='3' style='visibility:hidden;' id=".'values['.$table.']['.$THIS_RET['ID'].']['.$column.']'." name=".'values['.$table.']['.$THIS_RET['ID'].']['.$column.']'.">$value</textarea>
	<center><img id=img$name name=id=img".'values['.$table.']['.$THIS_RET['ID'].']['.$column.']'." src='assets/bbcomment.gif' alt='Add/Edit Comment' border=0 onclick=\"InsertComment('".'values['.$table.']['.$THIS_RET['ID'].']['.$column.']'."', this.id);\"></center> 	";
}

function _makeComments($value,$column)
{	global $THIS_RET,$table;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';

	return TextInput($value,'values['.$table.']['.$THIS_RET['ID'].']['.$column.']');
	
}


function _makeAlertComments($value,$column)
{	global $THIS_RET,$table;

	if(!$THIS_RET['ID'])
		$THIS_RET['ID'] = 'new';

	return TextInput($value,'values['.$table.']['.$THIS_RET['ID'].']['.$column.']','','size=50');

}
// ENROLLMENT
function _makeStartInputDate($value,$column)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	elseif($_REQUEST['student_id']=='new')
	{
		$id = 'new';
		$default = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS START_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		$default = $default[1]['START_DATE'];
		if(!$default || DBDate()>$default)
			$default = DBDate();
		$value = $default;
	}
	else
	{
		$add = '<TD>'.button('add').'</TD>';
		$id = 'new';
	}

//	if(!$add_codes)
//	{
//		
//	}

	if($_REQUEST['student_id']=='new')
		$div = false;
	else
		$div = true;

	return '<TABLE class=LO_field><TR>'.$add.'<TD>'.DateInput($value,'values[STUDENT_ENROLLMENT]['.$id.']['.$column.']','',$div,true).'</TD></TR></TABLE>';
}

function _makeStartInputCode($value,$column)
{
        global $THIS_RET;
        if($THIS_RET['ID'])
                $id = $THIS_RET['ID'];
        else
                $id='new';

        $add_codes=array();
        $options_RET = DBGet(DBQuery("SELECT ID,TITLE AS TITLE FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR='".($THIS_RET['SYEAR']!=''?$THIS_RET['SYEAR'] : UserSyear())."' AND (TYPE='Add' OR TYPE='Roll' OR TYPE='TrnE')"));

		if($options_RET)
		{
			foreach($options_RET as $option)
				$add_codes[$option['ID']] = $option['TITLE'];
		}
        return '<TABLE class=LO_field><TR><TD>'.SelectInput($THIS_RET['ENROLLMENT_CODE'],'values[STUDENT_ENROLLMENT]['.$id.'][ENROLLMENT_CODE]','',$add_codes,'N/A','style="max-width:150;"').'</TD></TR></TABLE>';
}

function _makeEndInputDate($value,$column)
{	global $THIS_RET;
                  $drop_codes=array();

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

// STUDENT_ENROLLMENT select create here
	return '<TABLE class=LO_field><TR><TD>'.DateInput($value,'values[STUDENT_ENROLLMENT]['.$id.']['.$column.']').'</TD></TR></TABLE>';
}

function _makeEndInputCode($value,$column)
{
        global $THIS_RET;
                  $drop_codes=array();

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

//	if(!$drop_codes)
//	{
		$options_RET = DBGet(DBQuery("SELECT ID,TITLE AS TITLE,TYPE FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR='".($THIS_RET['SYEAR']!=''?$THIS_RET['SYEAR'] : UserSyear())."'  AND (TYPE='Drop' OR TYPE='Roll' OR TYPE='TrnD')"));

		if($options_RET)
		{
			foreach($options_RET as $option)
				$drop_codes[$option['ID']] = $option['TITLE'];
		}
//	}
	$type_RET=DBGet(DBQuery("SELECT ID, TYPE FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR='".($THIS_RET['SYEAR']!=''?$THIS_RET['SYEAR'] : UserSyear())."' AND TYPE='TrnD'"));
                  if(count($type_RET)>0)
                      $type_id=$type_RET[1]['ID'];
// STUDENT_ENROLLMENT select create here
	return '<TABLE class=LO_field><TR><TD>'.SelectInput_for_EndInput($THIS_RET['DROP_CODE'],'values[STUDENT_ENROLLMENT]['.$id.'][DROP_CODE]','',$drop_codes,$type_id,'N/A','style="max-width:150;"').'</TD></TR></TABLE>';
}

function _makeSchoolInput($value,$column)
{	global $THIS_RET,$schools;
	$schools = array();
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if(!$schools)
		$schools = DBGet(DBQuery("SELECT ID,TITLE FROM SCHOOLS"),array(),array('ID'));

	foreach($schools as $sid=>$school)
		$options[$sid] = $school[1]['TITLE'];
		// mab - allow school to be editted if illegal value
	if($THIS_RET['SCHOOL_ID']){
				$name=DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID=".$THIS_RET['SCHOOL_ID']));
				return $name[1]['TITLE'];
	}elseif($_REQUEST['student_id']!='new')
	  {
		if($id!='new')
		  {
			if($schools[$value])
			{
				$name=DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID=".UserSchool()));
				return $name[1]['TITLE'];
			}
			else
				return SelectInput($value,'values[STUDENT_ENROLLMENT]['.$id.'][SCHOOL_ID]','',$options);
		  }		
		else
			return SelectInput(UserSchool(),'values[STUDENT_ENROLLMENT]['.$id.'][SCHOOL_ID]','',$options,false,'',false);
	 }		
	else
		return $schools[UserSchool()][1]['TITLE'];
}
?>