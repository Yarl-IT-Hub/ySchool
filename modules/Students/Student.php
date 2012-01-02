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



# ----------------------------- DELETE Goal & Progress -------------------------------------------- #

if($_REQUEST['action'] == 'delete_goal' || $_REQUEST['action']=='delete_goal_can' || $_REQUEST['action']=='delete_goal_ok')
{
	$goal_id = $_REQUEST['gid'];

	if(!isset($_REQUEST['ans']))
	{

		PopTable('header','Delete Confirmation');
		echo "<div class=clear></div><b>Are you sure want to delete this Goal?</b><div class=clear></div><div class=clear></div><center><a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_goal_ok&gid=".$goal_id."&ans=yes' style='text-decoration:none; padding:6px 24px 6px 25px;' class=btn_medium><strong>OK</strong></a> 
		
		<a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_goal_can&gid=".$goal_id."&ans=no' style='text-decoration:none; padding:6px 15px 6px 15px;' class=btn_medium><strong>Cancel</strong></a></center>";
		
		PopTable('footer');
	}
	elseif(isset($_REQUEST['ans']) && $_REQUEST['ans']=='yes')
	{
		$sql_pro = 'SELECT progress_id FROM PROGRESS WHERE goal_id='.$goal_id;
		$res_pro = mysql_query($sql_pro);
		$row_pro_id = mysql_fetch_array($res_pro);
		$pro_final= $row_pro_id[0];
		if(!$pro_final)
		{
			DBQuery("DELETE FROM GOAL WHERE GOAL_ID = '".$goal_id."'");
			$_REQUEST['action'] = 'delete';
			$_REQUEST['goal_id'] = 'new';
			$_REQUEST['action'] = 'delete_goal_ok';
			unset($_REQUEST['modfunc']);
		}
		else
		{
			$_REQUEST['action'] = 'delete';
			$_REQUEST['goal_id'] = $goal_id;
			$_REQUEST['action'] = 'delete_goal_can';
			echo '<div align="center"><font color="red"><b>Unable to delete Goal. Please delete Progresses first.</b></div>';
			unset($_REQUEST['modfunc']);
		}
	}
	else
	{
		$_REQUEST['action'] = 'delete';
		$_REQUEST['goal_id'] = $goal_id;
		$_REQUEST['action'] = 'delete_goal_can';
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['action']=='delete' || $_REQUEST['action']=='delete_can' || $_REQUEST['action']=='delete_ok')
{

	
	$goal_id = $_REQUEST['gid'];
	$progress_id = $_REQUEST['pid'];




	if(!isset($_REQUEST['ans']))
	{
		$_REQUEST['goal_id'] = $_REQUEST['gid'];
		
		PopTable('header','Delete Confirmation');
		echo "<div class=clear></div><b>Are you sure want to delete this Progress?</b><div class=clear></div><div class=clear></div><center><a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_ok&gid=".$goal_id."&pid=".$progress_id."&ans=yes' style='text-decoration:none; padding:6px 24px 6px 25px;' class=btn_medium><strong>OK</strong></a> 
		
		<a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_can&gid=".$goal_id."&pid=".$progress_id."&ans=no' style='text-decoration:none; padding:6px 15px 6px 15px;' class=btn_medium><strong>Cancel</strong></a></center>";
		
		PopTable('footer');
	}
	elseif(isset($_REQUEST['ans']) && $_REQUEST['ans']=='yes')
	{
		DBQuery("DELETE FROM PROGRESS WHERE PROGRESS_ID = '".$_REQUEST['pid']."'");
		$_REQUEST['action'] = 'delete';
		$_REQUEST['goal_id'] = $goal_id;
		$_REQUEST['action'] = 'delete_ok';
		unset($_REQUEST['modfunc']);
	}
	else
	{
		$_REQUEST['action'] = 'delete';
		$_REQUEST['goal_id'] = $goal_id;
		$_REQUEST['progress_id'] = $progress_id;
		$_REQUEST['action'] = 'delete_can';
		unset($_REQUEST['modfunc']);
	}

}


# ----------------------------------------------------------------------------------------------- #















if($_REQUEST['action']!='delete' && $_REQUEST['action']!='delete_goal')
{

    ####################
if(isset($_REQUEST['student_id']) && $_REQUEST['student_id']!='new')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM STUDENTS,STUDENT_ENROLLMENT WHERE STUDENTS.STUDENT_ID='".$_REQUEST['student_id']."' AND STUDENT_ENROLLMENT.STUDENT_ID = STUDENTS.STUDENT_ID "));
	//$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) ');
        }
}
####################





if(User('PROFILE')=='admin')
{
    if($_REQUEST['student_id']=='new')
    {
        if(!$_REQUEST['include'])
        {
            unset($_SESSION['student_id']);
            unset($_SESSION['_REQUEST_vars']['student_id']);
        }
    }
}
//////////////////////////////////////////////////////////////////////////////////

if($_REQUEST['modfunc']=='detail' && $_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
{
	if($_POST['button']=='Save' && AllowEdit())
	{
                        $drop_code=$_REQUEST['drop_code'];
                        $enroll_code=$_REQUEST['drop_code']+1;
		$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_END_DATE']=date("Y-m-d",strtotime($_REQUEST['year_TRANSFER']['STUDENT_ENROLLMENT_END_DATE'].'-'.$_REQUEST['month_TRANSFER']['STUDENT_ENROLLMENT_END_DATE'].'-'.$_REQUEST['day_TRANSFER']['STUDENT_ENROLLMENT_END_DATE']));

                        $gread_exists = DBGet(DBQuery("SELECT COUNT(TITLE) AS PRESENT,ID FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID=".$_REQUEST['TRANSFER']['SCHOOL']." AND TITLE=(SELECT TITLE FROM
        SCHOOL_GRADELEVELS WHERE ID=(SELECT GRADE_ID FROM STUDENT_ENROLLMENT WHERE
        STUDENT_ID=".$_REQUEST['student_id']." AND SCHOOL_ID=".UserSchool()."  AND SYEAR=".UserSyear()."))"));
        
		DBQuery("UPDATE STUDENT_ENROLLMENT SET DROP_CODE=$drop_code,END_DATE='".$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_END_DATE']."' WHERE STUDENT_ID=".$_REQUEST['student_id']." AND SCHOOL_ID=".UserSchool()."  AND SYEAR=".UserSyear());
		
		$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_START']=date("Y-m-d",strtotime($_REQUEST['year_TRANSFER']['STUDENT_ENROLLMENT_START'].'-'.$_REQUEST['month_TRANSFER']['STUDENT_ENROLLMENT_START'].'-'.$_REQUEST['day_TRANSFER']['STUDENT_ENROLLMENT_START']));
	$syear_RET= DBGet(DBQuery("SELECT MAX(SYEAR) AS SYEAR,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID=".$_REQUEST['TRANSFER']['SCHOOL']));
	$syear=$syear_RET[1]['SYEAR'];
	
	$last_school_RET= DBGet(DBQuery("SELECT SCHOOL_ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=".$_REQUEST['student_id']." AND SYEAR=".UserSyear()));
		$last_school=$last_school_RET[1]['SCHOOL_ID'];
	if($gread_exists[1]['PRESENT']==1 && $gread_exists[1]['ID']){
         DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR ,SCHOOL_ID ,STUDENT_ID ,GRADE_ID ,START_DATE ,END_DATE ,ENROLLMENT_CODE ,DROP_CODE ,NEXT_SCHOOL ,CALENDAR_ID ,LAST_SCHOOL) VALUES (".  UserSyear().",".$_REQUEST['TRANSFER']['SCHOOL'].",".$_REQUEST['student_id'].",".$gread_exists[1]['ID'].",'".$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_START']."','',$enroll_code,'','".$_REQUEST['TRANSFER']['SCHOOL']."',NULL,$last_school)");
    }else{   
             DBQuery("INSERT INTO STUDENT_ENROLLMENT (SYEAR ,SCHOOL_ID ,STUDENT_ID ,GRADE_ID ,START_DATE ,END_DATE ,ENROLLMENT_CODE ,DROP_CODE ,NEXT_SCHOOL ,CALENDAR_ID ,LAST_SCHOOL) VALUES (".  UserSyear().",".$_REQUEST['TRANSFER']['SCHOOL'].",".$_REQUEST['student_id'].",'','".$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_START']."','',$enroll_code,'','".$_REQUEST['TRANSFER']['SCHOOL']."',NULL,$last_school)");
        }
	$trans_school=$syear_RET[1]['TITLE'];

        $trans_student_RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".$_REQUEST['student_id']."'"));

        $trans_student=$trans_student_RET[1]['LAST_NAME'].' '.$trans_student_RET[1]['FIRST_NAME'];

	unset($_REQUEST['modfunc']);
	unset($_SESSION['student_id']);
                  unset($_SESSION['_REQUEST_vars']['student_id']);
	echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&student_id='.$_SESSION['student_id'].'&school_id='.UserSchool().'&transffer=done&trans_school='.$trans_school.'&trans_student='.$trans_student.'"; window.close();</script>';
	
		
	}
	else
	{
		
		echo '<BR>';
		PopTableforWindow('header',$title);
		$sql = "SELECT ID,TITLE FROM SCHOOLS WHERE ID !=".UserSchool();
		$QI = DBQuery($sql);
		$schools_RET = DBGet($QI);
		foreach($schools_RET as $school_array){
		$options[$school_array['ID']]=$school_array['TITLE'];
		}
		echo "<FORM name=popform id=popform action=for_window.php?modname=$_REQUEST[modname]&modfunc=detail&student_id=".UserStudentID()."&drop_code=".$_REQUEST['drop_code']." METHOD=POST>";
		echo '<TABLE>';
		echo '<TR><TD>Current school drop date</TD><TD>'.'  '.DateInput_for_EndInput('','TRANSFER[STUDENT_ENROLLMENT_END_DATE]','',$div,true).'</TD></TR>';
		echo '<TR><TD>Transferring to</TD><TD>'.SelectInput('','TRANSFER[SCHOOL]','',$options,false,'class=cell_medium').'</TD></TR>';
		echo '<TR><TD>New school\'s enrollment date</TD><TD>'.'  '.DateInput_for_EndInput('','TRANSFER[STUDENT_ENROLLMENT_START]','',$div,true).'</TD></TR>';
		
		
		
			echo '<TR><TD colspan=2 align=center><INPUT type=submit class=btn_medium name=button value=Save onclick="formload_ajax(\"popform\");">';
			echo '&nbsp;';
			echo '</TD></TR>';			

		echo '</TABLE>';
		PopTableWindow('footer');
		echo '</FORM>';

		unset($_REQUEST['values']);
		unset($_SESSION['_REQUEST_vars']['values']);
		unset($_REQUEST['button']);
		unset($_SESSION['_REQUEST_vars']['button']);
	}
}else{

/////////////////////////////////////////////////////////////////////////////////

if(!$_REQUEST['include'])
{
    $_REQUEST['include'] = 'General_Info';
    $_REQUEST['category_id'] = '1';
}
elseif(!$_REQUEST['category_id'])
if($_REQUEST['include']=='General_Info')
$_REQUEST['category_id'] = '1';
elseif($_REQUEST['include']=='Address')
$_REQUEST['category_id'] = '3';
elseif($_REQUEST['include']=='Medical')
$_REQUEST['category_id'] = '2';
elseif($_REQUEST['include']=='Comments')
$_REQUEST['category_id'] = '4';
#elseif($_REQUEST['include']=='Food_Service')
#$_REQUEST['category_id'] = '6';
elseif($_REQUEST['include']=='Goal')
$_REQUEST['category_id'] = '5';
elseif($_REQUEST['include']!='Other_Info')
{
    $include = DBGet(DBQuery("SELECT ID FROM STUDENT_FIELD_CATEGORIES WHERE INCLUDE='$_REQUEST[include]'"));
    $_REQUEST['category_id'] = $include[1]['ID'];
}
if($_REQUEST['category_id']==3 && !isset($_REQUEST['address_id']))
{
    $address_id = DBGet(DBQuery("SELECT ADDRESS_ID FROM ADDRESS WHERE STUDENT_ID='".UserStudentID()."'"));
    $address_id = $address_id[1]['ADDRESS_ID'];
    if(count($address_id)>0)
    $_REQUEST['address_id'] = $address_id;
    else
    $_REQUEST['address_id'] = 'new';
}

if($_REQUEST['category_id']==5 && !isset($_REQUEST['goal_id']))
	{
	$goal_id = DBGet(DBQuery("SELECT GOAL_ID,START_DATE,END_DATE FROM GOAL WHERE STUDENT_ID='".UserStudentID()."'"));
	$goal_id = $goal_id[1]['GOAL_ID'];
	if(count($goal_id)>0)
	$_REQUEST['goal_id'] = $goal_id;
	else
	$_REQUEST['goal_id'] = 'new';
	}
//if(strpos($_REQUEST['modname'],'?include='))
//	$_REQUEST['modname'] = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?include='));

if(User('PROFILE')!='admin')
{
    if(User('PROFILE')!='student')
    if(User('PROFILE_ID'))
    $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
    else
    $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
    else
    $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
    if($can_edit_RET)
    $_openSIS['allow_edit'] = true;
}

if($_REQUEST['modfunc']=='update' && AllowEdit())
{
    if(count($_REQUEST['month_students']))
    {
        foreach($_REQUEST['month_students'] as $column=>$value)
        {
            $_REQUEST['students'][$column] = $_REQUEST['day_students'][$column].'-'.$_REQUEST['month_students'][$column].'-'.$_REQUEST['year_students'][$column];
            if($_REQUEST['students'][$column]=='--')
            $_REQUEST['students'][$column] = '';
            elseif(!VerifyDate($_REQUEST['students'][$column]))
            {
                unset($_REQUEST['students'][$column]);
                $note = "The invalid date could not be saved.";
            }
        }
    }
    unset($_REQUEST['day_students']); unset($_REQUEST['month_students']); unset($_REQUEST['year_students']);

    if((count($_REQUEST['students']) || count($_REQUEST['values'])) && AllowEdit())
    {
		if($_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
        {
            
			//print_r($_REQUEST['students']);
			//$mycustom_RET=DbGet(DbQuery("select ID,TITLE from CUSTOM_FIELDS WHERE TYPE='multiple' AND SYSTEM_FIELD='N'"));
			if(count($_REQUEST['students']))
            {
                $sql = "UPDATE STUDENTS SET ";
                foreach($_REQUEST['students'] as $column_name=>$value)
                {
                                                                                $value=trim($value);
                                                                                if(substr($column_name,0,6)=='CUSTOM'){
                                    
                                                                                $custom_id=str_replace("CUSTOM_","",$column_name);
                                                                                $custom_RET=DBGet(DBQuery("SELECT TITLE,TYPE FROM CUSTOM_FIELDS WHERE ID=".$custom_id));

                                                                                $custom=DBGet(DBQuery("SHOW COLUMNS FROM STUDENTS WHERE FIELD='".$column_name."'"));
                                                                                $custom=$custom[1];
                                                                                if($custom['NULL']=='NO' && trim($value)=='' && $custom['DEFAULT']){
                                                                                    $value=$custom['DEFAULT'];
                                                                                }else if($custom['NULL']=='NO' && $value==''){
                                                                                    $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                                    echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                                                                    $error=true;
                                                                                }else if($custom_RET[1]['TYPE']=='numeric' &&  (!is_numeric($value) && $value!='')){
                                                                                    $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                                    echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is Numeric type.</b></font><br/>';
                                                                                    $error=true;
                                                                                }else{
                                                                                    $m_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from CUSTOM_FIELDS WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                                                                    if($m_custom_RET)
                                                                                    {
                                                                                        $str="";
                                                                                        foreach($value as $m_custom_val)
                                                                                        {
                                                                                            $str.="||".$m_custom_val;
                                                                                        }
                                                                                        $value=$str."||";
                                                                                    }

                                                                                    /*$c_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from CUSTOM_FIELDS WHERE ID='".$custom_id."' AND TYPE='codeds'"));
                                                                                    if($c_custom_RET)
                                                                                    {

                                                                                    }*/
                                                                                }  ###Myelse ends#####
					
				}  ###Custom Ends#####
                      
                    $value=paramlib_validation($column_name,$value);
                    if($column_name=='PASSWORD' && $value!=''){
                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$sql .= "$column_name='".str_replace("'","\'",str_replace("`","''",md5($value)))."',";
					}else
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",md5($value)))."',";
                    }else{
					if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$sql .= "$column_name='".str_replace("'","\'",str_replace("`","''",$value))."',";
					}else
                    $sql .= "$column_name='".str_replace("'","''",str_replace("'`","''",$value))."',";
					}
					if($column_name=='IS_DISABLE' && $value!='Y' ){
					DBQuery("UPDATE STUDENTS SET FAILED_LOGIN=NULL,LAST_LOGIN=NOW() WHERE STUDENT_ID=$_REQUEST[student_id]");
					}
                }
                $sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]'";
			if(!$error){                
			DBQuery($sql);}
            }

            if(count($_REQUEST['values']['STUDENT_ENROLLMENT'][UserStudentID()]))
            {
                $sql = "UPDATE STUDENT_ENROLLMENT SET ";
                foreach($_REQUEST['values']['STUDENT_ENROLLMENT'][UserStudentID()] as $column_name=>$value)
                if($column_name=='START_DATE' || $column_name=='END_DATE')
                $sql .= "$column_name='".str_replace("\'","''",date('Y-m-d',strtotime($value)))."',";
                else
                $sql .= "$column_name='".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
                $sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'";
				if(!$error){
                DBQuery($sql);}




            }
        }
        else
        {
            if($_REQUEST['assign_student_id'])
            {
                $student_id = $_REQUEST['assign_student_id'];
                if(count(DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS WHERE STUDENT_ID='$student_id'"))))
                BackPrompt('That Student ID is already taken. Please select a different one.');
            }
            else
            {
                do
                {
                   //  $student_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STUDENTS_SEQ').' AS STUDENT_ID '.FROM_DUAL));
                    $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'STUDENTS'"));
                    $student_id[1]['STUDENT_ID']= $id[1]['AUTO_INCREMENT'];
                    $student_id = $student_id[1]['STUDENT_ID'];
                }
                while(count(DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS WHERE STUDENT_ID='$student_id'"))));
            }

            $sql = "INSERT INTO STUDENTS ";
            $fields = '';
            $values = "";

            foreach($_REQUEST['students'] as $column=>$value)
            {
				$value=trim($value);
                                                                        if(substr($column,0,6)=='CUSTOM'){
                                                                         $custom_id=str_replace("CUSTOM_","",$column);
                                                                         $custom_RET=DBGet(DBQuery("SELECT TITLE,TYPE FROM CUSTOM_FIELDS WHERE ID=".$custom_id));

                                                                        $custom=DBGet(DBQuery("SHOW COLUMNS FROM STUDENTS WHERE FIELD='".$column."'"));
				$custom=$custom[1];
				if($custom['NULL']=='NO' && $value=='' && !$custom['DEFAULT']){
				$custom_TITLE=$custom_RET[1]['TITLE'];
                                                                            $required_faild_error=true;
                                                                            echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                                                            $error=true;
				}elseif($custom_RET[1]['TYPE']=='numeric' &&  (!is_numeric($value) && $value!='')){
                                                                            $type_faild_error=true;
                                                                            $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                            echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is Numeric type.</b></font><br/>';
                                                                            $error=true;
                                                                        }else{
                                                                            $m_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from CUSTOM_FIELDS WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                                                            if($m_custom_RET)
                                                                            {
                                                                                    $str="";
                                                                                    foreach($value as $m_custom_val)
                                                                                    {
                                                                                            $str.="||".$m_custom_val;
                                                                                    }
                                                                                    $value=$str."||";
                                                                            }
				
				}
                                                                }
                if($value)
                {
                  $value= paramlib_validation($column,$value);
                   $fields .= $column.',';
                    if($column=='PASSWORD'){

                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                        $values .= "'".str_replace("'","\'",md5($value))."',";
                    }else
                    $values .= "'".str_replace("\'","''",md5($value))."',";
                    }else{
                   if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                        $values .= "'".str_replace("'","\'",$value)."',";
                    }else
                    $values .= "'".str_replace("'","''",$value)."',";
                    }
                }
            }
            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			
			
			$un = $_REQUEST['students']['USERNAME'];
			
			$un_chk = "SELECT COUNT(*) FROM STUDENTS WHERE username = '$un'";
			$res_chk = mysql_query($un_chk);
			$row_chk = mysql_fetch_array($res_chk);
			
			if($row_chk[0] > 0)
			{ $un_chl_res = 'exist'; }
			
			
			$un_chk_user = "SELECT COUNT(*) FROM STAFF WHERE username = '$un'";
			$res_chk_user = mysql_query($un_chk_user);
			$row_chk_user = mysql_fetch_array($res_chk_user);
			
			if($row_chk_user[0] > 0)
			{ $un_chl_res = 'exist'; }
			
			
				
			if(!$error){
				
				if($un_chl_res != 'exist')
				{	
				DBQuery($sql);
				$max_stId=DBGet(DBQuery('SELECT MAX(STUDENT_ID) AS STU_ID FROM STUDENTS'));
				$query='INSERT INTO ADDRESS(STUDENT_ID) VALUES('.$max_stId[1]['STU_ID'].') ';
				DBQuery($query);
				$query='INSERT INTO STUDENTS_JOIN_ADDRESS(STUDENT_ID) VALUES('.$max_stId[1]['STU_ID'].') ';
				DBQuery($query);
				}
				#$_REQUEST['student_id'] = 'new';
				
				
				}elseif($error==true){
				$error_new_student=true;
				}
			   // $studentemrollment_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STUDENT_ENROLLMENT_SEQ').' AS STUDENTENROLL_ID '.FROM_DUAL));
					$id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'STUDENT_ENROLLMENT'"));
					$studentemrollment_id[1]['STUDENTENROLL_ID']= $id[1]['AUTO_INCREMENT'];
					$studentemrollment_id = $studentemrollment_id[1]['STUDENTENROLL_ID'] ;
	
	
				$sql = "INSERT INTO STUDENT_ENROLLMENT ";
				$fields = 'STUDENT_ID,SYEAR,SCHOOL_ID,';
				$values = "'$student_id','".UserSyear()."','".UserSchool()."',";
	
				if($_REQUEST['day_values'])
				$_REQUEST['values']['STUDENT_ENROLLMENT']['new']['START_DATE'] = $_REQUEST['day_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['year_values']['STUDENT_ENROLLMENT']['new']['START_DATE'];
				else
				$_REQUEST['values']['STUDENT_ENROLLMENT']['new']['START_DATE'] = '';
	
				foreach($_REQUEST['values']['STUDENT_ENROLLMENT']['new'] as $column=>$value)
				{
					if($value)
					{
                                                $value= paramlib_validation($column,$value);
						$fields .= $column.',';
						if($column=='START_DATE' || $column=='END_DATE')
						$values .= "'".date('Y-m-d',strtotime($value))."',";
						else
						$values .= "'".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
					}
				}
				$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
				if(!$error){
				if($un_chl_res != 'exist'){
				DBQuery($sql);
				}
			}
			
		
		
            if($required_faild_error==true || $type_faild_error==true)
            {
                $_REQUEST['student_id']='new';
                unset ($value);
            }
            if($openSISModules['Food_Service'])
            {
                // create default food service account for this student
                #$sql = "INSERT INTO FOOD_SERVICE_ACCOUNTS (ACCOUNT_ID,BALANCE,TRANSACTION_ID) values('$student_id','0.00','0')";
                #DBQuery($sql);

                // associate with default food service account and assign other defaults
                #$sql = "INSERT INTO FOOD_SERVICE_STUDENT_ACCOUNTS (STUDENT_ID,DISCOUNT,BARCODE,ACCOUNT_ID) values('$student_id','','','$student_id')";
                #DBQuery($sql);
            }
            if(!$error_new_student){
                if($un_chl_res != 'exist'){
                    $_SESSION['student_id'] = $_REQUEST['student_id'] = $student_id;
                }
                else
                {
                    $_REQUEST['student_id'] = "new";
                    unset($value);
                    echo "<font color=red><b>User name already exist. Please try with a different user name.</b></font>";
                }
            }
            $new_student = true;
        }
    }

    if($_REQUEST['values'] && $_REQUEST['include']=='Medical')
    // SaveData(array('STUDENT_MEDICAL_ALERTS'=>"ID='__ID__'",'STUDENT_MEDICAL'=>"ID='__ID__'",'STUDENT_MEDICAL_VISITS'=>"ID='__ID__'",'fields'=>array('STUDENT_MEDICAL'=>'ID,STUDENT_ID,','STUDENT_MEDICAL_ALERTS'=>'ID,STUDENT_ID,','STUDENT_MEDICAL_VISITS'=>'ID,STUDENT_ID,'),'values'=>array('STUDENT_MEDICAL'=>db_seq_nextval('STUDENT_MEDICAL_SEQ').",'".UserStudentID()."',",'STUDENT_MEDICAL_ALERTS'=>db_seq_nextval('STUDENT_MEDICAL_ALERTS_SEQ').",'".UserStudentID()."',",'STUDENT_MEDICAL_VISITS'=>db_seq_nextval('STUDENT_MEDICAL_VISITS_SEQ').",'".UserStudentID()."',")));
    // possible error 5.11.2009
    //SaveData(array('STUDENT_MEDICAL_ALERTS'=>"ID='__ID__'",'STUDENT_MEDICAL'=>"ID='__ID__'",'STUDENT_MEDICAL_VISITS'=>"ID='__ID__'",'fields'=>array('STUDENT_ID,STUDENT_ID,STUDENT_ID,'),'values'=>array("'".UserStudentID()."','".UserStudentID()."','".UserStudentID()."',")));
    SaveData(array('STUDENT_MEDICAL_NOTES'=>"ID='__ID__'",'STUDENT_MEDICAL_ALERTS'=>"ID='__ID__'",'STUDENT_MEDICAL'=>"ID='__ID__'",'STUDENT_MEDICAL_VISITS'=>"ID='__ID__'",'fields'=>array('STUDENT_MEDICAL_NOTES'=>'STUDENT_ID,','STUDENT_MEDICAL'=>'STUDENT_ID,','STUDENT_MEDICAL_ALERTS'=>'STUDENT_ID,','STUDENT_MEDICAL_VISITS'=>'STUDENT_ID,'),'values'=>array('STUDENT_MEDICAL_NOTES'=>"'".UserStudentID()."',",'STUDENT_MEDICAL'=>"'".UserStudentID()."',",'STUDENT_MEDICAL_ALERTS'=>"'".UserStudentID()."',",'STUDENT_MEDICAL_VISITS'=>"'".UserStudentID()."',")));
     if($_REQUEST['values'] && $_REQUEST['include']=='Comments')
    SaveData(array('STUDENT_MP_COMMENTS'=>"ID='__ID__'",'fields'=>array('STUDENT_MP_COMMENTS'=>'STUDENT_ID,SYEAR,MARKING_PERIOD_ID,STAFF_ID,'),'values'=>array('STUDENT_MP_COMMENTS'=>"'".UserStudentID()."','".UserSyear()."','".UserMP()."','".User('STAFF_ID')."',")));
   
    if($_REQUEST['include']!='General_Info' && $_REQUEST['include']!='Address' && $_REQUEST['include']!='Medical' &&  $_REQUEST['include']!='Goal' && $_REQUEST['include']!='Other_Info')
    if(!strpos($_REQUEST['include'],'/'))
    include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
    else
    include('modules/'.$_REQUEST['include'].'.inc.php');

    unset($_REQUEST['modfunc']);
    // SHOULD THIS BE HERE???
    if(!UserStudentID())
    unset($_REQUEST['values']);
    unset($_SESSION['_REQUEST_vars']['modfunc']);
    unset($_SESSION['_REQUEST_vars']['values']);
}

if($_REQUEST['student_id']=='new')
DrawBC('Students > Add a Student');
else
DrawBC("Students > ".ProgramTitle());

Search('student_id');

/*$Select=" AND LOWER(s.FIRST_NAME) LIKE '".strtolower($_REQUEST['first'])."%' AND LOWER(s.LAST_NAME) LIKE '".strtolower($_REQUEST['last'])."%' AND ssm.STUDENT_ID = '".$_REQUEST['stuid']."' AND s.ALT_ID = '$_REQUEST[altid]' AND ssm.GRADE_ID = '".$_REQUEST['grade']."' ";*/
if($_REQUEST['stuid'])
	{
		$select .= " AND ssm.STUDENT_ID = '".str_replace("'","\'",$_REQUEST[stuid])."' ";
		
	}
   if($_REQUEST['altid'])
	{
		$select .= " AND s.ALT_ID = '".str_replace("'","\'",$_REQUEST[altid])."' ";
		
	}
   if($_REQUEST['last'])
	{
		$select .= " AND LOWER(s.LAST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['last'])))."%' ";
		
	}
   if($_REQUEST['first'])
	{
		$select .= " AND LOWER(s.FIRST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['first'])))."%' ";
		
	}
	if($_REQUEST['grade'])
	{
		$select .= " AND ssm.GRADE_ID = '".str_replace("'","\'",$_REQUEST[grade])."' ";
		
	}
	if($_REQUEST['addr'])
		{
		$select .= " AND (LOWER(a.ADDRESS) LIKE '%".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.CITY) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.STATE)='".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."' OR ZIPCODE LIKE '".trim(str_replace("'","\'",$_REQUEST['addr']))."%')";
		
	}
	
	
	if($_REQUEST['mp_comment'])
	{
		$select .= " AND LOWER(smc.COMMENT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['mp_comment']))."%' AND s.STUDENT_ID=smc.STUDENT_ID ";
	}
	if($_REQUEST['goal_title'])
	{
		$select .= " AND LOWER(g.GOAL_TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_title']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
	}
		if($_REQUEST['goal_description'])
	{
		$select .= " AND LOWER(g.GOAL_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_description']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
	}
		if($_REQUEST['progress_name'])
	{
		$select .= " AND LOWER(p.PROGRESS_NAME) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_name']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
	}
	if($_REQUEST['progress_description'])
	{
		$select .= " AND LOWER(p.PROGRESS_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_description']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
	}
	if($_REQUEST['doctors_note_comments'])
	{
		$select .= " AND LOWER(smn.DOCTORS_NOTE_COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['doctors_note_comments']))."%' AND s.STUDENT_ID=smn.STUDENT_ID ";
	}
	if($_REQUEST['type'])
	{
		$select .= " AND LOWER(sm.TYPE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['type']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
	}
	if($_REQUEST['imm_comments'])
	{
		$select .= " AND LOWER(sm.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['imm_comments']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
	}
	if($_REQUEST['imm_day']&& $_REQUEST['imm_month']&& $_REQUEST['imm_year'])
	{
$imm_date=$_REQUEST['imm_year'].'-'.$_REQUEST['imm_month'].'-'.$_REQUEST['imm_day'];
		$select .= " AND sm.MEDICAL_DATE ='".date('Y-m-d',strtotime($imm_date))."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	}elseif($_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'])
	{
		if($_REQUEST['imm_day']){
		$select .= " AND SUBSTR(sm.MEDICAL_DATE,9,2) ='".$_REQUEST['imm_day']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$imm_date.=" Day :".$_REQUEST['imm_day'];
		}
		if($_REQUEST['imm_month']){
		$select .= " AND SUBSTR(sm.MEDICAL_DATE,6,2) ='".$_REQUEST['imm_month']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$imm_date.=" Month :".$_REQUEST['imm_month'];
		}
		if($_REQUEST['imm_year']){
		$select .= " AND SUBSTR(sm.MEDICAL_DATE,1,4) ='".$_REQUEST['imm_year']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$imm_date.=" Year :".$_REQUEST['imm_year'];
		}
	}
	if($_REQUEST['med_day']&&$_REQUEST['med_month']&&$_REQUEST['med_year'])
	{
$med_date=$_REQUEST['med_year'].'-'.$_REQUEST['med_month'].'-'.$_REQUEST['med_day'];
		$select .= " AND smn.DOCTORS_NOTE_DATE ='".date('Y-m-d',strtotime($med_date))."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	}elseif($_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
	if($_REQUEST['med_day']){
	$select .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,9,2) ='".$_REQUEST['med_day']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Day :".$_REQUEST['med_day'];
	}
	if($_REQUEST['med_month']){
	$select .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,6,2) ='".$_REQUEST['med_month']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Month :".$_REQUEST['med_month'];
	}
	if($_REQUEST['med_year']){
	$select .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,1,4) ='".$_REQUEST['med_year']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Year :".$_REQUEST['med_year'];
	}
	}
	if($_REQUEST['ma_day']&&$_REQUEST['ma_month']&&$_REQUEST['ma_year'])
	{
$ma_date=$_REQUEST['ma_year'].'-'.$_REQUEST['ma_month'].'-'.$_REQUEST['ma_day'];
		$select .= " AND sma.ALERT_DATE ='".date('Y-m-d',strtotime($ma_date))."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	}elseif($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
	if($_REQUEST['ma_day']){
	$select .= " AND SUBSTR(sma.ALERT_DATE,9,2) ='".$_REQUEST['ma_day']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Day :".$_REQUEST['ma_day'];
	}
	if($_REQUEST['ma_month']){
	$select .= " AND SUBSTR(sma.ALERT_DATE,6,2) ='".$_REQUEST['ma_month']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Month :".$_REQUEST['ma_month'];
	}
	if($_REQUEST['ma_year']){
	$select .= " AND SUBSTR(sma.ALERT_DATE,1,4) ='".$_REQUEST['ma_year']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Year :".$_REQUEST['ma_year'];
	}
	}
	if($_REQUEST['nv_day']&&$_REQUEST['nv_month']&&$_REQUEST['nv_year'])
	{
$nv_date=$_REQUEST['nv_year'].'-'.$_REQUEST['nv_month'].'-'.$_REQUEST['nv_day'];
		$select .= " AND smv.SCHOOL_DATE ='".date('Y-m-d',strtotime($nv_date))."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}elseif($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
	if($_REQUEST['nv_day']){
	$select .= " AND SUBSTR(smv.SCHOOL_DATE,9,2) ='".$_REQUEST['nv_day']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Day :".$_REQUEST['nv_day'];
	}
	if($_REQUEST['nv_month']){
	$select .= " AND SUBSTR(smv.SCHOOL_DATE,6,2) ='".$_REQUEST['nv_month']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Month :".$_REQUEST['nv_month'];
	}
	if($_REQUEST['nv_year']){
	$select .= " AND SUBSTR(smv.SCHOOL_DATE,1,4) ='".$_REQUEST['nv_year']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Year :".$_REQUEST['nv_year'];
	}
	}
	
	
	if($_REQUEST['med_alrt_title'])
	{
		$select .= " AND LOWER(sma.TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_alrt_title']))."%' AND s.STUDENT_ID=sma.STUDENT_ID ";
	}
	if($_REQUEST['reason'])
	{
		$select .= " AND LOWER(smv.REASON) LIKE '".str_replace("'","\'",strtolower($_REQUEST['reason']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}
	if($_REQUEST['result'])
	{
		$select .= " AND LOWER(smv.RESULT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['result']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}
	if($_REQUEST['med_vist_comments'])
	{
		$select .= " AND LOWER(smv.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_vist_comments']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}
	if($_REQUEST['day_to_birthdate']&&$_REQUEST['month_to_birthdate']&&$_REQUEST['day_from_birthdate']&&$_REQUEST['month_from_birthdate'])
	{
	$date_to=$_REQUEST['month_to_birthdate'].'-'.$_REQUEST['day_to_birthdate'];
	$date_from=$_REQUEST['month_from_birthdate'].'-'.$_REQUEST['day_from_birthdate'];
		$select .= " AND (SUBSTR(s.BIRTHDATE,6,2) BETWEEN ".$_REQUEST['month_from_birthdate']." AND ".$_REQUEST['month_to_birthdate'].") ";
		$select .= " AND (SUBSTR(s.BIRTHDATE,9,2) BETWEEN ".$_REQUEST['day_from_birthdate']." AND ".$_REQUEST['day_to_birthdate'].") ";
	}

   if(User('PROFILE')=='admin')
	{	
	   $admin_COMMON_FROM=" FROM STUDENTS s, ADDRESS a,STUDENT_ENROLLMENT ssm ";
	   $admin_COMMON_WHERE=" WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
	   if($_REQUEST['mp_comment'] || $_SESSION['smc'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MP_COMMENTS smc";
			$admin_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  if($_REQUEST['goal_description'] || $_REQUEST['goal_title'] || $_SESSION['g'])
		{
			$admin_COMMON_FROM .=" ,GOAL g ";
			$admin_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  if($_REQUEST['progress_name'] || $_REQUEST['progress_description'] || $_SESSION['p'])
		{
			$admin_COMMON_FROM .=" ,PROGRESS p ";
			$admin_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year'] || $_SESSION['smn'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL_NOTES smn ";
			$admin_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  if($_REQUEST['type'] || $_REQUEST['imm_comments'] || $_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'] || $_SESSION['sm'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL sm ";
			$admin_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		  if($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year'] || $_REQUEST['med_alrt_title'] || $_SESSION['sma'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL_ALERTS sma  ";
			$admin_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		  if($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year'] || $_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments'] || $_SESSION['smv'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL_VISITS smv   ";
			$admin_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$admin_COMMON= $admin_COMMON_FROM . $admin_COMMON_WHERE;
		
	}
	/////////////////////////////////// Teacher section ///////////////////////////////////
	if(User('PROFILE')=='teacher')
	{
		   $teacher_COMMON_FROM=" FROM STUDENTS s, STUDENT_ENROLLMENT ssm, COURSE_PERIODS cp,
	SCHEDULE ss,ADDRESS a ";
	   $teacher_COMMON_WHERE=" WHERE a.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
						AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
						
	   if($_REQUEST['mp_comment'] || $_SESSION['smc'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MP_COMMENTS smc";
			$teacher_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  if($_REQUEST['goal_description'] || $_REQUEST['goal_title'] || $_SESSION['g'])
		{
			$teacher_COMMON_FROM .=" ,GOAL g ";
			$teacher_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  if($_REQUEST['progress_name'] || $_REQUEST['progress_description'] || $_SESSION['p'])
		{
			$teacher_COMMON_FROM .=" ,PROGRESS p ";
			$teacher_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year'] || $_SESSION['smn'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL_NOTES smn ";
			$teacher_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  if($_REQUEST['type'] || $_REQUEST['imm_comments'] || $_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'] || $_SESSION['sm'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL sm ";
			$teacher_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		  if($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year'] || $_REQUEST['med_alrt_title'] || $_SESSION['sma'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL_ALERTS sma  ";
			$teacher_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		  if($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year'] || $_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments'] || $_SESSION['smv'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL_VISITS smv   ";
			$teacher_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$teacher_COMMON= $teacher_COMMON_FROM . $teacher_COMMON_WHERE;
 }
	
	////////////////////////////////// End Of Teacher Section /////////////////////////////
	
	
	
	
	
	if(!UserStudentID())
	{
	#$sql="SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID FROM STUDENTS s, ADDRESS a, STUDENT_ENROLLMENT ssm WHERE s.STUDENT_ID=ssm.STUDENT_ID ".$select;
	if(User('PROFILE')=='admin')
	{
		$sql="SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$admin_COMMON_FROM.$admin_COMMON_WHERE.$select;
	}
	elseif(User('PROFILE')=='teacher')
	{
			$sql="SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$teacher_COMMON_FROM.$teacher_COMMON_WHERE.$select;

	}
	
	$val=DBGet(DBQuery($sql));

	if($val[1]['STUDENT_ID']>1 && !$_SESSION['stu_search']['sql'])
        {
            unset($_SESSION['s']);
            unset($_SESSION['custom_count_sql']);
            unset($_SESSION['inactive_stu_filter']);
        }

	}
	if(!$_SESSION['s'])
	{
	   	$_SESSION['s']=$select;
        }

        if($_SESSION['inactive_stu_filter'])
        {
            $_SESSION['s'] .= $_SESSION['inactive_stu_filter'];
        }
   #$admin_COMMON=" FROM STUDENTS s, ADDRESS a,STUDENT_ENROLLMENT ssm WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";

/*  $teacher_COMMON="  FROM STUDENTS s, STUDENT_ENROLLMENT ssm, COURSE_PERIODS cp,
SCHEDULE ss
 WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
*/					
	if($_REQUEST['v'] && isset($_REQUEST['student_id']))
	{
		$val=$_REQUEST['v'];
		if($val==1)
		 {
		 	if(User('PROFILE')=='admin')
			 {
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1 "));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1 "));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		elseif($val==2)
		 {
			if(User('PROFILE')=='admin')
			 {
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID) <'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 	$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$teacher_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID) <'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		elseif($val==3)
		 {
			if(User('PROFILE')=='admin')
			 {
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID)>'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$teacher_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID) >'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		 elseif($val==4)
		 {
		 	if(User('PROFILE')=='admin')
			 {
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
	 }
 

if(UserStudentID() || $_REQUEST['student_id']=='new')
{
	if($_REQUEST['student_id']!='new')
	{
		if(User('PROFILE')=='admin')
		  {
                  
		$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()."  ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			
		$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
                if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                $ln=  str_replace("'","\'",$ln);
                }else{
                    $ln=str_replace("'","\'",$ln);
                }
		
		$s1_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
		$s2_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
		
		$count_STU=DBGet(DBQuery("SELECT COUNT(LAST_NAME) AS STUDENT ".$admin_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID)<'".$ln."' AND LAST_NAME LIKE '".strtolower($_REQUEST['last'])."%'".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		$count=$count_STU[1]['STUDENT'] + 1;
		$total=DBGet(DBQuery("SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$admin_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		  }
		  elseif(User('PROFILE')=='teacher')
		   {
		   
		   	/*$total=DBGet(DBQuery("SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID FROM STUDENTS s, STUDENT_ENROLLMENT ssm, COURSE_PERIODS cp,
SCHEDULE ss
 WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ".$_SESSION['s']));*/
			
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$teacher_COMMON." AND s.STUDENT_ID ='".UserStudentID()."'  ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			
		$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
		
		$s1_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
		
		$s2_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
		
		$count_STU=DBGet(DBQuery("SELECT COUNT(LAST_NAME) AS STUDENT ".$teacher_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID)<'".$ln."' AND LAST_NAME LIKE '".strtolower($_REQUEST['last'])."%'".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		$count=$count_STU[1]['STUDENT']+1;
				
			$total=DBGet(DBQuery("SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$teacher_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		   }
		  
		 echo '<br/>';
		 if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
		  {
				echo "<div style='text-align:right; padding-left:10px;'><table width='100%' cellpadding='0' cellspacing='0'><tr><td align='right'>";
				echo "<div style='margin-right:15px; font-weight:bold; font-size:14px;'>"."Showing ".$count." of ".$total[1]['STUDENT_ID']."</div>";
				echo "</td><td align='right' width='250px' style='padding-top:4px;'>";
                                echo '<div style="margin-right:15px; margin-bottom:8px;">';
				if($total[1]['STUDENT_ID']>1)
				{
				 if(UserStudentID()!=$s1_id[1]['STUDENT_ID'])
				 {
					echo "<span class='pg-prev' style='margin-right:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=1&student_id=".UserStudentID()." >&laquo; First</A></span>";
					//echo '&nbsp;&nbsp;&nbsp;';
					echo "<span class='pg-prev' style='margin-right:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=2&student_id=".UserStudentID()." >&lsaquo; Previous</A></span>";
				 }
				 if(UserStudentID()!=$s2_id[1]['STUDENT_ID'])
				 {
					//echo '&nbsp;&nbsp;&nbsp;';
					echo "<span class='pg-nxt' style='margin-left:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=3&student_id=".UserStudentID()." >Next &rsaquo;</A></span>";
					//echo '&nbsp;&nbsp;&nbsp;';
					echo "<span class='pg-nxt' style='margin-left:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=4&student_id=".UserStudentID()." >Last &raquo;</A></span>";
				 }
			}
			echo "</div></td></tr></table></div>";
	  	 }
		 
		 
	}
	
	/*echo "<A HREF=Modules.php?modname=Students/Student.php> Next >></A>";*/
    if($_REQUEST['modfunc']!='delete' || $_REQUEST['delete_ok']=='1')
    {
	
        if($_REQUEST['student_id']!='new')
        {
            $sql = "SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,s.USERNAME,s.PASSWORD,s.LAST_LOGIN,s.IS_DISABLE,s.ESTIMATED_GRAD_DATE,s.GENDER,s.ETHNICITY,s.COMMON_NAME,s.BIRTHDATE,s.LANGUAGE,s.PHYSICIAN,s.PHYSICIAN_PHONE,s.PREFERRED_HOSPITAL,s.ALT_ID,s.EMAIL,s.PHONE,(SELECT SCHOOL_ID FROM STUDENT_ENROLLMENT WHERE SYEAR='".UserSyear()."' AND STUDENT_ID=s.STUDENT_ID ORDER BY START_DATE DESC,END_DATE DESC LIMIT 1) AS SCHOOL_ID,
                        (SELECT GRADE_ID FROM STUDENT_ENROLLMENT WHERE SYEAR='".UserSyear()."' AND STUDENT_ID=s.STUDENT_ID ORDER BY START_DATE DESC,END_DATE DESC LIMIT 1) AS GRADE_ID
                    FROM STUDENTS s
                    WHERE s.STUDENT_ID='".UserStudentID()."'";
            $QI = DBQuery($sql);
            $student = DBGet($QI);
            $student = $student[1];
            $school = DBGet(DBQuery("SELECT SCHOOL_ID,GRADE_ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND ('".DBDate()."' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL)"));
            $_REQUEST['modname'] = str_replace('?student_id=new','',$_REQUEST['modname']);
            echo "<FORM name=student action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&student_id=".UserStudentID()."&modfunc=update method=POST>";
        }
        else
        echo "<FORM name=student action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=update method=POST>";
		
        $name = $student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'];

        if($_REQUEST['student_id']!='new')
        $name .= ' - '.$student['STUDENT_ID'];
        

        if(User('PROFILE')!='student')
        if(User('PROFILE_ID'))
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
        else
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
        else
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND CAN_USE='Y'"),array(),array('MODNAME'));
        $categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));

        foreach($categories_RET as $category)
        {
            if($can_use_RET['Students/Student.php&category_id='.$category['ID']])
            {
                if($category['ID']=='1')
                $include = 'General_Info';
                elseif($category['ID']=='3')
                $include = 'Address';
                elseif($category['ID']=='2')
                $include = 'Medical';
                elseif($category['ID']=='4')
                $include = 'Comments';
               # elseif($category['ID']=='5')
               # $include = 'Food_Service';
                elseif($category['ID']=='5')
				$include = 'Goal';
                elseif($category['INCLUDE'])
                $include = $category['INCLUDE'];
                else
                $include = 'Other_Info';

                $tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&include=$include&category_id=".$category['ID']);
            }
        }
		
        $_openSIS['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]";
        if($_REQUEST['category_id'])
        $_openSIS['selected_tab'] .= '&category_id='.$_REQUEST['category_id'];

        echo '<BR>';
        echo PopTable('header',$tabs,'');

        if(!strpos($_REQUEST['include'],'/'))
        include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
        else
        {
            include('modules/'.$_REQUEST['include'].'.inc.php');
            $separator = '<HR>';
            include('modules/Students/includes/Other_Info.inc.php');
        }
        echo PopTable('footer');

      if(isset($_REQUEST['goal_id']) && $_REQUEST['goal_id'] != 'new' && !isset($_REQUEST['progress_id']))
       echo '<CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>';
   else
        echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_student_student();"').'</CENTER>';
        echo '</FORM>';
    }
    else
    if(!strpos($_REQUEST['include'],'/'))
    include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
    else
    {
		
        include('modules/'.$_REQUEST['include'].'.inc.php');
        $separator = '<div class=break></div>';
        include('modules/Students/includes/Other_Info.inc.php');
    }
}
}

}
?>
