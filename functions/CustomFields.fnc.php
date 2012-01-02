<?php

/*
	Call in an SQL statement to select students based on custom fields

	Use in the where section of the query by CustomFIelds('where')
*/

function CustomFields($location,$table_arr='',$exp=0)
{	global $_openSIS;
	if(count($_REQUEST['month_cust_begin']))
	{
		foreach($_REQUEST['month_cust_begin'] as $field_name=>$month)
		{
			$_REQUEST['cust_begin'][$field_name] = $_REQUEST['day_cust_begin'][$field_name].'-'.$_REQUEST['month_cust_begin'][$field_name].'-'.$_REQUEST['year_cust_begin'][$field_name];
			$_REQUEST['cust_end'][$field_name] = $_REQUEST['day_cust_end'][$field_name].'-'.$_REQUEST['month_cust_end'][$field_name].'-'.$_REQUEST['year_cust_end'][$field_name];
			if(!VerifyDate($_REQUEST['cust_begin'][$field_name]) || !VerifyDate($_REQUEST['cust_end'][$field_name]))
			{
				unset($_REQUEST['cust_begin'][$field_name]);
				unset($_REQUEST['cust_end'][$field_name]);
			}
		}
		unset($_REQUEST['month_cust_begin']);unset($_REQUEST['year_cust_begin']);unset($_REQUEST['day_cust_begin']);
		unset($_REQUEST['month_cust_end']);unset($_REQUEST['year_cust_end']);unset($_REQUEST['day_cust_end']);
	}
	if(count($_REQUEST['cust']))
	{
		foreach($_REQUEST['cust'] as $key=>$value)
		{
			if($value=='')
				unset($_REQUEST['cust'][$key]);
		}
	}
	switch($location)
	{
		case 'from':
		break;

		case 'where':
		if(count($_REQUEST['cust']) || count($_REQUEST['cust_begin']))
			$fields = DBGet(DBQuery("SELECT TITLE,ID,TYPE,SYSTEM_FIELD FROM CUSTOM_FIELDS"),array(),array('ID'));

		if(count($_REQUEST['cust']))
		{
			foreach($_REQUEST['cust'] as $id => $value)
			{
				$field_name = $id;
				$id = substr($id,7);
				if($fields[$id][1]['SYSTEM_FIELD'] == 'Y')
					$field_name = strtoupper(str_replace(' ','_',$fields[$id][1]['TITLE']));
				if($value!='')
				{
					switch($fields[$id][1]['TYPE'])
					{
						case 'radio':
							$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].': </b></font>';
							if($value=='Y')
							{
								$string .= " and s.$field_name='$value' ";
								$_openSIS['SearchTerms'] .= 'Yes';
							}
							elseif($value=='N')
							{
								$string .= " and (s.$field_name!='Y' OR s.$field_name IS NULL) ";
								$_openSIS['SearchTerms'] .= 'No';
							}
							$_openSIS['SearchTerms'] .= '<BR>';
						break;

						case 'codeds':
							$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_openSIS['SearchTerms'] .= 'No Value';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_openSIS['SearchTerms'] .= $value;
							}
							$_openSIS['SearchTerms'] .= '<BR>';
							break;

						case 'select':
							$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_openSIS['SearchTerms'] .= 'No Value';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_openSIS['SearchTerms'] .= $value;
							}
							$_openSIS['SearchTerms'] .= '<BR>';
							break;

						case 'autos':
							$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_openSIS['SearchTerms'] .= 'No Value';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_openSIS['SearchTerms'] .= $value;
							}
							$_openSIS['SearchTerms'] .= '<BR>';
							break;

						case 'edits':
							$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_openSIS['SearchTerms'] .= 'No Value';
							}
							elseif($value=='~')
							{
								$string .= " and position('\n'||s.$field_name||'\r' IN '\n'||(SELECT SELECT_OPTIONS FROM CUSTOM_FIELDS WHERE ID='".$id."')||'\r')=0 ";
								$_openSIS['SearchTerms'] .= 'Other';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_openSIS['SearchTerms'] .= $value;
							}
							$_openSIS['SearchTerms'] .= '<BR>';
							break;

						case 'text':
							if(substr($value,0,2)=='\"' && substr($value,-2)=='\"')
							{
								$string .= " and s.$field_name='".substr($value,2,-2)."' ";
								$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].': </b></font>'.substr($value,2,-2).'<BR>';
							}
							else
							{
								$string .= " and LOWER(s.$field_name) LIKE '".strtolower($value)."%' ";
                                                                if($exp==1)
								$_openSIS['Search'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].' starts with: </b></font>'.$value.'<BR>';
                                                            elseif($exp==2){
                                                                $_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].' starts with: </b></font>'.$value.'<BR>';
                                                            }
                                                             else {
                                                                          $_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].' starts with: </b></font>'.$value.'<BR>';
                                                            }
							}
						break;
					}
				}
			}
		}
		if(count($_REQUEST['cust_begin']))
		{
			foreach($_REQUEST['cust_begin'] as $id => $value)
			{
				$field_name = $id;
				$id = substr($id,7);
				$column_name = $field_name;
				if($fields[$id][1]['SYSTEM_FIELD'] == 'Y')
					$column_name = strtoupper(str_replace(' ','_',$fields[$id][1]['TITLE']));
				if($fields[$id][1]['TYPE']=='numeric')
				{
					$_REQUEST['cust_end'][$field_name] = ereg_replace('[^0-9.-]+','',$_REQUEST['cust_end'][$field_name]);
					$value = ereg_replace('[^0-9.-]+','',$value);
				}

				if($_REQUEST['cust_begin'][$field_name]!='' && $_REQUEST['cust_end'][$field_name]!='')
				{
					if($fields[$id][1]['TYPE']=='numeric' && $_REQUEST['cust_begin'][$field_name]>$_REQUEST['cust_end'][$field_name])
					{
						$temp = $_REQUEST['cust_end'][$field_name];
						$_REQUEST['cust_end'][$field_name] = $value;
						$value = $temp;
					}
					$string .= " and s.$column_name BETWEEN '$value' AND '".$_REQUEST['cust_end'][$field_name]."' ";
					if($fields[$id][1]['TYPE']=='date')
						$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].' between: </b></font>'.ProperDate($value).' &amp; '.ProperDate($_REQUEST['cust_end'][$field_name]).'<BR>';
					else
						$_openSIS['SearchTerms'] .= '<font color=gray><b>'.$fields[$id][1]['TITLE'].' between: </b></font>'.$value.' &amp; '.$_REQUEST['cust_end'][$field_name].'<BR>';
				}
			}
		}

		break;
	}
		return $string;
}
?>
