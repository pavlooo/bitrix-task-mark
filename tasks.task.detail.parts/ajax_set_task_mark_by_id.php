<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

function SetUserField ($entity_id, $value_id, $uf_id, $uf_value) //запись значения
{
    return $GLOBALS["USER_FIELD_MANAGER"]->Update ($entity_id, $value_id,
        Array ($uf_id => $uf_value));
}

function GetUserField ($entity_id, $value_id, $uf_id) //считывание значения
{
    $arUF = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields ($entity_id, $value_id);
    return $arUF[$uf_id]["VALUE"];
}

$arRes = Array('res' => true, 'err' => '');
$arRes['POST'] = $_POST;
$taskID = intval($_POST['taskID']);

if($taskID === 0){
    $arRes['res'] = false;
    $arRes['err'] = 'Пустой ID задачи';
    echo json_encode($arRes, JSON_UNESCAPED_UNICODE);
    require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/epilog_after.php");
    exit;
}

/*тут необходима проверка на права, чтобы только нужный пользователь мог обновлять св-во*/

$arUpdate = Array();
foreach($_POST['formData'] as $k => $v){
    if(($v['name'] == 'mark') && (intval($v['value']) > 0))
        $arUpdate[] = intval($v['value']);
}
$arRes['arUpdate'] = $arUpdate;

$arRes['res']  = SetUserField('TASK', $taskID, 'UF_TASK_MARK', $arUpdate);

/*класс который припишем ссылке в зависимости от кол. выбранных пунктов*/
$cntProp = 0;
$rs = CUserFieldEnum::GetList(array('SORT' => 'ASC'), array('USER_FIELD_NAME' => 'UF_TASK_MARK'));
while($ar = $rs->GetNext()) {
    $cntProp++;
}

$arRes['className'] = '';
if($cntProp > 0){
    $done = count($arRes['arUpdate'])/$cntProp;
    if($done == 1){
        $arRes['className'] = 'task-mark-custom-well-done';
    }elseif($done == 0){
        $arRes['className'] = 'task-mark-custom-bad-done';

    }else{
        $arRes['className'] = 'task-mark-custom-midd-done';
    }
    $arRes['linkName'] = round($done, 1);
}
/*класс который припишем ссылке в зависимости от кол. выбранных пунктов*/

echo json_encode($arRes, JSON_UNESCAPED_UNICODE);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');