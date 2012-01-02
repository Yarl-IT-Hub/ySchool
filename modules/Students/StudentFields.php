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
DrawBC("Students > ".ProgramTitle());
$_openSIS['allow_edit'] = true;
$not_default=false;
if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']))
{
	$table = $_REQUEST['table'];
	foreach($_REQUEST['tables'] as $id=>$columns)
	{
		if($id!='new')
		{
                                                        if($columns['CATEGORY_ID'] && $columns['CATEGORY_ID']!=$_REQUEST['category_id'])
                                                        $_REQUEST['category_id'] = $columns['CATEGORY_ID'];

                                                        $sql = "UPDATE $table SET ";

                                                        foreach($columns as $column=>$value){
                                                            $value= paramlib_validation($column,$value);
                                                            $sql .= $column."='".str_replace("\'","''",trim($value))."',";
                                                        }
                                                        $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                                                        $go = true;
                                                        if($table=='CUSTOM_FIELDS')
                                                            $custom_field_id=$id;

		}
		else
		{
			$sql = "INSERT INTO $table ";

			if($table=='CUSTOM_FIELDS')
			{
				if($columns['CATEGORY_ID'])
				{
					$_REQUEST['category_id'] = $columns['CATEGORY_ID'];
					unset($columns['CATEGORY_ID']);
				}
				//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('CUSTOM_SEQ').' AS ID '.FROM_DUAL));
                                                                        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'CUSTOM_FIELDS'"));
                                                                        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				$id = $id[1]['ID'];
				$fields = "CATEGORY_ID,SYSTEM_FIELD,";
				$values ="'".$_REQUEST['category_id']."','N',";
				$_REQUEST['id'] = $id;

				switch($columns['TYPE'])
				{
                                                                                            case 'radio':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(1) ";
                                                                                            break;

                                                                                            case 'text':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(255)";
                                                                                            break;

                                                                                            case 'select':
                                                                                            case 'autos':
                                                                                            case 'edits':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(100)";
                                                                                            break;

                                                                                            case 'codeds':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(15)";
                                                                                            break;

                                                                                            case 'multiple':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id VARCHAR(255)";
                                                                                            break;

                                                                                            case 'numeric':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id NUMERIC(20,2)";
                                                                                            if(!is_numeric($columns['DEFAULT_SELECTION'])){
                                                                                                $not_default=true;
                                                                                                $columns['DEFAULT_SELECTION']='';
                                                                                            }
                                                                                            break;

                                                                                            case 'date':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id  DATE";
                                                                                            if(preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $columns['DEFAULT_SELECTION']) === 0){
                                                                                                $not_default=true;
                                                                                                $columns['DEFAULT_SELECTION']='';
                                                                                            }
                                                                                            break;

                                                                                            case 'textarea':
                                                                                            $Sql_add_column="ALTER TABLE STUDENTS ADD CUSTOM_$id LONGTEXT";
                                                                                            $not_default=true;
                                                                                            break;
				}
                                                                        if($columns['REQUIRED']){
                                                                            $Sql_add_column.=" NOT NULL ";
				}else{
                                                                            $Sql_add_column.=" NULL ";
                                                                        }
				if($columns['DEFAULT_SELECTION'] && $not_default==false){
                                                                            $Sql_add_column.=" DEFAULT  '".$columns['DEFAULT_SELECTION']."' ";
				}
				DBQuery($Sql_add_column);
                                                                        if($columns['TYPE']!='textarea')
				DBQuery("CREATE INDEX CUSTOM_IND$id ON STUDENTS (CUSTOM_$id)");
				unset($table);
			}
			elseif($table=='STUDENT_FIELD_CATEGORIES')
			{
				// $id = DBGet(DBQuery("SELECT ".db_seq_nextval('STUDENT_FIELD_CATEGORIES_SEQ').' AS ID '.FROM_DUAL));
                                                                        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'STUDENT_FIELD_CATEGORIES'"));
                                                                        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				$id = $id[1]['ID'];
				$fields = "";
				$values = "";
				$_REQUEST['category_id'] = $id;
				// add to profile or permissions of user creating it
				if(User('PROFILE_ID'))
					DBQuery("INSERT INTO PROFILE_EXCEPTIONS (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('".User('PROFILE_ID')."','Students/Student.php&category_id=$id','Y','Y')");
				else
					DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) values('".User('STAFF_ID')."','Students/Student.php&category_id=$id','Y','Y')");
			}

			$go = false;

			foreach($columns as $column=>$value)
			{
				if(trim($value))
				{
                                                                            $value= paramlib_validation($column,$value);
                                                                            $fields .= $column.',';
                                                                            $values .= "'".str_replace("\'","''",$value)."',";
                                                                            $go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
		}

		if($go)
                                        DBQuery($sql);
                if($custom_field_id)
                {
                    $custom_update=DBGet(DBQuery("SELECT TYPE,REQUIRED,DEFAULT_SELECTION,HIDE FROM CUSTOM_FIELDS WHERE ID=$custom_field_id"));
                    $custom_update=$custom_update[1];
                    switch($custom_update['TYPE'])
                    {
                            case 'radio':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id VARCHAR(1) ";
                            break;

                            case 'text':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id VARCHAR(255)";
                            break;

                            case 'select':
                            case 'autos':
                            case 'edits':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id VARCHAR(100)";
                            break;
                            
                            case 'codeds':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id VARCHAR(15)";
                            break;

                            case 'multiple':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id VARCHAR(255)";
                            break;

                            case 'numeric':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id NUMERIC(20,2)";
                            if(!is_numeric($columns['DEFAULT_SELECTION'])){
                                $not_default=true;
                            }
                            break;

                            case 'date':
                            $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id  DATE";
                            if(preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $columns['DEFAULT_SELECTION']) === 0){
                                $not_default=true;
                            }
                            break;

                            case 'textarea':
                                    $Sql_modify_column="ALTER TABLE STUDENTS MODIFY CUSTOM_$id LONGTEXT";
                                    $not_default=true;
                            break;
                }
                    if($custom_update['REQUIRED']){
                        $Sql_modify_column.=" NOT NULL";
                    }else{
                        $Sql_modify_column.=" NULL";
                    }
                    if($custom_update['DEFAULT_SELECTION'] && $not_default==false){
                        $Sql_modify_column.=" DEFAULT  '".$custom_update['DEFAULT_SELECTION']."' ";
                    }
                        
                    DBQuery($Sql_modify_column);
              }
      }
	unset($_REQUEST['tables']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	if($_REQUEST['id']){
	$id = $_REQUEST['id'];
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(CUSTOM_$id) AS TOTAL_ASSIGNED FROM STUDENTS WHERE CUSTOM_$id<>''"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	$msg='Cannot delete because student fields are associated.';
	}else if($_REQUEST['category_id']==5){
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM GOAL"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	$msg='Cannot delete because field categories are associated.';
	}
	if($has_assigned>0){
                  $queryString='category_id='.$_REQUEST['category_id'];
	UnableDeletePromptMod($msg,'',$queryString);
	}else{
	
	if($_REQUEST['id'])
	{
		if(DeletePromptCommon('student field'))
		{
			$id = $_REQUEST['id'];
			DBQuery("DELETE FROM CUSTOM_FIELDS WHERE ID='$id'");
			DBQuery("ALTER TABLE STUDENTS DROP COLUMN CUSTOM_$id");
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['id']);
		}
	}
	elseif($_REQUEST['category_id'])
	{
		if(DeletePromptCommon('student field category and all fields in the category'))
		{
			$fields = DBGet(DBQuery("SELECT ID FROM CUSTOM_FIELDS WHERE SYSTEM_FIELD='N' AND CATEGORY_ID='$_REQUEST[category_id]'"));
			foreach($fields as $field)
			{
				DBQuery("DELETE FROM CUSTOM_FIELDS WHERE ID='$field[ID]'");
				DBQuery("ALTER TABLE STUDENTS DROP COLUMN CUSTOM_$field[ID]");
			}
			DBQuery("DELETE FROM STUDENT_FIELD_CATEGORIES WHERE ID='$_REQUEST[category_id]'");
			// remove from profiles and permissions
			DBQuery("DELETE FROM PROFILE_EXCEPTIONS WHERE MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]'");
			DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]'");
			$_REQUEST['modfunc'] = '';
			unset($_REQUEST['category_id']);
		}
	}
	}
}

if(!$_REQUEST['modfunc'])
{
	// CATEGORIES
	$sql = "SELECT ID,TITLE,SORT_ORDER FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE";
	$QI = DBQuery($sql);
	$categories_RET = DBGet($QI);

	if(AllowEdit() && $_REQUEST['id']!='new' && $_REQUEST['category_id']!='new' && ($_REQUEST['id'] || $_REQUEST['category_id']>4))
		$delete_button = "<INPUT type=button value=Delete class=btn_medium onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&category_id=$_REQUEST[category_id]&id=$_REQUEST[id]\"'>"."&nbsp;";

	// ADDING & EDITING FORM
	if($_REQUEST['id'] && $_REQUEST['id']!='new')
	{
		$sql = "SELECT CATEGORY_ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,SORT_ORDER,REQUIRED,REQUIRED,HIDE,(SELECT TITLE FROM STUDENT_FIELD_CATEGORIES WHERE ID=CATEGORY_ID) AS CATEGORY_TITLE FROM CUSTOM_FIELDS WHERE ID='$_REQUEST[id]'";
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = $RET['CATEGORY_TITLE'].' - '.$RET['TITLE'];
	}
	elseif($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && $_REQUEST['id']!='new')
	{
		$sql = "SELECT TITLE,SORT_ORDER,INCLUDE
				FROM STUDENT_FIELD_CATEGORIES
				WHERE ID='$_REQUEST[category_id]'";
		$RET = DBGet(DBQuery($sql));
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['id']=='new')
		$title = 'New Student Field';
	elseif($_REQUEST['category_id']=='new')
		$title = 'New Student Field Category';

	if($_REQUEST['id'])
	{
		echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
		if($_REQUEST['id']!='new')
			echo "&id=$_REQUEST[id]";
		echo "&table=CUSTOM_FIELDS method=POST>";

		DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_student_studentField_F1();"')); //'<INPUT type=submit value=Save>');
		$header .= '<TABLE cellpadding=3 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['id'].'][TITLE]','Field Name') . '</TD>';

		// You can't change a student field type after it has been created
		// mab - allow changing between select and autos and edits and text
		if($_REQUEST['id']!='new')
		{
			if($RET['TYPE']!='select' && $RET['TYPE']!='autos' && $RET['TYPE']!='edits' && $RET['TYPE']!='text')
			{
				$allow_edit = $_openSIS['allow_edit'];
				$AllowEdit = $_openSIS['AllowEdit'][$modname];
				$_openSIS['allow_edit'] = false;
				$_openSIS['AllowEdit'][$modname] = array();
				$type_options = array('select'=>'Pull-Down','autos'=>'Auto Pull-Down','edits'=>'Edit Pull-Down','text'=>'Text','radio'=>'Checkbox','codeds'=>'Coded Pull-Down','numeric'=>'Number','multiple'=>'Select Multiple from Options','date'=>'Date','textarea'=>'Long Text');
			}
			else
				$type_options = array('select'=>'Pull-Down','autos'=>'Auto Pull-down','edits'=>'Edit Pull-Down','text'=>'Text');
		}
		else
			$type_options = array('select'=>'Pull-Down','autos'=>'Auto Pull-down','edits'=>'Edit Pull-Down','text'=>'Text','radio'=>'Checkbox','codeds'=>'Coded Pull-Down','numeric'=>'Number','multiple'=>'Select Multiple from Options','date'=>'Date','textarea'=>'Long Text');

		$header .= '<TD>' . SelectInput($RET['TYPE'],'tables['.$_REQUEST['id'].'][TYPE]','Data Type',$type_options,false,'id=type onchange="formcheck_student_studentField_F1_defalut();"') . '</TD>';
		if($_REQUEST['id']!='new' && $RET['TYPE']!='select' && $RET['TYPE']!='autos' && $RET['TYPE']!='edits' && $RET['TYPE']!='text')
		{
			$_openSIS['allow_edit'] = $allow_edit;
			$_openSIS['AllowEdit'][$modname] = $AllowEdit;
		}
		foreach($categories_RET as $type)
			$categories_options[$type['ID']] = $type['TITLE'];

		$header .= '<TD>' . SelectInput($RET['CATEGORY_ID']?$RET['CATEGORY_ID']:$_REQUEST['category_id'],'tables['.$_REQUEST['id'].'][CATEGORY_ID]','Student Field Category',$categories_options,false) . '</TD>';
                                    if($_REQUEST['id']=='new' || $RET['SORT_ORDER']=='')
                                        $header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['id'].'][SORT_ORDER]','Sort Order','maxlength=5 onkeydown="return numberOnly(event);"') . '</TD>';
                                    else
                                        $header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['id'].'][SORT_ORDER]','Sort Order','maxlength=5 onkeydown=\"return numberOnly(event);\"') . '</TD>';
		$header .= '</TR><TR>';
		$colspan = 2;
		if($RET['TYPE']=='autos' || $RET['TYPE']=='edits' || $RET['TYPE']=='select' || $RET['TYPE']=='codeds' || $RET['TYPE']=='multiple' || $_REQUEST['id']=='new')
		{
			$header .= '<TD colspan=2>'.TextAreaInput($RET['SELECT_OPTIONS'],'tables['.$_REQUEST['id'].'][SELECT_OPTIONS]','Pull-Down/Auto Pull-Down/Coded Pull-Down/Select Multiple Choices<BR>* one per line','rows=7 cols=40') . '</TD>';
			$colspan = 1;
		}
		$header .= '<TD valign=bottom colspan='.$colspan.'>'.TextInput_mod_a($RET['DEFAULT_SELECTION'],'tables['.$_REQUEST['id'].'][DEFAULT_SELECTION]','Default').'<small><BR>* for dates: YYYY-MM-DD,<BR> for checkboxes: Y <BR> for long text it will be ignored</small></TD>';

		$new = ($_REQUEST['id']=='new');
		$header .= '<TD>' . CheckboxInput($RET['REQUIRED'],'tables['.$_REQUEST['id'].'][REQUIRED]','Required','',$new) . '</TD>';
                                    $header .= '<TD>' . CheckboxInput($RET['HIDE'],'tables['.$_REQUEST['id'].'][HIDE]','Hide','',$new) . '</TD>';
		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	elseif($_REQUEST['category_id'])
	{
		echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&table=STUDENT_FIELD_CATEGORIES";
		if($_REQUEST['category_id']!='new')
			echo "&category_id=$_REQUEST[category_id]";
		echo " method=POST>";
		DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_student_studentField_F2();"'));
		$header .= '<TABLE cellpadding=3 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['category_id'].'][TITLE]','Title') . '</TD>';
		if($_REQUEST['category_id']=='new' || $RET['SORT_ORDER']=='')
                                        $header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['category_id'].'][SORT_ORDER]','Sort Order','maxlength=5 onkeydown="return numberOnly(event);"') . '</TD>';
                                    else
                                       $header .= '<TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['category_id'].'][SORT_ORDER]','Sort Order','maxlength=5 onkeydown=\"return numberOnly(event);\"') . '</TD>';

		$new = ($_REQUEST['category_id']=='new');
		if($_REQUEST['category_id']>4 || $new)
			$header .= '<TD>' . TextInput($RET['INCLUDE'],'tables['.$_REQUEST['category_id'].'][INCLUDE]','Include (should be left blank for most categories)') . '</TD>';

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	else
		$header = false;

	if($header)
	{
		DrawHeaderHome($header);
		echo '</FORM>';
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false,'add'=>true);

	echo '<TABLE><TR>';
        
	if(count($categories_RET))
	{
		if($_REQUEST['category_id'])
		{
			foreach($categories_RET as $key=>$value)
			{
				if($value['ID']==$_REQUEST['category_id'])
					$categories_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Category','SORT_ORDER'=>'Order');
	$link = array();
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
//	$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]\");'";
	$link['TITLE']['variables'] = array('category_id'=>'ID');
//	$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=new";
	$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&category_id=new\");'";	

	ListOutput($categories_RET,$columns,'Student Field Category','Student Field Categories',$link,array(),$LO_options);
	echo '</TD>';

	// FIELDS
	if($_REQUEST['category_id'] && $_REQUEST['category_id']!='new' && count($categories_RET))
	{
		$sql = "SELECT ID,TITLE,TYPE,SORT_ORDER,'Custom' AS FEILD FROM CUSTOM_FIELDS WHERE SYSTEM_FIELD='N' AND CATEGORY_ID='".$_REQUEST['category_id']."' ORDER BY SORT_ORDER,TITLE";
		$fields_RET = DBGet(DBQuery($sql),array('TYPE'=>'_makeType'));

		if(count($fields_RET))
		{
			if($_REQUEST['id'] && $_REQUEST['id']!='new')
			{
				foreach($fields_RET as $key=>$value)
				{
					if($value['ID']==$_REQUEST['id'])
						$fields_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('TITLE'=>'Student Field','FEILD'=>'Field Type');
		$link = array();
		
	//	$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]\");'";
		
	//	$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new";
                $count=0;
                $count++;
                switch ($_REQUEST[category_id]) {
                    case 1:
                             $arr=array('Name','Estimated Grad. Date','Gender','Ethnicity','Common Name','Birthdate','Primary Language','Email','Phone'
                                 ,'Student ID','Alternate ID','Grade','Calendar','Rolling/Retention Options','Username','Password','Last Login','Disable Student'
                                 ,'Start Date','Enrollment Code','Drop Date','Drop Code','School');
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

                        break;
                    case 2:
                             $arr=array('Primary Care Physician','Physician\'s Phone','Preferred Medical Facility','Date(Medical Notes)','Medical Notes','Type(Immunizations)'
                                 ,'Date(Immunizations)','Comments(Immunizations)','Alert Date','Medical Alert','Date(Nurse Visits)','Time In','Time Out','Reason','Result','Comments(Nurse Visits)');
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

                        break;
                     case 3:
                             $arr=array('Address Line 1','Address Line 2','City','State','Zip/Postal Code','School Bus Pick-up','School Bus Drop-off','Bus No','Relationship to Student'
                                 ,'First Name','Last Name','Home Phone','Work Phone','Cell/Mobile Phone','Email','Custody of Student');

                        break;
                     case 4:
                             $arr=array('Entered By','Date','Comments');

                        break;
                    case 5:
                             $arr=array('Goal Title','Begin Date','End Date','Goal Description');

                        break;
                    default:
                             $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]";
                             $link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&id=new\");'";
                             $link['TITLE']['variables'] = array('id'=>'ID');

                        break;
                }
                foreach ($arr as $key => $value) {
                    $fields_RET1[$count]=array('ID'=>'','TITLE'=>$value,'FEILD'=>'<span style="color:#ea8828;">Default</span>');
                    $count++;
                }
                $count2=1;
                foreach ($fields_RET1 as $key2) {
                    $dd[$count2]=$key2;
                    $count2++;
                }
                foreach ($fields_RET as  $row) {
                        $dd[$count2]=$row;
                    $count2++;
                       
                    }
		ListOutput($dd,$columns,'Student Field','Student Fields',$link,array(),$LO_options);

		echo '</TD>';
	}
	echo '</TR></TABLE>';
}

function _makeType($value,$name)
{
	$options = array('radio'=>'Checkbox','text'=>'Text','autos'=>'Auto Pull-Down','edits'=>'Edit Pull-Down','select'=>'Pull-Down','codeds'=>'Coded Pull-Down','date'=>'Date','numeric'=>'Number','textarea'=>'Long Text','multiple'=>'Select Multiple');
	return $options[$value];
}

?>
