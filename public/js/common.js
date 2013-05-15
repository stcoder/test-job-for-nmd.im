var formServiceCreate = $('form#form_service_create');
var dialogModal = $('#dialog-modal');
var bodyLists = $('.body');
formServiceCreate.submit(function() {
    var parentService = $(this).find('#parents option:selected').val();
    var serviceName   = $(this).find('#name');
    if (serviceName.val() == '' || serviceName.val().length > 255) {
        serviceName.focus();
        return false;
    }
    serviceName = serviceName.val();
    $.getJSON(this.action, {parent_id: parentService, service_name: serviceName}, function(response) {
        console.log(response);
        if (response.created == true) {
            var isChild = (response.item.parent_id == 0) ? '' : 'class="child-service"';
            var html = '<ul ' + isChild + '><li id="service' + response.item.id + '"><p>' + response.item.name + '<span class="controll"><a href="#" onclick="serviceEdit(this);return false;">edit</a><a href="#" onclick="serviceDel(this);return false;">del</a></span></p></li></ul>';
            if (isChild == '') {
                $('#service' + response.item.parent_id).append(html);
            } else {
                $('#service' + response.item.parent_id).parent().append(html);
            }
        }
    });
    return false;
});

function serviceEdit(el) {
    var id = $(el).parents('li').attr('id').slice(7);
    $.getJSON('/?q=edit', {id: id}, function(response) {
        console.log(response);
        dialogModal.html(response.form);
        dialogModal.dialog({
            width: 512,
            modal: true,
            title: 'Редактирование'
        });
    });
}

function serviceSave(el) {
    var form = $(el).parents('form');
    var parentService = form.find('#parents option:selected').val();
    var serviceName   = form.find('#name');
    var id = form.find('#id').val();
    if (serviceName.val() == '' || serviceName.val().length > 255) {
        serviceName.focus();
        return false;
    }
    serviceName = serviceName.val();
    $.getJSON(form.attr('action'), {id: id, parent_id: parentService, service_name: serviceName}, function(response) {
        if (response.saved == true) {
            bodyLists.html(response.items);
            dialogModal.dialog('close');
        }
    });
    return false;
}

function serviceDel(el) {
    var id = $(el).parents('li').attr('id').slice(7);
    $.getJSON('/?q=delete', {id: id}, function(response) {
        if (response.deleted == true) {
            bodyLists.html(response.items);
        }
    });
    return false;
}