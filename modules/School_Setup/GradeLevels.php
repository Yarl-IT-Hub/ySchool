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
if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
{
    foreach($_REQUEST['values'] as $id=>$columns)
    {
            if($id!='new')
            {
                $sql = "UPDATE SCHOOL_GRADELEVELS SET ";
                foreach($columns as $column=>$value)
                {
                    $value=trim(paramlib_validation($column,$value));
                    if($column==NEXT_GRADE_ID && (str_replace("\'","''",$value)=='' || str_replace("\'","''",$value)==0 ))
                        $sql .= $column."=NULL,";
                    else
                        $sql .= $column."='".str_replace("\'","''",$value)."',";
                }
                $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                    DBQuery($sql);
            }
            else
            {
                if(clean_param(trim($_REQUEST['values']['new']['TITLE']),PARAM_NOTAGS)!='')
                {
                    $sql = "INSERT INTO SCHOOL_GRADELEVELS ";
                    $fields = 'SCHOOL_ID,';
                    $values = "'".UserSchool()."',";

                    $go = 0;
                    foreach($columns as $column=>$value)
                    {
                            if(trim($value))
                            {
                                    $value=trim(paramlib_validation($column,$value));
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
}

DrawBC("School Setup > ".ProgramTitle());
#echo "Modules.php?modname=$_REQUEST[modname]";
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
{
    $grd_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[id]);
    $has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM STUDENT_ENROLLMENT WHERE GRADE_ID='$grd_id'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
	UnableDeletePrompt('Cannot delete because grade levels are associated.');
	}else{
	if(DeletePrompt_GradeLevel('grade level'))
	{
		DBQuery("DELETE FROM SCHOOL_GRADELEVELS WHERE ID='$grd_id'");
		unset($_REQUEST['modfunc']);
	}
	}
}

if($_REQUEST['modfunc']!='remove')
{
	$sql = "SELECT ID,TITLE,SHORT_NAME,SORT_ORDER,NEXT_GRADE_ID FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER";
	$QI = DBQuery($sql);
	$grades_RET = DBGet($QI,array('TITLE'=>'makeTextInput','SHORT_NAME'=>'makeTextInput','SORT_ORDER'=>'makeTextInput','NEXT_GRADE_ID'=>'makeGradeInput'));
	
	$columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','SORT_ORDER'=>'Sort Order','NEXT_GRADE_ID'=>'Next Grade');
	$link['add']['html'] = array('TITLE'=>makeTextInput('','TITLE'),'SHORT_NAME'=>makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>makeTextInputMod2('','SORT_ORDER'),'NEXT_GRADE_ID'=>makeGradeInput('','NEXT_GRADE_ID'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'ID');
	
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	#DrawHeader('','<INPUT type=submit value=Save>');
	ListOutput($grades_RET,$columns,'Grade Level','Grade Levels',$link, true, array('search'=>false));
	echo '<br><CENTER><INPUT class="btn_medium" type=submit value=Save onclick="formcheck_school_setup_grade_levels();"></CENTER>';
	echo '</FORM>';
}

function makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=5 class=cell_floating';
		else 
	$extra = 'class=cell_floating ';
	
	if($name=='SORT_ORDER')
	{
            if($id=='new')
                $extra = 'size=5 maxlength=5 class=cell_floating onKeyDown="return numberOnly(event);"';
            else 
                $extra = 'size=5 maxlength=5 class=cell_floating onKeyDown=\"return numberOnly(event);\"';
		
		$comment = '<!-- '.$value.' -->';
        }

	return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function makeTextInputMod1($value,$name)
{	global $THIS_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=2 class=cell_floating';
		else 
	$extra = 'class=cell_floating ';
	if($name=='SORT_ORDER')
		$comment = '<!-- '.$value.' -->';

	return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function makeTextInputMod2($value,$name)
{	global $THIS_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
            $extra = 'size=5 maxlength=5 class=cell_floating ';
		else 
	$extra = 'class=cell_floating ';
	if($name=='SORT_ORDER')
        {
            if($id=='new')
                $extra = 'size=5 maxlength=5 class=cell_floating onKeyDown="return numberOnly(event);"';
            else 
                $extra = 'size=5 maxlength=5 class=cell_floating onKeyDown=\"return numberOnly(event);\"';
		
		$comment = '<!-- '.$value.' -->';
        }
	return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}


function makeGradeInput($value,$name)
{	global $THIS_RET,$grades;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
		
	if(!$grades)
	{
		$grades_RET = DBGet(DBQuery("SELECT ID,TITLE FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		if(count($grades_RET))
		{
			foreach($grades_RET as $grade)
				$grades[$grade['ID']] = $grade['TITLE'];
		}
	}
	
	return SelectInput($value,'values['.$id.']['.$name.']','',$grades,'N/A');
}



?>