var thiss;
var isEditable = true;
var fieldCounter = 0; // FIELD ID
var tabCounter = 0; //TAB ID
var isClientView;
var isUserClient;

var filesArray = new Array();

// Temporary variables
var tempContainers;
var tempFields;
var clones = new Array();

// Transform fields in objects
function toFieldObject(){
  var obj = {
    _id : $(this).attr('data-id'),
    type :  $(this).attr('id').split("__")[0],
    isEditable : isEditable,
    setting : {
      options : [],
      rule : {
        ruleAction : $(this).find('.rule-action').val(),
        ruleTarget : $(this).find('.rule-target').val(),
        conditions : []
      }
    },
    comments : []
  };

  obj.setting.error = $('[name="incorrect__'+ obj._id +'"]:checked').val();
  obj.setting.ordenate = parseInt($(this).find('.ordenation').text().replace('(','').replace(')','')) ;
  obj.setting.isRequired = true;
  obj.setting.label = $(this).find('.update-label').text();
  obj.setting.help = $(this).find('.help-text').html();
  if(obj.type != 'file-upload'){
    obj.setting.value = $(this).find('.update-value').val();
  }
  obj.setting.checked = $(this).find('.update-value').prop('checked');
  obj.setting.min = $(this).find('.update-min').attr('min');
  obj.setting.max = $(this).find('.update-max').attr('max');
  obj.setting.step = $(this).find('.step-value').val();
  obj.setting.type = $(this).find('[type=radio]:checked').val();
  obj.setting.class = ($(this).hasClass('half-row')) ? 'half-row' : '';

  if(obj.type == 'signature'){
    [].forEach.call(canvasArray, function(sign){
      if(sign.field == obj._id){
        obj.setting.signature = sign.signature.toDataURL()
      }
    })
  }

  obj.comments = getComments(obj._id);

  var rules = $(this).find('.rules tr:not(:first-of-type)');

  rules.each(function(){
    var rule = {
      page : {
        _id : $(this).find('td.page-id').attr('page-id'),
        label : $(this).find('td.page-id').text()
      },
      field : {
        _id : $(this).find('td.field-id').attr('field-id'),
        index : $(this).find('td.field-id .ordenation').text().replace('(', '').replace(')',''),
        label : $(this).find('td.field-id .field-label').text()
      },
      comparison : {
        value : $(this).find('td.comparison').attr('value'),
        label : $(this).find('td.comparison').text()
      },
      value : {
        value : $(this).find('td.value').attr('value'),
        label : $(this).find('td.value').text()
      }
    }
    obj.setting.rule.conditions.push(rule);
  });

  if(obj.type == 'select'){
      var options = $(this).find('.drag-input select option');
      options.each(function(){
        var option = {
            label : $(this).text(),
            value : $(this).val(),
            prop : $(this).prop('selected')
        };
        obj.setting.options.push(option);
      });
    }

  if(obj.type == 'radio-group'){
    var options = $(this).find('.drag-input .radio');
    options.each(function(){
      var option = {
        label : $(this).find('label').text(),
        value : $(this).find('input').val(),
        prop : $(this).find('input').prop('checked')

      };
      obj.setting.options.push(option);
   });
  }

  if(obj.type == 'checkbox-group'){
    var options = $(this).find('.drag-input .checkbox');
    options.each(function(){
      var option = {
        label : $(this).find('label').text(),
        value : $(this).find('input').val(),
        prop : $(this).find('input').prop('checked')
      };
      obj.setting.options.push(option);
    });
  }
  tempFields.push(obj);
}

// Save current tabs and fields in Json
function toJson(){  
  tempContainers = new Array();
  var listContainers = $('.tab');

  listContainers.each(function(){
    var id = $(this).attr('id').replace("tab", "");
    var index = $(this).attr('id');
    var tabId = $(this).attr('tab-id');
    var title = $('a[href="#' + index + '"]').text();

    tempFields = new Array();
    var index = $(this).closest('.tab').index();
    var tab = {
      config : {
        _id : id,
        title : title,
        tabId : tabId
      },
      fields : []
    }
    var listFields = $(this).find('.draggable-input');
    listFields.each(toFieldObject);
    tab.fields = tempFields;
    tempContainers.push(tab);
  })

  var fieldArray = new Array();
  
  return JSON.stringify(tempContainers);
}

// Creates tabs from json
// Uses createFields
function createTabs(json, clientView = false, isClient){
  console.log(json);
  isUserClient = isClient;
  isClientView = clientView;
  $('#drag-container').toggleClass('client-view', clientView);
  $('.tab-control').remove();
  var objs = JSON.parse(json);
  objs.forEach(function(obj){
    clones = new Array();
    addTab(obj.config);
    if(obj.fields != undefined){
      [].forEach.call(obj.fields, function(obj){
        createFields(obj, clientView);
      });
    }
    clones.sort(function(a, b){
      var a = parseInt($(a).attr('class').split('order_')[1]);
      var b =  parseInt($(b).attr('class').split('order_')[1]);
      return a - b;
    });
    clones.forEach(function(clone){
      $(clone).appendTo('.tab.active');
      ordenateFields();
      updateRulesPages();
    });
  });
  
  $('.required-field').remove();
  $('.tab-control').removeClass('active');
  $('.tab-control:first-of-type').addClass('active');

  if(clientView){
    $('.draggable-input').removeClass('panel');
    $('.tabs-options #addTab').hide();
    $('.drag-heading li:not(:first-of-type)').hide();
    $('#list-container').hide();
    $('.drag-options').hide();
    $('.tab .modal').hide()
    $('nav .tab-control .fa').hide();
    $('.help .comment-icon').html($('<i>', {
      class : 'fa fa-comments toggle-comments',
      click : function(){
        $(this).closest('.draggable-input').find('.drag-comments').toggleClass('hidden');
      }
    }));
    $('#save-changes').show();
  }else{
    $('.filter').hide();
    $('#save-changes').hide();
  }
  
  if(isClient){
    $('.internal-comments').hide();
    $('.open-internal').hide();
  }

  if(isClient || !clientView){
    $('.drag-validate').hide();
  }
}

// Creates the fields related to the createTabs function
// Relates to createTabs
// Uses configureField
function createFields(obj, clientView){
  var clone = $('#input-types #' + obj.type).clone();
  $('.tab-control .tab-config').toggle(obj.isEditable);
  $('.tab-control .tab-remove').toggle(obj.isEditable);
  clone.find('.drag-heading').toggle(obj.isEditable);
  clone.find('.drag-options').toggle(obj.isEditable);
  configureField(clone, obj.setting, obj.type, obj._id);
  obj.setting.signature;
  clones.splice(obj.setting.ordenate, 0, clone);


  if(obj.type == 'file-upload'){
    filesArray[obj._id] = obj.setting.value;
  }

  obj.setting.options.forEach(function(option){
    addOption(obj.type, clone, option.label, option.value, option.prop, obj._id);
  });

  if(obj.comments != null){
    obj.comments.forEach(function(comment){
      appendComment(comment.username, comment.msg, comment.type, $(clone), comment._id);
    })
  }

  addEvents(clone[0], obj._id, obj.setting.signature);
  //rules
  clone.find('.rule-action').val(obj.setting.rule.ruleAction);
  clone.find('.rule-target').val(obj.setting.rule.ruleTarget);

  obj.setting.rule.conditions.forEach(function(condition){
    var page = condition.page;
    var field = condition.field;
    var comparison = condition.comparison;
    var value = condition.value;
    addRule( clone.find('.rules'), page, field, comparison, value);

  });

  if(obj.isEditable && clientView) {
    activateRule(obj.setting.ordenate, obj.setting.rule.ruleAction, obj.setting.rule.ruleTarget, obj.setting.rule.conditions);
  }
}

// Field values according to json from createTabs
// Relates to createFields
function configureField(node, options, type, id){

  /*Visual*/
  node.addClass('order_' + options.ordenate);

  node.find('.update-label').text(options.label);
  node.find('.update-label').val(options.label);
  node.find('.help + .text').html(options.help);
  node.find('.help-text').html(options.help);
  if(node.find('.help-text').html() == '') node.find('.help .icon').hide();

  //Size of the field
  node.addClass(options.class);

  node.find('.drag-validate input[value="'+ options.error +'"]').prop('checked', true);

  //other attributes
  node.find('.drag-input input').attr({
    'min' : options.min,
    'max' : options.max,
    'value' : options.value,
    'step' : options.step,
  });


  if( type == 'file-upload' ){
    if(options.value != null){
      [].forEach.call(options.value, function(file){
        var fileIcon;
        if(file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/svg+xml"){
          fileIcon = 'fa-file-image-o';
        }else if(file.type == "application/pdf"){
          fileIcon = 'fa-file-pdf-o';
        }else{
          fileIcon = 'fa-file-o';
        }
        file = file;
        var link = $('<h5 title="'+ file.name +'"><a href="/storage/' + file.path + '"><i class="fa '+ fileIcon +' m-r-10"></i><div>'+ file.name+'</div></a><div class="pull-right"><i class="fa fa-times remove-file"></i></div></h5>');
        link.find('.remove-file').click(function(){
          $(this).closest('h5').remove();
          [].forEach.call(filesArray[id], function(item, index){
            if(item.name == file.name){
              filesArray[id].splice(index, 1);
              checkFieldValue(id, filesArray[id])
            }
          });
        });
        node.find('.file-holder').append(link);
      });
    }else{
      node.find('.file-holder').append('No files attached.');
    }
    
  }

  if( type == 'checkbox' ){
    node.find('.drag-input input').prop('checked', options.checked)
  }

  if( type == 'button' ){
    node.find('.drag-input button').attr({'type' : options.type});
  }

  // required
  node.find('.span-required').toggle(options.isRequired);
  node.find('.update-required').toggleClass('required', options.isRequired);

  node.find('.update-value').text(options.value);
  /*Options*/
  node = node.find('.drag-options');
  node.find('.is-required').prop('checked', options.isRequired);
  node.find('.label-text').val(options.label);
  node.find('.help-text').html(options.help);

  node.find('.value').val(options.value);
  node.find('.min-value').val(options.min);
  node.find('.max-value').val(options.max);
  node.find('.step-value').val(options.step);
  node.find('input[value="' + options.type+'"]' ).prop('checked', true);
}

//activateRule(action, target, page, field, comparison, value)
function activateRule(obj_id, ruleAction, ruleTarget, conditions) {
  var cond = "";
  var changes = "";
  if(conditions.length >0){
    var i = 0;

    conditions.forEach(function( condition){
      var is_last_item = (i == (conditions.length - 1));

      var page = condition.page;
      var field = condition.field;
      var comparison = condition.comparison;
      var value = condition.value;
      changes += "'.order_"+field.index + " .drag-input .form-control'";
      cond += " " + "$('.order_"+field.index + " .drag-input .form-control').val()" + comparison.value + value.value;
      if(conditions.length>1 && !is_last_item){
        cond += (ruleTarget == "all" ? " && " : " || " );
        changes += ", ";
      }
      i++;
    });

    $(eval(changes)).change(function() {
      if(eval(cond)){
        if(ruleAction === "show"){
          $(".order_" + obj_id).show();
        }else{
          $(".order_" + obj_id).hide();
        }
      }else{
        if(ruleAction === "show"){
          $(".order_" + obj_id).hide();
        }else{
          $(".order_" + obj_id).show();
        }
      }
    });
    $(document).ready(function(){
      if(ruleAction === "show"){
        $(".order_" + obj_id).hide();
      }else{
        $(".order_" + obj_id).show();
      }
    })
  }
}
// Creates form from current tabs and fields
function toHtml(){
  isEditable = false;
  json = toJson();

  $('#drag-container').remove();
  container = $('body').append($('<form>', {
    id : 'blanko-form'
  }));
  container.find('#blanko-form').append($('<div>', {
    id : "drag-container"
  }));
  
  appendNavigation();
  appendModel();

  createTabs(json);
  
  $('#input-types').remove();
  $('.tabs-options').remove();
  $('.modal').remove();
  $('.drag-options').remove();
  $('.tab-config').remove();
  $('.tab-remove').remove();

  // $('form').submit(function( event ) {
  //   var obj = new Array();
  //   $(document.forms[0]).find('input, select').each(function(){
  //     obj.push ( {
  //       id : $(this).attr('id'),
  //       value : $(this).val()
  //     });
  //   });
  //   event.preventDefault();
  // });

  return ;
}

function checkFieldValue(id, value, options, isIncorrect, file){
  console.log(id, value, options, isIncorrect, file);
  $('#save-changes').removeClass('btn-default').addClass('btn-save').html('<i class="fa fa-check m-r-20"></i> Save Changes');
  var elem = $('.draggable-input[data-id="'+id+'"]');

  var obj = {
    _id : id,
    setting : {
    }
  };

  if(value != null){
    obj.setting.value = value;
  }
  if(options != null){
    obj.setting.options = options;
  }
  if(isIncorrect != null){
    obj.setting.error = isIncorrect;
  }

  if(file != null){
    if(filesArray[id] == null){
      filesArray[id] = new Array();
    }
      filesArray[id].push(file);
      obj.setting.value = filesArray[id];
    //Como está
    //obj.setting.value = file; //substitui pelo ultimo arquivo
    // Como tem que ser
  }

/*
  if(elem.find("input[type=file]").length > 0){
    var fData = new FormData();
    fData.append("folder", appFolder);
    fData.append("_token", window.Laravel.csrfToken);
    fData.append("upload", elem.find("input[type=file]").prop('files')[0]);
    $.ajax({
      url: "/upload-files", // Url to which the request is send
      type: "POST",             // Type of request to be send, called as method
      data: fData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,  // To send DOMDocument or non processed data file it is set to false
      async: false,
      success: function(data)   // A function to be called if request succeeds
      {
        obj.setting.value = data.upload;
      }
    });
  }
*/

  var sequence = { _token: window.Laravel.csrfToken, field: JSON.stringify(obj) };

  $.ajax({
    url: '/workflow/updateFormField',
    dataType: "json",
    method: 'POST',
    data: sequence,
    success: function (data) {
      console.log('Success!');
      //window.location.href = window.location.protocol + "//" + window.location.hostname;
    },
    error: function (data) {
      console.log('Error!');
    }
  });

  return obj;
}

function getComments(id){
  var elem = $('.draggable-input[data-id="'+id+'"]');
  var result = new Array();
  var comments = $(elem).find('.comments li');

  comments.each(function(){
    var comment = {
        _id : $(this).attr('comment-id'),
        fieldId : id,
        username : $(this).find('span.username').text(),
        msg : $(this).find('.message').text(),
        type : $(this).attr('comment-type')
      };
    result.push(comment);
  });

  return result;
}


function saveComments(id, username, message, type){
  var comment = {
    fieldId : id,
    username : username,
    msg : message,
    type : type
  };

  var sequence = { _token: window.Laravel.csrfToken, comment:  JSON.stringify(comment)  };
  $.ajax({
    url: '/workflow/addFieldComment',
    dataType: "json",
    method: 'POST',
    data: sequence,
    success: function (result) {
      return commentCallback(result);
    },
    error: function (data) {
      
    }
  });
}
function commentCallback(result) {
  console.log(result.commentId);
}

