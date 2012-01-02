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
$sysyear = UserSyear();

$fields[STUDENTS] = array(	'FIRST_NAME'=>'First Name','LAST_NAME'=>'Last Name','MIDDLE_NAME'=>'Middle Name',
							'CURRENT_SCHOOL'=>'Current School','PREVIOUS_SCHOOL'=>'Previous School','NEXT_SCHOOL'=>'Next School',
							'BIRTH_DATE'=>'Birth Date','BIRTH_PLACE'=>'Birth Place'
						 );

$cust_RET = DBGet(DBQuery("SELECT TITLE, concat('CUSTOM_', ID) as COLUMN_NAME FROM CUSTOM_FIELDS"),array('TITLE'=>'GetCapWords'));
if(count($cust_RET))
{
	foreach($cust_RET as $cust)
		$fields[CUSTOM][$cust[COLUMN_NAME]] = $cust[TITLE];
}

$org['Student Info'] = array('STUDENTS');
$limit['STUDENT_ENROLLMENT']['SYEAR'] = $sysyear;
$functions = array('FIRST_NAME'=>'GetCapWords','LAST_NAME'=>'GetCapWords','MIDDLE_NAME'=>'GetCapWords',
					'GRADE_ID'=>'GetGrade',
					'SCHOOL'=>'GetSchool','PREVIOUS_SCHOOL'=>'GetSchool','NEXT_SCHOOL'=>'GetSchool','CURRENT_SCHOOL'=>'GetSchool',
					'ENROLL_DATE'=>'DBDateConv','BIRTH_DATE'=>'DBDateConv');

// -------------------------------- END SETUP --------------------------------- \\
$modfunc = $_REQUEST[modfunc];
if($modfunc=='')
	$modfunc = 'find';

if($modfunc=='list')
{
	$field_list = $_REQUEST[field_list];
	
	$i=2;
	if(count($field_list))
	{
		foreach($field_list as $table_name=>$column_list)
		{
			// PRODUCE FROM AND WHERE LISTS
			if($table_name!='STUDENTS')
				$from .= ",$table_name a$i";
			else
				$i=1;
			$tables[$i] = 'a'.$i;
			for($j=1;$j<$i;$j++)
				$where .= "and a$j.STUDENT_ID=a$i.STUDENT_ID ";
			if(count($limit[$table_name]))
			{
				foreach($limit[$table_name] as $column_name=>$value)
					$where .= "and a$i.$column_name='$value' ";
			}
			
			// PRODUCE SELECT LIST
			if(count($column_list))
			{
				foreach($column_list as $column_name=>$on)
				{
					$select .= ",a$i.$column_name";
					$LO_columns[$column_name] = $fields[$table_name][$column_name];
					$LO_functions[$column_name] = $functions[$column_name];
				}
			}
				
			$i++;
		}
	}
	$select = 'a1.STUDENT_ID'.$select;
	$where = substr($where,4);
	$from = 'STUDENTS a1'.$from;
	
	if(trim($where)=='')
		$where .= ' 1=1 ';
	if($_REQUEST[last])
		$where .= "and a1.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%'";
	if($_REQUEST[first])
		$where .= "and a1.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%'";
	if($_REQUEST[stuid])
		$where .= "and a1.STUDENT_ID = '".$_REQUEST[stuid]."'";

	
	// CONSTRUCT SQL
	$sql = "SELECT $select FROM $from WHERE $where ORDER BY a1.STUDENT_ID";
	$QI = DBQuery($sql);
	$RET = DBGet($QI,$LO_functions);
	
	$_REQUEST[modfunc] = 'list';
	ListOutput($RET,$LO_columns,'Student','Students');
}

if($modfunc=='find')
{
	PopTable('header','Find a Student');
	echo "<FORM action='Modules.php?modname=$_REQUEST[modname]&modfunc=list' METHOD=POST>";
	echo '<b>Search Criteria:</b>';
	Warehouse('searchstu');
	echo '</TABLE>';
	echo '<HR>';
	echo '<b>List:</B>';
	echo '<TABLE><TR><TD>';

	foreach($org as $cat_name=>$tables)
	{
		echo '<TABLE border=0 cellpadding=5><TR><TD colspan=7 align=left><b>'.$cat_name.'</b></TD></TR>';
		echo '<TR><TD></TD>';
		$col = 1;
		foreach($tables as $table_name)
		{
			if(count($fields[$table_name]))
			{
				foreach($fields[$table_name] as $column_name=>$column_disp)
				{
					echo "<TD><INPUT type=checkbox name='field_list[$table_name][$column_name]'></TD><TD>$column_disp</TD>";
					$col++;
					if($col==4)
					{
						echo '</TR><TR><TD width=10>&nbsp;</TD>';
						$col=1;
					}
				}
			}
		}
		if($col==1)
			echo '<TD></TD><TD></TD><TD></TD><TD></TD><TD></TD><TD></TD></TR></TABLE>';
		elseif($col==2)
			echo '<TD></TD><TD></TD><TD></TD><TD></TD></TR></TABLE>';
		elseif($col==3)
			echo '<TD></TD><TD></TD></TR></TABLE>';
	}
	echo '</TD></TR></TABLE>';
	echo '<center>';
	echo Buttons('Submit','Reset');
	echo '</center>';
	echo '</FORM>';
	PopTable('footer');
}

?>
