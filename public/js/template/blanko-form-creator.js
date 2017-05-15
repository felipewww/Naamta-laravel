var thiss;
var isEditable = true;
var fieldCounter = 0; // FIELD ID
var tabCounter = 0; //TAB ID
var username = 'User Name'; // USER NAME
var ruleField;

// Temporary variables
var tempContainers;
var tempFields;
var clones = new Array();

// Retrieve variables from storage
 //var form = localStorage.getItem('form');
 //fieldCounter = localStorage.getItem('fieldCounter');
 //tabCounter = localStorage.getItem('tabCounter');

//if(form != null){
// createTabs(form);
//}

/*
Example

var tabObj1 = {
  config : {
    id : 1959595,
    title: 'Title',
  },
  fields : [
    {
      id : 1233123,
      type : 'checkbox-group',
      isEditable : true,
      comments : [
        {
          username : 'John',
          msg : 'A Comment'
        },
        {
          username : 'Josephine',
          msg : 'Another Comment'
        }
      ],
      options : {
        isRequired : true,
        label : 'Label',
        help : 'Help Text',
        value : '',
        min : '',
        max : '',
        step : '',
        type : '',
        options : [
          {
            label : 'Option Label 1',
            value : 'Option Value 1'
          },
          {
            label : 'Option Label 2',
            value : 'Option Value 2'
          }
        ]
      }
    }
  ]
}

// Array of tabs
var tabsObj = [tabObj1];
// Stringify array
tabsObj = JSON.stringify(tabsObj);
// Create from stringified array
createTabs(tabsObj);

*/

// Transform fields in objects
function toFieldObject(){
  var obj = {
    id : $(this).attr('data-id'),
    type :  $(this).attr('id').split("__")[0],
    isEditable : isEditable,
    options : {
      options : [],
      rules : {
        ruleAction : $(this).find('.rule-action').val(),
        ruleTarget : $(this).find('.rule-target').val(),
        conditions : []
      }
    },
    comments : []
  };
  
  obj.options.ordenate = parseInt($(this).find('.ordenation').text().replace('(','').replace(')','')) ;
  obj.options.isRequired = $(this).find('.is-required').prop('checked');
  obj.options.label = $(this).find('.update-label').text();
  obj.options.help = $(this).find('.help .text').text();
  obj.options.value = $(this).find('.update-value').val();
  obj.options.checked = $(this).find('.update-value').prop('checked');
  obj.options.min = $(this).find('.update-min').attr('min');
  obj.options.max = $(this).find('.update-max').attr('max');
  obj.options.step = $(this).find('.step-value').val();
  obj.options.type = $(this).find('[type=radio]:checked').val();
  obj.options.class = ($(this).hasClass('half-row')) ? 'half-row' : '';

  if(obj.type == 'signature'){
    [].forEach.call(canvasArray, function(sign){
      if(sign.field == obj.id){
        obj.options.signature = sign.signature.toDataURL()
      }
    })
  }

  var comments = $(this).find('.comments li');

  comments.each(function(){
    var comment = {
        fieldId : obj.id,
        username : $(this).find('span.username').text(),
        msg : $(this).find('span.message').text(),
        type : $(this).attr('comment-type')
      };
    obj.comments.push(comment);
  });

  var rules = $(this).find('.rules tr:not(:first-of-type)');

  rules.each(function(){
    var rule = {
      page : {
        id : $(this).find('td.page-id').attr('page-id'),
        label : $(this).find('td.page-id').text()
      },
      field : {
        id : $(this).find('td.field-id').attr('field-id'),
        index : $(this).find('td.field-id .ordenation').text(),
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
    obj.options.rules.conditions.push(rule);
  });

  if(obj.type == 'select'){
      var options = $(this).find('.drag-input select option');
      options.each(function(){
        var option = {
            label : $(this).text(),
            value : $(this).val(),
            prop : $(this).prop('selected')
        };
        obj.options.options.push(option);
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
      obj.options.options.push(option);
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
      obj.options.options.push(option);
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
        id : id,
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
function createTabs(json, clientView = false){
  $('.tab-control').remove();
  var objs = JSON.parse(json);
  
  objs.forEach(function(obj){
    clones = new Array();
    addTab(obj.config);
    if(obj.fields != undefined){
      obj.fields.forEach(createFields);
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
      resizeCanvas();
    });
  });
  
  $('.tab-control').removeClass('active');
  $('.tab-control:first-of-type').addClass('active');

  if(clientView){
    $('#drag-container').addClass('client-view');
    $('.draggable-input').removeClass('panel');
    $('.tabs-options').remove();
    $('.drag-heading').hide();
    $('#list-container').remove();
    $('.drag-options').remove();
    $('nav .tab-control .fa').remove();
    $('.help .comment-icon').html($('<i>', {
      class : 'fa fa-comments toggle-comments',
      click : function(){
        $(this).closest('.draggable-input').find('.drag-comments').toggleClass('hidden');
      }
    }));
  }
}

// Creates the fields related to the createTabs function
// Relates to createTabs
// Uses configureField
function createFields(obj, index, array, isRule){
  var clone = $('#input-types #' + obj.type).clone();
  
  $('.tab-control .tab-config').toggle(obj.isEditable);
  $('.tab-control .tab-remove').toggle(obj.isEditable);
  clone.find('.drag-heading').toggle(obj.isEditable);
  clone.find('.drag-options').toggle(obj.isEditable);
  configureField(clone, obj.options, obj.type);
  obj.options.signature;
  addEvents(clone[0], obj.id, obj.options.signature);
  clones.splice(obj.options.ordenate, 0, clone);

  if(obj.comments != null){
    obj.comments.forEach(function(comment){
      appendComment(comment.username, comment.msg, comment.type, $(clone));
    })
  }

  //rules
  clone.find('.rule-action').val(obj.options.rules.ruleAction);
  clone.find('.rule-target').val(obj.options.rules.ruleTarget);

  if(obj.options.rules != null){
    obj.options.rules.conditions.forEach(function(condition){
      var page = condition.page;
      var field = condition.field;
      var comparison = condition.comparison;
      var value = condition.value;
      addRule( clone.find('.rules'), page, field, comparison, value);
    });
  }

  if(isRule != null){
    ruleField = clone;
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

  options.options.forEach(function(obj){
    addOption(type, node, obj.label, obj.value, obj.prop);
  });

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
  //   console.log(JSON.stringify(obj));
  //   event.preventDefault();
  // });

  return 
}