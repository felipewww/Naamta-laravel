/*
Example
var tabJson1 = {
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

var tabsJson = [tabJson1];
tabsJson = JSON.stringify(tabsJson);
createTabs(tabsJson)
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

//Creates form from current tabs and fields
function toHtml(){
  isEditable = false;
  json = toJson();

  $('#drag-container').remove();
  container = $('#cospe').append($('<form>'));
  container.find('form').append($('<div>', {
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
  $('form').submit(function( event ) {
    var obj = new Array();
    $(document.forms[0]).find('input, select').each(function(){
      obj.push ( {
        id : $(this).attr('id'),
        value : $(this).val()
      });
    });
    console.log(JSON.stringify(obj));
    event.preventDefault();
  });
}

