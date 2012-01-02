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
// INSERT/UPDATE,[fields][values]/WHERE,[]fields/,Propernames
function SaveData($iu_extra,$fields_done=false,$field_names=false)
{
	if(!$fields_done)
		$fields_done = array();
	if(!$field_names)
		$field_names = array();

	if($_REQUEST['month_values'])
	{
		foreach($_REQUEST['month_values'] as $table=>$values)
		{
			foreach($values as $id=>$columns)
			{
				foreach($columns as $column=>$value)
				{
					#$_REQUEST['values'][$table][$id][$column] = $_REQUEST['day_values'][$table][$id][$column].'-'.$value.'-'.$_REQUEST['year_values'][$table][$id][$column];
					
					if($value == 'JAN')
						$value = '01';
					if($value == 'FEB')
						$value = '02';
					if($value == 'MAR')
						$value = '03';
					if($value == 'APR')
						$value = '04';
					if($value == 'MAY')
						$value = '05';
					if($value == 'JUN')
						$value = '06';
					if($value == 'JUL')
						$value = '07';
					if($value == 'AUG')
						$value = '08';
					if($value == 'SEP')
						$value = '09';
					if($value == 'OCT')
						$value = '10';
					if($value == 'NOV')
						$value = '11';
					if($value == 'DEC')
						$value = '12';


					
					$_REQUEST['values'][$table][$id][$column] = $_REQUEST['year_values'][$table][$id][$column].'-'.$value.'-'.$_REQUEST['day_values'][$table][$id][$column];
					
					if($_REQUEST['values'][$table][$id][$column]=='--')
						$_REQUEST['values'][$table][$id][$column] = '';
				}
			}
		}
	}
	foreach($_REQUEST['values'] as $table=>$values)
	{
		$table_properties = db_properties($table);
		foreach($values as $id=>$columns)
		{
			foreach($columns as $column=>$value)
			{
				if($field_names[$table][$column])
					$name = 'The value for '.$field_names[$table][$column];
				else
					$name = 'The value for '.ucwords(strtolower(str_replace('_',' ',$column)));

				// COLUMN DOESN'T EXIST
				if(!$table_properties[$column])
				{
					$error[] = 'There is no column for '.$name.'. This value was not saved.';
					continue;
				}

				// VALUE IS TOO LONG
				if($table_properties[$column]['TYPE']=='VARCHAR' && strlen($value) > $table_properties[$column]['SIZE'])
				{
					$value = substr($value,0,$table_properties[$column]['SIZE']);
					$error[] = $name . ' was too long.  It was truncated to fit in the field.';
				}

				// FIELD IS NUMERIC, VALUE CONTAINS NON-NUMERICAL CHARACTERS
				if($table_properties[$column]['TYPE']=='NUMERIC' && ereg('[^0-9-]',$value))
				{
					$value = ereg_replace('[^0-9]','',$value);
					$error[] = $name . ', a numerical field, contained non-numerical characters.  These characaters were removed.';
				}

				// FIELD IS DATE, DATE IS WRONG
				if($table_properties[$column]['TYPE']=='DATE' && $value && !VerifyDate($value))
				{
					$error[] = $name . ', a date field, was not a valid date.  This value could not be saved.';
					continue;
				}
				if($table_properties[$column]['TYPE']=='DATE' && $value)
				{
					$value = date('Y-m-d',strtotime($value));
				}
				if($id=='new')
				{
					if(trim($value))
					{
                                                $value=paramlib_validation($column,$value);
						$ins_fields[$table] .= $column.',';
                                                if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                               $ins_values[$table] .= "'".str_replace("'"," \'",$value)." ',";
                                               
                                                }else
						$ins_values[$table] .= "'".$value." ',";
						$go = true;
					}
				}
				else{
                                  
					if(strlen($value)>0){
                                             $value=paramlib_validation($column,$value);
                                             if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                             $values = "$column='".str_replace("'","\'",str_replace('&#39;',"''",$value))." ',";
                                             }else
					     $values = "$column='".$value." ',";
                                        
                                             $sql[$table] .= str_replace('%u201D', "\"", $values);
                                             if($column == 'END_DATE' && $table=='STUDENT_ENROLLMENT' )
                                             {
 DBQuery("UPDATE SCHEDULE SET END_DATE='".$value."' WHERE STUDENT_ID=".$_REQUEST['student_id']." AND SCHOOL_ID=".UserSchool()."  AND SYEAR=".UserSyear());
                                             }
                                        }else{
						$sql[$table] .= "$column=NULL,";
                                        }}
			}
			if($id=='new')
				 $sql[$table] = 'INSERT INTO '.$table.' (' . $iu_extra['fields'][$table].substr($ins_fields[$table],0,-1) . ') values(' . $iu_extra['values'][$table].substr($ins_values[$table],0,-1) . ')';
			else
                        {
                            $sql[$table] = 'UPDATE '.$table.' SET '.substr($sql[$table],0,-1).' WHERE '.str_replace('__ID__',$id,$iu_extra[$table]);
                            if($table=='STUDENT_ENROLLMENT')
                            {
                                  $enrollment_record=DBGet(DBQuery("SELECT * FROM STUDENT_ENROLLMENT WHERE STUDENT_ID='$_REQUEST[student_id]' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
                                  $enrollment_record=$enrollment_record[1];
                             
                                    if($enrollment_record['END_DATE'] && $enrollment_record['DROP_CODE'])
                                    {
                                        $select_parent=DBGet(DBQuery("SELECT STAFF_ID FROM STUDENTS_JOIN_USERS WHERE STUDENT_ID='$_REQUEST[student_id]'"));
                                     
                                        foreach($select_parent as $key)
                                        {
                                            $parent=DBGet(DBQuery("SELECT * FROM STAFF WHERE STAFF_ID='$key[STAFF_ID]' AND SYEAR='".UserSyear()."' AND CURRENT_SCHOOL_ID='".UserSchool()."'"));
                                           
                                            if($parent)
                                            {
                                                
                                                $update_parent=DBQuery("UPDATE STAFF SET IS_DISABLE='Y' WHERE STAFF_ID='".$parent[1][STAFF_ID]."'");
                                            }
                                        }
                                    }
                                 else {
                                        $select_parent=DBGet(DBQuery("SELECT STAFF_ID FROM STUDENTS_JOIN_USERS WHERE STUDENT_ID='$_REQUEST[student_id]'"));

                                        foreach($select_parent as $key)
                                        {
                                            $parent=DBGet(DBQuery("SELECT * FROM STAFF WHERE STAFF_ID='$key[STAFF_ID]' AND SYEAR='".UserSyear()."' AND CURRENT_SCHOOL_ID='".UserSchool()."'"));

                                            if($parent)
                                            {
                                                
                                                $update_parent=DBQuery("UPDATE STAFF SET IS_DISABLE=NULL WHERE STAFF_ID='".$parent[1][STAFF_ID]."'");
                                            }
                                        }
                                }
                            }
                        }
			echo ErrorMessage($error);
			if($id!='new' || $go==true)
                        {
					DBQuery($sql[$table]);
}
			$error = $ins_fields = $ins_values = $sql = $go = '';
		}
	}
}
?>
