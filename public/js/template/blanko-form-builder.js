var canvas;
var canvasArray = new Array();
appendNavigation();
appendList();
appendModel();

// For Drag And Drop //

// Drop location
var lastValidField;
//Source Element
var dragSrcEl = null;

// Element to represent where it will be placed
var blankSpace = $( "<div>", { id: "blankSpace" } )[0];
// Add drag events to it
blankSpace.addEventListener('dragover', handleDragOver);
blankSpace.addEventListener('drop', handleDrop);
 
// Fixed menu while scrolling
var menuTop = $('#list-container').offset().top;
$(window).on('scroll', function(){
  var scrollTop = $(window).scrollTop();
  // If the scroll reached the menu, the menu fixes on the screen
  if(scrollTop >= menuTop && (scrollTop + $('#list-container').height()) < (scrollTop + $('#drag-container').height()) ){
      $('#list-container').addClass('fixed').css({
          top: scrollTop - menuTop
      });
  }else{
      $('#list-container').removeClass('fixed');
  }
});

//When drag starts
function handleDragStart(e) {
  // (this) is the source node.
  // Save the source
  var field = $(this).parent()[0];
  dragSrcEl = field;
  // Change opacity to identify the field that will be moved
  $(field).addClass('on-drag');
  if($(field).hasClass('half-row')){
    $(blankSpace).addClass('half-row')
  }else{
    $(blankSpace).removeClass('half-row');
  }
}

// When any element is dragged over a field in the form-holder
function handleDragOver(e) {
  // Prevents any action
  if (e.preventDefault) {
    e.preventDefault();
  }
  // If it's not a blankSpace, then it's a valid field
  if(this != $('#blankSpace')[0]){
    lastValidField = this;
  }
  // Remove the last blankSpace
  $('#blankSpace').remove();

  // If the source and the valid field are not the same
  if(lastValidField != dragSrcEl){
    // If the source element is under the valid field
    if(dragSrcEl.offsetTop > lastValidField.offsetTop){
      // Put blankSpace above the valid field
      $(blankSpace).insertBefore(lastValidField);
      
    // If the source element is above the valid field
    }else{
      // Put blankSpace under the valid field
      $(blankSpace).insertAfter(lastValidField);
    }
  }
  return false;
}

// When an element of the list is dragged from the menu
function handleDragEnd(e) {
  // Remove on-drag class from the source element
  $(dragSrcEl).removeClass('on-drag');
  ordenateFields();
  
  updateRulesPages();
}

// When an element of the list is dragged from the menu
function handleDragStartList(e) {
  // (this) is the element that is beeing dragged
  // Get the Id of the field to be cloned
  var id = getHash(this);
  //The source is now the clone of the matching element in the input-types
  dragSrcEl = $('#input-types ' + id).clone();
  // Add necessary events to the source
  addEvents(dragSrcEl[0]);
}

// When an element of the list is dragged over a field in the form-holder
function handleDragOverList(e) {
  if (e.preventDefault) {
    e.preventDefault(); // Necessary. Allows us to drop.
  }
  return false;
}

// When any element is dropped
function handleDrop(e) {
  // this/e.target is current target element.
  if (e.stopPropagation) {
    e.stopPropagation(); // Stops some browsers from redirecting.
  }
  // Don't do anything if dropping the same column we're dragging.
  if (dragSrcEl != this) {
    //Drop directly over tab
    if(this == $('#drag-container .tab.active')[0]){
      $(dragSrcEl).appendTo(this);
    //Drop over other field
    }else{
      //Put element above
      if(dragSrcEl.offsetTop > lastValidField.offsetTop){
        $(dragSrcEl).insertBefore(lastValidField);
      //Put element under
      }else{
        $(dragSrcEl).insertAfter(lastValidField);
      }
    }
  }
  $('#blankSpace').remove();
  return false;
}

// Adds all events necessary for the field
function addEvents(elem, id = null, signature = null){
  var type;
  type = $(elem).attr('id').split("__")[0];

  if(id == null){
    $(elem).attr('id', type + '__' + fieldCounter);
    $(elem).find('.drag-options [type="radio"]').attr('name', 'button-type__' + fieldCounter );
    $(elem).attr('data-id', "");
    fieldCounter++;
    localStorage.setItem('fieldCounter', fieldCounter);
  }else{
    $(elem).attr('id', type + '__' + id);
    $(elem).find('.drag-options [type="radio"]').attr('name', 'button-type__' + id );
    $(elem).attr('data-id', id);
  }

  id = $(elem).attr('id').split("__")[1];

  if( !$('#drag-container').hasClass('client-view') ){
    // Add Event Listeners for Drag and Drop
    elem.addEventListener('dragover', handleDragOver, false);
    elem.addEventListener('drop', handleDrop, false);
    elem.addEventListener('dragend', handleDragEnd, false);
    $(elem).find('.drag-heading')[0].addEventListener('dragstart', handleDragStart, false);
  }

  // Update Label Text
  $(elem).find('.drag-options .label-text').keyup(function(){
    $(elem).find('.update-label').val($(this).val());
    $(elem).find('.update-label').text($(this).val());
  });

  // Update Help Text
  $(elem).find('.drag-options .help-text').keyup(function(){
    $(elem).find('.drag-label .help .text').text($(this).val());
    $(elem).find('.drag-input .help .text').text($(this).val());
    if($(this).val() == ""){
      $(elem).find('.help .icon').hide();
    }else{
      $(elem).find('.help .icon').show();
    }
  });

  // Update Value
  $(elem).find('.drag-options .value').keyup(function(){
    $(elem).find('.update-value').val($(this).val());
    $(elem).find('.update-value').text($(this).val());
  });

  // Update Number Field
  $(elem).find('.drag-options .min-value').keyup(function(){
    $(elem).find('.update-min').attr('min', $(this).val());
  });  
  $(elem).find('.drag-options .max-value').keyup(function(){
    $(elem).find('.update-max').attr('max', $(this).val());
  });  
  $(elem).find('.drag-options .step-value').keyup(function(){
    $(elem).find('.update-step').attr('step', $(this).val());
  });

  // Update Date Field and Color Field values
  if(type == 'date-field' || type == 'color-field'){
    $(elem).find('.drag-options .value').change(function(){
      $(elem).find('.update-value').val($(this).val());
    });
  }

  // Update Date Field and Color Field values
  if(type == 'checkbox'){
   $(elem).find('.drag-options .value').change(function(){
      $(elem).find('.update-value').prop( 'checked', $(this).val() );
    }); 
  }

  //Update Button Type
  $(elem).find('.drag-options [type="radio"]').change(function(){
    $(elem).find('.drag-input button').attr('type', $(this).val() );
  });

  // Update Required
  
  $(elem).find('.span-required').toggle($(elem).find('.drag-options .is-required').prop('checked'));
  $(elem).find('.drag-options .is-required').change(function(){
    var isRequired = $(this).prop('checked');
    $(elem).find('.span-required').toggle(isRequired);
    $(elem).find('.update-required').toggleClass('required', isRequired);
  });

  // Configure Field
  $(elem).find('.drag-heading .fa-cog').click(function(){
    $(this).closest('.draggable-input').find('.drag-options').toggleClass('hidden');
  });

  // Toggle Comments
  $(elem).find('.drag-heading .fa-comments-o').click(function(){
    $(this).closest('.draggable-input').find('.drag-comments').toggleClass('hidden');
  });
  
  // Delete Field
  $(elem).find('.drag-heading .fa-times').click(function(){
    var fieldId = $(this).closest('.draggable-input').attr('id').split('__')[1];
    $('.rules tr td').each(function(){
      
      if($(this).attr('field-id') == fieldId ){
        $(this).parent().remove();
      }
    })
    $(this).closest('.draggable-input').remove();
    ordenateFields();
  });

  if($('#drag-container.client-view').length > 0){
    $(elem).removeClass('half-row');
    $(elem).find('.drag-heading .expand-field').addClass('fa-expand').removeClass('fa-compress');
  }else{
    if($(elem).hasClass('half-row')){
      $(elem).find('.drag-heading .expand-field').addClass('fa-expand').removeClass('fa-compress');
    }else{
      $(elem).find('.drag-heading .expand-field').addClass('fa-compress').removeClass('fa-expand');
    }
  }

  $(elem).find('.drag-heading .expand-field').click(function(){
    var field = $(this).closest('.draggable-input');
    $(this).toggleClass('fa-expand').toggleClass('fa-compress');
    
    field.toggleClass('expanded').siblings().removeClass('expanded');
    field.toggleClass('half-row');
    //$(field).find(canvas).hide();
    field.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',   
      function(e) {
        
    });
  });

  // Clone Field
  $(elem).find('.drag-heading .fa-clone').click(function(){
    var el = $(this).closest('.draggable-input');
    var clone = el.clone();
    addEvents(clone[0], null);
    el.after(clone);
    ordenateFields();
    
  });

  // Add Options
  $(elem).find('.drag-options .add-options').click(function(e){
    e.preventDefault();
    var field = $(this).closest('.draggable-input');
    var label = field.find('.label-input');
    var value = field.find('.value-input');
    if(label.val() != "" && value.val() != ""){
      addOption( type , field, label.val(), value.val(), false, id);
      label.val('');
      value.val('');
    }
  });

  //Remove options
  $(elem).find('.drag-options .remove-option i').on('click', function(e){
    e.preventDefault();
    var index = $(this).closest('tr').index();
    $(elem).find('.checkbox-group .checkbox:nth-of-type('+ (index+1) +')').remove();
    $(elem).find('.radio-group .radio:nth-of-type('+ (index+1) +')').remove();
    $(elem).find('select option:nth-of-type('+ (index+1) +')').remove();
    $(this).closest('tr').remove();
  });

  //Comments
  var commentType = $(elem).find('input[type="radio"][name="comment-type"]');
  commentType.attr('name', commentType.attr('name') + id);
  $(commentType[1]).prop('checked', true);

  $(elem).find('.add-comment').click(function(){
    var message = $(elem).find('.comment-msg');
    var type = $(elem).find('input[name*="comment-type"]:checked').val();
    appendComment(username, message.val(), type, $(elem));
    message.val('');
  });

  //Signature Pad
  if(type == "signature"){
    var dragInput = $(elem).find('.drag-input');
    canvas = $(elem).find("canvas")[0];
    canvas.height = 120;
    canvas.width = 400;
    canvas = dragInput.find('canvas');
    
    var sign = new SignaturePad (canvas[0],{
        minWidth : 0.6,
        maxWidth : 1,
        penColor: "rgb(13,5,40)"
    });

    if(signature != null){
      sign.fromDataURL(signature);
    }

    canvasArray.push({
      field : id,
      signature : sign
    });

    $(elem).find('.clear').click(function(e){
      e.preventDefault();
      sign.clear();
    });

  }

  //rules
  if($('#drag-container.report-view').length <= 0){
    if( $(elem).find('.rules-container').length <= 0){
      var comparisonOptions = '<option value="==">Is</option><option value="!=">Is not</option><option value="<">Less than</option><option value="<=">Less or equal</option><option value=">">More than</option><option value=">=">More or equal</option>';
      var addRules = '<div class="rules-container"><h4>Rules</h4><div class="row m-b-20"><select class="col-lg-2 rule-action"><option value="hide">Hide if</option><option value="show">Show if</option></select> <select class="col-lg-2 rule-target"><option value="all">All</option><option value="any">Any</option></select></div><h5>New Condition</h5><div class="form-group row rule"><div class="col-lg-2"><select class="tabs form-control"></select></div><div class="col-lg-2"><select class="fields form-control"><option value="initial">Select Field</option></select></div><div class="col-lg-2"><select class="form-control comparison">'+ comparisonOptions +'</select></div><div class="col-lg-2 input-holder"><input type="text" class="form-control" placeholder="Value"></div> <button class=" add-rule btn btn-default">Add Condition</button></div>';
      addRules += '<table class="table rules"><tr><td> Page</td><td> Field</td><td>comparison </td><td>Value </td><td class="text-right">Delete</td> </tr></table></div>';
      $(elem).find('.drag-options').append(addRules);
    }
  }
  $(elem).find('.add-rule').click(function(e){
    e.preventDefault();
    node = $(this).closest('.draggable-input').find('.rules');
    pages = {
      id : $(this).parent().find('select.tabs').val(),
      label : $(this).parent().find('select.tabs option:selected').text()
    };

    fields = {
      id : $(this).parent().find('select.fields').val(),
      index : $(this).parent().find('select.fields option:selected').attr('ordenation'),
      label : $(this).parent().find('select.fields option:selected').text().split(')')[1]
    };

    comparison = {
      value : $(this).parent().find('select.comparison').val(),
      label : $(this).parent().find('select.comparison option:selected').text()
    }

    var input = $(this).parent().find('.input-holder .rule-value');
    console.log(fields.label);
    switch(fields.label){
      case ' Select':
        ruleValue = input.val();
        ruleValueLabel = input.find(':selected').text();
        break;
      case ' Checkbox':
        ruleValue = input.val();
        ruleValueLabel = input.find(':selected').text();
        break;
      case ' Radio Button':
        ruleValue = input.val();
        ruleValueLabel = input.find(':selected').text();
        break;
      default :
        ruleValue = input.val();
        ruleValueLabel = input.val();
        break;
    }
    value = {
      value: ruleValue,
      label : ruleValueLabel
    } 

    if( (pages._id != 'initial') && (fields._id != 'initial') && (value.value != '')){
      addRule(node, pages, fields, comparison, value);

      $(elem).find('a.remove-row').click(function(e){
        e.preventDefault();
        $(this).closest('tr').remove();
      });

    }
  });

  $(elem).find('.tabs').on('change', function(){
    var id = $(this).find(':selected').val();
    var fields = $('#tab' + id).find('.draggable-input');
    var elemId = $(this).closest('.draggable-input').attr('id').split('__')[1];
    options = '<option value="initial">Select Field</option>';

    [].forEach.call(fields, function(field, index){
      id = $(field).attr('id').split('__')[1];
      title = $(field).find('.drag-heading h4').text();
      ordenation = $(field).find('.drag-heading .ordenation').text()
      if( ( (title != "Static Paragraph") && (title != "Static Header") && (title != "Signature") && (title != "File Upload") ) && ( elemId != id) ){
        options += '<option value="'+ id +'" ordenation="'+ ordenation +'">'+ ordenation + title +'</option>';
      }
    });
    $(this).closest('.drag-options').find('.fields').html(options);
  });

  $(elem).find('.fields').on('change', function(){
    fieldId = $(this).find('option:selected').val();
    getOptions($(elem),fieldId);
  })

  updateRulesPages();
}

// Appends field models
function appendModel(){
  var html = $('<div id="input-types" class="hidden"> <div class="draggable-input panel" data-id="" id="checkbox-group"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Checkbox</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Checkbox </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group checkbox-group update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Checkbox"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Label"> </div><div class="col-sm-4"> <input type="text" class="form-control value-input" placeholder="Value"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="radio-group"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Radio Button</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Radio Button </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group radio-group update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Radio Button"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Label"> </div><div class="col-sm-4"> <input type="text" class="form-control value-input" placeholder="Value"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="select"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Select</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Select </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <select class="form-control update-required"></select> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Select"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Label"> </div><div class="col-sm-4"> <input type="text" class="form-control value-input" placeholder="Value"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="date-field"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Date Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Date Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <input type="date" class="form-control update-value update-required"> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Date Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="date" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="file-upload"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>File Upload</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">File Upload </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <input type="file"/> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="File Upload"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="header"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Static Header</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"> <h3 class="update-label">Header</h3> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Header"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="paragraph"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Static Paragraph</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"> <p class="update-label">Paragraph</p></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Content</label> <div class="col-sm-10"> <textarea class="form-control label-text">Paragraph</textarea> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="number-field"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Number Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Number Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <input type="number" class="form-control update-value update-required update-min update-max update-step" min="" max="" step=""> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Number Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Min</label> <div class="col-sm-10"> <input type="number" class="form-control min-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Max</label> <div class="col-sm-10"> <input type="number" class="form-control max-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Step</label> <div class="col-sm-10"> <input type="number" class="form-control step-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="number" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="text-field"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Text Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Text Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <input type="text" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Text Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="email-field"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>E-mail Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">E-mail Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <input type="email" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="E-mail Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="email" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="phone-field"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Phone Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Phone Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <input type="tel" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Phone Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="tel" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="text-area"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Text Area</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Text Area </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <textarea class="form-control update-value update-required"></textarea> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Text Area"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <textarea class="form-control value"></textarea> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="signature"> <div class="drag-heading" draggable="true"> <span class="ordenation"></span> <h4>Signature</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Signature </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div><div class="comment-icon"></div></div></div><div class="drag-input"> <div class="form-group"> <button class="clear btn btn-default">Clear</button> </div><canvas class="update-required"></canvas> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Signature"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-5 radio"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-2"> <div class="radio"> <input type="radio" name="comment-type" value="internal" class=""> <label>Internal</label> </div><div class="radio"> <input type="radio" name="comment-type" value="external" class=""> <label>External</label> </div></div><div class="col-sm-3 radio"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div></div>');
  
  $("#drag-container").append(html);
  $('.drag-heading').append($('<div>'));  
}

// Appends fields menu
function appendList(){
  var html = $('<div id="list-container"> <h3>Input Types</h3> <ul class="input-list"> <li><a draggable="true" href="#header">Header</a></li><li><a draggable="true" href="#paragraph">Paragraph</a></li><li><a draggable="true" href="#checkbox-group">Checkbox</a></li><li><a draggable="true" href="#radio-group">Radio Button</a></li><li><a draggable="true" href="#select">Select</a></li><li><a draggable="true" href="#date-field">Date Field</a></li><li><a draggable="true" href="#file-upload">File Upload</a></li><li><a draggable="true" href="#number-field">Number Field</a></li><li><a draggable="true" href="#text-field">Text Field</a></li><li><a draggable="true" href="#email-field">Email Field</a></li><li><a draggable="true" href="#phone-field">Phone Field</a></li><li><a draggable="true" href="#text-area">Text Area</a></li><li><a draggable="true" href="#signature">Signature</a></li></ul></div>');
  if($('#drag-container.report-view').length > 0){
    html.find('li:not(:nth-of-type(1)):not(:nth-of-type(2))').remove()
  }
  $("#drag-container").append(html);

  var list = $('#list-container .input-list li');

  list.click(function(e){
      e.preventDefault();
      var id = getHash(this);
      var clone = $('#input-types ' + id).clone();
      addEvents(clone[0]);
      $('#drag-container .tab.active').append(clone);
      ordenateFields();
      
      updateRulesPages();
  });

  [].forEach.call(list, function(item) {
    item.addEventListener('dragstart', handleDragStartList, false);
    item.addEventListener('dragend', handleDragEnd, false);
  });
}

// Appends tabs navigation
function appendNavigation(){
  var html = '<div class="form-holder"><div class="tabs-options"><a class="btn btn-custom2" id="addTab">Add Page</a><a class="btn btn-save m-l-20" id="save">Save History</a><a class="btn btn-default m-l-20" id="open-history">History</a></div><nav><ul></ul></nav><div class="tabs-holder"></div></div>';
  $('#drag-container').append(html);  
  
  $('#addTab').click(function(e){
    e.preventDefault();
    addTab();
  });

  $('#save').click(function(e){
    e.preventDefault();
    saveCheckpoint();
  });

  //Append History
  var historyModal = '<div id="history" class="modal modal-dialog modal-md"><div class="modal-content"><div class="modal-header"><button type="button" id="close-history" class="close" aria-hidden="true">×</button><h4 class="modal-title" id="mySmallModalLabel">Reviews History</h4> </div><div class="modal-body"> <div id="checkpoints"></div> </div></div></div>';
  $('#drag-container').append(historyModal);  

  $('#open-history').click(function(e){
    e.preventDefault();
    $('#history').show();
  });

  $('#close-history').click(function(e){
    e.preventDefault();
    $('#history').hide();
  });

  $('#drag-container nav ul li').click(function(e){
    e.preventDefault();
    
  });
}

// Adds a new tab on the form-holder
function addTab(obj = null){
  var title = (obj != null) ? obj.title : 'New Page';
  $('.tab-control').removeClass('active');
  var tabId = (obj != null) ? obj._id : tabCounter++;
  localStorage.setItem('tabCounter', tabCounter);
  // create tab navigation
  var navTab = $('<li>', { // create list element
    class : 'tab-control active',
    click : function(e){
      e.preventDefault();
      var id = getHash(this);
      $('.form-holder .tabs-holder ' + id).addClass('active').siblings().removeClass('active').find('.modal').hide();
      $(this).addClass('active').siblings().removeClass('active');
    }
  }).append($('<a>', { // create anchor element
    'href' : "#tab" + tabId,
    class : 'update-title',
    text : title
  })).append($('<i>', { // create config icon
    class : 'fa fa-cog tab-config'
  })).append($('<i>', { // create remove icon
    class : 'fa fa-times tab-remove'
  }));

  
  var config = '<div class="form-group"><label>Title</label><input type="text" class="form-control tab-title" value="'+ title +'"></div>';
  var modal = '<div class="modal modal-dialog modal-md"><div class="modal-content"><div class="modal-header"><button type="button" class="close" aria-hidden="true">×</button><h4 class="modal-title" id="mySmallModalLabel">Tab Configuration</h4> </div><div class="modal-body">' + config + '</div></div></div>';
  var containerTab = $('<div id="tab'+ tabId +'" tab-id="" class="tab tab-control active">' + modal + '</div>');
  $('.form-holder nav ul').append(navTab);
  $('.form-holder .tabs-holder').append(containerTab);

  if(obj != null){
    $('#tab' + tabId).attr('tab-id', obj.tabId);
  }

  //Update Tab Title
  $('.modal').find('.tab-title').keyup(function(){
    var id = $(this).closest('.tab').attr('id');
    var title = $(this).val();
    $('a[href="#' + id + '"]').text(title);

    $('.rule .tabs option').each(function(){
      if( $(this).val() == id.replace('tab', '') ){
        $(this).text(title);
      }
    });

    $('.rules td').each(function(){
      if( $(this).attr('page-id') == id.replace('tab', '') ){
        $(this).text(title);
      }
    });
  });

  // remove tab
  $('.tab-remove').click(function(){
    var nav = $(this).closest('li');
    var tabId = getHash(nav);
    $('.form-holder .tabs-holder ' + tabId).remove();
    $('.rules .tabs option').each(function(){
      if( $(this).attr('value') == tabId.replace('#tab', '')){
        $(this).parent().siblings().find('option').remove()
        $(this).remove();
      }
    });
    nav.remove();
    updateRulesPages();
    $('.rules tr td').each(function(){
      
      if($(this).attr('page-id') == tabId.replace('#tab', '')){
        $(this).parent().remove();
      }
    })


  });

  // open config modal
  $('.tab-config').click(function(){
    var nav = $(this).closest('li');
    var tabId = getHash(nav);
    $(tabId + ' .modal').show();
  });

  // close config modal
  $('.modal-header button.close').click(function(){
    $(this).closest('.modal').hide();
  });
  
  var tabs = $('#drag-container .tab');

  [].forEach.call(tabs, function(tab) {
    tab.addEventListener('dragover', handleDragOverList, false);
    tab.addEventListener('drop', handleDrop, false);
  });

  updateRulesPages()
}

// Adds options to field groups
function addOption(type, node, label, value, prop, id){
      var html;
      if(type == 'checkbox-group'){
        prop = (prop) ? 'checked' : '';
        html = '<div class="checkbox checkbox-success"><input type="checkbox" name="radio-group__'+ id +'" value="' + value + '" '+ prop  +'><label>'+ label + '</label></div>'
        $(html).appendTo(node.find('.drag-input .checkbox-group'));
      }else if(type == 'radio-group'){
        prop = (prop) ? 'checked' : '';
        html = '<div class="radio radio-success"> <input type="radio" name="radio-group__'+ id +'" value="'+ value +'" '+ prop +'> <label> '+ label +' </label> </div>';
        $(html).appendTo(node.find('.drag-input .form-group'));
      }else if(type == 'select'){
        prop = (prop) ? 'selected' : '';
        var html = '<option value="' + value + '" '+ prop +'>'+ label + '</option>';
        $(html).appendTo(node.find('.drag-input select'));
      }
      node.find('.drag-options .options-table').append('<tr><td>' + label + '</td><td>' + value + '</td><td class="text-nowrap"><a href="#" data-toggle="tooltip" data-original-title="Delete" class="remove-option"> <i class="fa fa-close text-danger"></i> </a></td></tr>');

      //Remove options
      node.find('.drag-options .remove-option i').on('click', function(e){
        e.preventDefault();
        var index = $(this).closest('tr').index();
        
        node.find('.checkbox-group .checkbox:nth-of-type('+ (index+1) +')').remove();
        node.find('.radio-group .radio:nth-of-type('+ (index+1) +')').remove();
        node.find('select option:nth-of-type('+ (index+1) +')').remove();
        $(this).closest('tr').remove();
      });
}

// Returns the hash of the child 'a' element
function getHash(elem){
  return $(elem).find('a').attr('href');
}

function appendComment(user, msg, type = 'external', node){
  if(msg != ''){
    node.find('.comments').append('<li comment-type="'+ type +'"><p><span class="username">'+ user +'</span><span class="message">'+ msg +'</span></p></li>');
    node.find('.comment-icon').addClass('has-comments');
  }
}

function ordenateFields(){
  var fields = $('.form-holder .draggable-input');

  fields.each(function(index){
    $(this).find('.drag-heading .ordenation').text('(' + (index+1) + ') ' );
    var fieldId = $(this).attr('id').split('__')[1];
    $('.rules tr td').each(function(){
      if($(this).attr('field-id') == fieldId ){
        $(this).find('.drag-heading .ordenation').text('('+ (index+1) +')');
      }
    })
  })

  var rules = $('.rules td');

  rules.each(function(){
    var id = $(this).attr('field-id');
    $(this).find('.ordenation').html($('.draggable-input[data-id="'+ id +'"] .drag-heading .ordenation').text());
  });

}


function addRule(node, page, field, comparison, value) {
  
    var html =  '<tr> <td class="page-id" page-id="' + page._id + '">' + page.label + '</td> <td class="field-id" field-id="' + field._id + '"><span class="ordenation">'+ field.index + '</span><span class="field-label">' + field.label + '</span></td><td class="comparison" value="'+ comparison.value +'">'+ comparison.label+'</td> <td class="value" value="'+ value.value +'">'+ value.label +'</td> <td><a class="remove-row close col-lg-1">×</a></td> </tr>';
    node.append(html);


  node.find('a.remove-row').click(function(e){
    e.preventDefault();
    $(this).closest('tr').remove();
  });

  $('.rule .tabs').val('initial');
  $('.rule .fields').val('initial');
  $('.rule .input-holder').html('<input type="text">');
}
 
function updateRulesPages(){
  var ruleOptions = '<option value="initial">Select Page</option>';
  $('li.tab-control a').each(function(){
    ruleOptions += '<option value="'+ $(this).attr('href').replace('#tab', '') +'">'+ $(this).text() +'</option>'
  });
  $('.rule').find('.tabs').html(ruleOptions);
}


function getOptions(node, fieldId){
  var field = $('.draggable-input[id*="'+fieldId+'"]');
  var type = field.attr('id').split('__')[0];
  clone = field.find('.drag-input').clone();
  if( type == 'checkbox-group' ){
    var html = '<select class="rule-value form-control">';
    var options = clone.find('.checkbox');
    [].forEach.call(options, function(option){
      val = $(option).find('input').attr('value');
      label = $(option).find('label').text();
      html+= '<option value="'+ val +'">'+ label +'</option>'
    });
    html += '</select>';
    node.find('.rule .input-holder').html(html);
  }else if( type == 'radio-group' ){
    var html = '<select class="rule-value form-control">';
    var options = clone.find('.radio');
    [].forEach.call(options, function(option){
      val = $(option).find('input').attr('value');
      label = $(option).find('label').text();
      html+= '<option value="'+ val +'">'+ label +'</option>'
    });
    html += '</select>';
    node.find('.rule .input-holder').html(html);
  }else if( type == 'select' ){
    var html = '<select class="rule-value form-control">';
    var options = clone.find('select option');
    [].forEach.call(options, function(option){
      val = $(option).attr('value');
      label = $(option).text();
      html+= '<option value="'+ val +'">'+ label +'</option>'
    });
    html += '</select>';
    node.find('.rule .input-holder').html(html);
  }else{
    clone.find('input').addClass('rule-value');
    clone.find('textarea').addClass('rule-value');
    node.find('.rule .input-holder').html(clone);
  }
  
}