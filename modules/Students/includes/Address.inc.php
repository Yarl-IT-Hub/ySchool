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
include 'modules/Students/config.inc.php';
if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
{
	if($_REQUEST['values']['EXISTING'])
	{
		if($_REQUEST['values']['EXISTING']['address_id'] && $_REQUEST['address_id']=='old')
		{
			$_REQUEST['address_id'] = $_REQUEST['values']['EXISTING']['address_id'];
			$address_RET = DBGet(DBQuery("SELECT '' FROM STUDENTS_JOIN_ADDRESS WHERE ADDRESS_ID='$_REQUEST[address_id]' AND STUDENT_ID='".UserStudentID()."'"));
			if(count($address_RET)==0)
			{
			DBQuery("INSERT INTO STUDENTS_JOIN_ADDRESS (STUDENT_ID,ADDRESS_ID) values('".UserStudentID()."','$_REQUEST[address_id]')");
			DBQuery("INSERT INTO STUDENTS_JOIN_PEOPLE (STUDENT_ID,PERSON_ID,ADDRESS_ID) SELECT DISTINCT ON (PERSON_ID) '".UserStudentID()."',PERSON_ID,ADDRESS_ID FROM STUDENTS_JOIN_PEOPLE WHERE ADDRESS_ID='$_REQUEST[address_id]'");
			}
		}
		elseif($_REQUEST['values']['EXISTING']['person_id'] && $_REQUEST['person_id']=='old')
		{
			$_REQUEST['person_id'] = $_REQUEST['values']['EXISTING']['person_id'];
			$people_RET = DBGet(DBQuery("SELECT '' FROM STUDENTS_JOIN_PEOPLE WHERE PERSON_ID='$_REQUEST[person_id]' AND STUDENT_ID='".UserStudentID()."'"));
			if(count($people_RET)==0)
			{
			DBQuery("INSERT INTO STUDENTS_JOIN_PEOPLE (STUDENT_ID,ADDRESS_ID,PERSON_ID) values('".UserStudentID()."','$_REQUEST[address_id]','$_REQUEST[person_id]')");
			}
		}
	}

	if(clean_param($_REQUEST['values']['ADDRESS'],PARAM_NOTAGS))
	{
	// echo 'sid= '.$_REQUEST['address_id'];
		if($_REQUEST['address_id']!='new')
		{
			$sql = "UPDATE ADDRESS SET ";

			foreach($_REQUEST['values']['ADDRESS'] as $column=>$value)
			{
				if(!is_array($value)){
                                    
                                    $value=paramlib_validation($column,$value);
                                    $sql .= $column."='".str_replace("\'","''",$value)."',";}
				else
				{
					$sql .= $column."='||";
					foreach($value as $val)
					{
						if($val)
							$sql .= str_replace('&quot;','"',$val).'||';
					}
					$sql .= "',";
				}
			}
			$sql = substr($sql,0,-1) . " WHERE ADDRESS_ID='$_REQUEST[address_id]'";
			DBQuery($sql);
			$query="SELECT ADDRESS_ID FROM 
STUDENTS_JOIN_ADDRESS
 WHERE STUDENT_ID='".UserStudentID()."'";
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];
			if($a_ID == 0)
			{
				$id=DBGet(DBQuery("SELECT ADDRESS_ID  FROM ADDRESS WHERE STUDENT_ID='".UserStudentID()."'"));
				$id=$id[1]['ADDRESS_ID'];
				DBQuery("UPDATE STUDENTS_JOIN_ADDRESS SET ADDRESS_ID='".$id."',RESIDENCE='".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['RESIDENCE']."', MAILING='".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['MAILING']."',BUS_PICKUP='".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['BUS_PICKUP']."', BUS_DROPOFF='".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['BUS_DROPOFF']."' WHERE STUDENT_ID='".UserStudentID()."'");
			if($_REQUEST['r4']=='Y' && $_REQUEST['r4']!='N')
			{
			DBQuery("UPDATE ADDRESS SET MAIL_ADDRESS='".$_REQUEST['values']['ADDRESS']['ADDRESS']."',MAIL_STREET='".$_REQUEST['values']['ADDRESS']['STREET']."', MAIL_CITY='".$_REQUEST['values']['ADDRESS']['CITY']."',MAIL_STATE='".$_REQUEST['values']['ADDRESS']['STATE']."', MAIL_ZIPCODE='".$_REQUEST['values']['ADDRESS']['ZIPCODE']."' WHERE STUDENT_ID='".UserStudentID()."'");
			}
			if($_REQUEST['r5']=='Y' && $_REQUEST['r5']!='N')
			{
			DBQuery("UPDATE ADDRESS SET PRIM_ADDRESS='".$_REQUEST['values']['ADDRESS']['ADDRESS']."',PRIM_STREET='".$_REQUEST['values']['ADDRESS']['STREET']."', PRIM_CITY='".$_REQUEST['values']['ADDRESS']['CITY']."',PRIM_STATE='".$_REQUEST['values']['ADDRESS']['STATE']."', PRIM_ZIPCODE='".$_REQUEST['values']['ADDRESS']['ZIPCODE']."' WHERE STUDENT_ID='".UserStudentID()."'");
			}
			if($_REQUEST['r6']=='Y' && $_REQUEST['r6']!='N')
			{
			DBQuery("UPDATE ADDRESS SET SEC_ADDRESS='".$_REQUEST['values']['ADDRESS']['ADDRESS']."',SEC_STREET='".$_REQUEST['values']['ADDRESS']['STREET']."', SEC_CITY='".$_REQUEST['values']['ADDRESS']['CITY']."',SEC_STATE='".$_REQUEST['values']['ADDRESS']['STATE']."', SEC_ZIPCODE='".$_REQUEST['values']['ADDRESS']['ZIPCODE']."' WHERE STUDENT_ID='".UserStudentID()."'");
			}

		  }		
		}
		else
		{
			/*
			$id = DBGet(DBQuery('SELECT '.db_seq_nextval('ADDRESS_SEQ').' as SEQ_ID '.FROM_DUAL));
			$id = $id[1]['SEQ_ID'];

			$sql = "INSERT INTO ADDRESS ";

			$fields = 'ADDRESS_ID,STUDENT_ID,';
			$values = "'".$id."','".UserStudentID()."',";
			*/

			$sql = "INSERT INTO ADDRESS ";

			$fields = 'STUDENT_ID,';
			$values = "'".UserStudentID()."',";


######################################## For Same Mailing Address ###################################

		if($_REQUEST['r4']=='Y' && $_REQUEST['r4']!='N')
		{
			$fields .= 'MAIL_ADDRESS,MAIL_STREET,MAIL_CITY,MAIL_STATE,MAIL_ZIPCODE,';
			$values .= "'".$_REQUEST['values']['ADDRESS']['ADDRESS']."','".$_REQUEST['values']['ADDRESS']['STREET']."','".$_REQUEST['values']['ADDRESS']['CITY']."','".$_REQUEST['values']['ADDRESS']['STATE']."','".$_REQUEST['values']['ADDRESS']['ZIPCODE']."',";
		}

######################################## For Same Mailing Address ###################################
################################ For Same Primary  Emergency Contact ###################################

		if($_REQUEST['r5']=='Y' && $_REQUEST['r5']!='N')
		{
			$fields .= 'PRIM_ADDRESS,PRIM_STREET,PRIM_CITY,PRIM_STATE,PRIM_ZIPCODE,';
			$values .= "'".$_REQUEST['values']['ADDRESS']['ADDRESS']."','".$_REQUEST['values']['ADDRESS']['STREET']."','".$_REQUEST['values']['ADDRESS']['CITY']."','".$_REQUEST['values']['ADDRESS']['STATE']."','".$_REQUEST['values']['ADDRESS']['ZIPCODE']."',";
		}

############################### For Same Primary  Emergency Contact ####################################

############################# For Same Secondary  Emergency Contact ####################################

		if($_REQUEST['r6']=='Y' && $_REQUEST['r6']!='N')
		{
			$fields .= 'SEC_ADDRESS,SEC_STREET,SEC_CITY,SEC_STATE,SEC_ZIPCODE,';
			$values .= "'".$_REQUEST['values']['ADDRESS']['ADDRESS']."','".$_REQUEST['values']['ADDRESS']['STREET']."','".$_REQUEST['values']['ADDRESS']['CITY']."','".$_REQUEST['values']['ADDRESS']['STATE']."','".$_REQUEST['values']['ADDRESS']['ZIPCODE']."',";
		}
###############################For Same Secondary  Emergency Contact ###################################

			$go = 0;
			foreach($_REQUEST['values']['ADDRESS'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                       if($go)
			{

				DBQuery($sql);
                               $id=DBGet(DBQuery("select max(address_id) as ADDRESS_ID  from ADDRESS"));
                               $id=$id[1]['ADDRESS_ID'];
                               DBQuery("INSERT INTO STUDENTS_JOIN_ADDRESS (STUDENT_ID,ADDRESS_ID,RESIDENCE,MAILING,BUS_PICKUP,BUS_DROPOFF) values('".UserStudentID()."','".$id."','".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['RESIDENCE']."','".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['MAILING']."','".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['BUS_PICKUP']."','".$_REQUEST['values']['STUDENTS_JOIN_ADDRESS']['BUS_DROPOFF']."')");
				$_REQUEST['address_id'] = $id;
			}
		}
	}

	if(clean_param($_REQUEST['values']['PEOPLE'],PARAM_NOTAGS))
	{
		if($_REQUEST['person_id']!='new')
		{
			$sql = "UPDATE PEOPLE SET ";

			foreach($_REQUEST['values']['PEOPLE'] as $column=>$value)
			{
                            $value=paramlib_validation($column,$value);
                            $sql .= $column."='".str_replace("\'","''",$value)."',";
			}
			$sql = substr($sql,0,-1) . " WHERE PERSON_ID='$_REQUEST[person_id]'";
			DBQuery($sql);
		}
		else
		{
			//$id = DBGet(DBQuery('SELECT '.db_seq_nextval('PEOPLE_SEQ').' as SEQ_ID '.FROM_DUAL));
                        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'PEOPLE'"));
                        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
			$id = $id[1]['ID'];

			$sql = "INSERT INTO PEOPLE ";

			$fields = '';
			$values = "";

			$go = 0;
			foreach($_REQUEST['values']['PEOPLE'] as $column=>$value)
			{
                            $value=paramlib_validation($column,$value);
				if($value)
				{
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			if($go)
			{
				DBQuery($sql);
				DBQuery("INSERT INTO STUDENTS_JOIN_PEOPLE (PERSON_ID,STUDENT_ID,ADDRESS_ID,CUSTODY,EMERGENCY) values('$id','".UserStudentID()."','".$get_data['ADDRESS_ID']."','".$_REQUEST['values']['STUDENTS_JOIN_PEOPLE']['CUSTODY']."','".$_REQUEST['values']['STUDENTS_JOIN_PEOPLE']['EMERGENCY']."')");
				$_REQUEST['person_id'] = $id;
			}
		}
	}

	if(clean_param($_REQUEST['values']['PEOPLE_JOIN_CONTACTS'],PARAM_NOTAGS))
	{
		foreach($_REQUEST['values']['PEOPLE_JOIN_CONTACTS'] as $id=>$values)
		{
			if($id!='new')
			{
				$sql = "UPDATE PEOPLE_JOIN_CONTACTS SET ";

				foreach($values as $column=>$value)
				{
					$sql .= $column."='".str_replace("\'","''",$value)."',";
				}
				$sql = substr($sql,0,-1) . " WHERE ID='$id'";
				DBQuery($sql);
			}
			else
			{
				if($info_apd || $values['TITLE'] && $values['TITLE']!='Example Phone' && $values['VALUE'] && $values['VALUE']!='(xxx) xxx-xxxx')
				{
					$sql = "INSERT INTO PEOPLE_JOIN_CONTACTS ";

					$fields = 'PERSON_ID,';
					$vals = "'$_REQUEST[person_id]',";

					$go = 0;
					foreach($values as $column=>$value)
					{
						if($value)
						{
							$fields .= $column.',';
							$vals .= "'".str_replace("\'","''",$value)."',";
							$go = true;
						}
					}
					$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($vals,0,-1) . ')';
					if($go)
						DBQuery($sql);
				}
			}
		}
	}

	if($_REQUEST['values']['STUDENTS_JOIN_PEOPLE'] && $_REQUEST['person_id']!='new')
	{
		$sql = "UPDATE STUDENTS_JOIN_PEOPLE SET ";

		foreach($_REQUEST['values']['STUDENTS_JOIN_PEOPLE'] as $column=>$value)
		{ 
                        $value=paramlib_validation($column,$value);
			$sql .= $column."='".str_replace("\'","''",$value)."',";
		}
		$sql = substr($sql,0,-1) . " WHERE PERSON_ID='$_REQUEST[person_id]' AND STUDENT_ID='".UserStudentID()."'";
		DBQuery($sql);
	}

	if($_REQUEST['values']['STUDENTS_JOIN_ADDRESS'] && $_REQUEST['address_id']!='new')
	{
		$sql = "UPDATE STUDENTS_JOIN_ADDRESS SET ";

		foreach($_REQUEST['values']['STUDENTS_JOIN_ADDRESS'] as $column=>$value)
		{
			$sql .= $column."='".str_replace("\'","''",$value)."',";
		}
		$sql = substr($sql,0,-1) . " WHERE ADDRESS_ID='$_REQUEST[address_id]' AND STUDENT_ID='".UserStudentID()."'";
		DBQuery($sql);
	}
############################Student Join People Address Same as ########################################
if($_REQUEST['r7']=='Y' && $_REQUEST['r7']!='N' && isset($_REQUEST['person_id']))
	{
		$get_data = DBGet(DBQuery("SELECT ADDRESS_ID,ADDRESS,STREET,CITY,STATE,ZIPCODE,BUS_NO,BUS_PICKUP,BUS_DROPOFF FROM ADDRESS WHERE STUDENT_ID='".UserStudentID()."'"));
		$get_data = $get_data[1];
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_ADDRESS='".$get_data['ADDRESS']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_STREET='".$get_data['STREET']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_CITY='".$get_data['CITY']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_STATE='".$get_data['STATE']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_ZIPCODE='".$get_data['ZIPCODE']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_BUS_PICKUP='".$get_data['BUS_PICKUP']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_BUS_DROPOFF='".$get_data['BUS_DROPOFF']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
		DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDN_BUSNO='".$get_data['BUS_NO']."' WHERE PERSON_ID='".$_REQUEST['person_id']."'");
	}
############################Student Join People Address Same as ########################################
	unset($_REQUEST['modfunc']);
	unset($_REQUEST['values']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	if($_REQUEST['contact_id'])
	{
		if(DeletePrompt('contact information'))
		{
			DBQuery("DELETE FROM PEOPLE_JOIN_CONTACTS WHERE ID='$_REQUEST[contact_id]'");
			unset($_REQUEST['modfunc']);
		}
	}
	elseif($_REQUEST['person_id'])
	{
		if(DeletePrompt('contact'))
		{
			DBQuery("DELETE FROM STUDENTS_JOIN_PEOPLE WHERE PERSON_ID='$_REQUEST[person_id]' AND STUDENT_ID='".UserStudentID()."'");
			if(count(DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS_JOIN_PEOPLE WHERE PERSON_ID='$_REQUEST[person_id]'")))==0)
			{
				DBQuery("DELETE FROM PEOPLE WHERE PERSON_ID='$_REQUEST[person_id]'");
				DBQuery("DELETE FROM PEOPLE_JOIN_CONTACTS WHERE PERSON_ID='$_REQUEST[person_id]'");
			}
			unset($_REQUEST['modfunc']);
			unset($_REQUEST['person_id']);
			if(!isset($_REQUEST['address_id']))
			{
				$stu_ad_id = DBGet(DBQuery("SELECT ADDRESS_ID FROM ADDRESS WHERE STUDENT_ID='".UserStudentID()."'"));
				$stu_ad_id = $stu_ad_id[1]['ADDRESS_ID'];
				if(count($stu_ad_id))
					$_REQUEST['address_id']=$stu_ad_id;
				else
					$_REQUEST['address_id']='new';
			}
		}
	}
	elseif($_REQUEST['address_id'])
	{
		if(DeletePrompt('address'))
		{
			DBQuery("UPDATE STUDENTS_JOIN_PEOPLE SET ADDRESS_ID='0' WHERE STUDENT_ID='".UserStudentID()."' AND ADDRESS_ID='$_REQUEST[address_id]'");
			DBQuery("DELETE FROM STUDENTS_JOIN_ADDRESS WHERE STUDENT_ID='".UserStudentID()."' AND ADDRESS_ID='".$_REQUEST['address_id']."'");
			if(count(DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS_JOIN_ADDRESS WHERE ADDRESS_ID='".$_REQUEST['address_id']."'")))==0)
				DBQuery("DELETE FROM ADDRESS WHERE ADDRESS_ID='".$_REQUEST['address_id']."'");
			unset($_REQUEST['modfunc']);
			$_REQUEST['address_id']='new';
		}
	}
}

if(!$_REQUEST['modfunc'])
{
	$addresses_RET = DBGet(DBQuery("SELECT a.ADDRESS_ID, sjp.STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.BUS_NO,a.BUS_PICKUP,a.BUS_DROPOFF,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_CUSTODY,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_CUSTODY,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE,  sjp.CUSTODY,sja.MAILING,sja.RESIDENCE FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja,STUDENTS_JOIN_PEOPLE sjp WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND sjp.STUDENT_ID=sja.STUDENT_ID" .
				  " UNION SELECT a.ADDRESS_ID,'' AS STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.BUS_NO,a.BUS_PICKUP,a.BUS_DROPOFF,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_CUSTODY,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_CUSTODY,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE,a.PRIM_CUSTODY AS CUSTODY,sja.MAILING,sja.RESIDENCE FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."' AND NOT EXISTS (SELECT '' FROM STUDENTS_JOIN_PEOPLE sjp WHERE sjp.STUDENT_ID=sja.STUDENT_ID AND sjp.ADDRESS_ID=a.ADDRESS_ID) ORDER BY CUSTODY ASC,STUDENT_RELATION"),array(),array('ADDRESS_ID'));
	if(count($addresses_RET)==1 && $_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='old' && $_REQUEST['address_id']!='0')
		$_REQUEST['address_id'] = key($addresses_RET);

	echo '<TABLE border=0><TR><TD valign=top>'; // table 1
	echo '<TABLE border=0><TR><TD valign=top>'; // table 2
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>'; // table 3
	if(count($addresses_RET)>0 || $_REQUEST['address_id']=='new' || $_REQUEST['address_id']=='0')
	{
		$i = 1;
		if(!isset($_REQUEST['address_id']))
			$_REQUEST['address_id'] = key($addresses_RET);

		if(count($addresses_RET))
		{
			foreach($addresses_RET as $address_id=>$addresses)
			{
				echo '<TR>';

				// find other students associated with this address
				$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME,RESIDENCE,BUS_PICKUP,BUS_DROPOFF,MAILING FROM STUDENTS s,STUDENTS_JOIN_ADDRESS sja WHERE s.STUDENT_ID=sja.STUDENT_ID AND sja.ADDRESS_ID='$address_id' AND sja.STUDENT_ID!='".UserStudentID()."'"));
				if(count($xstudents))
				{
					$warning = 'Other students associated with this address:<BR>';
					foreach($xstudents as $xstudent)
					{
						$ximages = '';
						if($xstudent['RESIDENCE']=='Y')
							$ximages .= ' <IMG SRC=assets/house_button.gif>';
						if($xstudent['BUS_PICKUP']=='Y' || $xstudent['BUS_DROPOFF']=='Y')
							$ximages .= ' <IMG SRC=assets/bus_button.gif>';
						if($xstudent['MAILING']=='Y')
							$ximages .= ' <IMG SRC=assets/mailbox_button.gif>';
						$warning .= '<b>'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['FULL_NAME']).'</b>'.$ximages.'<BR>';
					}
					echo '<TD>'.button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'').'</TD>';
				}
				else
					echo '<TD></TD>';

				$relation_list = '';
				foreach($addresses as $address)
					$relation_list .= ($address['STUDENT_RELATION']&&strpos($address['STUDENT_RELATION'].', ',$relation_list)==false?$address['STUDENT_RELATION']:'---').', ';
				$address = $addresses[1];
				$relation_list = substr($relation_list,0,-2);

				$images = '';
				if($address['RESIDENCE']=='Y')
					#$images .= ' <IMG SRC=assets/house_button.gif>';
				if($address['BUS_PICKUP']=='Y' || $address['BUS_DROPOFF']=='Y')
					#$images .= ' <IMG SRC=assets/bus_button.gif>';
				if($address['MAILING']=='Y')
					#$images .= ' <IMG SRC=assets/mailbox_button.gif>';
				echo '<TD colspan=2 style="border:0; border-style: none none solid none;"><B>'.$relation_list.'</B>'.($relation_list&&$images?'<BR>':'').$images.'</TD>';

				echo '</TR>';

				$style = '';
				if($i!=count($addresses_RET))
					$style = ' style="border:1; border-style: none none dashed none;"';
				elseif($i!=1)
					$style = ' style="border:1; border-style: dashed none none none;"';
				$style .= ' ';

				if($address_id==$_REQUEST['address_id'] && $_REQUEST['address_id']!='0' && $_REQUEST['address_id']!='new')
					$this_address = $address;

				$i++;
				$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$address['ADDRESS_ID'].'\';"';
				echo '</TD>';
				echo '<TD></TD>';
				echo '</TR>';
			}
			echo '<TR><TD colspan=3 height=40></TD></TR>';
		}
	}
	else
		echo '';
		
	############################################################################################
		
		$style = '';
		if($_REQUEST['person_id']=='new')
		{
			if($_REQUEST['address_id']!='new')
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'\';" ><TD>';
			else
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=new\';" ><TD>';
			echo '<A style="cursor:pointer"><b>Student\'s Address </b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=$_REQUEST[address_id]\';" onmouseover=\'this.style.color="white";\'><TD>';
			if($_REQUEST['person_id']==$contact['PERSON_ID'])
			echo '<A style="cursor:pointer;color:#FF0000"><b>Student\'s Address </b></A>';
			elseif($_REQUEST['person_id']!=$contact['PERSON_ID'])
			echo '<A style="cursor:pointer"><b>Student\'s Address </b></A>';
			else
			echo '<A style="cursor:pointer;color:#FF0000"><b>Student\'s Address </b></A>';
		}
		echo '</TD>';
		echo '<TD><A><IMG SRC=assets/arrow_right.gif></A></TD>';
		echo '</TR><tr><td colspan=2 class=break></td></tr>';
			
			
			$contacts_RET = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.ADDN_HOME_PHONE,sjp.ADDN_WORK_PHONE,sjp.ADDN_MOBILE_PHONE,sjp.ADDN_EMAIL,sjp.CUSTODY,sjp.ADDN_ADDRESS,sjp.ADDN_BUS_PICKUP,sjp.ADDN_BUS_DROPOFF,sjp.ADDN_BUSNO,sjp.ADDN_STREET,sjp.ADDN_CITY,sjp.ADDN_STATE,sjp.ADDN_ZIPCODE,sjp.EMERGENCY,sjp.STUDENT_RELATION FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."' ORDER BY sjp.STUDENT_RELATION"));
			$i = 1;
			if(count($contacts_RET))
			{
				foreach($contacts_RET as $contact)
				{
					$THIS_RET = $contact;
					if($contact['PERSON_ID']==$_REQUEST['person_id'])
						$this_contact = $contact;
					$style .= ' ';

					$i++;
					$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id='.$contact['PERSON_ID'].'&con_info=old\';"';
					if(AllowEdit())
						$remove_button = button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$contact[PERSON_ID]",20);
					else
						$remove_button = '';
					if($_REQUEST['person_id']==$contact['PERSON_ID'])
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.'>';
					else
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.' style=white-space:nowrap>';

					$images = '';

					// find other students associated with this person
					$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME,STUDENT_RELATION,CUSTODY,EMERGENCY FROM STUDENTS s,STUDENTS_JOIN_PEOPLE sjp WHERE s.STUDENT_ID=sjp.STUDENT_ID AND sjp.PERSON_ID='$contact[PERSON_ID]' AND sjp.STUDENT_ID!='".UserStudentID()."'"));
					if(count($xstudents))
					{
						$warning = 'Other students associated with this person:<BR>';
						foreach($xstudents as $xstudent)
						{
							$ximages = '';
							if($xstudent['CUSTODY']=='Y')
								$ximages .= ' <IMG SRC=assets/gavel_button.gif>';
							if($xstudent['EMERGENCY']=='Y')
								$ximages .= ' <IMG SRC=assets/emergency_button.gif>';
							$warning .= '<b>'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['FULL_NAME']).'</b> ('.($xstudent['STUDENT_RELATION']?str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['STUDENT_RELATION']):'---').')'.$ximages.'<BR>';
						}
						$images .= ' '.button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
					}

					if($contact['CUSTODY']=='Y')
						$images .= ' <IMG SRC=assets/gavel_button.gif>';
					if($contact['EMERGENCY']=='Y')
						$images .= ' <IMG SRC=assets/emergency_button.gif>';
if ($_REQUEST['person_id']==$contact['PERSON_ID']) {
					echo '<A style="cursor:pointer; font-weight:bold;color:#ff0000" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					} else {
					echo '<A style="cursor:pointer; font-weight:bold;" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					}
					echo '</TD>';
					echo '<TD valign=middle align=right> &nbsp; <A style="cursor: pointer;"><IMG SRC=assets/arrow_right.gif></A></TD>';
					echo '</TR></table></td></tr>';
				}
			}
	############################################################################################	
	
	// New Address
	if(AllowEdit())
	{
		if($_REQUEST['address_id']!=='new' && $_REQUEST['address_id']!=='old')
		{

			echo '<TABLE width=100%><TR><TD>';
			if($_REQUEST['address_id']==0)
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			else
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			// New Contact
			if(AllowEdit())
			{
				$style = 'class=break';
			}

			echo '</TABLE>';
		}

		if(clean_param($_REQUEST['person_id'],PARAM_ALPHAMOD)=='new')
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
			echo '<A style="cursor: pointer;color:#FF0000"><b>Add New Contact</b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
			echo '<A style="cursor: pointer;"><b>Add New Contact</b></A>';
		}
		echo '</TD>';
		echo '<TD><IMG SRC=assets/arrow_right.gif></TD>';
		echo '</TR>';

	}
	echo '</TABLE>';
	echo '</TD>';
	echo '<TD class=vbreak>&nbsp;</TD><TD valign=top>';

	if(isset($_REQUEST['address_id']) && $_REQUEST['con_info']!='old')
	{
		echo "<INPUT type=hidden name=address_id value=$_REQUEST[address_id]>";

		if($_REQUEST['address_id']!='0' && $_REQUEST['address_id']!=='old')
		{
			$query="SELECT ADDRESS_ID FROM STUDENTS_JOIN_ADDRESS WHERE STUDENT_ID='".UserStudentID()."'";
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];

			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
				$size = true;
			else
				$size = false;

			$city_options = _makeAutoSelect('CITY','ADDRESS',array(array('CITY'=>$this_address['CITY']),array('CITY'=>$this_address['MAIL_CITY'])),$city_options);
			$state_options = _makeAutoSelect('STATE','ADDRESS',array(array('STATE'=>$this_address['STATE']),array('STATE'=>$this_address['MAIL_STATE'])),$state_options);
			$zip_options = _makeAutoSelect('ZIPCODE','ADDRESS',array(array('ZIPCODE'=>$this_address['ZIPCODE']),array('ZIPCODE'=>$this_address['MAIL_ZIPCODE'])),$zip_options);

			echo '<TABLE width=100%><TR><TD>'; // open 3a
			echo '<FIELDSET><LEGEND><FONT color=gray>Student\'s Home Address</FONT></LEGEND><TABLE width=100%>';
			echo '<TR><td><span class=red>*</span>Address Line 1</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.TextInput($this_address['ADDRESS'],'values[ADDRESS][ADDRESS]','','class=cell_medium').'</td><td>';
			if($_REQUEST['address_id']!='0')
			{
				$display_address = urlencode($this_address['ADDRESS'].', '.($this_address['CITY']?' '.$this_address['CITY'].', ':'').$this_address['STATE'].($this_address['ZIPCODE']?' '.$this_address['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></TD></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_address['STREET'],'values[ADDRESS][STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>City</td><td>:</td><TD>'.TextInput($this_address['CITY'],'values[ADDRESS][CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>State</td><td>:</td><TD>'.TextInput($this_address['STATE'],'values[ADDRESS][STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_address['ZIPCODE'],'values[ADDRESS][ZIPCODE]','','class=cell_medium').'</TD></tr>';
			echo '<tr><TD>School Bus Pick-up</td><td>:</td><td>'.CheckboxInput($this_address['BUS_PICKUP'],'values[ADDRESS][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><TD>School Bus Drop-off</td><td>:</td><td>'.CheckboxInput($this_address['BUS_DROPOFF'],'values[ADDRESS][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><td>Bus No</td><td>:</td><td>'.TextInput($this_address['BUS_NO'],'values[ADDRESS][BUS_NO]','','class=cell_small').'</TD></tr>';
			echo '</TABLE></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; //close 3a

			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			{
				$new = true;
				$this_address['RESIDENCE'] = 'Y';
				$this_address['MAILING'] = 'Y';
				if($use_bus)
				{
					$this_address['BUS_PICKUP'] = 'Y';
					$this_address['BUS_DROPOFF'] = 'Y';
										
				}
			}
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3b
			echo '<FIELDSET><LEGEND><FONT color=gray>Student\'s Mailing Address</FONT></LEGEND>';
			
/*			$query="SELECT ADDRESS_ID FROM STUDENTS_JOIN_ADDRESS WHERE STUDENT_ID='".UserStudentID()."'";
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];
*/			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<table><TR><TD><span class=red>*</span><input type="radio" id="r4" name="r4" value="Y" onClick="hidediv();" checked>&nbsp;Same as Home Address &nbsp;&nbsp; <input type="radio" id="r4" name="r4" value="N" onClick="showdiv();">&nbsp;Add New Address</TD></TR></TABLE>'; 
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<div id="hideShow" style="display:none">';
			else
			echo '<div id="hideShow">';
			echo '<TABLE>';
			echo '<TR><td style=width:120px>Address Line 1</td><td>:</td><TD>'.TextInput($this_address['MAIL_ADDRESS'],'values[ADDRESS][MAIL_ADDRESS]','','class=cell_medium').'</TD>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_address['MAIL_STREET'],'values[ADDRESS][MAIL_STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_address['MAIL_CITY'],'values[ADDRESS][MAIL_CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_address['MAIL_STATE'],'values[ADDRESS][MAIL_STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_address['MAIL_ZIPCODE'],'values[ADDRESS][MAIL_ZIPCODE]','','class=cell_medium').'</TD></tr>';
			
			echo '</TABLE>';
			echo '</div>';

			echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3b
			
			
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3c
			echo '<FIELDSET><LEGEND><FONT color=gray>Primary Emergency Contact</FONT></LEGEND><TABLE width=100%><tr><td>';
			echo '<table border=0 width=100%>';
			echo '<tr><td style=width:120px><span class=red>*</span>Relationship to Student</TD><td>:</td><td>'._makeAutoSelectInputX($this_address['PRIM_STUDENT_RELATION'],'PRIM_STUDENT_RELATION','ADDRESS','',$relation_options).'</TD></tr>';
			echo '<TR><td><span class=red>*</span>First Name</td><td>:</td><TD>'.TextInput($this_address['PRI_FIRST_NAME'],'values[ADDRESS][PRI_FIRST_NAME]','','class=cell_medium').'</TD></tr>';
			
			echo '<TR><td><span class=red>*</span>Last Name</td><td>:</td><TD>'.TextInput($this_address['PRI_LAST_NAME'],'values[ADDRESS][PRI_LAST_NAME]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Home Phone</td><td>:</td><TD>'.TextInput($this_address['HOME_PHONE'],'values[ADDRESS][HOME_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Work Phone</td><td>:</td><TD>'.TextInput($this_address['WORK_PHONE'],'values[ADDRESS][WORK_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Cell/Mobile Phone</td><td>:</td><TD>'.TextInput($this_address['MOBILE_PHONE'],'values[ADDRESS][MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Email</td><td>:</td><TD>'.TextInput($this_address['EMAIL'],'values[ADDRESS][EMAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><TD>Custody of Student</TD><td>:</td><TD>'.CheckboxInput($this_address['PRIM_CUSTODY'],'values[ADDRESS][PRIM_CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';   
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><table><TR><TD><TD><span class=red>*</span><input type="radio" id="r5" name="r5" value="Y" onClick="prim_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="r5" name="r5" value="N" onClick="prim_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></td></tr>'; 
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><div id="prim_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=5><div id="prim_hideShow">';
			echo '<div class=break></div>';
			echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['PRIM_ADDRESS'],'values[ADDRESS][PRIM_ADDRESS]','','class=cell_medium').'</TD><td>';
			//if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			if($a_ID!=0)
			{
				$display_address = urlencode($this_address['PRIM_ADDRESS'].', '.($this_address['PRIM_CITY']?' '.$this_address['PRIM_CITY'].', ':'').$this_address['PRIM_STATE'].($this_address['PRIM_ZIPCODE']?' '.$this_address['PRIM_ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_address['PRIM_STREET'],'values[ADDRESS][PRIM_STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_address['PRIM_CITY'],'values[ADDRESS][PRIM_CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_address['PRIM_STATE'],'values[ADDRESS][PRIM_STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_address['PRIM_ZIPCODE'],'values[ADDRESS][PRIM_ZIPCODE]','','class=cell_medium').'</TD>';
			echo '</table>';
			echo '</div></td></tr>';

			echo '</table></td></tr></table></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3c
			
############################################################################################		
			echo '<TABLE border=0 width=100%><TR><TD>'; // open 3d
			echo '<FIELDSET><LEGEND><FONT color=gray>Secondary Emergency Contact</FONT></LEGEND><TABLE width=100%><tr><td>';
			
			echo '<table><tr><td style=width:120px><span class=red>*</span>Relationship to Student</td><td>:</td><TD>'._makeAutoSelectInputX($this_address['SEC_STUDENT_RELATION'],'SEC_STUDENT_RELATION','ADDRESS','',$relation_options).'</TD></tr>';
			echo '<TR><td><span class=red>*</span>First Name</td><td>:</td><TD>'.TextInput($this_address['SEC_FIRST_NAME'],'values[ADDRESS][SEC_FIRST_NAME]','','class=cell_medium').'</TD></tr>';
			
			
			echo '<TR><td><span class=red>*</span>Last Name</td><td>:</td><TD>'.TextInput($this_address['SEC_LAST_NAME'],'values[ADDRESS][SEC_LAST_NAME]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Home Phone</td><td>:</td><TD>'.TextInput($this_address['SEC_HOME_PHONE'],'values[ADDRESS][SEC_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Work Phone</td><td>:</td><TD>'.TextInput($this_address['SEC_WORK_PHONE'],'values[ADDRESS][SEC_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Cell/Mobile Phone</td><td>:</td><TD>'.TextInput($this_address['SEC_MOBILE_PHONE'],'values[ADDRESS][SEC_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Email</td><td>:</td><TD>'.TextInput($this_address['SEC_EMAIL'],'values[ADDRESS][SEC_EMAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><TD>Custody of Student</TD><td>:</td><TD>'.CheckboxInput($this_address['SEC_CUSTODY'],'values[ADDRESS][SEC_CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR></table>';
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><table><TR><TD><span class=red >*</span><input type="radio" id="r6" name="r6" value="Y" onClick="sec_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="r6" name="r6" value="N" onClick="sec_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></td></tr>';
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><div id="sec_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=3><div id="sec_hideShow">';
			echo '<div class=break></div>';
			echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['SEC_ADDRESS'],'values[ADDRESS][SEC_ADDRESS]','','class=cell_medium').'</TD><td>';
			//if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			if($a_ID!=0)
			{
				$display_address = urlencode($this_address['SEC_ADDRESS'].', '.($this_address['SEC_CITY']?' '.$this_address['SEC_CITY'].', ':'').$this_address['SEC_STATE'].($this_address['SEC_ZIPCODE']?' '.$this_address['SEC_ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_address['SEC_STREET'],'values[ADDRESS][SEC_STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_address['SEC_CITY'],'values[ADDRESS][SEC_CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_address['SEC_STATE'],'values[ADDRESS][SEC_STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_address['SEC_ZIPCODE'],'values[ADDRESS][SEC_ZIPCODE]','','class=cell_medium').'</TD>';
			echo '</TABLE>';
			echo '</div></td></tr></table></td></tr></table>';

			#echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>';  // close 3d
			
			
############################################################################################			
			
		}

	}
	else
		echo '';
		
	
	$separator = '<HR>';
}


if($_REQUEST['person_id'] && $_REQUEST['con_info']=='old')
{
			echo "<INPUT type=hidden name=person_id value=$_REQUEST[person_id]>";

			if($_REQUEST['person_id']!='old')
			{
				$relation_options = _makeAutoSelect('STUDENT_RELATION','STUDENTS_JOIN_PEOPLE',$this_contact['STUDENT_RELATION'],$relation_options);

				echo '<TABLE><TR><TD><FIELDSET><LEGEND><FONT color=gray>Additional Contact</FONT></LEGEND><TABLE width=100% border=0>'; // open 3e
				if($_REQUEST['person_id']!='new' && $_REQUEST['con_info']=='old')
				{
					echo '<TR><TD colspan=3><table><tr><td>'.CheckboxInput($this_contact['EMERGENCY'],'values[STUDENTS_JOIN_PEOPLE][EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD> This is an Emergency Contact</TD></TR></table></td></tr>';
					echo '<tr><td colspan=3 class=break></td></tr>';
					echo '<TR><TD>Name</td><td>:</td><td><DIV id=person_'.$this_contact['PERSON_ID'].'><div onclick=\'addHTML("<table><TR><TD>'.str_replace('"','\"',_makePeopleInput($this_contact['FIRST_NAME'],'FIRST_NAME','First')).'</TD><TD>'.str_replace('"','\"',_makePeopleInput($this_contact['LAST_NAME'],'LAST_NAME','Last')).'</TD></TR></TABLE>","person_'.$this_contact['PERSON_ID'].'",true);\'>'.$this_contact['FIRST_NAME'].' '.$this_contact['MIDDLE_NAME'].' '.$this_contact['LAST_NAME'].'</div></DIV></TD></TR>';
					echo '<TR><td style="width:120px">Relationship to Student</td><td>:</td><TD>'._makeAutoSelectInputX($this_contact['STUDENT_RELATION'],'STUDENT_RELATION','STUDENTS_JOIN_PEOPLE','',$relation_options).'</TD>';
					echo '<tr><TD>Home Phone</td><td>:</td><td> '.TextInput($this_contact['ADDN_HOME_PHONE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Work Phone</td><td>:</td><td>'.TextInput($this_contact['ADDN_WORK_PHONE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Mobile Phone</td><td>:</td><td> '.TextInput($this_contact['ADDN_MOBILE_PHONE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Email </td><td>:</td><td>'.TextInput($this_contact['ADDN_EMAIL'],'values[STUDENTS_JOIN_PEOPLE][ADDN_EMAIL]','','class=cell_medium').'</TD></tr>';
					echo '<TR><TD>Custody</TD><td>:</td><TD>'.CheckboxInput($this_contact['CUSTODY'],'values[STUDENTS_JOIN_PEOPLE][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';
					echo '<tr><td colspan=3 class=break></td></tr>';	
					echo '<tr><td style="width:120px">Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_contact['ADDN_ADDRESS'],'values[STUDENTS_JOIN_PEOPLE][ADDN_ADDRESS]','','class=cell_medium').'</TD><td>';
					if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
					{
						$display_address = urlencode($this_contact['ADDN_ADDRESS'].', '.($this_contact['ADDN_CITY']?' '.$this_contact['ADDN_CITY'].', ':'').$this_contact['ADDN_STATE'].($this_contact['ADDN_ZIPCODE']?' '.$this_contact['ADDN_ZIPCODE']:''));
						$link = 'http://google.com/maps?q='.$display_address;
						echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
					}
					echo '</td></tr></table></td></tr>';
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STREET'],'values[STUDENTS_JOIN_PEOPLE][ADDN_STREET]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_contact['ADDN_CITY'],'values[STUDENTS_JOIN_PEOPLE][ADDN_CITY]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STATE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_STATE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_contact['ADDN_ZIPCODE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_ZIPCODE]','','class=cell_medium').'</TD></tr>';	
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_PICKUP'],'values[STUDENTS_JOIN_PEOPLE][ADDN_BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_DROPOFF'],'values[STUDENTS_JOIN_PEOPLE][ADDN_BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>Bus No</td><td>:</td><TD>'.TextInput($this_contact['ADDN_BUSNO'],'values[STUDENTS_JOIN_PEOPLE][ADDN_BUSNO]','','class=cell_small').'</TD></tr>';
					echo '</table>';
					$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM PEOPLE_JOIN_CONTACTS WHERE PERSON_ID='$_REQUEST[person_id]'"));
					if($info_apd)
						$info_options = _makeAutoSelect('TITLE','PEOPLE_JOIN_CONTACTS',$info_RET,$info_options_x);

					echo '<TR><TD>';
					echo '<TABLE border=0 cellpadding=3 cellspacing=0>';
					if(!$info_apd)
					{
						echo '<TR><TD style="border-color: #BBBBBB; border: 1; border-style: none none solid none;"></TD><TD style="border-color: #BBBBBB; border: 1; border-style: none solid solid none;"><font color=gray>Description</font> &nbsp; </TD><TD style="border-color: #BBBBBB; border: 1; border-style: none none solid none;"><font color=gray>Value</font></TD></TR>';
						if(count($info_RET))
						{
							foreach($info_RET as $info)
							{
							echo '<TR>';
							if(AllowEdit())
								echo '<TD width=20>'.button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$_REQUEST[person_id]&contact_id=".$info['ID']).'</TD>';
							else
								echo '<TD></TD>';
							if($info_apd)
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'._makeAutoSelectInputX($info['TITLE'],'TITLE','PEOPLE_JOIN_CONTACTS','',$info_options,$info['ID']).'</TD>';
							else
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'.TextInput($info['TITLE'],'values[PEOPLE_JOIN_CONTACTS]['.$info['ID'].'][TITLE]','','maxlength=100').'</TD>';
							echo '<TD>'.TextInput($info['VALUE'],'values[PEOPLE_JOIN_CONTACTS]['.$info['ID'].'][VALUE]','','maxlength=100').'</TD>';
							echo '</TR>';
							}
						}
						if(AllowEdit() && $use_contact)
						{
							echo '<TR>';
							echo '<TD width=20>'.button('add').'</TD>';
							if($info_apd)
							{
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'.(count($info_options)>1?SelectInput('','values[PEOPLE_JOIN_CONTACTS][new][TITLE]','',$info_options,'N/A'):TextInput('','values[PEOPLE_JOIN_CONTACTS][new][TITLE]','')).'</TD>';
								echo '<TD>'.TextInput('','values[PEOPLE_JOIN_CONTACTS][new][VALUE]','').'</TD>';
							}
							else
							{
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;"><INPUT size=15 type=TEXT value="Example Phone" style="color: #BBBBBB;" name=values[PEOPLE_JOIN_CONTACTS][new][TITLE] '."onfocus='if(this.value==\"Example Phone\") this.value=\"\"; this.style.color=\"000000\";' onblur='if(this.value==\"\") {this.value=\"Example Phone\"; this.style.color=\"BBBBBB\";}'></TD>";
								echo '<TD><INPUT size=15 type=TEXT value="(xxx) xxx-xxxx" style="color: #BBBBBB;" name=values[PEOPLE_JOIN_CONTACTS][new][VALUE] '."onfocus='if(this.value==\"(xxx) xxx-xxxx\") this.value=\"\"; this.style.color=\"000000\";' onblur='if(this.value==\"\") {this.value=\"(xxx) xxx-xxxx\"; this.style.color=\"BBBBBB\";}'></TD>";
							}
							echo '</TR>';
						}
					}
					else
					{
						if(count($info_RET))
						{
							foreach($info_RET as $info)
							{
								echo '<TR>';
								if(AllowEdit())
									echo '<TD width=20>'.button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$_REQUEST[person_id]&contact_id=".$info['ID']).'</TD>';
								else
									echo '<TD></TD>';
								echo '<TD><DIV id=info_'.$info['ID'].'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput($info['VALUE'],'values[PEOPLE_JOIN_CONTACTS]['.$info['ID'].'][VALUE]','','',false).'<BR>'.str_replace("'",'&#39;',_makeAutoSelectInputX($info['TITLE'],'TITLE','PEOPLE_JOIN_CONTACTS','',$info_options,$info['ID'],false))).'</TD></TR></TABLE>","info_'.$info['ID'].'",true);\'>'.$info['VALUE'].'<BR><small><FONT color='.($info_options_x[$info['TITLE']]?Preferences('TITLES'):'blue').'>'.$info['TITLE'].'</FONT></small></div></DIV></TD>';
								echo '</TR>';
							}
						}
						if(AllowEdit() && $use_contact)
						{
							echo '<TR>';
							echo '</TR>';
						}
					}
					echo '</TABLE>';
					echo '</TD></TR>';
					echo '</TABLE>';
					#echo '</FIELDSET>';
					echo '</TD></TR>';
					echo '</TABLE>'; // close 3e
					

				}
				else
				{
					echo '<TABLE border=0><TR><TD colspan=3><table><tr><td>'.CheckboxInput($this_contact['EMERGENCY'],'values[STUDENTS_JOIN_PEOPLE][EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD>This is an Emergency Contact</TD></TR></table></TD></TR><tr><td colspan=3 class=break></td></tr>';	
					echo '<TR><td style="width:120px" style=white-space:nowrap><span class=red>*</span>Relationship to Student</td><td>:</td><TD>'.SelectInput($this_contact['STUDENT_RELATION'],'values[STUDENTS_JOIN_PEOPLE][STUDENT_RELATION]','',$relation_options,'N/A').'</TD></TR>';
					echo '<TR><TD><span class=red>*</span>First Name</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput('','FIRST_NAME','','class=cell_medium')).'</TD></tr><tr><td ><span class=red>*</span>Last Name</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput($this_contact['LAST_NAME'],'LAST_NAME','','class=cell_medium')).'</TD></TR>';
					echo '<tr><TD>Home Phone</td><td>:</td><td> '.TextInput($this_contact['ADDN_HOME_PHONE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Work Phone</td><td>:</td><td>'.TextInput($this_contact['ADDN_WORK_PHONE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Mobile Phone</td><td>:</td><td> '.TextInput($this_contact['ADDN_MOBILE_PHONE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Email </td><td>:</td><td>'.TextInput($this_contact['ADDN_EMAIL'],'values[STUDENTS_JOIN_PEOPLE][ADDN_EMAIL]','','class=cell_medium').'</TD></tr>';
					echo '<TR><TD>Custody of Student</td><td>:</td><TD>'.CheckboxInput($this_contact['CUSTODY'],'values[STUDENTS_JOIN_PEOPLE][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'<small><FONT color='.Preferences('TITLES').'></FONT></small></TD></TR>';
					echo '<TR><TD colspan=3><table><TR><TD style=white-space:nowrap><span class=red>*</span><input type="radio" id="r7" name="r7" value="Y" onClick="addn_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="r7" name="r7" value="N" onClick="addn_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></TD></TR>';
					echo '<TR><TD colspan=3><div id="addn_hideShow" style="display:none">';
					echo '<div class=break></div>';
					echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD>'.TextInput($this_address['ADDN_ADDRESS'],'values[STUDENTS_JOIN_PEOPLE][ADDN_ADDRESS]','','class=cell_medium').'</TD></td>';
					
					#echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['SEC_ADDRESS'],'values[ADDRESS][SEC_ADDRESS]','','class=cell_medium').'</TD><td>';
					
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STREET'],'values[STUDENTS_JOIN_PEOPLE][ADDN_STREET]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_contact['ADDN_CITY'],'values[STUDENTS_JOIN_PEOPLE][ADDN_CITY]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STATE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_STATE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_contact['ADDN_ZIPCODE'],'values[STUDENTS_JOIN_PEOPLE][ADDN_ZIPCODE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_PICKUP'],'values[STUDENTS_JOIN_PEOPLE][ADDN_BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_DROPOFF'],'values[STUDENTS_JOIN_PEOPLE][ADDN_BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><td>Bus No</TD><td>:</td><td>'.TextInput($this_contact['ADDN_BUSNO'],'values[STUDENTS_JOIN_PEOPLE][ADDN_BUSNO]','','class=cell_small').'</TD></tr>';
					echo '</table></div></td></tr></table>';
					echo '</FIELDSET>';
					echo '</TD></TR>';
					echo '</TABLE>';
				}
				
				
			}
			elseif($_REQUEST['person_id']=='old')
			{
				$people_RET = DBGet(DBQuery("SELECT PERSON_ID,FIRST_NAME,LAST_NAME FROM PEOPLE WHERE PERSON_ID NOT IN (SELECT PERSON_ID FROM STUDENTS_JOIN_PEOPLE WHERE STUDENT_ID='".UserStudentID()."') ORDER BY LAST_NAME,FIRST_NAME"));
				foreach($people_RET as $people)
					$people_select[$people['PERSON_ID']] = $people['LAST_NAME'].', '.$people['FIRST_NAME'];
				echo SelectInput('','values[EXISTING][person_id]',$title='Select Person',$people_select);
			}
			
			if($_REQUEST['person_id']=='new') {
		echo '</TD></TR>';
		echo '</TABLE>'; // end of table 2
		}
		unset($_REQUEST['address_id']);
		unset($_REQUEST['person_id']);
		}
		
	echo '</TD></TR>';
	echo '</TABLE>'; // end of table 1

	

function _makePeopleInput($value,$column,$title='',$options='')
{	global $THIS_RET;

	if($column=='MIDDLE_NAME')
		$options = 'class=cell_medium';
	if($_REQUEST['person_id']=='new')
		$div = false;
	else
		$div = true;

	if($column=='STUDENT_RELATION')
		$table = 'STUDENTS_JOIN_PEOPLE';
	else
		$table = 'PEOPLE';

	return TextInput($value,"values[$table][$column]",$title,$options,false);
}

function _makeAutoSelect($column,$table,$values='',$options=array())
{
	$options_RET = DBGet(DBQuery("SELECT DISTINCT $column,upper($column) AS `KEY` FROM $table ORDER BY `KEY`"));

	// add the 'new' option, is also the separator
	$options['---'] = '---';
	// add values already in table
	if(count($options_RET))
		foreach($options_RET as $option)
			if($option[$column]!='' && !$options[$option[$column]])
				$options[$option[$column]] = array($option[$column],'<FONT color=blue>'.$option[$column].'</FONT>');
	// make sure values are in the list
	if(is_array($values))
	{
		foreach($values as $value)
			if($value[$column]!='' && !$options[$value[$column]])
				$options[$value[$column]] = array($value[$column],'<FONT color=blue>'.$value[$column].'</FONT>');
	}
	else
		if($values!='' && !$options[$values])
			$options[$values] = array($values,'<FONT color=blue>'.$values.'</FONT>');

	return $options;
}

function _makeAutoSelectInputX($value,$column,$table,$title,$select,$id='',$div=true)
{
	if($column=='CITY' || $column=='MAIL_CITY')
		$options = 'maxlength=60';
	if($column=='STATE' || $column=='MAIL_STATE')
		$options = 'size=3 maxlength=10';
	elseif($column=='ZIPCODE' || $column=='MAIL_ZIPCODE')
		$options = 'maxlength=10';
	else
		$options = 'maxlength=100';

	if($value!='---' && count($select)>1)
		return SelectInput($value,"values[$table]".($id?"[$id]":'')."[$column]",$title,$select,'N/A','',$div);
	else
		return TextInput($value=='---'?array('---','<FONT color=red>---</FONT>'):$value,"values[$table]".($id?"[$id]":'')."[$column]",$title,$options,$div);
}
?>