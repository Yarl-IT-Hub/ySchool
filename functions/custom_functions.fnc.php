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
function TextAreaInputOrg($value,$name,$title='',$options='',$div=true, $divwidth='500px')
{
	if(Preferences('HIDDEN')!='Y')
		$div = false;

	if(AllowEdit() && !$_REQUEST['_openSIS_PDF'])
	{
		$value = str_replace("'",'&#39;',str_replace('"','&rdquo;',$value));

		if(strpos($options,'cols')===false)
			$options .= ' cols=30';
		if(strpos($options,'rows')===false)
			$options .= ' rows=4';
		$rows = substr($options,strpos($options,'rows')+5,2)*1;
		$cols = substr($options,strpos($options,'cols')+5,2)*1;

		if($value=='' || $div==false)
			return "<TEXTAREA name=$name $options>$value</TEXTAREA>".($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');
		else
			return "<DIV id='div$name'><div style='width:500px;' onclick='javascript:addHTML(\"<TEXTAREA id=textarea$name name=$name $options>".ereg_replace("[\n\r]",'\u000D\u000A',str_replace("\r\n",'\u000D\u000A',str_replace("'","&#39;",$value)))."</TEXTAREA>".($title!=''?"<BR><small>".str_replace("'",'&#39;',(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':''))."</small>":'')."\",\"div$name\",true); document.getElementById(\"textarea$name\").value=unescape(document.getElementById(\"textarea$name\").value);'><TABLE class=LO_field height=100%><TR><TD>".((substr_count($value,"\r\n")>$rows)?'<DIV style="overflow:auto; height:'.(15*$rows).'px; width:'.($cols*10).'; padding-right: 16px;">'.nl2br($value).'</DIV>':'<DIV style="overflow:auto; width:'.$divwidth.'; padding-right: 16px;">'.nl2br($value).'</DIV>').'</TD></TR></TABLE>'.($title!=''?'<BR><small>'.str_replace("'",'&#39;',(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'')).'</small>':'').'</div></DIV>';
	}
	else
		return (($value!='')?nl2br($value):'-').($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');
}

function ShowErr($msg)
{
	echo "<script type='text/javascript'>
	document.getElementById('divErr').innerHTML='<font color=red>".$msg."</font>';</script>";
}
function ShowErrPhp($msg)
{
	echo '<font color=red>'.$msg.'<br /></font>';
}
function for_error()
{
 		$css=getCSS(); 		
		echo "<br><br><form action=Modules.php?modname=$_REQUEST[modname] method=post>";
		echo '<BR><CENTER>'.SubmitButton('Try Again','','class=btn_medium').'</CENTER>';
		echo "</form>";	
		echo "</div>";

	echo "</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>

			<tr>
            <td class=\"footer\">
			<table width=\"100%\" border=\"0\">
  <tr>
    <td align='center' class='copyright'>
       <center>Copyright &copy; 2011 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                openSIS is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </center></td>
  </tr>
</table>
			</td>
          	</tr>
        </table></td>
    </tr>
  </table>
</center>
</body>
</html>";

		exit();
}



function ExportLink($modname,$title='',$options='')
{
	if(AllowUse($modname))
		$link = '<A HREF=for_export.php?modname='.$modname.$options.'>';
	if($title)
		$link .= $title;
	if(AllowUse($modname))
		$link .= '</A>';

	return $link;
}

function getCSS()
{
		$css='Blue';
		if(User('STAFF_ID'))
		{
		$sql = "select value from PROGRAM_USER_CONFIG where title='THEME' and user_id=".User('STAFF_ID');
		$data = DBGet(DBQuery($sql));
		if(count($data[1]))
		$css=$data[1]['VALUE']; 
		}
		return $css;		
}


function Prompt_Calender($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM name=prompt_form id=prompt_form action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick='formcheck_school_setup_calender();'>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}


function Prompt_Copy_School($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM name=prompt_form id=prompt_form action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick='formcheck_school_setup_copyschool();'>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=School_Setup/Calendar.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}


function Prompt_rollover($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		//echo '';
		PopTable('header',$title);
	//	echo "<CENTER><h4>$question</h4><FORM name=roll_over id=roll_over action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick=\"document.roll_over.submit();\">&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
                echo "<CENTER><h4>$question</h4><FORM name=roll_over id=roll_over action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><font color=red>Caution : </font>Rollover is an irreversible process.  If you are sure you want to proceed, type in the <BR>effective  roll over date below. You can use the next school year’s attendance start date.<BR><BR>";
                echo DrawHeader('<center>'.DateInput('','roll_start_date','').'</center>')."<BR><BR><INPUT type=submit class=btn_medium value=Rollover onclick=\"return formcheck_rollover();\">&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=Tools/LogDetails.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}

function Prompt_rollover_back($title='Rollover',$question='',$pdf='')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;

	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
	//	echo "<CENTER><h4>$question</h4><FORM name=roll_over id=roll_over action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick=\"document.roll_over.submit();\">&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
                echo "<CENTER><h4>$question</h4><FORM name=roll_over id=roll_over action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><BR>&nbsp;<INPUT type=submit class=btn_medium name=delete_cancel value=Ok onclick='load_link(\"Modules.php?modname=Tools/LogDetails.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}




function Prompt_Runschedule($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=Scheduling/Schedule.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}



#############################################################################################
# This function is written for the date reset problem, so if any date  resets to Jan 1 20 use this 

// SEND PrepareDateSchedule a name prefix, and a date in oracle format 'd-M-y' as the selected date to have returned a date selection series
// of pull-down menus
// For the default to be Not Specified, send a date of 00-000-00 -- For today's date, send nothing
// The date pull-downs will create three variables, monthtitle, daytitle, yeartitle
// The third parameter (booleen) specifies whether Not Specified should be allowed as an option

function PrepareDateSchedule($date='',$title='',$allow_na=true,$options='')
{	global $_openSIS;

	if($options=='')
		$options = array();
	if(!$options['Y'] && !$options['M'] && !$options['D'] && !$options['C'])
		$options += array('Y'=>true,'M'=>true,'D'=>true,'C'=>true);
		
	if($options['short']==true)
		$extraM = "style='width:60;' ";
	if($options['submit']==true)
	{
		$tmp_REQUEST['M'] = $tmp_REQUEST['D'] = $tmp_REQUEST['Y'] = $_REQUEST;
		unset($tmp_REQUEST['M']['month'.$title]);
		unset($tmp_REQUEST['D']['day'.$title]);
		unset($tmp_REQUEST['Y']['year'.$title]);
		$extraM .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['M'])."&amp;month$title=\"+this.form.month$title.value;'";
		$extraD .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['D'])."&amp;day$title=\"+this.form.day$title.value;'";
		$extraY .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['Y'])."&amp;year$title=\"+this.form.year$title.value;'";
	}
	
	if($options['C'])
		$_openSIS['PrepareDate']++;

	if(strlen($date)==9) // ORACLE
	{
		$day = substr($date,0,2);
		$month = substr($date,3,3);
		$year = substr($date,7,2);

		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}
	else // POSTGRES
	{
		$temp = split('-',$date);
		if(strlen($temp[0])==4)
		{
			$day = $temp[2];
			$year = substr($temp[0],2,2);
		}
		else
		{
			$day = $temp[0];
			$year = substr($temp[2],2,2);
		}
		$month = MonthNWSwitch($temp[1],'tochar');

		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}

	// MONTH  ---------------
	if($options['M'])
	{
		$return .= "<div style='float:left; margin-right:2px;'><SELECT CLASS=cal_month NAME=month".$title." id=monthSelect".$_openSIS['PrepareDate']." SIZE=1 $extraM>";
		//  -------------------------------------------------------------------------- //
		
		if($month == 'JAN')
			$month = 1;
		elseif($month == 'FEB')
			$month = 2;
		elseif($month == 'MAR')
			$month = 3;
		elseif($month == 'APR')
			$month = 4;
		elseif($month == 'MAY')
			$month = 5;
		elseif($month == 'JUN')
			$month = 6;
		elseif($month == 'JUL')
			$month = 7;
		elseif($month == 'AUG')
			$month = 8;
		elseif($month == 'SEP')
			$month = 9;
		elseif($month == 'OCT')
			$month = 10;
		elseif($month == 'NOV')
			$month = 11;
		elseif($month == 'DEC')
			$month = 12;
		
		//  -------------------------------------------------------------------------- //
		if($allow_na)
		{
			if($month=='000')
				$return .= "<OPTION value=\"\" SELECTED>N/A";else $return .= "<OPTION value=\"\">N/A";
		}
		
		if($month=='1'){$return .= "<OPTION VALUE=JAN SELECTED>January";}else{$return .= "<OPTION VALUE=JAN>January";}
		if($month=='2'){$return .= "<OPTION VALUE=FEB SELECTED>February";}else{$return .= "<OPTION VALUE=FEB>February";}
		if($month=='3'){$return .= "<OPTION VALUE=MAR SELECTED>March";}else{$return .= "<OPTION VALUE=MAR>March";}
		if($month=='4'){$return .= "<OPTION VALUE=APR SELECTED>April";}else{$return .= "<OPTION VALUE=APR>April";}
		if($month=='5'){$return .= "<OPTION VALUE=MAY SELECTED>May";}else{$return .= "<OPTION VALUE=MAY>May";}
		if($month=='6'){$return .= "<OPTION VALUE=JUN SELECTED>June";}else{$return .= "<OPTION VALUE=JUN>June";}
		if($month=='7'){$return .= "<OPTION VALUE=JUL SELECTED>July";}else{$return .= "<OPTION VALUE=JUL>July";}
		if($month=='8'){$return .= "<OPTION VALUE=AUG SELECTED>August";}else{$return .= "<OPTION VALUE=AUG>August";}
		if($month=='9'){$return .= "<OPTION VALUE=SEP SELECTED>September";}else{$return .= "<OPTION VALUE=SEP>September";}
		if($month=='10'){$return .= "<OPTION VALUE=OCT SELECTED>October";}else{$return .= "<OPTION VALUE=OCT>October";}
		if($month=='11'){$return .= "<OPTION VALUE=NOV SELECTED>November";}else{$return .= "<OPTION VALUE=NOV>November";}
		if($month=='12'){$return .= "<OPTION VALUE=DEC SELECTED>December";}else{$return .= "<OPTION VALUE=DEC>December";}
		
		$return .= "</SELECT></div>";
	}

	// DAY  ---------------
	if($options['D'])
	{
		$return .="<div style='float:left; margin-right:2px;'><SELECT NAME=day".$title." id=daySelect".$_openSIS['PrepareDate']." SIZE=1 $extraD>";
		if($allow_na)
		{
			if($day=='00'){$return .= "<OPTION value=\"\" SELECTED>N/A";}else{$return .= "<OPTION value=\"\">N/A";}
		}
		
		for($i=1;$i<=31;$i++)
		{
			if(strlen($i)==1)
				$print='0'.$i;
			else
				$print = $i;
			
			$return .="<OPTION VALUE=".$print;
			if($day==$print)
				$return .=" SELECTED";
			$return .=">$i ";
		}
		$return .="</SELECT></div>";
	}
	
	// YEAR	 ---------------
	if($options['Y'])
	{
		if(!$year)
		{
			$begin = date('Y') - 20;
			$end = date('Y') + 5;
		}
		else
		{
			if($year<50)
				$year = '20'.$year;
			else
				$year = '19'.$year;
			$begin = $year - 5;
			$end = $year + 5;
		}
	
		$return .="<div style='float:left; margin-right:2px;'><SELECT NAME=year".$title." id=yearSelect".$_openSIS['PrepareDate']." SIZE=1 $extraY>";
		if($allow_na)
		{
			if($year=='00'){$return .= "<OPTION value=\"\" SELECTED>N/A";}else{$return .= "<OPTION value=\"\">N/A";}
		}
			
		for($i=$begin;$i<=$end;$i++)
		{
			$return .="<OPTION VALUE=".substr($i,0);
			if($year==$i){$return .=" SELECTED";}
			$return .=">".$i;
		}
		$return .="</SELECT></div>";
	}
	
	if($options['C'])
		$return .= '<div style="margin-top:-3px; float:left"><img src="assets/calendar.gif" id="trigger'.$_openSIS['PrepareDate'].'" style="cursor: hand;" onmouseover=this.style.background=""; onmouseout=this.style.background=""; onClick='."MakeDate('".$_openSIS['PrepareDate']."',this);".' /></div>';
	
	if($_REQUEST['_openSIS_PDF'])
		$return = ProperDate($date);
	return $return;
}
#############################################################################################
function PromptCourseWarning($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}


# ---------------------- Solution for screen error in Group scheduling start ---------------------------------------- #

function for_error_sch()
{
 		$css=getCSS(); 		
		echo "<br><br><form action=Modules.php?modname=$_REQUEST[modname] method=post>";
		echo '<BR><CENTER>'.SubmitButton('Try Again','','class=btn_medium').'</CENTER>';
		echo "</form>";	
		echo "</div>";

	echo "</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>

        </table></td>
    </tr>
  </table>
</center>
</body>
</html>";

		exit();
}

# ------------------------------ Solution for screen error in Group scheduling end------------------------------------- #

################################### Select input with Disable Onlcik edit feature ##############

function SelectInput_Disonclick($value,$name,$title='',$options,$allow_na='N/A',$extra='',$div=true)
{
	if(Preferences('HIDDEN')!='Y')
		$div = false;

	if ($value!='' && !$options[$value])
		$options[$value] = array($value,'<FONT color=red>'.$value.'</FONT>');
		
		$return = (((is_array($options[$value])?$options[$value][1]:$options[$value])!='')?(is_array($options[$value])?$options[$value][1]:$options[$value]):($allow_na!==false?($allow_na?$allow_na:'-'):'-')).($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');

	return $return;
}


###################################################################################################

###########################################################################
function GetStuListAttn(& $extra)
{	global $contacts_RET,$view_other_RET,$_openSIS;

	if((!$extra['SELECT_ONLY'] || strpos($extra['SELECT_ONLY'],'GRADE_ID')!==false) && !$extra['functions']['GRADE_ID'])
		$functions = array('GRADE_ID'=>'GetGrade');
	else
		$functions = array();

	if($extra['functions'])
		$functions += $extra['functions'];

	if(!$extra['DATE'])
	{
		$queryMP = UserMP();
		$extra['DATE'] = DBDate();
	}
	else{
	#	$queryMP = GetCurrentMP('QTR',$extra['DATE'],false);
                $queryMP = UserMP();
        }
	if($_REQUEST['expanded_view']=='true')
	{
		if(!$extra['columns_after'])
			$extra['columns_after'] = array();
#############################################################################################
//Commented as it crashing for Linux due to  Blank Database tables
		//$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsView' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y'"));
#############################################################################################
		$view_address_RET = DBGet(DBQuery("SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE='ADDRESS' AND USER_ID='".User('STAFF_ID')."'"));
		$view_address_RET = $view_address_RET[1]['VALUE'];
		$view_other_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE IN ('CONTACT_INFO','HOME_PHONE','GUARDIANS','ALL_CONTACTS') AND USER_ID='".User('STAFF_ID')."'"),array(),array('TITLE'));

		if(!count($view_fields_RET) && !isset($view_address_RET) && !isset($view_other_RET['CONTACT_INFO']))
		{
			$extra['columns_after'] = array('CONTACT_INFO'=>'<IMG SRC=assets/down_phone_button.gif border=0>','gender'=>'Gender','ethnicity'=>'Ethnicity','ADDRESS'=>'Mailing Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode') + $extra['columns_after'];
			$select = ',s.STUDENT_ID AS CONTACT_INFO,s.GENDER,s.ETHNICITY,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE ';
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.MAILING='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
			$functions['CONTACT_INFO'] = 'makeContactInfo';
			// if gender is converted to codeds type
			//$functions['CUSTOM_200000000'] = 'DeCodeds';
			$extra['singular'] = 'Student Address';
			$extra['plural'] = 'Student Addresses';

			$extra2['NoSearchTerms'] = true;
			$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
			$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
			$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
			$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
			$extra2['group'] = array('STUDENT_ID','PERSON_ID');

			// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
			if(!$_REQUEST['_openSIS_PDF'])
			{
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			else
				unset($extra2['columns_after']['CONTACT_INFO']);
		}
		else
		{
			if($view_other_RET['CONTACT_INFO'][1]['VALUE']=='Y' && !$_REQUEST['_openSIS_PDF'])
			{
				$select .= ',NULL AS CONTACT_INFO ';
				$extra['columns_after']['CONTACT_INFO'] = '<IMG SRC=assets/down_phone_button.gif border=0>';
				$functions['CONTACT_INFO'] = 'makeContactInfo';

				$extra2 = $extra;
				$extra2['NoSearchTerms'] = true;
				$extra2['SELECT'] = '';
				$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID,COALESCE(sjp.CUSTODY,\'N\') ';
				$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
				$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
				$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
				$extra2['group'] = array('STUDENT_ID','PERSON_ID');
				$extra2['functions'] = array();
				$extra2['link'] = array();

				// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			foreach($view_fields_RET as $field)
			{
				$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
				if($field['TYPE']=='date')
					$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
				elseif($field['TYPE']=='numeric')
					$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
				elseif($field['TYPE']=='codeds')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				$select .= ',s.CUSTOM_'.$field['ID'];
			}
			if($view_address_RET)
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.".$view_address_RET."='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$extra['columns_after'] += array('ADDRESS'=>ucwords(strtolower(str_replace('_',' ',$view_address_RET))).' Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode');
				if($view_address_RET!='MAILING')
					$select .= ",a.ADDRESS_ID,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS";
				else
					$select .= ",a.ADDRESS_ID,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS ";
				$extra['singular'] = 'Student Address';
				$extra['plural'] = 'Student Addresses';

				if($view_other_RET['HOME_PHONE'][1]['VALUE']=='Y')
				{
					$functions['PHONE'] = 'makePhone';
					$extra['columns_after']['PHONE'] = 'Home Phone';
				}
				if($view_other_RET['GUARDIANS'][1]['VALUE']=='Y' || $view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
				{
					$functions['PARENTS'] = 'makeParents';
					if($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
						$extra['columns_after']['PARENTS'] = 'Contacts';
					else
						$extra['columns_after']['PARENTS'] = 'Guardians';
				}
			}
			elseif($_REQUEST['addr'] || $extra['addr'])
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$distinct = 'DISTINCT ';
			}
		}
		$extra['SELECT'] .= $select;
	}
	elseif($_REQUEST['addr'] || $extra['addr'])
	{
		$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
		$distinct = 'DISTINCT ';
	}

	switch(User('PROFILE'))
	{
		case 'admin':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID AS LIST_SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				if($_REQUEST['include_inactive']=='Y')
					$sql .= ','.db_case(array("(ssm.SYEAR='".UserSyear()."' AND ('".date('Y-m-d',strtotime($extra['DATE']))."'>ssm.START_DATE AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE ';
			}

			$sql .= " FROM STUDENTS s,STUDENT_ENROLLMENT ssm ".$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID ";
			if($_REQUEST['include_inactive']=='Y')
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR<='".UserSyear()."' ORDER BY START_DATE DESC LIMIT 1)";
			else
				$sql .= " AND ssm.SYEAR='".UserSyear()."' AND ('".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";

			if(UserSchool() && $_REQUEST['_search_all_schools']!='Y')
				$sql .= " AND ssm.SCHOOL_ID='".UserSchool()."'";
			else
			{
				if(User('SCHOOLS'))
					$sql .= " AND ssm.SCHOOL_ID IN (".substr(str_replace(',',"','",User('SCHOOLS')),2,-2).") ";
				$extra['columns_after']['LIST_SCHOOL_ID'] = 'School';
				$functions['LIST_SCHOOL_ID'] = 'GetSchool';
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
				$extra['columns_after']['ACTIVE'] = 'Status';
		break;

		case 'teacher':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				if($_REQUEST['include_inactive']=='Y')
				{
					$sql .= ','.db_case(array("('".$extra['DATE']."'>=ssm.START_DATE AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE';
					$sql .= ','.db_case(array("('".$extra['DATE']."'>=ss.START_DATE AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE_SCHEDULE';
				}
			}

		#	$sql .= " FROM STUDENTS s,COURSE_PERIODS cp,SCHEDULE ss,STUDENT_ENROLLMENT ssm ".$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).") AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID";
		
			$sql .= " FROM STUDENTS s,COURSE_PERIODS cp,SCHEDULE ss,STUDENT_ENROLLMENT ssm ".$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID";

			if($_REQUEST['include_inactive']=='Y')
			{
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
				$sql .= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR AND MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).") AND COURSE_ID=cp.COURSE_ID AND COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";
			}
			else
			{
				$sql .= " AND ('".$extra['DATE']."'>=ssm.START_DATE AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
				$sql .= " AND ('".$extra['DATE']."'>=ss.START_DATE AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
			{
				$extra['columns_after']['ACTIVE'] = 'School Status';
				$extra['columns_after']['ACTIVE_SCHEDULE'] = 'Course Status';
			}
		break;

		case 'parent':
		case 'student':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
			}
			$sql .= " FROM STUDENTS s,STUDENT_ENROLLMENT ssm ".$extra['FROM']."
					WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."' AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND '".DBDate()."'>ssm.START_DATE)) AND ssm.STUDENT_ID".($extra['ASSOCIATED']?" IN (SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".$extra['ASSOCIATED']."')":"='".UserStudentID()."'");
		break;
		default:
			exit('Error');
	}

	$sql = appendSQL($sql,$extra);

	$sql .= $extra['WHERE'].' ';
	$sql .= CustomFields('where');

	if($extra['GROUP'])
		$sql .= ' GROUP BY '.$extra['GROUP'];

	if(!$extra['ORDER_BY'] && !$extra['SELECT_ONLY'])
	{
		if(Preferences('SORT')=='Grade')
			$sql .= " ORDER BY (SELECT SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID),FULL_NAME";
		else
			$sql .= " ORDER BY FULL_NAME";
		$sql .= $extra['ORDER'];
	}
	elseif($extra['ORDER_BY'])
		$sql .= ' ORDER BY '.$extra['ORDER_BY'];

	if($extra['DEBUG']===true)
		echo '<!--'.$sql.'-->';
		
	return DBGet(DBQuery($sql),$functions,$extra['group']);
}

###########################################################################
########################validation functions#######################################
function scheduleAssociation($cp_id)
{
    $asso=DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE COURSE_PERIOD_ID='$cp_id' LIMIT 0,1"));
    if($asso[1]['COURSE_PERIOD_ID']!='')
        return true;
}

function gradeAssociation($cp_id)
{
    $asso=DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM STUDENT_REPORT_CARD_GRADES WHERE COURSE_PERIOD_ID='$cp_id' LIMIT 0,1"));
    if($asso[1]['COURSE_PERIOD_ID']!='')
        return true;
}
###########################################################################
?>
