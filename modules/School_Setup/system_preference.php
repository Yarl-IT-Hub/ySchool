<?php
include('../../Redirect_modules.php');
DrawBC("School Setup >> ".ProgramTitle());
if($_REQUEST['page_display']){
	
echo '<style type="text/css">
.back_preference { padding:2px 0px 10px 8px; text-align:left; margin:5px 5px; }
</style>';
echo "<div class=back_preference><a href=Modules.php?modname=$_REQUEST[modname]><strong>&laquo; Back to System Preference</strong>
</a></div><br/>";
}
if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='SYSTEM_PREFERENCE'){
if((clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'update') && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save') && clean_param($_REQUEST['values'],PARAM_NOTAGS) && $_POST['values'] && User('PROFILE')=='admin')
{

	$sql = "UPDATE SYSTEM_PREFERENCE SET ";
	foreach($_REQUEST['values'] as $column=>$value)
	{
        $value=paramlib_validation($column,$value);
		$sql .= $column."='".str_replace("\'","''",$value)."',";
	}
	$sql = substr($sql,0,-1) . " WHERE SCHOOL_ID='".UserSchool()."'";
	DBQuery($sql);
	
}
elseif((clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'insert') && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save') && clean_param($_REQUEST['values'],PARAM_NOTAGS) && $_POST['values'] && User('PROFILE')=='admin')
{

	$sql = "INSERT INTO SYSTEM_PREFERENCE SET ";
	foreach($_REQUEST['values'] as $column=>$value)
	{
        $value=paramlib_validation($column,$value);
		$sql .= $column."='".str_replace("\'","''",$value)."',";
	}
	$sql = substr($sql,0,-1) . ",school_id='".UserSchool()."'";
	DBQuery($sql);
	
}

$sys_pref = DBGet(DBQuery("SELECT * FROM SYSTEM_PREFERENCE WHERE SCHOOL_ID=".UserSchool()));
$sys_pref = $sys_pref[1];

PopTable('header','Half-day and full-day minutes');
if($sys_pref==''){
    echo "<FORM name=sys_pref id=sys_pref action=Modules.php?modname=$_REQUEST[modname]&action=insert&page_display=SYSTEM_PREFERENCE method=POST>";
}else{
    echo "<FORM name=sys_pref id=sys_pref action=Modules.php?modname=$_REQUEST[modname]&action=update&page_display=SYSTEM_PREFERENCE method=POST>";
}
echo "<table width=300px><tr><td><table border=0 cellpadding=4 align=center>";
echo "<tr><td><strong>Full day minutes :</strong> </td><td>".TextInput($sys_pref['FULL_DAY_MINUTE'],'values[FULL_DAY_MINUTE]', '','class=cell_floating size=5')."</td></tr><tr><td><strong>Half day minutes :</strong></td><td>".TextInput($sys_pref['HALF_DAY_MINUTE'],'values[HALF_DAY_MINUTE]', '','class=cell_floating size=5')."</td></tr>";
echo "</table></td></tr></table>";
DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Save'></CENTER>");
echo "</FORM>";
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='MAINTENANCE'){
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update'){

if(clean_param($_REQUEST['maintain'],PARAM_NOTAGS)){
$sql="UPDATE SYSTEM_PREFERENCE_MISC SET ";
foreach($_REQUEST['maintain'] as $column_name=>$value)
					{ 
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",$value))."',";

}
$sql= substr($sql,0,-1) ." WHERE 1=1";
DBQuery($sql);
}
foreach($_REQUEST['values'] as $id=>$columns)
	{
		if($id!='new')
		{
			$sql = "UPDATE LOGIN_MESSAGE SET ";
			foreach($columns as $column=>$value)
		{

                   
		if($value=='DISPLAY')
			$sql .= $column."='Y',";
			else
			$sql .= $column."='".str_replace("\'","''",$value)."',";
		}
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			DBQuery($sql);
		}
		else
		{
			$sql = "INSERT INTO LOGIN_MESSAGE ";
			$go = 0;
			foreach($columns as $column=>$value)
			{
				if($value)
				{
					if($value=='DISPLAY')
					 {
 					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",'Y')."',";
					 }
					else
					 {
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					 }
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			if($go)
			{
				DBQuery($sql);
			}
		}
	}
foreach($_REQUEST['val'] as $col=>$val)
	{
		$id=trim(substr($val,0,strpos($val,',')));
		$value=trim(substr($val,strpos($val,',')+1));
		if($id!='new')
		{
		$ID=DBGet(DBQuery("SELECT ID FROM LOGIN_MESSAGE"));
		foreach($ID as $get_ID)
		 {
		    if($get_ID['ID']==$id)
			$sql = "UPDATE LOGIN_MESSAGE SET ".$col."='Y' WHERE ID=".$get_ID['ID'];
			else
			$sql = "UPDATE LOGIN_MESSAGE SET ".$col."='N' WHERE ID=".$get_ID['ID'];
			DBQuery($sql);
		 }
			
		}
		else
		{
		    $ID=DBGet(DBQuery("SELECT ID FROM LOGIN_MESSAGE"));
			foreach($ID as $get_ID)
			 {
				if($get_ID['ID']==$id)
				$sql = "UPDATE LOGIN_MESSAGE SET ".$col."='Y' WHERE ID=".$get_ID['ID'];
				else
				$sql = "UPDATE LOGIN_MESSAGE SET ".$col."='N' WHERE ID=".$get_ID['ID'];
				DBQuery($sql);
			 }
			$max_ID=DBGet(DBQuery("SELECT MAX(ID) AS ID FROM LOGIN_MESSAGE"));
			$login_VAL=DBGet(DBQuery("SELECT ID,MESSAGE FROM LOGIN_MESSAGE WHERE ID=".$max_ID[1]['ID']." "));
			$sql="UPDATE LOGIN_MESSAGE SET ";
			if($login_VAL[1]['MESSAGE'] !='')
			{
				$sql .= $col."='Y' ";
				$sql .=  " WHERE ID=".$max_ID[1]['ID']."";
			}
				DBQuery($sql);
		}
	}	
unset($_REQUEST['maintain']);
}
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
{
	if(DeletePrompt('login message'))
	{
		DBQuery("DELETE FROM LOGIN_MESSAGE WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
	
}
if($_REQUEST['modfunc']!='remove')
 {
	$maintain_RET=DBGet(DBQuery("SELECT SYSTEM_MAINTENANCE_SWITCH FROM SYSTEM_PREFERENCE_MISC LIMIT 1"));
	$maintain=$maintain_RET[1];
	echo "<FORM name=maintenance id=maintenance action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&page_display=MAINTENANCE method=POST>";
	echo '<table>';
	echo '<tr><td align=left><span style="font-size:12px; font-weight:bold;">Under Maintenance :</td><td><span style="font-weight:bold;">'.CheckboxInput($maintain['SYSTEM_MAINTENANCE_SWITCH'],'maintain[SYSTEM_MAINTENANCE_SWITCH]').'</span></td></tr>';
	$sql = "SELECT ID,MESSAGE,DISPLAY FROM LOGIN_MESSAGE ORDER BY ID";
	$QI = DBQuery($sql);
	$login_MESSAGE=DBGet($QI,array('MESSAGE'=>'_makeContentInput','DISPLAY'=>'_makeRadio'));
	$link['add']['html'] = array('MESSAGE'=>_makeContentInput('','MESSAGE'),'DISPLAY'=>_makeRadio('','DISPLAY'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&page_display=MAINTENANCE";
	$link['remove']['variables'] = array('id'=>'ID');
	$columns = array('MESSAGE'=>'Login Message','DISPLAY'=>'Display');
	ListOutput($login_MESSAGE,$columns,'Message','Messages',$link, true, array('search'=>false));
	
	echo '<tr><td><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER></td></tr>';
	echo '</table>';
	echo '</FORM>';
 }
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='INACTIVITY'){
PopTable('header','User Inactivity Days');
include("User_activity_days.php");
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='FAILURE'){
PopTable('header','Login Failure Allowance');
include("Failure_count.php");
PopTable('footer');
}else if(clean_param($_REQUEST['page_display'],PARAM_ALPHAMOD)=='CURRENCY'){
PopTable('header','Currency');
include("Set_currency.php");
PopTable('footer');
}
else{

echo '
<style type="text/css">
.time_schedule { background:url(assets/time_schedule.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.login_failure { background:url(assets/login_failure.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.user_inactivity { background:url(assets/user_inactivity.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.maintenance { background:url(assets/maintenance.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
.currency { background:url(assets/currency.png) no-repeat 0px 0px; padding:10px 0px 10px 45px; text-align:left; margin:14px 280px; }
</style>

<div style=padding:20px 0px 0px 0px;>';
echo "<div class=time_schedule><a href=Modules.php?modname=$_REQUEST[modname]&page_display=SYSTEM_PREFERENCE><strong>Set half-day and full-day minutes</strong></a></div>";
echo "<div class=login_failure><a href=Modules.php?modname=$_REQUEST[modname]&page_display=FAILURE><strong>Set login failure allowance count</strong></a></div>";
echo "<div class=user_inactivity><a href=Modules.php?modname=$_REQUEST[modname]&page_display=INACTIVITY><strong>Set  allowable user inactivity days</strong></a></div>";
echo "<div class=maintenance><a href=Modules.php?modname=$_REQUEST[modname]&page_display=MAINTENANCE><strong>Put system in maintenance mode</strong></a></div>";
echo "<div class=currency><a href=Modules.php?modname=$_REQUEST[modname]&page_display=CURRENCY><strong>Set Currency</strong></a></div>";
echo '</div>';
}
function _makeContentInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
		$THIS_RET['ID'];
	return TextareaInput($value,"values[$id][$name]",'','rows=8 cols=55');
}
function makeTextInput($value,$name)
{	global $THIS_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name!='MESSAGE')
		$extra = 'size=5 maxlength=2 class=cell_floating';
		else 
	$extra = 'class=cell_floating ';
	
	if($name=='SORT_ORDER')
		$comment = '<!-- '.$value.' -->';

	return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
function _makeRadio($value,$name)
{	global $THIS_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($THIS_RET[$name]=='Y')
		return "<TABLE align=center><TR><TD><INPUT type=radio name=val[".$name."] value=".$id.",".$name." CHECKED></TD></TR></TABLE>";
	else
		return "<TABLE align=center><TR><TD><INPUT type=radio name=val[".$name."] value=".$id.",".$name."".(AllowEdit()?'':' ')."></TD></TR></TABLE>";
}

?>