var thiss;
var isEditable = true;
var fieldCounter = 0; // FIELD ID
var tabCounter = 0; //TAB ID


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

  obj.setting.error = $(this).find('.is-incorrect').prop('checked');
  obj.setting.ordenate = parseInt($(this).find('.ordenation').text().replace('(','').replace(')','')) ;
  obj.setting.isRequired = $(this).find('.update-required').hasClass('required');
  obj.setting.label = $(this).find('.update-label').text();
  obj.setting.help = $(this).find('.help .text').text();
  obj.setting.value = $(this).find('.update-value').val();
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
  
  $('.tab-control').removeClass('active');
  $('.tab-control:first-of-type').addClass('active');

  if(clientView){
    $('.draggable-input').removeClass('panel');
    $('.tabs-options').remove();
    $('.drag-heading li:not(:first-of-type)').remove();
    $('#list-container').remove();
    $('.drag-options').hide();
    $('.tab .modal').remove()
    $('nav .tab-control .fa').remove();
    $('.help .comment-icon').html($('<i>', {
      class : 'fa fa-comments toggle-comments',
      click : function(){
        $(this).closest('.draggable-input').find('.drag-comments').toggleClass('hidden');
      }
    }));
  }

  if(isClient){
    $('.internal-comments').remove();
    $('.open-internal').remove();

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
  configureField(clone, obj.setting, obj.type);
  obj.setting.signature;
  clones.splice(obj.setting.ordenate, 0, clone);

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
function configureField(node, options, type){

  /*Visual*/
  node.addClass('order_' + options.ordenate);

  node.find('.update-label').text(options.label);
  node.find('.update-label').val(options.label);
  node.find('.help .text').text(options.help);
  if(node.find('.help .text').text() == '') node.find('.help .icon').hide();

  //Size of the field
  node.addClass(options.class);

  node.find('.drag-validate').find('input[type="checkbox"]').prop('checked', options.error);

  //other attributes
  node.find('.drag-input input').attr({
    'min' : options.min,
    'max' : options.max,
    'value' : options.value,
    'step' : options.step,
  });

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
  node.find('.help-text').val(options.help);
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

function checkFieldValue(id, value, options){
  var obj = {
    _id : id,
    setting : {}
  };

  if(value != null){
    obj.setting.value = value;
  }else{
    obj.setting.options = options;
  }

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
        msg : $(this).find('span.message').text(),
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

