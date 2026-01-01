const HOST_URL = 'http://localhost:8071';
let popupData;
let CurrentNodeUser = null;
let userDict = {

};

let colorMale = '#669ef2ff';
let colorFemale = '#ea828cff';

function openModal() {
    $('#nodeModal').removeClass('d-none').addClass('show');
}

function closeModal() {
    $('#nodeModal').addClass('d-none').removeClass('show');

    // clear fields
    $('#nodeModal').find('input, textarea').val('');
    $('input[name="dob"]').val('1996');
    $('#nodeModal').find('select').prop('selectedIndex', 0);

    const $modal = $('#nodeModal');

    $modal.find('input, textarea, select')
    .removeClass('is-invalid error')
    .removeAttr('aria-invalid');

    $modal.find('.error-text, .invalid-feedback').remove();
}

$('.close-btn').on('click', closeModal);



function getRootUserData(root_id = 1) {
    $.ajax({
        url: HOST_URL + '/root/get_tree?root_id=' + root_id,
        type: 'GET',
        success: function (response) {
            if (response.status == 1) {
                CurrentNodeUser = response.data;
                renderDom();
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}

function getUserData(root_id = 1,callback) {
    $.ajax({
        url: HOST_URL + '/root/view_user?root_id=' + root_id,
        type: 'GET',
        success: function (response) {
            if (response.status == 1) {
                let temp = response.data;
                userDict[root_id] = temp;
                if(temp.spouse.length > 0){
                    let spouse = temp.spouse[0].spouse;
                    callback(spouse.name,spouse.id);
                }else if(temp.spouse_of.length > 0){
                    let spouse_of = temp.spouse_of[0].person;
                    callback(spouse_of.name,spouse_of.id);
                }else{
                    callback('-',0);
                }
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}

function updateParentNode(data) {
    let html = `<div class="h-line"></div>`;
    for (i of data) {
        let temp = i.parent;
        let color = temp.gender === 'male' ? colorMale : colorFemale;
        html += `
        <div class="node" id="node_id_${temp.id}" data-val='${JSON.stringify(temp)}' style='background: ${color}'>
            ${temp.name}
        </div>
        `;
    }
    if (data.length === 0) {
        $('#parents_node').hide();
        $('#arrow1').hide();
        return;
    } else {
        $('#arrow1').show();
        $('#parents_node').show();
    }
    $('#parents_node').html(html);
}

function updateChildNode(data) {
    let html = `<div class="h-line"></div>`;
    for (i of data) {
        let temp = i.child;
        let color = temp.gender === 'male' ? colorMale : colorFemale;
        html += `
        <div class="node" id="node_id_${temp.id}" data-val='${JSON.stringify(temp)}' style='background: ${color}'>
            ${temp.name}
        </div>
        `;
    }
    if (data.length === 0) {
        $('#child_node').hide();
        $('#arrow2').hide();
        return;
    } else {
        $('#arrow2').show();
        $('#child_node').show();
    }
    $('#child_node').html(html);
}

function updateSiblingsNode(data) {
    let html = `<div class="h-line"></div>`;
    for (let temp of data) {
        let color = temp.gender === 'male' ? colorMale : colorFemale;
        html += `
        <div class="node" id="node_id_${temp.id}" data-val='${JSON.stringify(temp)}' style='background: ${color}'>
            ${temp.name}
        </div>
        `;
    }

    $('#current_node').html(html);
}

function renderDom() {
    const { id, dob, name, gender, spouse, spouse_of } = CurrentNodeUser;

    const currentUser = { id, dob, name, spouse, spouse_of ,gender};

    let arrSibling = CurrentNodeUser.siblings;
    const middleIndex = Math.floor(arrSibling.length / 2);


    arrSibling.splice(middleIndex, 0, currentUser);
    
    updateParentNode(CurrentNodeUser.parents);
    updateChildNode(CurrentNodeUser.children);
    updateSiblingsNode(arrSibling);

    $('#title').text(name + ' Family Tree');
}

$(document).ready(function () {
    getRootUserData(BaseID);
});

$(document).on('click', '.node', function () {
    openModal();
    popupData = $(this).attr('data-val');
    popupData = JSON.parse(popupData);
    $('#popup_name').text(popupData.name);
    
    const gender =
    popupData.gender
        ? popupData.gender.charAt(0).toUpperCase() + popupData.gender.slice(1)
        : '';
    $('#popup_gender').text(gender);
    $('#popup_dob').text(popupData.dob ?? '-');

    function callback(val,id=1){
        if(val == '-'){
            $('#view_tree2').hide();
        }else{
            $('#view_tree2').show();
        }
        $('#popup_relation').text(val);
        $('#view_tree2').attr('data-key',id);
    }

    getUserData(popupData.id,callback);
    
    // $('#popup_relation').text(popupData.dob);
    
});



$(document).ready(function () {

    $('#saveBtn').on('click', function (e) {
        e.preventDefault();

        // clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        let isValid = true;

        // Relation
        if ($('#relation_add').val() === '0') {
            showError('#relation_add', 'Please select a relation');
            isValid = false;
        }

        // Name
        if ($('input[name="add_name"]').val().trim() === '') {
            showError('input[name="add_name"]', 'Name is required');
            isValid = false;
        }

        // DOB
        if ($('input[name="dob"]').val() === '') {
            showError('input[name="dob"]', 'Date of birth is required');
            isValid = false;
        }

        // Gender
        if ($('#gender').val() === '') {
            showError('#gender', 'Please select gender');
            isValid = false;
        }

        // ❌ stop if validation fails
        if (!isValid) return;

        // ✅ everything OK → call AJAX
        callAjax();
    });

    function showError(selector, message) {
        const el = $(selector);
        el.addClass('is-invalid');
        el.after(`<div class="invalid-feedback">${message}</div>`);
    }

    function callAjax() {
        let dob = $('input[name="dob"]').val();
        let formData = {
            relation: $('#relation_add').val(),
            name: $('input[name="add_name"]').val(),
            dob: dob + '-01-01',
            gender: $('#gender').val(),
        };
        formData.root_id = popupData.id;
        
        // // 🔽 your ajax logic here
        $.ajax({
          url: HOST_URL+'/root/add',
          method: 'POST',
          data: formData,
          success: function (res) {
            if(res.status == 0){
                alert(res.message);
            }else{
                alert(res.message);
                getRootUserData(CurrentNodeUser.id);
                closeModal();
            }
          },
          error: function (err) {
            alert('Something went wrong');
          }
        });
    }

});


$('#view_tree').click(function(){
    getRootUserData(popupData.id);
    closeModal();
});

$('#view_tree2').click(function(){
    let getID = $(this).attr('data-key');
    getRootUserData(getID);
    closeModal();
    
});