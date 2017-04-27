var tempContainers;
var tempFields;
var isEditable = true;

//Temporary variables
var tabId = 0;
var fieldId = 0;
var inputId = 0;
var tabCounter = 0;


// // Retrieve the json from storage
 var form = localStorage.getItem('form');
 fieldId = localStorage.getItem('fieldId');
 tabCounter = localStorage.getItem('tabCounter');

if(form != null){
  createTabs(form);
}


/*
Example
var tabObj1 = {
  config : {},
  fields : [
    {
      type : 'checkbox-group',
      isEditable : true,
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
            label : 'Option Label',
            value : 'Option Value'
          }
        ]
      }
    }
  ]
}
//Array of tabs
var tabsObj = [tabObj1];
Stringify array
tabsObj = JSON.stringify(tabsObj);
Create from stringified array
createTabs(tabsObj);

*/



//Transform fields in objects
function toFieldObject(){
	var obj = {
    id : $(this).attr('id').split("__")[1],
		type :  $(this).attr('id').split("__")[0],
		isEditable : isEditable,
		options : {
			options : []
		}
	};

	obj.options.isRequired = $(this).find('.is-required').prop('checked');
	obj.options.label = $(this).find('.label-text').val();
	obj.options.help = $(this).find('.help-text').val();
	obj.options.value = $(this).find('.value').val();
	obj.options.min = $(this).find('.min-value').val();
	obj.options.max = $(this).find('.max-value').val();
	obj.options.step = $(this).find('.step-value').val();
  obj.options.type = $(this).find('[type=radio]:checked').val();

	if(obj.type == 'select'){
		var options = $(this).find('select option');
		options.each(function(){
			var option = {
				label : $(this).text(),
        value : $(this).val()
			};
			obj.options.options.push(option);
		});
	}

  if(obj.type == 'radio-group'){
    var options = $(this).find('.drag-input .radio');
    options.each(function(){
      var option = {
        label : $(this).find('label').text(),
        value : $(this).find('input').val()
      };
      obj.options.options.push(option);
    });
  }

  if(obj.type == 'checkbox-group'){
    var options = $(this).find('.drag-input .checkbox');
    options.each(function(){
      var option = {
        label : $(this).find('label').text(),
        value : $(this).find('input').val()
      };
      obj.options.options.push(option);
    });
  }
	tempFields.push(obj);
}

//Save current tabs and fields in Json
function toJson(){	
  tempContainers = new Array();
  var listContainers = $('.tab');

  listContainers.each(function(){
    tempFields = new Array();

    var tab = {
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
function createTabs(json){
  var objs = JSON.parse(json);
  objs.forEach(function(obj){
    $('.tab-control').removeClass('active');
    addTab();
    obj.fields.forEach(createFields);
  });
}

// Creates the fields related to the createTabs function
// Relates to createTabs
// Uses configureField
function createFields(obj){
  var clone = $('#input-types #' + obj.type).clone();
  $('.tab-control .tab-config').toggle(obj.isEditable);
  $('.tab-control .tab-remove').toggle(obj.isEditable);
  clone.find('.drag-heading').toggle(obj.isEditable);
  clone.find('.drag-options').toggle(obj.isEditable);
  configureField(clone, obj.options, obj.type);

  addEvents(clone[0], obj.id);
  
  $(clone).appendTo('.tab.active');
}

// Sets field values according to json from createTabs
// Relates to createFields
function configureField(node, options, type){
  /*Visual*/
  node.find('.update-label').text(options.label);
  node.find('.update-label').val(options.label);
  node.find('.help .text').text(options.help);
  if(options.help == '') node.find('.help .icon').hide();

  //other attributes
  node.find('.drag-input input').attr({
    'min' : options.min,
    'max' : options.max,
    'value' : options.value,
    'step' : options.step,
  });

  if( type == 'button'){
    node.find('.drag-input button').attr({'type' : options.type});
  }

  // required

  node.find('.span-required').toggle(options.isRequired);
  node.find('.update-required').toggleClass('required', options.isRequired);

  node.find('.update-value').text(options.value);

  options.options.forEach(function(obj){
    addOption(type, node, obj.label, obj.value);

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

//Creates form from current tabs and fields
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