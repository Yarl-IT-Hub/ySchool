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
if($_REQUEST['month_values'] && ($_POST['month_values'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['month_values'] as $id=>$columns)
	{
		foreach($columns as $column=>$value)
		{
			$_REQUEST['values'][$id][$column] = $_REQUEST['day_values'][$id][$column].'-'.$value.'-'.$_REQUEST['year_values'][$id][$column];
			if($_REQUEST['values'][$id][$column]=='--')
				$_REQUEST['values'][$id][$column] = '';
		}
	}
	$_POST['values'] = $_REQUEST['values'];
}

if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']))
{
       
	foreach($_REQUEST['values'] as $id=>$columns)
	{	
		if($id!='new')
		{
                   if($_REQUEST['values'][$id]['START_DATE'])
                   {
                       $check=$_REQUEST['values'][$id]['START_DATE'];
                   }
                     else {
                         $check_date=DBGet(DBQuery("SELECT * FROM ELIGIBILITY_ACTIVITIES WHERE ID='".$id."'"));
                         $check_date=$check_date[1];
                         $check=$check_date['START_DATE'];
                    }
                    if($_REQUEST['values'][$id]['END_DATE'])
                   {
                       $check1=$_REQUEST['values'][$id]['END_DATE'];
                   }
                     else {
                         $check_date1=DBGet(DBQuery("SELECT * FROM ELIGIBILITY_ACTIVITIES WHERE ID='".$id."'"));
                         $check_date1=$check_date1[1];
                         $check1=$check_date['END_DATE'];
                    }
                   $days=floor((strtotime($check1,0)-strtotime($check,0))/86400); 
                    if($days>0)				
                        {
			$sql = "UPDATE ELIGIBILITY_ACTIVITIES SET ";
							
			foreach($columns as $column=>$value)
			{
                              if($column=='TITLE')
                                {
                                    $value=str_replace("'","\'",clean_param($value,PARAM_SPCL));
                                }
                                else
                                {
                                    $value=clean_param($value,PARAM_SPCL);
                                }
				$sql .= $column."='".str_replace("\'","''",$value)."',";
			}
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			DBQuery($sql);
		}
                     else {
                        echo "<font color=red>End date should be greater than begin date..</font>";
                    }
		}
		else
		{
			$sql = "INSERT INTO ELIGIBILITY_ACTIVITIES ";

			$fields = 'SCHOOL_ID,SYEAR,';
			$values = "'".UserSchool()."','".UserSyear()."',";

			$go = 0;
			foreach($columns as $column=>$value)
			{ 
				if($column=='TITLE')
					{ $value=str_replace("'","\'",clean_param($value,PARAM_SPCL));
						
					}	
				if(trim($value))
				{       
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			
			if($go)
				DBQuery($sql);
		}
	}
}

DrawBC("Eligibility > ".ProgramTitle());

//if($_REQUEST['modfunc']=='remove')
if(optional_param('modfunc','',PARAM_NOTAGS)=='remove')
{	
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM STUDENT_ELIGIBILITY_ACTIVITIES WHERE ACTIVITY_ID='$_REQUEST[id]'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
	UnableDeletePrompt('Cannot delete because eligibility activities are associated.');
	}else{
	if(DeletePrompt('activity'))
	{
		DBQuery("DELETE FROM ELIGIBILITY_ACTIVITIES WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
	}
}

if($_REQUEST['modfunc']!='remove')
{
	$sql = "SELECT ID,TITLE,START_DATE,END_DATE FROM ELIGIBILITY_ACTIVITIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY TITLE";
	$QI = DBQuery($sql);
	$activities_RET = DBGet($QI,array('TITLE'=>'makeTextInput','START_DATE'=>'makeDateInput','END_DATE'=>'makeDateInput'));
	
	$columns = array('TITLE'=>'Title','START_DATE'=>'Begins','END_DATE'=>'Ends');
	$link['add']['html'] = array('TITLE'=>makeTextInput('','TITLE'),'START_DATE'=>makeDateInput('','START_DATE'),'END_DATE'=>makeDateInput('','END_DATE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'ID');
	
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=".optional_param('modname','',PARAM_NOTAGS)."&modfunc=update method=POST>";
	#DrawHeader('',SubmitButton('Save'));
	ListOutput($activities_RET,$columns,'Activity','Activities',$link);
	echo '<br /><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_eligibility_activies();"').'</CENTER>';
	echo '</FORM>';
}

function makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	return TextInput($value,'values['.$id.']['.$name.']','','class=cell_floating');
}

function makeDateInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	return DateInput($value,'values['.$id.']['.$name.']');
}



?>