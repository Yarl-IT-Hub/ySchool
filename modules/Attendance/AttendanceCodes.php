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
if(!isset($_REQUEST['table']))
    $_REQUEST['table'] = 0;

if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax'])) {
    foreach($_REQUEST['values'] as $id=>$columns) 
   {
        if(!(isset($columns['TITLE']) && trim($columns['TITLE'])==''))
        {
                if($columns['DEFAULT_CODE'] == 'Y')
                    DBQuery("UPDATE ATTENDANCE_CODES SET DEFAULT_CODE=NULL WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='".$_REQUEST['table']."'");

                if($id!='new')
                {
                        $sql = "UPDATE ATTENDANCE_CODES SET ";

                        foreach($columns as $column=>$value) 
                        {
                            if($column=='TITLE'||$column=='SHORT_NAME')
                            { $value=clean_param($value,PARAM_SPCL);
                            }
                            $sql .= $column."='".str_replace("\'","''",trim($value))."',";
                        }
                        $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                        DBQuery($sql);
            }
            else {
                $sql = "INSERT INTO ATTENDANCE_CODES ";

                $fields = 'SCHOOL_ID,SYEAR,TABLE_NAME,';
                $values = "'".UserSchool()."','".UserSyear()."','".$_REQUEST['table']."',";


                $go = 0;

                foreach($columns as $column=>$value) {
                        if(trim($value))
                        {
                            $fields .= $column.',';
                            if($column=='TITLE'||$column=='SHORT_NAME')
                            {
                                $value=clean_param($value,PARAM_SPCL);
                            }
                            $values .= "'".str_replace("\'","''",trim($value))."',";
                            $go = true;
                        }
                }
            
                $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
        

                if($go)
                    DBQuery($sql);
            }
    }   // Title validation ends to show error message add else after this line
   }
}

DrawBC("Attendance > ".ProgramTitle());

if($_REQUEST['new_category_title']) {
/*	$id = DBGet(DBQuery("SELECT ".db_seq_nextval('ATTENDANCE_CODE_CATEGORIES_SEQ').' AS ID'.FROM_DUAL));
	$id = $id[1]['ID']; */



   /* DBQuery("INSERT INTO ATTENDANCE_CODE_CATEGORIES (SYEAR,SCHOOL_ID,
    TITLE) values('".UserSyear()."','".UserSchool()."','".$_REQUEST['new_category_title']."')");*/
	$new_cat=optional_param('new_category_title','',PARAM_SPCL);
    if($new_cat)
    {
	 DBQuery("INSERT INTO ATTENDANCE_CODE_CATEGORIES (SYEAR,SCHOOL_ID,TITLE) values('".UserSyear()."','".UserSchool()."','".$new_cat."')");
    
    // possible modification start
    $id = DBGet(DBQuery("SELECT max(ID) as ID from ATTENDANCE_CODE_CATEGORIES"));
    $id = $id[1]['ID'];
    $_REQUEST['table'] = $id;
    }
   else
     {
         echo "<font color='red'><b>Unable to save data, because Special Charecters do not allow in Category Title</b></font>";
    }
    // possible modification end
}

//if($_REQUEST['modfunc']=='remove')
if(optional_param('modfunc','',PARAM_ALPHA)=='remove') {
if($_REQUEST['id']){
//$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM ATTENDANCE_PERIOD WHERE ATTENDANCE_CODE='$_REQUEST[id]'"));


$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM ATTENDANCE_PERIOD WHERE ATTENDANCE_CODE='".optional_param('id','',PARAM_INT)."'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	}else{
	$has_assigned=0;
		}
	if($has_assigned>0){
	UnableDeletePrompt('Cannot delete because attendance codes are associated.');
	}else{
    if($_REQUEST['id']) {
        if(DeletePromptCommon('attendance code')) {
            //DBQuery("DELETE FROM ATTENDANCE_CODES WHERE ID='$_REQUEST[id]'");
			
			 DBQuery("DELETE FROM ATTENDANCE_CODES WHERE ID='".optional_param('id','',PARAM_INT)."'");
            unset($_REQUEST['modfunc']);
        }
    }
    elseif($_REQUEST['table']) {
        if(DeletePromptCommon('category')) {
            DBQuery("DELETE FROM ATTENDANCE_CODE_CATEGORIES WHERE ID='$_REQUEST[table]'");
			
            unset($_REQUEST['modfunc']);
            $_REQUEST['table'] = '0';
        }
    }
}
}

if($_REQUEST['modfunc']!='remove')
 {
    if($_REQUEST['table']!=='new') {
       // $sql = "SELECT ID,TITLE,SHORT_NAME,TYPE,DEFAULT_CODE,STATE_CODE,SORT_ORDER FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='".$_REQUEST['table']."' ORDER BY SORT_ORDER,TITLE";
		
		//$tabl=optional_param('table','',PARAM_ALPHANUM);
		  $sql = "SELECT ID,TITLE,SHORT_NAME,TYPE,DEFAULT_CODE,STATE_CODE,SORT_ORDER,TABLE_NAME FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='".$_REQUEST['table']."' ORDER BY SORT_ORDER,TITLE";
		  
		  
        $QI = DBQuery($sql);
        $attendance_codes_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInput','TYPE'=>'_makeSelectInput','STATE_CODE'=>'_makeSelectInput','DEFAULT_CODE'=>'_makeCheckBoxInput'));
    }

    $columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','SORT_ORDER'=>'Sort Order','TYPE'=>'Type','DEFAULT_CODE'=>'Default for Teacher','STATE_CODE'=>'State Code');
	/*
    if($_REQUEST['table']!='0')
        unset($columns['STATE_CODE']);
	*/	
    $link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>_makeTextInput('','SORT_ORDER'),'TYPE'=>_makeSelectInput('','TYPE'),'DEFAULT_CODE'=>_makeCheckBoxInput('','DEFAULT_CODE'),'STATE_CODE'=>_makeSelectInput('','STATE_CODE'));
    $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
    $link['remove']['variables'] = array('id'=>'ID','table'=>'TABLE_NAME');

    echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&table=$_REQUEST[table] method=POST>";
    #DrawHeader('',SubmitButton('Save'));


    $tabs = array(array('title'=>'Attendance','link'=>"Modules.php?modname=$_REQUEST[modname]&table=0"));
    $categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ATTENDANCE_CODE_CATEGORIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
    foreach($categories_RET as $category)
        $tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&table=".$category['ID']);


    if($_REQUEST['table']==='new')
        $tabs[] = array('title'=>button('white_add'),'link'=>"Modules.php?modname=$_REQUEST[modname]&table=new"); 
    else
        $tabs[] = array('title'=>button('add'),'link'=>"Modules.php?modname=$_REQUEST[modname]&table=new");       

		

    if($_REQUEST['table']!=='new') {
        if(count($attendance_codes_RET)==0) {
            $_openSIS['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]";
            echo PopTable('header',$tabs);
            ListOutput($attendance_codes_RET,$columns,'','',$link,array(),array('download'=>false,'search'=>false));
            if($_REQUEST['table']!=0)
                echo '<BR><CENTER>'.button('remove','Delete this category',"Modules.php?modname=$_REQUEST[modname]&modfunc=remove&table=$_REQUEST[table]");
            echo '<BR><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_attendance_codes();"').'</CENTER>';
            echo PopTable('footer');
        }
        else {
			
			echo '<style type="text/css">#div_margin { margin-top:-20px; _margin-top:-1px; }</style>';
            echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]").'</CENTER>';
			echo '<div id="div_margin">';
            PopTable_wo_header ('header');
            ListOutput($attendance_codes_RET,$columns,'','',$link,array(),array('download'=>false,'search'=>false));
            echo '<BR><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_attendance_codes();"').'</CENTER>';
            PopTable ('footer');
			echo '</div>';
        }
    }
    else {
        $_openSIS['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]";
        echo PopTable('header',$tabs);
        echo '<CENTER>New Category Title <INPUT type=text name=new_category_title></CENTER>';
        echo '<BR><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_attendance_category();"').'</CENTER>';
        echo PopTable('footer');
    }
    echo '</FORM>';
}

function _makeTextInput($value,$name) 
{
    global $THIS_RET;
    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';
    if($name=='TITLE' && $id=='new')
        $extra='id=title';
    if($name=='SHORT_NAME')
        $extra = 'size=5 maxlength=5 class=cell_floating';
    if($name=='SORT_ORDER')
    {
        if($id == 'new' || $THIS_RET['SORT_ORDER']=='')
            $extra = 'size=5 maxlength=5 class=cell_floating onkeydown="return numberOnly(event);"';
        else
            $extra = 'size=5 maxlength=5 class=cell_floating onkeydown=\"return numberOnly(event);\"';
    }

    return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeSelectInput($value,$name) 
{
    global $THIS_RET;

    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';

    if($name=='TYPE'){
        $allow_na='N/A';
        $options = array('teacher'=>'Teacher & Office','official'=>'Office Only');
    }
    elseif($name=='STATE_CODE'){
        if($id=='new')
            $allow_na='N/A';
        else
            $allow_na=false;
        $options = array('P'=>'Present','A'=>'Absent','H'=>'Half');
    }

    return SelectInput($value,'values['.$id.']['.$name.']','',$options,$allow_na);
}

function _makeCheckBoxInput($value,$name) {	global $THIS_RET;

    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else {
        $id = 'new';
        $new = true;
    }

    return CheckBoxInput($value,'values['.$id.']['.$name.']','','',$new);
}

/////////////////////////////////////////////////// Validation Start ////////////////////////////////////////////////////////



/*if($_REQUEST['modfunc']!='remove') {
    echo '
		<script language="JavaScript" type="text/javascript">
	
		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=100", "Max length for Title is 100");
		
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=100", "Max length for Short Name is 50");
			
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order Code allows only numeric value");
		
		
			
		</script>
		';
}

if($_REQUEST['table']=='new') {
    echo '
		<script language="JavaScript" type="text/javascript">
	
		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("new_category_title","req","Please enter the New Category Title");
		frmvalidator.addValidation("new_category_title","maxlen=10", "Max length for Title is 10");
			
		</script>
		';
}*/

///////////////////////////////////////////////// Validation Enb //////////////////////////////////////////////////////////////	


?>