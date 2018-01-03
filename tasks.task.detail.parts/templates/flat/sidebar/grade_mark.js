function insertAfter(elem, refElem) {
    return refElem.parentNode.insertBefore(elem, refElem.nextSibling);
}

Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}

function removeClassNameByPart($el, part){
    var classes = $el.attr('class');
    if(!classes) return false;

    var classArray = [];
    classes = classes.split(' ');

    for(var i=0, len=classes.length; i<len; i++)
        if(classes[i].indexOf(part) === -1) classArray.push(classes[i]);

    $el.attr('class', classArray.join(' '));
    return $el;
}

function setMarkTaskDataByID(taskID){
    var url = BX.message('TASK_COMPONENT_PATH');
    var res = false;
    if(!taskID){
        alert('Пустой ID задачи');
        return false;
    }
    var form = $('#bx-task-grade-simple-popup-custom-form-'+taskID);
    var formData = form.serializeArray();

    var data = {taskID: taskID, formData: formData};

    $.ajax({
        url: url + '/ajax_set_task_mark_by_id.php',
        dataType : 'json',
        data: data,
        method: 'POST',
        type: 'POST',
        success: function (data, textStatus) {
            if(data.res == false){
                alert(data.err);
            }else{
                res = data.arr;
                var $el = $('#task-detail-mark-show');
                if(typeof(data.className) !== 'undefined'){
                    removeClassNameByPart($el, 'task-mark-custom-');
                    $el.addClass(data.className);
                }

                if(typeof(data.linkName) !== 'undefined')
                    $el.text(data.linkName);
            }

        },
        error: function( jqXHR, textStatus, errorThrown) {
            console.log('jqXHR = ' + jqXHR);
            console.log('textStatus = ' + textStatus);
            console.log('errorThrown = ' + errorThrown);
        }
    });

    return res;
}

function getMarkTaskDataByID(taskID){
    var url = BX.message('TASK_COMPONENT_PATH');
    var res = false;
    if(!taskID){
        alert('Пустой ID задачи');
        return false;
    }
    var data = {taskID: taskID};
    $.ajax({
        url: url + '/ajax_get_task_mark_by_id.php',
        dataType : 'json',
        data: data,
        method: 'POST',
        async: false,
        type: 'POST',
        success: function (data, textStatus) {
            if(data.res == false){
                alert(data.err);
            }else{
                res = data.arr;
                var $el = $('#task-detail-mark-show');
                if(typeof(data.className) !== 'undefined'){
                    removeClassNameByPart($el, 'task-mark-custom-');
                    $el.addClass(data.className);
                }
                if(typeof(data.linkName) !== 'undefined')
                    $el.text(data.linkName);
            }

        },
        error: function( jqXHR, textStatus, errorThrown) {
            console.log('jqXHR = ' + jqXHR);
            console.log('textStatus = ' + textStatus);
            console.log('errorThrown = ' + errorThrown);
        }
    });

    return res;
}

function closeCustomPopup(id){
    var el = document.getElementById(id);
    if(!el)
        return false;
    el.remove();
    return false;
}

function openGrageMarkWindow(taskID){
    var el = document.getElementById('bx-task-grade-simple-popup-custom');
    if(el){
        el.remove();
        return false;
    }
    var arr = getMarkTaskDataByID(taskID);
    if(!arr)
        return false;

    var str = '<form id="bx-task-grade-simple-popup-custom-form-'+taskID+'">';
    var checked;
    for (var key in arr) {
        checked = arr[key]['SELECTED'] ? 'checked' : '';
        str += '<div class="task-popup-list-item"><input '+checked+' type="checkbox" onchange="setMarkTaskDataByID('+taskID+')" name="mark" id="task-mark-'+key+'" value="'+key+'"><label class="task-popup-list-item__label" for="task-mark-'+key+'">'+arr[key]['VALUE']+'</label></div>'
    }
    str += '</form>';

    var div = document.createElement('div');
    div.id = "bx-task-grade-simple-popup-custom";
    div.className = "popup-window";
    div.innerHTML = '<div class="popup-window-close-icon" onclick=\'closeCustomPopup("'+div.id +'");\'></div><div id="popup-window-content-bx-task-grade-simple-popup" class="popup-window-content __web-inspector-hide-shortcut__"><div class="task-grade-popup" style="display: block;"><div class="task-grade-popup-title">Оценка</div><div class="popup-window-hr"><i></i></div><div class="task-popup-list-list">'+str+'</div></div></div>';

    insertAfter(div, document.getElementById('task-detail-mark-show'));
}