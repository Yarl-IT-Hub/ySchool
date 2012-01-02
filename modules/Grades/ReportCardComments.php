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
include 'modules/Grades/DeletePromptX.fnc.php';
//echo '<pre>'; var_dump($_REQUEST); echo '</pre>';
DrawBC("Gradebook > ".ProgramTitle());

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
{
	if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
                  {
                    if($_REQUEST['tab_id']!='')
                    {
                        foreach($_REQUEST['values'] as $id=>$columns)
                        {
                            if($id!='new')
                            {
                                $sql = "UPDATE REPORT_CARD_COMMENTS SET ";

                                foreach($columns as $column=>$value){
                                $value= paramlib_validation($column,$value);
                                $sql .= $column."='".str_replace("\'","''",$value)."',";
                                }
                                $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                                DBQuery($sql);
                            }
                            else
                            {
                                if(clean_param(trim($_REQUEST['values']['new']['TITLE']),PARAM_NOTAGS)!='')
                                {
                                    $sql = 'INSERT INTO REPORT_CARD_COMMENTS ';
                                    $fields = 'SCHOOL_ID,SYEAR,COURSE_ID,';
                                    $values = '\''.UserSchool().'\',\''.UserSyear().'\','.($_REQUEST['tab_id']!='new'?"'$_REQUEST[tab_id]'":'NULL').',';

                                    $go = false;
                                    foreach($columns as $column=>$value)
                                    if(trim($value))
                                    {
                                        $value= paramlib_validation($column,$value);
                                        $fields .= $column.',';

                                        $values .= ' " '.str_replace("\'","'",$value).' ",';
                                        //   $values .= '\''.str_replace("\'","''",$value).'\',';

                                        $go = true;
                                    }
                                    $sql .= '(' . substr($fields,0,-1) . ') values( ' . substr($values,0,-1) . ' )';

                                    if($go)
                                        DBQuery($sql);
                                }
                            }
                        }
                    }
                  }
	unset($_REQUEST['modfunc']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
{
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM STUDENT_REPORT_CARD_COMMENTS WHERE REPORT_CARD_COMMENT_ID='$_REQUEST[id]'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	
	if($has_assigned>0){
	UnableDeletePromptX('Cannot delete because report card comments are associated.');
	}else{
	if($_REQUEST['tab_id']!='new')
	{
		if(DeletePromptX('Report Card Comment'))
		{
			DBQuery("DELETE FROM REPORT_CARD_COMMENTS WHERE ID='$_REQUEST[id]'");
		}
	}
	else
		if(DeletePromptX('Report Card Comment'))
		{
			DBQuery("DELETE FROM REPORT_CARD_COMMENTS WHERE ID='$_REQUEST[id]'");
		}
		}
}

if(!$_REQUEST['modfunc'])
{
	if(User('PROFILE')=='admin')
	{
		$courses_RET = DBGet(DBQuery("SELECT TITLE,COURSE_ID FROM COURSES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID IN (SELECT DISTINCT COURSE_ID FROM COURSE_PERIODS WHERE GRADE_SCALE_ID IS NOT NULL) ORDER BY TITLE"));
		if(!$_REQUEST['course_id'])
			$_REQUEST['course_id'] = $courses_RET[1]['COURSE_ID'];

		$course_select = '<SELECT name=course_id onchange="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&course_id=\'+this.options[selectedIndex].value">';
		foreach($courses_RET as $course)
			$course_select .= '<OPTION value='.$course['COURSE_ID'].($_REQUEST['course_id']==$course['COURSE_ID']?' SELECTED':'').'>'.$course['TITLE'].'</OPTION>';
		$course_select .= '</SELECT>';
	}
	else
	{
		$course_period_RET = DBGet(DBQuery('SELECT GRADE_SCALE_ID,DOES_BREAKOFF,TEACHER_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID=\''.UserCoursePeriod().'\''));
		if(!$course_period_RET[1]['GRADE_SCALE_ID'])
			ErrorMessage(array('This course is not graded.'),'fatal');
		$courses_RET = DBGet(DBQuery("SELECT TITLE,COURSE_ID FROM COURSES WHERE COURSE_ID=(SELECT COURSE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".UserCoursePeriod()."')"));
		//$course_select = $courses_RET[1]['TITLE'];
		$_REQUEST['course_id'] = $courses_RET[1]['COURSE_ID'];
	}

	if($_REQUEST['tab_id']!='0' && $_REQUEST['tab_id']!='new')
		$_REQUEST['tab_id'] = $_REQUEST['course_id'];

	$course_RET = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='$_REQUEST[course_id]'"));
	$tabs = array(1=>array('title'=>$course_RET[1]['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&course_id=$_REQUEST[course_id]&tab_id=$_REQUEST[course_id]"),
		2=>array('title'=>'All Courses','link'=>"Modules.php?modname=$_REQUEST[modname]&course_id=$_REQUEST[course_id]&tab_id=0"),
		3=>array('title'=>'General','link'=>"Modules.php?modname=$_REQUEST[modname]&course_id=$_REQUEST[course_id]&tab_id=new"));

	if($_REQUEST['tab_id']!='new')
	{
		if($_REQUEST['tab_id'])
			$sql = 'SELECT * FROM REPORT_CARD_COMMENTS WHERE COURSE_ID=\''.$_REQUEST['tab_id'].'\' ORDER BY SORT_ORDER';
		else
			// need to be more specific since course_period_id=0 is not unique
		       $sql = 'SELECT * FROM REPORT_CARD_COMMENTS WHERE COURSE_ID=\''.$_REQUEST['tab_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER';
		$functions = array('TITLE'=>'makeCommentsInput','SORT_ORDER'=>'makeCommentsInput');

		$LO_columns = array('TITLE'=>'Comment','SORT_ORDER'=>'Sort Order');

		$link['add']['html'] = array('TITLE'=>makeCommentsInput('','TITLE'),'SORT_ORDER'=>makeCommentsInput('','SORT_ORDER'));
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&table=REPORT_CARD_GRADES";
		$link['remove']['variables'] = array('id'=>'ID');
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&tab_id=$_REQUEST[tab_id]";
		$link['remove']['variables'] = array('id'=>'ID');
		$link['add']['html']['remove'] = button('add');
	}
	else
	{
		$sql = 'SELECT * FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND COURSE_ID IS NULL ORDER BY SORT_ORDER';
		$functions = array('SORT_ORDER'=>'makeTextInput','TITLE'=>'makeTextInput');
		$LO_columns = array('SORT_ORDER'=>'ID','TITLE'=>'Comment');

		$link['add']['html'] = array('SORT_ORDER'=>makeTextInput('','SORT_ORDER'),'TITLE'=>makeTextInput('','TITLE'));
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&tab_id=new";
		$link['remove']['variables'] = array('id'=>'ID');
		$link['add']['html']['remove'] = button('add');
	}
	$LO_ret = DBGet(DBQuery($sql),$functions);

	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&course_id=$_REQUEST[course_id]&tab_id=$_REQUEST[tab_id] method=POST>";
	DrawHeaderHome($course_select,SubmitButton('Save','','class=btn_medium onclick="formcheck_grade_comment();"')); 
	#echo '<BR>';
	
	echo '<style type="text/css">#div_margin { margin-top:-20px; _margin-top:-1px; }</style>';
	
	echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&course_id=$_REQUEST[course_id]&tab_id=$_REQUEST[tab_id]").'</CENTER>';
	echo '<div id="div_margin">';
	PopTable_wo_header ('header');
	echo "<table width=300px height=120px><tr><td>"; //hack for an empty poptable 
	ListOutputMod($LO_ret,$LO_columns,'','',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
	echo "</td></tr></table>";
	PopTable ('footer');
	echo '</div>';
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_grade_comment();"').'</CENTER>';
	echo '</FORM>';
}

function makeGradesInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name=='COMMENT')
		$extra = 'size=15 maxlength=100 class=cell_floating';
	elseif($name=='GPA_VALUE')
		$extra = 'size=5 maxlength=5 class=cell_floating';
	else
		$extra = 'size=5 maxlength=3 class=cell_floating';

	return TextInput($value,"values[$id][$name]",'',$extra);
}

function makeTextInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	return TextInput($value,"values[$id][$name]",'',$extra);
}

function makeCommentsInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name=='SORT_ORDER')
        {
            if($id == 'new')
		$extra = 'size=5 maxlength=5 class=cell_floating onkeydown="return numberOnly(event);"';
            else
    		$extra = 'size=5 maxlength=5 class=cell_floating onkeydown=\"return numberOnly(event);\"';
        }
	return TextInput($value,"values[$id][$name]",'',$extra);
}



?>