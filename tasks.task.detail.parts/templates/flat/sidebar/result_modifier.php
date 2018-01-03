<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */

$taskData = $arParams["TEMPLATE_DATA"]["DATA"]["TASK"];

$arParams["TEMPLATE_DATA"]["PATH_TO_TEMPLATES_TEMPLATE"] = \Bitrix\Tasks\UI\Task\Template::makeActionUrl($arParams["PATH_TO_TEMPLATES_TEMPLATE"], $taskData["SE_TEMPLATE"]["ID"], 'view');
$arParams["TEMPLATE_DATA"]["PATH_TO_TEMPLATES_TEMPLATE_SOURCE"] = \Bitrix\Tasks\UI\Task\Template::makeActionUrl($arParams["PATH_TO_TEMPLATES_TEMPLATE"], $taskData["SE_TEMPLATE.SOURCE"]["ID"], 'view');

$arParams["TEMPLATE_DATA"]["TAGS"] = \Bitrix\Tasks\UI\Task\Tag::formatTagString($taskData["SE_TAG"]);

//Dates
$dates = array(
	"STATUS_CHANGED_DATE",
	"DEADLINE",
	"CREATED_DATE",
	"START_DATE_PLAN",
	"END_DATE_PLAN"
);

foreach ($dates as $date)
{
	$formattedDate = "";
	if (isset($taskData[$date]) && strlen($taskData[$date]))
	{
		$formattedDate = \Bitrix\Tasks\UI::formatDateTime(\Bitrix\Tasks\UI::parseDateTime($taskData[$date]), '^'.\Bitrix\Tasks\UI::getDateTimeFormat());
	}
	
	$arParams["TEMPLATE_DATA"][$date] = $formattedDate;
}

$iAmAuditor = false;
$currentUserId = \Bitrix\Tasks\Util\User::getId();
if(is_array($taskData["SE_AUDITOR"]))
{
	foreach($taskData["SE_AUDITOR"] as $user)
	{
		if($user['ID'] == $currentUserId)
		{
			$iAmAuditor = true;
			break;
		}
	}
}

if($arParams['USER'])
{
	$arParams['USER'] = \Bitrix\Tasks\Util\User::extractPublicData($arParams['USER']);
	$arParams['USER']['AVATAR'] = \Bitrix\Tasks\UI::getAvatar($arParams['USER']['PERSONAL_PHOTO'], 58, 58);
}

$arParams['TEMPLATE_DATA']['I_AM_AUDITOR'] = $iAmAuditor;

/*class name и link title для поля Оценка результата*/
$cntProp = 0;
$rs = CUserFieldEnum::GetList(array('SORT' => 'ASC'), array('USER_FIELD_NAME' => 'UF_TASK_MARK'));
while($ar = $rs->GetNext()) {
    $arRes['arr'][$ar['ID']] = Array(
        'VALUE' => $ar['VALUE'],
        'SELECTED' => in_array($ar['ID'], $arRes['checked'])
    );
    $cntProp++;
}

if(!function_exists('GetUserField')) {
    function GetUserField($entity_id, $value_id, $uf_id) //считывание значения
    {
        $arUF = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields($entity_id, $value_id);
        return $arUF[$uf_id]["VALUE"];
    }
}

$arRes['checked']  = GetUserField('TASK', $arResult['TASK_ID'], 'UF_TASK_MARK');
$cntChecked = is_array($arRes['checked']) ? count($arRes['checked']) : 0;

$arResult['customMarkclassName'] = '';
if($cntProp > 0){
    $done = $cntChecked/$cntProp;
    if($done == 1){
        $arResult['customMarkclassName'] = 'task-mark-custom-well-done';
    }elseif($done == 0){
        $arResult['customMarkclassName'] = 'task-mark-custom-bad-done';
    }else{
        $arResult['customMarkclassName'] = 'task-mark-custom-midd-done';
    }
    $arResult['customMarkLinkName'] = round($done, 1);
}
/*class name и link title для поля Оценка результата*/