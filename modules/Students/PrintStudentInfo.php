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
if($_REQUEST['modfunc']=='save')
{
    
    /* $b=$_REQUEST['category'];
	 foreach($b as $key=>$val)
	 {
	 $key;
	 
	 }*/
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
	

	//$extra['functions'] = array('GRADE_ID'=>'_grade_id');
	if($_REQUEST['mailing_labels'] == 'Y')
				
	Widgets('mailing_labels');

    $RET = GetStuList($extra);
  
	if(count($RET))
	{
		include('modules/Students/includes/functions.php');
		//------------Comment Heading -----------------------------------------------------
		//$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"),array(),array('ID'));

		// get the address and contacts custom fields, create the select lists and expand select and codeds options
		$address_categories_RET = DBGet(DBQuery("SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,c.RESIDENCE,c.MAILING,c.BUS,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM ADDRESS_FIELD_CATEGORIES c,ADDRESS_FIELDS f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE"),array(),array('CATEGORY_ID'));
		$people_categories_RET = DBGet(DBQuery("SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,c.CUSTODY,c.EMERGENCY,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM PEOPLE_FIELD_CATEGORIES c,PEOPLE_FIELDS f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE"),array(),array('CATEGORY_ID'));
		explodeCustom($address_categories_RET, $address_custom, 'a');
		explodeCustom($people_categories_RET, $people_custom, 'p');

		unset($_REQUEST['modfunc']);
		$handle = PDFStart();

		foreach($RET as $student)
		{
			$_SESSION['student_id'] = $student['STUDENT_ID'];
                                                        echo "<table width=100% style=\" font-family:Arial; font-size:12px;\" >";
                                                                                echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Information Report</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";

                                                        echo "<table cellspacing=0  border=\"0\" style=\"border-collapse:collapse\">";
                                                                                echo "<tr><td colspan=3 style=\"height:18px\"></td></tr>";
                                                        if($StudentPicturesPath && (($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG','r')) || ($file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.UserStudentID().'.JPG','r'))))
                                                        {
                                                                                echo '<tr><td width=300><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150  style="padding:4px; background-color:#fff; border:1px solid #333" ></td><td width=12px></td>';
                                                        }
                                                        else
                                                        {
                                                        echo '<tr><td width=300><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144  style="padding:4px; background-color:#fff; border:1px solid #333"></td><td width=12px></td>';
                                                        }

                                                        fclose($file);

                                                        #$sql=DBGet(DBQuery("SELECT s.CUSTOM_200000000 AS GENDER, s.CUSTOM_200000001 AS ETHNICITY, s.CUSTOM_200000002 AS COMMON_NAME,  s.CUSTOM_200000003 AS SOCIAL_SEC_NO, s.CUSTOM_200000004 AS BIRTHDAY, s.CUSTOM_200000005 AS LANGUAGE, s.CUSTOM_200000006 AS PHYSICIAN_NAME, s.CUSTOM_200000007 AS PHYSICIAN_PHONO,s.custom_200000008 AS HOSPITAL,se.START_DATE AS START_DATE,sec.TITLE AS STATUS, se.NEXT_SCHOOL AS ROLLING  FROM STUDENTS s, STUDENT_ENROLLMENT se,STUDENT_ENROLLMENT_CODES sec WHERE s.STUDENT_ID='".$_SESSION['student_id']."' AND s.STUDENT_ID=se.STUDENT_ID AND se.SYEAR=sec.SYEAR"));

                                                        # ---------------- Sql Including Comment ------------------------------- #

                                                        #$sql=DBGet(DBQuery("SELECT s.CUSTOM_200000000 AS GENDER, s.CUSTOM_200000001 AS ETHNICITY, s.CUSTOM_200000002 AS COMMON_NAME,  s.CUSTOM_200000003 AS SOCIAL_SEC_NO, s.CUSTOM_200000004 AS BIRTHDAY, s.CUSTOM_200000005 AS LANGUAGE, s.CUSTOM_200000006 AS PHYSICIAN_NAME, s.CUSTOM_200000007 AS PHYSICIAN_PHONO,s.custom_200000008 AS HOSPITAL,se.START_DATE AS START_DATE,sec.TITLE AS STATUS, se.NEXT_SCHOOL AS ROLLING, smc.comment AS COMMENT  FROM STUDENTS s, STUDENT_ENROLLMENT se,STUDENT_ENROLLMENT_CODES sec, STUDENT_MP_COMMENTS smc WHERE s.STUDENT_ID='".$_SESSION['student_id']."' AND s.STUDENT_ID=se.STUDENT_ID AND s.STUDENT_ID=smc.STUDENT_ID AND se.SYEAR=sec.SYEAR"));


                                                        #$sql=DBGet(DBQuery("SELECT s.CUSTOM_200000000 AS GENDER, s.CUSTOM_200000001 AS ETHNICITY, s.CUSTOM_200000002 AS COMMON_NAME,  s.CUSTOM_200000003 AS SOCIAL_SEC_NO, s.CUSTOM_200000004 AS BIRTHDAY, s.CUSTOM_200000005 AS LANGUAGE, s.CUSTOM_200000006 AS PHYSICIAN_NAME, s.CUSTOM_200000007 AS PHYSICIAN_PHONO,s.custom_200000008 AS HOSPITAL,s.custom_200000009 AS MCOMNT,s.custom_200000011 AS DNOTE,se.START_DATE AS START_DATE,sec.TITLE AS STATUS, se.NEXT_SCHOOL AS ROLLING, smc.comment AS COMMENT  FROM STUDENTS s, STUDENT_ENROLLMENT se,STUDENT_ENROLLMENT_CODES sec, STUDENT_MP_COMMENTS smc WHERE s.STUDENT_ID='".$_SESSION['student_id']."' AND s.STUDENT_ID=se.STUDENT_ID AND s.STUDENT_ID=smc.STUDENT_ID AND se.SYEAR=sec.SYEAR"));

                                                        
                                                            $sql=DBGet(DBQuery("SELECT s.gender AS GENDER, s.ethnicity AS ETHNICITY, s.common_name AS COMMON_NAME,  s.social_security AS SOCIAL_SEC_NO, s.birthdate AS BIRTHDAY, s.email AS EMAIL, s.phone AS PHONE, s.language AS LANGUAGE, s.physician AS PHYSICIAN_NAME, s.physician_phone AS PHYSICIAN_PHONO,s.preferred_hospital AS HOSPITAL,se.START_DATE AS START_DATE,sec.TITLE AS STATUS, se.NEXT_SCHOOL AS ROLLING  FROM STUDENTS s, STUDENT_ENROLLMENT se,STUDENT_ENROLLMENT_CODES sec WHERE s.STUDENT_ID='".$_SESSION['student_id']."' AND s.STUDENT_ID=se.STUDENT_ID AND se.SYEAR=sec.SYEAR"),array('BIRTHDAY'=>'ProperDate'));


                                                        $sql = $sql[1];

                                                        $medical_note=DBGet(DBQuery("SELECT doctors_note_date AS MCOMNT,doctors_note_comments AS DNOTE FROM STUDENT_MEDICAL_NOTES WHERE  STUDENT_ID='".$_SESSION['student_id']."' "),array('MCOMNT'=>'ProperDate'));
                                                        unset($_openSIS['DrawHeader']);

                                                        echo "<td valign=top width=300>";



                                                        echo "<table width=100% ><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px;  font-weight:bold;\">Personal Information</td></tr>";

                                                    if($_REQUEST['category']['1'])
                                                    {
			 
			//----------------------------------------------
			echo "<tr><td width=45% style='font-weight:bold'>Student Name:</td>";
			echo "<td width=55%>" .$student['FULL_NAME']. "</td></tr>";
			echo "<tr><td style='font-weight:bold'>ID:</td>";
			echo "<td>". $student['STUDENT_ID'] ." </td></tr>";
			if($student['ALT_ID']!='')
			{
				echo "<tr><td style='font-weight:bold'>Alt ID:</td>";
				echo "<td>". $student['ALT_ID'] ." </td></tr>";
			}
			echo "<tr><td style='font-weight:bold'>Grade:</td>";
			echo "<td>". $student['GRADE_ID'] ." </td></tr>";
			echo "<tr><td style='font-weight:bold'>Gender:</td>";
			echo "<td>".$sql['GENDER'] ."</td></tr>";
			echo "<tr><td style='font-weight:bold'>Ethnicity:</td>";
			echo "<td>".$sql['ETHNICITY'] ."</td></tr>";
			if($sql['COMMON_NAME'] !='')
			{
			echo "<tr><td style='font-weight:bold'>Common Name:</td>";
			echo "<td>".$sql['COMMON_NAME'] ."</td></tr>";
			}
			if($sql['SOCIAL_SEC_NO'] !='')
			{
			echo "<tr><td style='font-weight:bold'>Social Security:</td>";
			echo "<td>".$sql['SOCIAL_SEC_NO'] ."</td></tr>";
			}
			echo "<tr><td style='font-weight:bold'>Birth Date:</td>";
			echo "<td>".$sql['BIRTHDAY'] ."</td></tr>";
			if($sql['LANGUAGE'] !='')
			{
			echo "<tr><td style='font-weight:bold'>Language Spoken:</td>";
			echo "<td>".$sql['LANGUAGE'] ."</td></tr>";
			
			}
                        if($sql['EMAIL'] !='')
			{
			echo "<tr><td style='font-weight:bold'>Email ID:</td>";
			echo "<td>".$sql['EMAIL'] ."</td></tr>";
			
			}
                        if($sql['PHONE'] !='')
			{
			echo "<tr><td style='font-weight:bold'>Phone:</td>";
			echo "<td>".$sql['PHONE'] ."</td></tr>";
			echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
			}
			if($sql['ROLLING'] !='' && $sql['ROLLING']!=0 && $sql['ROLLING']!=-1)

			{

			$rolling=DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID='".$sql['ROLLING']."'"));

			$rolling=$rolling[1]['TITLE'];

			}

			elseif($sql['ROLLING']!=0)

			$rolling = 'Do not enroll after this school year';

			elseif($sql['ROLLING']!=-1)

			$rolling = 'Retain';

			/*echo "<tr><td style='font-weight:bold'>Rolling / Retention Options:</td>";

			echo "<td valign=top>".$rolling ."</td></tr>";*/

           if($student['MAILING_LABEL'] !='')

			{

			echo "<tr>";

			echo "<td colspan=2>".$student['MAILING_LABEL']."</td></tr>";

			}
			//----------------------------------------------
			}
			
                           ######################## PRINT MEDICAL CUSTOM FIELDS ################################################
                                $fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED FROM CUSTOM_FIELDS WHERE CATEGORY_ID='1' ORDER BY SORT_ORDER,TITLE"));
                        	$custom_RET = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	                        $value = $custom_RET[1];
                                if(count($fields_RET))
                                {

                                    $i = 1;
                                    foreach($fields_RET as $field)
                                    {
                                      if(($value['CUSTOM_'.$field['ID']])!='')
                                      {
                                      echo '<TR>';
                                      echo '<td style="font-weight:bold">'.$field['TITLE'].':</td><td>';
                                      if($field['TYPE']=='date'){
                                          $cust_date=DBGet(DBQuery("SELECT CUSTOM_$field[ID] AS C_DATE FROM STUDENTS WHERE STUDENT_ID=".UserStudentID()),array('C_DATE'=>'ProperDate'));
                                          echo $cust_date[1]['C_DATE'];
                                      }else{
                                          echo $value['CUSTOM_'.$field['ID']];
                                      }
                                      echo '</TD>';
                                      echo '</TR>';
                                      }
                                    }
                                }
                              ####################################################################################################
echo '</table>';
echo "</td></tr>";
echo "<tr><td colspan=3 height=18px></td></tr>";
echo "<tr><td valign=top width=300>";
  if($_REQUEST['category']['3'])
   {
	  $addresses_RET = DBGet(DBQuery("SELECT a.ADDRESS_ID,             sjp.STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,    sjp.CUSTODY,sja.MAILING,sja.RESIDENCE$address_custom FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja,STUDENTS_JOIN_PEOPLE sjp WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND sjp.STUDENT_ID=sja.STUDENT_ID
				  UNION SELECT a.ADDRESS_ID,'No Contacts' AS STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,NULL AS CUSTODY,sja.MAILING,sja.RESIDENCE$address_custom FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja                          WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."' AND NOT EXISTS (SELECT '' FROM STUDENTS_JOIN_PEOPLE sjp WHERE sjp.STUDENT_ID=sja.STUDENT_ID AND sjp.ADDRESS_ID=a.ADDRESS_ID) ORDER BY ADDRESS ASC,CUSTODY ASC,STUDENT_RELATION"));
			$address_previous = "x";
			foreach($addresses_RET as $address)
			{
				$address_current = $address['ADDRESS'];
				if($address_current != $address_previous)
				{
				echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px; font-weight:bold;\">Home Address</td></tr>";
				echo "<tr><td width=45% style='font-weight:bold'>Address1:</td>";
				echo "<td width=55%>".$address['ADDRESS']."</td></tr>";
				if($address['STREET']!='')
     			{
				echo "<tr><td width=35% style='font-weight:bold'>Address2:</td>";
				echo "<td width=65%>".$address['STREET']."</td></tr>";
				}
				echo "<tr><td style='font-weight:bold'>City:</td>";
				echo"<td>".($address['CITY']?$address['CITY'].'':'')."</td></tr>";
				echo "<tr><td style='font-weight:bold'>State:</td>";
				echo"<td>".$address['STATE']."</td></tr>";
				echo "<tr><td style='font-weight:bold'>Zipcode:</td>";
				echo"<td>".($address['ZIPCODE']?$address['ZIPCODE'].'':'')."</td></tr>";
				echo "</table>";

				echo "</td><td></td><td valign=top width=300>";
				echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px;  font-weight:bold;\">Mailing Address</td></tr>";
				echo "<tr><td width=45% style='font-weight:bold'>Address1:</td>";
				echo"<td width=55%>".$address['MAIL_ADDRESS']."</td></tr>";
				if($address['MAIL_STREET']!='')
     			{
				echo "<tr><td width=35% style='font-weight:bold'>Address2:</td>";
				echo "<td width=65%>".$address['MAIL_STREET']."</td></tr>";
				}
				echo "<tr><td style='font-weight:bold'>City:</td>";
				echo"<td>".$address['MAIL_CITY']."</td></tr>";
				echo "<tr><td style='font-weight:bold'>State:</td>";
				echo"<td>".$address['MAIL_STATE']."</td></tr>";
				echo "<tr><td style='font-weight:bold'>Zipcode:</td>";
				echo"<td>".$address['MAIL_ZIPCODE']."</td></tr>";
				echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
				echo "</table>";
				echo "</td></tr>";
				echo "<tr><td valign=top>";

	foreach($address_categories_RET as $categories)
			{
			   if(!$categories[1]['RESIDENCE']&&!$categories[1]['MAILING']&&!$categories[1]['BUS'] || $categories[1]['RESIDENCE']=='Y'&&$address['RESIDENCE']=='Y' || $categories[1]['MAILING']=='Y'&&$address['MAILING']=='Y' || $categories[1]['BUS']=='Y'&&($address['BUS_PICKUP']=='Y'||$address['BUS_DROPOFF']=='Y'))
							printCustom($categories,$address);

			}

				echo "<table width=100% border=0><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px;  font-weight:bold;\">Primary Emergency Contact</td></tr>";

					#$contacts_RET = DBGet(DBQuery("SELECT p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,sjp.ADDN_HOME_PHONE,sjp.ADDN_WORK_PHONE,sjp.ADDN_MOBILE_PHONE,sjp.ADDN_EMAIL FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."'"));

					$contacts_RET = DBGet(DBQuery("SELECT a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."'"));
                   
					#$contacts_RET = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.CUSTODY,sjp.EMERGENCY,sjp.STUDENT_RELATION$people_custom FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."' AND sjp.ADDRESS_ID='".$address['ADDRESS_ID']."'"));
					foreach($contacts_RET as $contact)
					{


					  	echo "<tr><td width=45% style='font-weight:bold'>Relation :</td><td width=55%>".$contact['PRIM_STUDENT_RELATION']."</td></tr>";

						echo "<tr><td style='font-weight:bold'>First Name :</td><td>".$contact['PRI_FIRST_NAME']."</td></tr>";
						echo "<tr><td style='font-weight:bold'>Last Name :</td><td>".$contact['PRI_LAST_NAME']."</td></tr>";
					   if($contact['HOME_PHONE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Home Phone :</td><td>".$contact['HOME_PHONE']."</td></tr>";
						 }
						 if($contact['WORK_PHONE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Work Phone :</td><td>".$contact['WORK_PHONE']."</td></tr>";
						 }
						 if($contact['MOBILE_PHONE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Mobile Phone :</td><td>".$contact['MOBILE_PHONE']."</td></tr>";
						 }
						 if($contact['EMAIL']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Email :</td><td>".$contact['EMAIL']."</td></tr>";
						 }

						 if($contact['PRIM_ADDRESS']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Address1 :</td><td>".$contact['PRIM_ADDRESS']."</td></tr>";
						 }

						 if($contact['PRIM_STREET']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Address2 :</td><td>".$contact['PRIM_STREET']."</td></tr>";
						 }

						 if($contact['PRIM_CITY']!='')
						{
						     echo "<tr><td style='font-weight:bold'>City :</td><td>".$contact['PRIM_CITY']."</td></tr>";
						 }

						 if($contact['PRIM_STATE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>State :</td><td>".$contact['PRIM_STATE']."</td></tr>";
						 }

						 if($contact['PRIM_ZIPCODE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Zipcode :</td><td>".$contact['PRIM_ZIPCODE']."</td></tr>";
						 }
						echo "</table>";

						#echo "<tr><td colspan=2>".$contact['FIRST_NAME'].' '.($contact['MIDDLE_NAME']?$contact['MIDDLE_NAME'].' ':'').$contact['LAST_NAME'].($contact['STUDENT_RELATION']?': '.$contact['STUDENT_RELATION']:'')."</td></tr>";

					#$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM PEOPLE_JOIN_CONTACTS WHERE PERSON_ID='".$contact['PERSON_ID']."'"));
					#echo $s = "SELECT p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,sjp.ADDN_HOME_PHONE,sjp.ADDN_WORK_PHONE,sjp.ADDN_MOBILE_PHONE,sjp.ADDN_EMAIL FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."'";

echo "</td><td></td><td valign=top>";

						echo "<table width=100% border=0><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px;  font-weight:bold;\">Secondary Emergency Contact</td></tr>";

					  	echo "<tr><td width=45% style='font-weight:bold'>Relation :</td><td width=55%>".$contact['SEC_STUDENT_RELATION']."</td></tr>";	     			echo "<tr><td style='font-weight:bold'>First Name :</td><td>".$contact['SEC_FIRST_NAME']."</td></tr>";
						echo "<tr><td style='font-weight:bold'>Last Name :</td><td>".$contact['SEC_LAST_NAME']."</td></tr>";
					   if($contact['SEC_HOME_PHONE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Home Phone :</td><td>".$contact['SEC_HOME_PHONE']."</td></tr>";
						 }
						 if($contact['SEC_WORK_PHONE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Work Phone :</td><td>".$contact['SEC_WORK_PHONE']."</td></tr>";
						 }
						 if($contact['SEC_MOBILE_PHONE']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Mobile Phone :</td><td>".$contact['SEC_MOBILE_PHONE']."</td></tr>";
						 }
						 if($contact['SEC_EMAIL']!='')
						{
						     echo "<tr><td style='font-weight:bold'>Email :</td><td>".$contact['SEC_EMAIL']."</td></tr>";
						 }

						 if($contact['SEC_ADDRESS']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Address1 :</td><td>".$contact['SEC_ADDRESS']."</td></tr>";
						  }

						  if($contact['SEC_STREET']!='')
						  {
						     echo "<tr><td style='font-weight:bold'>Address2 :</td><td>".$contact['SEC_STREET']."</td></tr>";
						   }

						  if($contact['SEC_CITY']!='')
						  {
						     echo "<tr><td style='font-weight:bold'>City :</td><td>".$contact['SEC_CITY']."</td></tr>";
						   }

						    if($contact['SEC_STATE']!='')
						   {
						     echo "<tr><td style='font-weight:bold'>State :</td><td>".$contact['SEC_STATE']."</td></tr>";
						    }

						   if($contact['SEC_ZIPCODE']!='')
						   {
						     echo "<tr><td style='font-weight:bold'>Zipcode :</td><td>".$contact['SEC_ZIPCODE']."</td></tr>";
						    }

							echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
					  echo "</table>";
echo "</td></tr>";

echo "<tr><td valign=top>";
$info_RET = DBGet(DBQuery("SELECT p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,sjp.ADDN_HOME_PHONE,sjp.ADDN_WORK_PHONE,sjp.ADDN_MOBILE_PHONE,sjp.ADDN_EMAIL,sjp.ADDN_ADDRESS,sjp.ADDN_STREET,sjp.ADDN_CITY,sjp.ADDN_STATE,sjp.ADDN_ZIPCODE FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."'"));

					if($info_RET[1]['STUDENT_RELATION']!='')
					{
					  echo '<table width=100%>';
					  echo "<tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px; font-weight:bold;\">Additional Contact</td></tr>";
						foreach($info_RET as $info)
						{

						 echo "<tr><td width=45% style='font-weight:bold'>Relation :</td><td width=55%>".$info['STUDENT_RELATION']."</td></tr>";
						 echo "<tr><td style='font-weight:bold'>First Name :</td><td>".$info['FIRST_NAME']."</td></tr>";
						 if($info['MIDDLE_NAME']!='')
						 {
					   		 echo "<tr><td style='font-weight:bold'>Middle Name :</td><td>".$info['MIDDLE_NAME']."</td></tr>";
					   	 }
					  	echo "<tr><td style='font-weight:bold'>Last Name :</td><td>".$info['LAST_NAME']."</td></tr>";
					    if($info['ADDN_HOME_PHONE']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Home Phone :</td><td>".$info['ADDN_HOME_PHONE']."</td></tr>";
						 }
						 if($info['ADDN_WORK_PHONE']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Work Phone :</td><td>".$info['ADDN_WORK_PHONE']."</td></tr>";
						 }
						 if($info['ADDN_MOBILE_PHONE']!='')
						 {
						    echo "<tr><td style='font-weight:bold'>Mobile Phone :</td><td>".$info['ADDN_MOBILE_PHONE']."</td></tr>";
						 }
						 if($info['ADDN_EMAIL']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Email :</td><td>".$info['ADDN_EMAIL']."</td></tr>";
						 }

						 if($info['ADDN_ADDRESS']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Address1 :</td><td>".$info['ADDN_ADDRESS']."</td></tr>";
						 }

						if($info['ADDN_STREET']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Address2 :</td><td>".$info['ADDN_STREET']."</td></tr>";
						 }

						if($info['ADDN_CITY']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>City :</td><td>".$info['ADDN_CITY']."</td></tr>";
						 }

						if($info['ADDN_STATE']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>State :</td><td>".$info['ADDN_STATE']."</td></tr>";
						 }

						 if($info['ADDN_ZIPCODE']!='')
						 {
						     echo "<tr><td style='font-weight:bold'>Zipcode :</td><td>".$info['ADDN_ZIPCODE']."</td></tr>";
						 }

							/*echo '<tr><td style='font-weight:bold'>'.$info['TITLE'].'</td>';
							echo '<td>'.$info['VALUE'].'</td></tr>';*/
							 echo "<tr><td colspan=2 style=\"border-bottom:1px dashed #999999;\">&nbsp;</td></tr>";
							 echo "<tr><td colspan=2 style=\"height:5px;\">&nbsp;</td></tr>";
						}
                        echo "</table>";
						}
echo "</td><td></td><td valign=top>";
echo "</td></tr>";
echo "<tr><td valign=top colspan=3>";
	foreach($people_categories_RET as $categories)
							if(!$categories[1]['CUSTODY']&&!$categories[1]['EMERGENCY'] || $categories[1]['CUSTODY']=='Y'&&$contact['CUSTODY']=='Y' || $categories[1]['EMERGENCY']=='Y'&&$contact['EMERGENCY']=='Y')
								printCustom($categories,$contact);

					}
				}
				$address_previous = $address_current;
			}

			$contacts_RET2 = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.CUSTODY,sjp.EMERGENCY,sjp.STUDENT_RELATION$people_custom FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."' AND sjp.ADDRESS_ID='0'"));
			foreach($contacts_RET2 as $contact)
			{
				echo '<B>'.$contact['FIRST_NAME'].' '.($contact['MIDDLE_NAME']?$contact['MIDDLE_NAME'].' ':'').$contact['LAST_NAME'].($contact['STUDENT_RELATION']?': '.$contact['STUDENT_RELATION']:'').' &nbsp;</B>';
				$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM PEOPLE_JOIN_CONTACTS WHERE PERSON_ID='".$contact['PERSON_ID']."'"));
				foreach($info_RET as $info)
				{
					echo '<TR>';
					echo '<TD>'.$info['TITLE'].'</TD>';
					echo '<TD>'.$info['VALUE'].'</TD>';
					echo '</TR>';
				}

				foreach($people_categories_RET as $categories)
					if(!$categories[1]['CUSTODY']&&!$categories[1]['EMERGENCY'] || $categories[1]['CUSTODY']=='Y'&&$contact['CUSTODY']=='Y' || $categories[1]['EMERGENCY']=='Y'&&$contact['EMERGENCY']=='Y')
						printCustom($categories,$contact);
				//echo '</TABLE>';

			}
			#echo '<BR>&nbsp;<BR>';
 #echo '</td><td></td><td></td></tr></table></TABLE><div style="page-break-before: always;">&nbsp;</div>';

			}

			if($_REQUEST['category']['2'] && ($sql['PHYSICIAN_NAME'] !='' || $sql['PHYSICIAN_PHONO'] !='' || $sql['HOSPITAL'] !=''))
			{
                            
                                //------------------------------------------------------------------------------
				#echo "<br>";
				#echo $_REQUEST['category']['2']!='Y'
				echo "<table width='100%'><tr><td style=\"border-bottom:1px solid #333;  font-size:14px; font-weight:bold;\">Medical Information</td></tr></table>";
				echo "</td><td></td><td valign=top>";
				echo "</td></tr>";
				echo "<tr><td valign=top colspan=3>";
				echo "<table width='100%'><tr><td colspan=\"2\" style=\"border-bottom:1px solid #9a9a9a; font-weight:bold; color:4a4a4a; font-size:12px;\">General Information</td></tr>
				<tr><td colspan=2 style=\"height:5px;\"></td></tr>";
				if($sql['PHYSICIAN_NAME'] !='')
				{
				echo "<tr><td width=21% style='font-weight:bold'>Physician Name:</td>";
				echo "<td width=79%>".$sql['PHYSICIAN_NAME'] ."</td></tr>";
				}
				if($sql['PHYSICIAN_PHONO'] !='')
				{
				echo "<tr><td style='font-weight:bold'>Physicians Phone:</td>";
				echo "<td>".$sql['PHYSICIAN_PHONO'] ."</td></tr>";
				}
				if($sql['HOSPITAL'] !='')
				{
				echo "<tr><td style='font-weight:bold'>Hospital Name:</td>";
				echo "<td>".$sql['HOSPITAL'] ."</td></tr>";
				}

                                foreach($medical_note as $medical)
                                {
				if($medical['MCOMNT'] !='')
				{
				echo "<tr><td valign='top' style='font-weight:bold'>Date:</td>";
				echo "<td align='justify'>".$medical['MCOMNT']."</td></tr>";
				}
				if($medical['DNOTE'] !='')
				{
				echo "<tr><td valign='top' style='font-weight:bold'>Doctor's Note:</td>";
				echo "<td align='justify'>".$medical['DNOTE'] ."</td></tr>";
				}
                                }
				echo '</table>';

					//DrawHeader($categories_RET['2'][1]['TITLE']);
					//include('modules/Students/includes/Medical.inc.php');
				########################################################################
                                $fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED FROM CUSTOM_FIELDS WHERE CATEGORY_ID='2' ORDER BY SORT_ORDER,TITLE"));
                        	$custom_RET = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	                        $value = $custom_RET[1];
                                if(count($fields_RET))
                                {
                                    echo '<TABLE cellpadding=5>';
                                    $i = 1;
                                    foreach($fields_RET as $field)
                                    {
                                      if(($value['CUSTOM_'.$field['ID']])!='')
                                      {
                                      echo '<TR>';
                                      echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
                                      echo _makeTextInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
                                      echo '</TD>';
                                      echo '</TR>';
                                      }
                                    }
                               ######################## PRINT MEDICAL CUSTOM FIELDS ################################################
                                $fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED FROM CUSTOM_FIELDS WHERE CATEGORY_ID='2' ORDER BY SORT_ORDER,TITLE"));
                        	$custom_RET = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	                        $value = $custom_RET[1];
                                if(count($fields_RET))
                                {

                                    $i = 1;
                                    foreach($fields_RET as $field)
                                    {
                                      if(($value['CUSTOM_'.$field['ID']])!='')
                                      {
                                      echo '<TR>';
                                      echo "<td style='font-weight:bold'>".$field['TITLE'].':</td><td>';
                                      echo $value['CUSTOM_'.$field['ID']];
                                      echo '</TD>';
                                      echo '</TR>';
                                      }
                                    }


                                }
                                echo '</table>';
                              ####################################################################################################

                                }
 #############################################################################
                               echo '<!-- NEW PAGE -->';
			}

			echo "</td></tr>";
			echo "<tr><td valign=top colspan=3>";

			# ---------------------------------- Immunization/Physical Record ---------------- #

			$res_immunization = DBGet(DBQuery("SELECT TYPE,MEDICAL_DATE,COMMENTS FROM STUDENT_MEDICAL WHERE student_id='".$_SESSION['student_id']."'"),array('MEDICAL_DATE'=>'ProperDate'));
			if($_REQUEST['category']['2'] && count($res_immunization) >= 1)
			{
				//------------------------------------------------------------------------------
				#echo "<br>";
				#echo $_REQUEST['category']['2']!='Y'

				echo "<table width=100%>
				<tr><td colspan=2 style=\"border-bottom:1px solid #9a9a9a; font-weight:bold; color:4a4a4a; font-size:12px;\">Immunization / Physical Record</td></tr>
				<tr><td colspan=2 style=\"height:5px;\"></td></tr>";

				foreach($res_immunization as $row_immunization)
                                                                        {
					if($row_immunization['TYPE'] !='')
					{
					echo "<tr><td width=21% style='font-weight:bold'>Type:</td>";
					echo "<td width=79%>".$row_immunization['TYPE'] ."</td></tr>";
					}
					if($row_immunization['MEDICAL_DATE'] !='')
					{
					echo "<tr><td style='font-weight:bold'>Date:</td>";
					echo "<td>".$row_immunization['MEDICAL_DATE'] ."</td></tr>";
					}
					if($row_immunization['COMMENTS'] !='')
					{
					echo "<tr><td valign='top' style='font-weight:bold'>Comments:</td>";
					echo "<td align='justify'>".$row_immunization['COMMENTS'] ."</td></tr>";
					}
					echo "<tr><td colspan=2 style=\"border-bottom:1px dashed #999999;\">&nbsp;</td></tr>";
				echo "<tr><td colspan=2 style=\"height:5px;\">&nbsp;</td></tr>";
				}

				echo '</table>';


					//DrawHeader($categories_RET['2'][1]['TITLE']);
					//include('modules/Students/includes/Medical.inc.php');
					echo '<!-- NEW PAGE -->';
			}
			echo "</td></tr>";
			echo "<tr><td valign=top colspan=3>";



# ---------------------------------- Medical Alert ---------------- #

			$res_alert = DBGet(DBQuery("SELECT TITLE,ALERT_DATE FROM STUDENT_MEDICAL_ALERTS WHERE student_id='".$_SESSION['student_id']."'"),array('ALERT_DATE'=>'ProperDate'));
			if($_REQUEST['category']['2'] && count($res_alert) >= 1)
			{
				//------------------------------------------------------------------------------
				#echo "<br>";
				#echo $_REQUEST['category']['2']!='Y'
				echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #9a9a9a; font-weight:bold; color:4a4a4a; font-size:12px;\">Medical Alert</td></tr>
				<tr><td colspan=2 style=\"height:5px;\"></td></tr>";

				foreach($res_alert as $row_alert)
				{
					if($row_alert['TITLE'] !='')
					{
					echo "<tr><td width=21% style='font-weight:bold'>Medical Alert:</td>";
					echo "<td width=79% align='justify'>".$row_alert['TITLE'] ."</td></tr>";
					}
                                                                                        if($row_alert['ALERT_DATE'] !='')
					{
					echo "<tr><td width=21% style='font-weight:bold'>Date:</td>";
					echo "<td width=79% align='justify'>".$row_alert['ALERT_DATE'] ."</td></tr>";
					}
				 echo "<tr><td colspan=2 style=\"border-bottom:1px dashed #999999;\">&nbsp;</td></tr>";

				}
				echo '</table>';

					//DrawHeader($categories_RET['2'][1]['TITLE']);
					//include('modules/Students/includes/Medical.inc.php');
					echo '<!-- NEW PAGE -->';
			}
			echo "</td></tr>";
			echo "<tr><td valign=top colspan=3>";


# ---------------------------------- Nurse Visit Record ---------------- #

			$res_visit =DBGet(DBQuery("SELECT SCHOOL_DATE,TIME_IN,TIME_OUT,REASON,RESULT,COMMENTS FROM STUDENT_MEDICAL_VISITS WHERE student_id='".$_SESSION['student_id']."'"),array('SCHOOL_DATE'=>'ProperDate'));

			if($_REQUEST['category']['2'] && count($res_visit) >= 1)
			{
				//------------------------------------------------------------------------------
				#echo "<br>";
				#echo $_REQUEST['category']['2']!='Y'
				echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #9a9a9a; font-weight:bold; color:4a4a4a; font-size:12px;\">Nurse Visit Record</td></tr>
				<tr><td colspan=2 style=\"height:5px;\"></td></tr>";

				foreach($res_visit as $row_visit)
				{
					if($row_visit['SCHOOL_DATE'] !='')
					{
					echo "<tr><td width=21% style='font-weight:bold'>Date:</td>";
					echo "<td width=79%>".$row_visit['SCHOOL_DATE'] ."</td></tr>";
					}
					if($row_visit['TIME_IN'] !='')
					{
					echo "<tr><td style='font-weight:bold'>Time In:</td>";
					echo "<td>".$row_visit['TIME_IN'] ."</td></tr>";
					}
					if($row_visit['TIME_OUT'] !='')
					{
					echo "<tr><td style='font-weight:bold'>Time Out:</td>";
					echo "<td>".$row_visit['TIME_OUT'] ."</td></tr>";
					}
					if($row_visit['REASON'] !='')
					{
					echo "<tr><td style='font-weight:bold'>Reason:</td>";
					echo "<td>".$row_visit['REASON'] ."</td></tr>";
					}
					if($row_visit['RESULT'] !='')
					{
					echo "<tr><td style='font-weight:bold'>Result:</td>";
					echo "<td>".$row_visit['RESULT'] ."</td></tr>";
					}
					if($row_visit['COMMENTS'] !='')
					{
					echo "<tr><td valign='top' style='font-weight:bold'>Comments:</td>";
					echo "<td align='justify'>".$row_visit['COMMENTS'] ."</td></tr>";
					}
				 echo "<tr><td colspan=2 style=\"border-bottom:1px dashed #999999;\">&nbsp;</td></tr>";
				 echo "<tr><td colspan=2 style=\"height:5px;\">&nbsp;</td></tr>";
				}
				echo '</table>';

					//DrawHeader($categories_RET['2'][1]['TITLE']);
					//include('modules/Students/includes/Medical.inc.php');
					echo '<!-- NEW PAGE -->';
			}
			echo "</td></tr>";

			echo "<tr><td valign=top colspan=3>";

                       #  if(User('PROFILE')=='admin')
                        $res_comment = DBGet(DBQuery("SELECT ID,COMMENT_DATE,COMMENT,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME)AS USER_NAME FROM STUDENT_MP_COMMENTS,STAFF s WHERE STUDENT_ID='".$_SESSION['student_id']."'  AND s.STAFF_ID=STUDENT_MP_COMMENTS.STAFF_ID"),array('COMMENT_DATE'=>'ProperDate'));
                        #else
                       # $sql_comment = DBGet(DBQuery("SELECT ID,COMMENT_DATE,COMMENT,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME)AS USER_NAME FROM STUDENT_MP_COMMENTS,STAFF s WHERE STUDENT_ID='".$_SESSION['student_id']."' AND STUDENT_MP_COMMENTS.SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."' AND s.STAFF_ID=STUDENT_MP_COMMENTS.STAFF_ID"));
			//$sql_comment = "select * from STUDENT_MP_COMMENTS where student_id='".$_SESSION['student_id']."'";
                            foreach($res_comment as $row_comment)
                            {
                                if($_REQUEST['category']['4'] && $row_comment['COMMENT'] != '')
                                {
                                        #DrawHeader($categories_RET['4'][1]['TITLE']);
                                        //include('modules/Students/includes/Comments.inc.php');
                                        echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px; font-weight:bold;\">Comment</td></tr>";
                                         if($row_comment['USER_NAME'] !='')
                                        {
                                        echo "<tr><td width=21% valign='top' style='font-weight:bold'>Entered by:</td>";
                                        echo "<td width=79% align=justify>".$row_comment['USER_NAME'] ."</td></tr>";
                                        }
                                        if($row_comment['COMMENT_DATE'] !='')
                                        {
                                        echo "<tr><td width=21% valign='top' style='font-weight:bold'>Date:</td>";
                                        echo "<td width=79% align=justify>".$row_comment['COMMENT_DATE'] ."</td></tr>";
                                        }
                                        if($row_comment['COMMENT'] !='')
                                        {
                                        echo "<tr><td width=21% valign='top' style='font-weight:bold'>Comment:</td>";
                                        echo "<td width=79% align=justify>".$row_comment['COMMENT'] ."</td></tr>";
                                        }

                                        echo '</table>';

                                        echo '<!-- NEW PAGE -->';
                                }
                            }

				echo "</td></tr>";
				echo "<tr><td colspan=3 valign=top>";
                
                //===NEWLY ADDED====================================================================================
                $cus_RET = DBGet(DBQuery("SELECT sfc.ID,cf.ID as ID1,cf.TITLE, sfc.TITLE AS TITLE1, cf.TYPE, cf.SELECT_OPTIONS, cf.DEFAULT_SELECTION, cf.REQUIRED
                FROM CUSTOM_FIELDS AS cf, STUDENT_FIELD_CATEGORIES AS sfc
                WHERE sfc.ID = cf.CATEGORY_ID
                AND sfc.ID != '1'
                AND sfc.ID != '2'
                AND sfc.ID != '3'
                AND sfc.ID != '4'
                AND sfc.ID != '5'
                GROUP BY cf.category_id
                ORDER BY cf.ID"));
                
                //$fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TITLE,sfc.TITLE as TITLE1,cf.TYPE,cf.SELECT_OPTIONS,cf.DEFAULT_SELECTION,cf.REQUIRED FROM CUSTOM_FIELDS as cf,STUDENT_FIELD_CATEGORIES as sfc WHERE sfc.ID=cf.CATEGORY_ID and sfc.ID != '1' and sfc.ID != '2' and sfc.ID != '3' and sfc.ID != '4'  and sfc.ID != '5' ORDER BY cf.SORT_ORDER,cf.TITLE "));
                foreach($cus_RET as $cus)
                {
                
                $fields_RET = DBGet(DBQuery("SELECT ID,TITLE FROM CUSTOM_FIELDS where CATEGORY_ID='".$cus['ID']."'"));
                $b=$cus['ID'];
                if($_REQUEST['category'][$b])
                     {
                	
                	 $custom_RET = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
                	
                	 $value = $custom_RET[1];
                	 echo "<table width=100% >";
                 
                   
                									  							 
                  if(($value['CUSTOM_'.$cus['ID1']])!='')
                     {                           
                
                 echo "<tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px;  font-weight:bold;\">".$cus['TITLE1']."</td></tr>";
                     }
                  
                
                                        	
                							
                							    if(count($fields_RET))
                                                {
                								
                                      
                                                    $i = 1;
                                                    foreach($fields_RET as $field)
                                                    {
                									  							 
                                                      if(($value['CUSTOM_'.$field['ID']])!='')
                                                      {
                									  $date =DBGet(DBQuery("SELECT type,id FROM CUSTOM_FIELDS WHERE ID='".$field['ID']."'")); 
                									
                									  foreach($date as $da)		
                									  {
                									  if($da['TYPE']=='date')
                									  {
                									  $sql= DBGet(DBQuery("SELECT CUSTOM_".$da['ID']." as DATE FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"),array('DATE'=>'ProperDate'));
                									  foreach($sql as $sq)
                									  {
                									  echo '<TR>';
                                                      echo '<td width=125px style="font-weight:bold">'.$field['TITLE'].':</td>';
                									  echo '<td class=cell_medium>'.$sq['DATE'].'';
                                                    
                                                      echo '</TD>';
                                                      echo '</TR>';
                									  }
                									  }
                									  else
                									  {
                                                      echo '<TR>';
                                                      echo '<td width=125px style="font-weight:bold">'.$field['TITLE'].':</td><td>';
                                                      echo _makeTextInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
                                                      echo '</TD>';
                                                      echo '</TR>';
                                                      }
                									  }
                									  }
                                                   
                							     
                							        }
                							   
                				   
                							     }
                								 
                echo "</TABLE>";
                		
                     }
                	 
                }						
                //===NEWLY ADDED====================================================================================
                
                echo "</td><tr>";
                echo "</table>";
                
				echo '<div style="page-break-before: always;">&nbsp;</div>';
			foreach($categories_RET as $id=>$category)
			{
				if($id!='1' && $id!='3' && $id!='2' && $id!='4' && $_REQUEST['category'][$id])
				{
					$_REQUEST['category_id'] = $id;
					//DrawHeader($category[1]['TITLE']);
					$separator = '';
					if(!$category[1]['INCLUDE'])
						include('modules/Students/includes/Other_Info.inc.php');
					elseif(!strpos($category[1]['INCLUDE'],'/'))
						include('modules/Students/includes/'.$category[1]['INCLUDE'].'.inc.php');
					else
					{
						include('modules/'.$category[1]['INCLUDE'].'.inc.php');
						$separator = '<HR>';
						//include('modules/Students/includes/Other_Info.inc.php');
					}

				}
			}
		

		}
		
		
	
	
            
	
		PDFStop($handle);
	
	
	}
	else
		BackPrompt('No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student.');
	unset($_SESSION['student_id']);
	//echo '<pre>'; var_dump($_REQUEST['modfunc']); echo '</pre>';
	$_REQUEST['modfunc']=true;
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]&_openSIS_PDF=true method=POST target=_blank>";
		//$extra['header_right'] = '<INPUT type=submit value=\'Print Info for Selected Students\'>';

		$extra['extra_header_left'] = '<TABLE>';
		//Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '';

		if(User('PROFILE_ID'))
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));
		$extra['extra_header_left'] .= '';
		foreach($categories_RET as $category)
			//if($can_use_RET['Students/Student.php&category_id='.$category['ID']] && $category['ID']!=5)
			if($can_use_RET['Students/Student.php&category_id='.$category['ID']])
			{
			$extra['extra_header_left'] .= '<TR><TD align="right" style="white-space:nowrap">'.$category['TITLE'].'</td>';
				$extra['extra_header_left'] .= '<td><INPUT type=checkbox name=category['.$category['ID'].'] value=Y checked></TD></TR>';

			}
		$extra['extra_header_left'] .= '</TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Widgets('mailing_labels');
	Widgets('course');
	Widgets('request');
	Widgets('activity');
	Widgets('absences');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');
	Widgets('eligibility');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Print Info for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

// GetStuList by default translates the grade_id to the grade title which we don't want here.
// One way to avoid this is to provide a translation function for the grade_id so here we
// provide a passthru function just to avoid the translation.
function _grade_id($value)
{
	return $value;
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function explodeCustom(&$categories_RET, &$custom, $prefix)
{
	foreach($categories_RET as $id=>$category)
		foreach($category as $i=>$field)
		{
			$custom .= ','.$prefix.'.CUSTOM_'.$field['ID'];
			if($field['TYPE']=='select' || $field['TYPE']=='codeds')
			{
				$select_options = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$select_options = explode("\r",$select_options);
				$options = array();
				foreach($select_options as $option)
				{
					if($field['TYPE']=='codeds')
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							$options[$option[0]] = $option[1];
					}
					else
						$options[$option] = $option;
				}
				$categories_RET[$id][$i]['SELECT_OPTIONS'] = $options;
			}
		}
}

function printCustom(&$categories, &$values)
{
	echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-size:14px;  font-weight:bold;\">".$categories[1]['CATEGORY_TITLE']."</td></tr>";
	foreach($categories as $field)
	{
		echo '<TR>';
		echo '<TD>'.($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'<FONT color=red>':'').$field['TITLE'].($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'</FONT>':'').'</TD>';
		if($field['TYPE']=='select')
			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'<FONT color=red>').$values['CUSTOM_'.$field['ID']].($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'</FONT>').'</TD>';
		elseif($field['TYPE']=='codeds')
			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?$field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]:'<FONT color=red>'.$values['CUSTOM_'.$field['ID']].'</FONT>').'</TD>';
		else
			echo '<TD>'.$values['CUSTOM_'.$field['ID']].'</TD>';
		echo '</TR>';
	}
	echo '</table>';
}

?>
