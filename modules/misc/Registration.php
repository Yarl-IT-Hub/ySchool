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
if(!UserStudentID())
{
	$_SESSION['UserSyear'] = Config('SYEAR');
	$RET = DBGet(DBQuery("SELECT sju.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,se.SCHOOL_ID FROM STUDENTS s,STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));
	$_SESSION['student_id'] = $RET[1]['STUDENT_ID'];
}

$_openSIS['allow_edit'] = true;

$_REQUEST['values']['STUDENTS']['BIRTHDATE'] = $_REQUEST['day_birth_date'].'-'.$_REQUEST['month_birth_date'].'-'.$_REQUEST['year_birth_date'];
unset($_REQUEST['day_birth_date']); unset($_REQUEST['month_birth_date']); unset($_REQUEST['year_birth_date']);
if(!VerifyDate($_REQUEST['values']['STUDENTS']['BIRTHDATE']))
	unset($_REQUEST['values']['STUDENTS']['BIRTHDATE']);


if($_REQUEST['values'])
{
	if($_REQUEST['values']['ADDRESS'])
	{
		foreach($_REQUEST['values']['ADDRESS'] as $key=>$columns)
		{
			if($columns['ADDRESS'] && !$inserted_addresses[ereg_replace('[^0-9A-Za-z]+','',strtolower($columns['ADDRESS']))])
			{
				/*
				$address_RET = DBGet(DBQuery("SELECT ".db_seq_nextval('ADDRESS_SEQ').' AS ADDRESS_ID '.FROM_DUAL));
				$address_id[$key] = $address_RET[1]['ADDRESS_ID'];
				if($key==1)
					$address_id[2] = $address_RET[1]['ADDRESS_ID'];
				$sql = "INSERT INTO ADDRESS ";

				$fields = 'ADDRESS_ID,';
				$values = $address_id[$key].',';
				*/
				
				$sql = "INSERT INTO ADDRESS ";

				$fields = '';
				$values = '';

				if($columns['ADDRESS'])
					$columns += PrepareAddress($columns['ADDRESS']);
				$columns['PHONE'] = substr(ereg_replace('[^0-9]+','',$columns['PHONE']),0,7);

				unset($address['ADDRESS']);
				$go = 0;
				foreach($columns as $column=>$value)
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
					DBQuery("INSERT INTO STUDENTS_JOIN_ADDRESS (STUDENT_ID,ADDRESS_ID) values('".UserStudentID()."','".$address_id[$key]."')");
				}
				$inserted_addresses[ereg_replace('[^0-9A-Za-z]+','',strtolower($columns['ADDRESS']))] = $address_id[$key];
			}
			else
				$address_id[$key] = $inserted_addresses[ereg_replace('[^0-9A-Za-z]+','',strtolower($columns['ADDRESS']))];
		}
	}

	if($_REQUEST['values']['PEOPLE'])
	{
		foreach($_REQUEST['values']['PEOPLE'] as $key=>$person)
		{
			if($person['FIRST_NAME'] && $person['LAST_NAME'])
			{
                            $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'PEOPLE'"));
                                      $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
				//$person_id = DBGet(DBQuery("SELECT ".db_seq_nextval('PEOPLE_SEQ').' AS PERSON_ID '.FROM_DUAL));
				$person_id = $id[1]['ID'];


				if($key==1 || $key==2)
					$person['extra']['Relation'] = 'Parent';
				elseif($key>=3 && $key<=6)
					$person['extra']['Relation'] = 'Grandparent';

				foreach($person['extra'] as $column=>$value)
				{
					if($value)
					{
						$value = str_replace("\'","''",$value);
						$sql = "INSERT INTO PEOPLE_JOIN_CONTACTS ";
						$fields = 'PERSON_ID,TITLE,VALUE,';
						$values = "'".$person_id."','$column','$value',";
						$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
						DBQuery($sql);
					}
				}

				unset($person['extra']);

				$sql = "INSERT INTO PEOPLE ";
				$fields = '';
				$values = "";
				$go = 0;
				foreach($person as $column=>$value)
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
					if($key==1 || $key==2)
						DBQuery("INSERT INTO STUDENTS_JOIN_PEOPLE (STUDENT_ID,PERSON_ID,ADDRESS_ID,CUSTODY) values('".UserStudentID()."','".$person_id."','".$address_id[$key]."','Y')");
					elseif($address_id[$key])
						DBQuery("INSERT INTO STUDENTS_JOIN_PEOPLE (STUDENT_ID,PERSON_ID,ADDRESS_ID) values('".UserStudentID()."','".$person_id."','".$address_id[$key]."')");
					else
						DBQuery("INSERT INTO STUDENTS_JOIN_PEOPLE (STUDENT_ID,PERSON_ID,ADDRESS_ID,EMERGENCY) values('".UserStudentID()."','".$person_id."','".$address_id[1]."','Y')");
				}
			}
		}
	}

	if($_REQUEST['values']['STUDENTS'])
	{
		$sql = "UPDATE STUDENTS SET ";
		foreach($_REQUEST['values']['STUDENTS'] as $column_name=>$value)
		{
			$sql .= "$column_name='".str_replace("\'","''",$value)."',";
		}

		$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='".UserStudentID()."'";
		DBQuery($sql);
	}

	$student = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	mail('mgamson@tampabay.rr.com','New Registration',$student[1]['FIRST_NAME'].' '.$student[1]['LAST_NAME'].' ('.UserStudentID().') has been registered by '.User('NAME').'.');
	unset($_SESSION['_REQUEST_vars']['values']);
}
echo '<H4>Welcome, '.User('NAME').', to the '.$openSISTitle.'</H4>';
$addresses = DBGet(DBQuery("SELECT COUNT(*) AS COUNT FROM STUDENTS_JOIN_ADDRESS WHERE STUDENT_ID='".UserStudentID()."'"));
echo '<small>We would appreciate it if you would enter just a little bit of information about you and your child to help us out this school year.  Thanks!</small>';
if($addresses[1]['COUNT']!=0)
	echo '<BR><BR><IMG SRC=assets/check.gif><b>Your child has been registered.</b>';
echo '<BR><BR><TABLE><TR><TD valign=top>';
echo '<B>Information about you:</B><BR><BR>';
echo '<TABLE cellpadding=3><TR><TD>';
echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST onsubmit='for(i=0;i<document.forms[0].elements.length;i++){if(document.forms[0].elements[i].style.color==\"rgb(187, 187, 187)\" || document.forms[0].elements[i].style.color==\"#bbbbbb\") document.forms[0].elements[i].value=\"\";} '>";
echo _makeInput('values[PEOPLE][1][FIRST_NAME]','First Name');
echo _makeInput('values[PEOPLE][1][LAST_NAME]','Last Name');
echo '<BR>'._makeInput('values[PEOPLE][1][extra][Cell]','Cell Phone','','size=30');
echo '<BR>'._makeInput('values[PEOPLE][1][extra][Workplace]','Workplace','','size=30');
echo '</TD></TR></TABLE>';
echo '</TD><TD>';
echo '<B>Information about your spouse or significant other residing with you:</B><BR><small>Leave this section blank if you are separated.</small>';
echo '<TABLE><TR><TD valign=top>';
echo _makeInput('values[PEOPLE][2][FIRST_NAME]','First Name');
echo _makeInput('values[PEOPLE][2][LAST_NAME]','Last Name');
echo '<BR>'._makeInput('values[PEOPLE][2][extra][Cell]','Cell Phone','','size=30');
echo '<BR>'._makeInput('values[PEOPLE][2][extra][Workplace]','Workplace','','size=30');
echo '</TD></TR></TABLE>';
echo '</TD></TR><TR><TD colspan=2>';

echo '<B>Your Address:</B>';
echo '<TABLE><TR><TD>';
echo _makeInput('values[ADDRESS][1][ADDRESS]','Address','','size=40');
echo '<BR>'._makeInput('values[ADDRESS][1][CITY]','City','','size=35');
echo ' '._makeInput('values[ADDRESS][1][STATE]','ST','','size=3 maxlength=2');
echo '<BR>'._makeInput('values[ADDRESS][1][ZIPCODE]','Zip','','size=6');
echo '<BR><BR> '._makeInput('values[ADDRESS][1][PHONE]','Phone','','size=9 maxlength=30');
echo '<BR><BR>';
echo '</TD></TR>';

for($i=3;$i<=6;$i++)
{
	if($i==3 || $i==5)
		echo '<TR>';
	echo '<TD valign=top>';
	echo '<B>Grandparent Information:</B>';
	echo '<BR>'._makeInput('values[PEOPLE]['.$i.'][FIRST_NAME]','First Name');
	echo _makeInput('values[PEOPLE]['.$i.'][LAST_NAME]','Last Name');
	echo '<BR>';
	echo _makeInput('values[PEOPLE]['.$i.'][extra][Cell]','Cell Phone','','size=30');
	echo '<BR>';
	echo _makeInput('values[ADDRESS]['.$i.'][ADDRESS]','Address','','size=40');
	echo '<BR>'._makeInput('values[ADDRESS]['.$i.'][CITY]','City','','size=35');
	echo ' '._makeInput('values[ADDRESS]['.$i.'][STATE]','ST','','size=3 maxlength=2');
	echo '<BR>'._makeInput('values[ADDRESS]['.$i.'][ZIPCODE]','Zip','','size=6 maxlength=10');
	echo '<BR><BR>'._makeInput('values[ADDRESS]['.$i.'][PHONE]','Phone','','size=9 maxlength=30');
	if($i==4)
		echo '<BR><BR>';
	echo '</TD>';
	if($i==4 || $i==6)
		echo '</TR>';
}

echo '<TR><TD colspan=2>';
echo '<BR><B>Other Contacts:</B><BR>';

echo _makeInput('values[PEOPLE][7][FIRST_NAME]','First Name');
echo _makeInput('values[PEOPLE][7][LAST_NAME]','Last Name');
echo _makeInput('values[PEOPLE][7][extra][Relation]','Relation to Student','','size=30');
echo _makeInput('values[PEOPLE][7][extra][Cell]','Cell Phone','','size=30');

echo '<BR>'._makeInput('values[PEOPLE][8][FIRST_NAME]','First Name');
echo _makeInput('values[PEOPLE][8][LAST_NAME]','Last Name');
echo _makeInput('values[PEOPLE][8][extra][Relation]','Relation to Student','','size=30');
echo _makeInput('values[PEOPLE][8][extra][Cell]','Cell Phone','','size=30');

echo '</TD></TR></TABLE>';
echo '</TD></TR></TABLE>';
echo '<HR>';
$student = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
echo '<B>Information about '.$student[1]['FIRST_NAME'].' '.$student[1]['LAST_NAME'].':</B>';
echo '<TABLE>';
echo '<TR>';
echo '<TD>';
echo DateInput($student['BIRTHDATE'],'birth_date','Birthdate');
echo '</TD>';
echo '<TD>';
echo _makeInput('values[STUDENTS][SOCIAL_SECURITY]','SSN');
echo '</TD>';
echo '</TR>';
echo '<TR>';
echo '<TD>';
$ethnic_options = array('White, Non-Hispanic'=>'White, Non-Hispanic','Black, Non-Hispanic'=>'Black, Non-Hispanic','Amer. Indian or Alaskan Native'=>'Amer. Indian or Alaskan Native','Asian or Pacific Islander'=>'Asian or Pacific Islander','Hispanic'=>'Hispanic','Other'=>'Other');
echo SelectInput($student['ETHNICITY'],'values[STUDENTS][ETHNICITY]','Ethnicity',$ethnic_options);
echo '</TD>';
echo '<TD>';
$language_options = array('English'=>'English','Achinese'=>'Achinese','Acholi'=>'Acholi','Adangme'=>'Adangme','Afro-Asiatic (Other)'=>'Afro-Asiatic (Other)','Afrihili (Artificial language)'=>'Afrihili (Artificial language)','Afrikaans'=>'Afrikaans','Aljamia'=>'Aljamia','Akkadian'=>'Akkadian','Albanian'=>'Albanian','Aleut'=>'Aleut','Algonquian languages'=>'Algonquian languages','Amharic'=>'Amharic','English, Old (ca. 450-1100)'=>'English, Old (ca. 450-1100)','Apache languages'=>'Apache languages','Arabic'=>'Arabic','Aramaic'=>'Aramaic','Armenian'=>'Armenian','Araucanian'=>'Araucanian','Arapaho'=>'Arapaho','Artificial (Other)'=>'Artificial (Other)','Arawak'=>'Arawak','American Sign Language'=>'American Sign Language','Assamese'=>'Assamese','Athabascan languages'=>'Athabascan languages','Avaric'=>'Avaric','Avestan'=>'Avestan','Awadhi'=>'Awadhi','Aymara'=>'Aymara','Azerbaijani'=>'Azerbaijani','Banda'=>'Banda','Bamileke languages'=>'Bamileke languages','Bashkir'=>'Bashkir','Baluchi'=>'Baluchi','Bambara'=>'Bambara','Balinese'=>'Balinese','Basque'=>'Basque','Basa'=>'Basa','Baltic (Other)'=>'Baltic (Other)','Beja'=>'Beja','Byelorussian'=>'Byelorussian','Bemba'=>'Bemba','Bengali'=>'Bengali','Berber languages'=>'Berber languages','Bhojpuri'=>'Bhojpuri','Bikol'=>'Bikol','Bini'=>'Bini','Siksika'=>'Siksika','Braj'=>'Braj','Breton'=>'Breton','Buginese'=>'Buginese','Bulgarian'=>'Bulgarian','Burmese'=>'Burmese','Caddo'=>'Caddo','Central American Indian (Other)'=>'Central American Indian (Other)','Khmer'=>'Khmer','Carib'=>'Carib','Catalan'=>'Catalan','Caucasian (Other)'=>'Caucasian (Other)','Cebuano'=>'Cebuano','Celtic languages'=>'Celtic languages','Chamorro'=>'Chamorro','Chibcha'=>'Chibcha','Chechen'=>'Chechen','Chagatai'=>'Chagatai','Chinese'=>'Chinese','Chinook jargon'=>'Chinook jargon','Choctaw'=>'Choctaw','Cherokee'=>'Cherokee','Church Slavic'=>'Church Slavic','Chuvash'=>'Chuvash','Cheyenne'=>'Cheyenne','Coptic'=>'Coptic','Cornish'=>'Cornish','Creoles and Pidgins, English-based (Other)'=>'Creoles and Pidgins, English-based (Other)','Creoles and Pidgins, French-based (Other)'=>'Creoles and Pidgins, French-based (Other)','Creoles and Pidgins, Portugues-Based (Other)'=>'Creoles and Pidgins, Portugues-Based (Other)','Cree'=>'Cree','Creoles and Pidgins (Other)'=>'Creoles and Pidgins (Other)','Cushitic (Other)'=>'Cushitic (Other)','Czech'=>'Czech','Dakota'=>'Dakota','Danish'=>'Danish','Delaware'=>'Delaware','Dinka'=>'Dinka','Dogri'=>'Dogri','Dravidian (Other)'=>'Dravidian (Other)','Duala'=>'Duala','Dutch, Middle (ca. 1050-1350)'=>'Dutch, Middle (ca. 1050-1350)','Dutch'=>'Dutch','Dyula'=>'Dyula','Efik'=>'Efik','Egyptian'=>'Egyptian','Ekajuk'=>'Ekajuk','Elamite'=>'Elamite','English'=>'English','English, Middle (1100-1500)'=>'English, Middle (1100-1500)','Eskimo Languages'=>'Eskimo Languages','Esperanto'=>'Esperanto','Estonian'=>'Estonian','Ethiopic'=>'Ethiopic','Ewe'=>'Ewe','Ewondo'=>'Ewondo','Fang'=>'Fang','Faroese'=>'Faroese','Fanti'=>'Fanti','Fijian'=>'Fijian','Finnish'=>'Finnish','Finno-Ugrian (Other)'=>'Finno-Ugrian (Other)','Fon'=>'Fon','French'=>'French','Friesian'=>'Friesian','French, Middle (ca. 1400-1600)'=>'French, Middle (ca. 1400-1600)','French, Old (ca. 842-1400)'=>'French, Old (ca. 842-1400)','Fula'=>'Fula','G��'=>'G��','Gaelic (Scots)'=>'Gaelic (Scots)','Gallegan'=>'Gallegan','Oromo'=>'Oromo','Gayo'=>'Gayo','Germanic (Other)'=>'Germanic (Other)','Georgian'=>'Georgian','German'=>'German','Gilbertese'=>'Gilbertese','German, Middle High (ca. 1050-1500)'=>'German, Middle High (ca. 1050-1500)','German, Old High (ca. 750-1050)'=>'German, Old High (ca. 750-1050)','Gondi'=>'Gondi','Gothic'=>'Gothic','Grebo'=>'Grebo','Greek, Ancient (to 1453)'=>'Greek, Ancient (to 1453)','Greek, Modern (1453- )'=>'Greek, Modern (1453- )','Guarani'=>'Guarani','Gujarati'=>'Gujarati','Haida'=>'Haida','Hausa'=>'Hausa','Hawaiian'=>'Hawaiian','Hebrew'=>'Hebrew','Herero'=>'Herero','Hiligaynon'=>'Hiligaynon','Himachali'=>'Himachali','Hindi'=>'Hindi','Hiri Motu'=>'Hiri Motu','Hungarian'=>'Hungarian','Hupa'=>'Hupa','Iban'=>'Iban','Igbo'=>'Igbo','Icelandic'=>'Icelandic','Ijo'=>'Ijo','Iloko'=>'Iloko','Indic (Other)'=>'Indic (Other)','Indonesian'=>'Indonesian','Indo-European (Other)'=>'Indo-European (Other)','Interlingua (International Auxiliary Language Association'=>'Interlingua (International Auxiliary Language Association','Iranian (Other)'=>'Iranian (Other)','Irish'=>'Irish','Iroquoian languages'=>'Iroquoian languages','Italian'=>'Italian','Javanese'=>'Javanese','Japanese'=>'Japanese','Judeo-Persian'=>'Judeo-Persian','Judeo-Arabic'=>'Judeo-Arabic','Kara-Kalpak'=>'Kara-Kalpak','Kabyle'=>'Kabyle','Kachin'=>'Kachin','Kamba'=>'Kamba','Kannada'=>'Kannada','Karen'=>'Karen','Kashmiri'=>'Kashmiri','Kanuri'=>'Kanuri','Kawi'=>'Kawi','Kazakh'=>'Kazakh','Khasi'=>'Khasi','Khoisan (Other)'=>'Khoisan (Other)','Khotanese'=>'Khotanese','Kikuyu'=>'Kikuyu','Kinyarwanda'=>'Kinyarwanda','Kirghiz'=>'Kirghiz','Konkani'=>'Konkani','Kongo'=>'Kongo','Korean'=>'Korean','Kpelle'=>'Kpelle','Kru'=>'Kru','Kurukh'=>'Kurukh','Kuanyama'=>'Kuanyama','Kurdish'=>'Kurdish','Kusaie'=>'Kusaie','Kutenai'=>'Kutenai','Ladino'=>'Ladino','Lahnd'=>'Lahnd','Lamba'=>'Lamba','Langue d��oc (post-1500)'=>'Langue d��oc (post-1500)','Lao'=>'Lao','Lapp'=>'Lapp','Latin'=>'Latin','Latvian'=>'Latvian','Lingala'=>'Lingala','Lithuanian'=>'Lithuanian','Mongo'=>'Mongo','Lozi'=>'Lozi','Luba-Katanga'=>'Luba-Katanga','Ganda'=>'Ganda','Luiseno'=>'Luiseno','Lunda'=>'Lunda','Luo (Kenya and Tanzania)'=>'Luo (Kenya and Tanzania)','Macedonian'=>'Macedonian','Madurese'=>'Madurese','Magahi'=>'Magahi','Marshall'=>'Marshall','Maithili'=>'Maithili','Makasar'=>'Makasar','Malayalam'=>'Malayalam','Mandingo'=>'Mandingo','Maori'=>'Maori','Austronesian (Other)'=>'Austronesian (Other)','Marathi'=>'Marathi','Masai'=>'Masai','Manx'=>'Manx','Malay'=>'Malay','Mende'=>'Mende','Micmac'=>'Micmac','Minangkabau'=>'Minangkabau','Miscellaneous (Other)'=>'Miscellaneous (Other)','Mon-Khmer (Other)'=>'Mon-Khmer (Other)','Malagasy'=>'Malagasy','Maltese'=>'Maltese','Manipuri'=>'Manipuri','Manobo languages'=>'Manobo languages','Mohawk'=>'Mohawk','Moldavian'=>'Moldavian','Mongolian'=>'Mongolian','Mossi'=>'Mossi','Maliseet'=>'Maliseet','Multiple languages'=>'Multiple languages','Munda (Other)'=>'Munda (Other)','Creek'=>'Creek','Marwari'=>'Marwari','Mayan languages'=>'Mayan languages','Aztec'=>'Aztec','North American Indian (Other)'=>'North American Indian (Other)','Navajo'=>'Navajo','Ndebele (Zimbabwe)'=>'Ndebele (Zimbabwe)','Ndonga'=>'Ndonga','Nepali'=>'Nepali','Newari'=>'Newari','Niger-Kordofanian (Other)'=>'Niger-Kordofanian (Other)','Niuean'=>'Niuean','Norwegian'=>'Norwegian','Northern Sotho'=>'Northern Sotho','Nubian languages'=>'Nubian languages','Nyanja'=>'Nyanja','Nyamwezi'=>'Nyamwezi','Nyankole'=>'Nyankole','Nyoro'=>'Nyoro','Nzima'=>'Nzima','Ojibwa'=>'Ojibwa','Oriya'=>'Oriya','Osage'=>'Osage','Ossetic'=>'Ossetic','Turkish, Ottoman'=>'Turkish, Ottoman','Otomian languages'=>'Otomian languages','Papuan-Australian (Other)'=>'Papuan-Australian (Other)','Pangasinan'=>'Pangasinan','Pahlavi'=>'Pahlavi','Pampanga'=>'Pampanga','Panjabi'=>'Panjabi','Papiamento'=>'Papiamento','Passamaquoddy'=>'Passamaquoddy','Palauan'=>'Palauan','Old Persian (ca. 600-400 B.C.)'=>'Old Persian (ca. 600-400 B.C.)','Persian'=>'Persian','Pali'=>'Pali','Polish'=>'Polish','Ponape'=>'Ponape','Portuguese'=>'Portuguese','Prakrit languages'=>'Prakrit languages','Provencal, Old (to 1500)'=>'Provencal, Old (to 1500)','Pushto'=>'Pushto','Quechua'=>'Quechua','Rajasthani'=>'Rajasthani','Rarotongan'=>'Rarotongan','Romance (Other)'=>'Romance (Other)','Raeto-Romance'=>'Raeto-Romance','Romany'=>'Romany','Romanian'=>'Romanian','Rundi'=>'Rundi','Russian'=>'Russian','Sandawe'=>'Sandawe','Sango'=>'Sango','South American Indian (Other)'=>'South American Indian (Other)','Salishan languages'=>'Salishan languages','Samaritan Aramaic'=>'Samaritan Aramaic','Sanskrit'=>'Sanskrit','Samoan'=>'Samoan','Serbo-Croatian (Cyrillic)'=>'Serbo-Croatian (Cyrillic)','Scots'=>'Scots','Serbo-Croatian (Roman)'=>'Serbo-Croatian (Roman)','Selkup'=>'Selkup','Semitic (Other)'=>'Semitic (Other)','Shan'=>'Shan','Shona'=>'Shona','Sidamo'=>'Sidamo','Siouan languages'=>'Siouan languages','Sino-Tibetan (Other)'=>'Sino-Tibetan (Other)','Slavic (Other)'=>'Slavic (Other)','Slovak'=>'Slovak','Slovenian'=>'Slovenian','Sindhi'=>'Sindhi','Sinhalese'=>'Sinhalese','Somali'=>'Somali','Songhai'=>'Songhai','Spanish'=>'Spanish','Serer'=>'Serer','Sotho'=>'Sotho','Sukuma'=>'Sukuma','Sudanese'=>'Sudanese','Susu'=>'Susu','Sumerian'=>'Sumerian','Swahili'=>'Swahili','Swazi'=>'Swazi','Syriac'=>'Syriac','Tagalog'=>'Tagalog','Tahitian'=>'Tahitian','Tajik'=>'Tajik','Tamil'=>'Tamil','Tatar'=>'Tatar','Telugu'=>'Telugu','Timne'=>'Timne','Tereno'=>'Tereno','Thai'=>'Thai','Tibetan'=>'Tibetan','Tigre'=>'Tigre','Tigrinya'=>'Tigrinya','Tivi'=>'Tivi','Tlingit'=>'Tlingit','Tonga (Nyasa)'=>'Tonga (Nyasa)','Tonga (Tonga Islands)'=>'Tonga (Tonga Islands)','Truk'=>'Truk','Tsimshian'=>'Tsimshian','Tsonga'=>'Tsonga','Tswana'=>'Tswana','Turkmen'=>'Turkmen','Tumbuka'=>'Tumbuka','Turkish'=>'Turkish','Altaic (Other)'=>'Altaic (Other)','Twi'=>'Twi','Ugaritic'=>'Ugaritic','Uighur'=>'Uighur','Ukrainian'=>'Ukrainian','Umbundu'=>'Umbundu','Undetermined'=>'Undetermined','Urdu'=>'Urdu','Uzbek'=>'Uzbek','Vai'=>'Vai','Venda'=>'Venda','Vietnamese'=>'Vietnamese','Votic'=>'Votic','Wakashan languages'=>'Wakashan languages','Walamo'=>'Walamo','Waray'=>'Waray','Washo'=>'Washo','Welsh'=>'Welsh','Sorbian languages'=>'Sorbian languages','Wolof'=>'Wolof','Xhosa'=>'Xhosa','Yao'=>'Yao','Yap'=>'Yap','Yiddish'=>'Yiddish','Yoruba'=>'Yoruba','Zapotec'=>'Zapotec','Zenaga'=>'Zenaga','Zulu'=>'Zulu','Zuni'=>'Zuni');
echo SelectInput($student['LANGUAGE'],'values[STUDENTS][LANGUAGE]','Language',$language_options,'N/A','style="width:200"');
echo '</TD>';
echo '</TR>';

echo '<TR>';
echo '<TD>';
echo SelectInput($student['GENDER'],'values[STUDENTS][GENDER]','Gender',array('M'=>'Male','F'=>'Female'));
echo '</TD>';
echo '<TD>';
echo _makeInput('values[STUDENTS][COMMON_NAME]','Hebrew Name');
echo '</TD>';
echo '</TR>';
echo '<TR><TD colspan=2 align=center>';
echo '<BR>'._makeInput('values[STUDENTS][PHYSICIAN]','Physician','','size=30');
echo '<BR>'._makeInput('values[STUDENTS][PHYSICIAN_PHONE]','Physician Phone','','size=30');
echo '<BR>'._makeInput('values[STUDENTS][PREFERRED_HOSPITAL]','Preferred Hospital','','size=30');
echo '<BR><TEXTAREA name=values[STUDENTS][comments] cols=26 rows=5 style="color: BBBBBB;" onfocus=\'if(this.value=="Medical Comments") this.value=""; this.style.color="000000";\' onblur=\'if(this.value=="") {this.value="Medical Comments"; this.style.color="BBBBBB";}\'">Medical Comments</TEXTAREA>';
echo '</TD></TR>';
echo '</TABLE>';
echo '<BR>';
$_openSIS['DrawHeader'] = 'E8E8E9';
DrawHeader('','','<INPUT type=submit value=Save>');
echo '</form>';

function _makeInput($name,$title,$value='',$extra='')
{
	return "<INPUT type=text name=$name value=\"$title\" style=\"color:
	BBBBBB\" onfocus='if(this.value==\"$title\") this.value=\"\"; this.style.color=\"000000\"' onsubmit='if(this.value==\"$title\") this.value=\"\"; this.style.color=\"000000\"' onblur='if(this.value==\"\") {this.value=\"$title\"; this.style.color=\"BBBBBB\"}' $extra>";
}

function PrepareAddress($temp)
{
	$address = array();
	ereg('^[0-9]+',$temp,$regs);$temp = ereg_replace('^[0-9]+ ','',$temp);
	if($regs[0])
		$address['HOUSE_NO'] = $regs[0];

	$temp_dir = strtoupper(str_replace('.',' ',substr($temp,0,2)));
	if($temp_dir=='W ' || $temp_dir=='E ' || $temp_dir=='N ' || $temp_dir=='S ')
	{
		$address['DIRECTION'] = substr($temp,0,1);
		$address['STREET'] = substr($temp,2);
	}
	elseif($temp_dir=='NO' || $temp_dir=='SO' || $temp_dir=='WE' || $temp_dir=='EA')
	{
		$temp_dir = str_replace('.','',strtoupper(substr($temp,0,strpos($temp,' '))));
		switch($temp_dir)
		{
			case 'NORTH':
				$address['DIRECTION'] = 'N';
				$address['STREET'] = substr($temp,strpos($temp,' '));
			break;

			case 'SOUTH':
				$address['DIRECTION'] = 'S';
				$address['STREET'] = substr($temp,strpos($temp,' '));
			break;

			case 'EAST':
				$address['DIRECTION'] = 'E';
				$address['STREET'] = substr($temp,strpos($temp,' '));
			break;

			case 'WEST':
				$address['DIRECTION'] = 'W';
				$address['STREET'] = substr($temp,strpos($temp,' '));
			break;

			default:
				$address['STREET'] = $temp;
			break;
		}
		$address['STREET'] = trim($address['STREET']);
	}
	else
		$address['STREET'] = $temp;
	return $address;
}
?>