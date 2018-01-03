<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

function GetUserField ($entity_id, $value_id, $uf_id)
{
    $arUF = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields ($entity_id, $value_id);
    return $arUF[$uf_id]["VALUE"];
}

$arRes = Array('res' => true, 'err' => '');
$taskID = intval($_POST['taskID']);

if($taskID === 0){
    $arRes['res'] = false;
    $arRes['err'] = 'Пустой ID задачи';
    echo json_encode($arRes, JSON_UNESCAPED_UNICODE);
    require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/epilog_after.php");
    exit;
}

$arRes['checked']  = GetUserField('TASK', $taskID, 'UF_TASK_MARK');
$cntChecked = is_array($arRes['checked']) ? count($arRes['checked']) : 0;

$cntProp = 0;
$rs = CUserFieldEnum::GetList(array('SORT' => 'ASC'), array('USER_FIELD_NAME' => 'UF_TASK_MARK'));
while($ar = $rs->GetNext()) {
    $arRes['arr'][$ar['ID']] = Array(
        'VALUE' => $ar['VALUE'],
        'SELECTED' => in_array($ar['ID'], $arRes['checked'])
    );
    $cntProp++;
}

$arRes['className'] = '';
if($cntProp > 0){
    $done = $cntChecked/$cntProp;
    if($done == 1){
        $arRes['className'] = 'task-mark-custom-well-done';
    }elseif($done == 0){
        $arRes['className'] = 'task-mark-custom-bad-done';
    }else{
        $arRes['className'] = 'task-mark-custom-midd-done';
    }
    $arRes['linkName'] = round($done, 1);
}

echo json_encode($arRes, JSON_UNESCAPED_UNICODE);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/epilog_after.php");