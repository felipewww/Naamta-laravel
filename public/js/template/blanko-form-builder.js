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
function addEvents(elem, id = null){
  if(id == null){
    var type = $(elem).attr('id').split("__")[0];
    $(elem).attr('id', type + '__' + fieldCounter);
    $(elem).find('.drag-options [type="radio"]').attr('name', 'button-type__' + fieldCounter );
    $(elem).attr('data-id', "");
    fieldCounter++;
    localStorage.setItem('fieldCounter', fieldCounter);
  }else{
    var type = $(elem).attr('id').split("__")[0];
    $(elem).attr('id', type + '__' + id);
    $(elem).find('.drag-options [type="radio"]').attr('name', 'button-type__' + id );
    $(elem).attr('data-id', id);
  }
  // Add Event Listeners for Drag and Drop
  // elem.addEventListener('dragstart', handleDragStart, false);
  elem.addEventListener('dragover', handleDragOver, false);
  elem.addEventListener('drop', handleDrop, false);
  elem.addEventListener('dragend', handleDragEnd, false);
  $(elem).find('.drag-heading')[0].addEventListener('dragstart', handleDragStart, false);
  // $(elem).find('.drag-heading')[0].addEventListener('dragover', handleDragOver, false);
  // $(elem).find('.drag-heading')[0].addEventListener('drop', handleDrop, false);
  // $(elem).find('.drag-heading')[0].addEventListener('dragend', handleDragEnd, false);

  // Update Label Text
  $(elem).find('.drag-options .label-text').change(function(){
    $(elem).find('.update-label').val($(this).val());
    $(elem).find('.update-label').text($(this).val());
  });

  // Update Help Text
  $(elem).find('.drag-options .help-text').change(function(){
    $(elem).find('.drag-label .help .text').text($(this).val());
    if($(this).val() == ""){
      $(elem).find('.help .icon').hide();
    }else{
      $(elem).find('.help .icon').show();
    }
  });

  // Update Value
  $(elem).find('.drag-options .value').change(function(){
    $(elem).find('.update-value').val($(this).val());
    $(elem).find('.update-value').text($(this).val());
  });

  // Update Number Field
  $(elem).find('.drag-options .min-value').change(function(){
    $(elem).find('.update-min').attr('min', $(this).val());
  });  
  $(elem).find('.drag-options .max-value').change(function(){
    $(elem).find('.update-max').attr('max', $(this).val());
  });  
  $(elem).find('.drag-options .step-value').change(function(){
    $(elem).find('.update-step').attr('step', $(this).val());
  });  

  //Update Button Type
  $(elem).find('.drag-options [type="radio"]').change(function(){
    $(elem).find('.drag-input button').attr('type', $(this).val() );
  });

  // Update Required
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
      $(this).closest('.draggable-input').remove();
  });

  //Expand Field
  $(elem).find('.drag-heading .expand-field').click(function(){
    $(this).toggleClass('fa-expand');
    $(this).toggleClass('fa-compress');
    $(this).closest('.draggable-input').toggleClass('half-row');
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
        addOption( field.attr('id').split("__")[0] , field, label.val(), value.val());
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

  //Add Comment
  $(elem).find('.add-comment').click(function(){
    var message = $(elem).find('.comment-msg');
    appendComment(username, message.val(), $(elem));
    message.val('');
  });
}

// Appends field models
function appendModel(){
  
  var html = '<div id="input-types" class="hidden"> <div class="draggable-input panel" data-id="" id="checkbox" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Checkbox</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"></span> <div class="checkbox checkbox-success"> <input type="checkbox" class="update-value update-required"> <label class="update-label">Checkbox </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Checkbox"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="checkbox-group" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Checkbox Group</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Checkbox Group </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group checkbox-group update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Checkbox Group"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Label"> </div><div class="col-sm-4"> <input type="text" class="form-control value-input" placeholder="Value"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="radio-group" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Radio Group</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Radio Group </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group radio-group update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Radio Group"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Label"> </div><div class="col-sm-4"> <input type="text" class="form-control value-input" placeholder="Value"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="select" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Select</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Select </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <select class="form-control update-required"></select> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Select"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Label"> </div><div class="col-sm-4"> <input type="text" class="form-control value-input" placeholder="Value"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="date-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Date Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Date Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <input type="date" class="form-control update-value update-required"> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Date Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="date" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="button" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Button</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body"> <div class="drag-input"> <button class="btn update-label" type="">Button</button> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Button"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Type</label> <div class="col-sm-10"> <div class="radio radio-success col-sm-3"> <input type="radio" class="type" name="button-type" value="submit" checked> <label> Submit </label> </div><div class="radio radio-success col-sm-3"> <input type="radio" class="type" name="button-type" value="reset"> <label> Reset </label> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="file-upload" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>File Upload</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">File Upload </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <input type="file"/> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="File Upload"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="header" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Header</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"> <h3 class="update-label">Header</h3> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Header"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="paragraph" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Paragraph</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"> <p class="update-label">Paragraph</p></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Content</label> <div class="col-sm-10"> <textarea class="form-control label-text">Paragraph</textarea> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="number-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Number Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Number Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <input type="number" class="form-control update-value update-required update-min update-max update-step" min="" max="" step=""> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Number Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Min</label> <div class="col-sm-10"> <input type="number" class="form-control min-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Max</label> <div class="col-sm-10"> <input type="number" class="form-control max-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Step</label> <div class="col-sm-10"> <input type="number" class="form-control step-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="number" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="text-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Text Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Text Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <input type="text" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Text Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="password-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Password Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Password Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <input type="password" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Password Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="password" class="form-control"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="email-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>E-mail Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">E-mail Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <input type="email" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="E-mail Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="email" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="phone-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Phone Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Phone Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <input type="tel" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Phone Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="tel" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="color-field" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Color Field</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Color Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <div class="checkbox checkbox-success"> <input type="color" class="update-value update-required"> </div></div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Color Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <input type="color" class="value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="text-area" > <div class="drag-heading" draggable="true"> <span class="ordenation"></span><h4>Text Area</h4> <ul> <li><i class="fa fa-compress expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-comments-o"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"></span> <label class="update-label">Text Area </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="text">Help Text Goes Here</div></div></div><div class="drag-input"> <div class="form-group"> <div class="checkbox checkbox-success"> <textarea class="form-control update-value update-required"></textarea> </div></div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Text Area"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <input type="text" class="form-control help-text" value="Help Text Goes Here"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Initial Value</label> <div class="col-sm-10"> <textarea class="form-control value"></textarea> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4>Comments</h4> <ul class="comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-9"> <textarea placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div></div>';
  $("#drag-container").append(html);
  $('.drag-heading').append($('<div>'));
  
}

// Appends fields menu
function appendList(){
  var html = '<div id="list-container"><h3>Input Types</h3><ul class="input-list"><li><a draggable="true" href="#checkbox">checkbox</a></li><li><a draggable="true" href="#checkbox-group">checkbox group</a></li><li><a draggable="true" href="#radio-group">radio group</a></li><li><a draggable="true" href="#select">select</a></li><li><a draggable="true" href="#date-field">date field</a></li><li><a draggable="true" href="#button">button</a></li><li><a draggable="true" href="#file-upload">file upload</a></li><li><a draggable="true" href="#header">header</a></li><li><a draggable="true" href="#paragraph">paragraph</a></li><li><a draggable="true" href="#number-field">number field</a></li><li><a draggable="true" href="#text-field">text field</a></li><li><a draggable="true" href="#password-field">password field</a></li><li><a draggable="true" href="#email-field">email field</a></li><li><a draggable="true" href="#phone-field">phone field</a></li><li><a draggable="true" href="#color-field">color field</a></li><li><a draggable="true" href="#text-area">text area</a></li></ul></div>';
  $("#drag-container").append(html)

  var list = $('#list-container .input-list li');

  list.click(function(e){
      e.preventDefault();
      var id = getHash(this);
      var clone = $('#input-types ' + id).clone();
      addEvents(clone[0]);
      $('#drag-container .tab.active').append(clone);
      ordenateFields();
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
  var tabId = (obj != null) ? obj.id : tabCounter++;
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

  var config = '<div class="form-group"><label>Title</label><input type="text" class="form-control tab-title" value="'+ title +'"></div><div class="form-group"></div>';
  var modal = '<div class="modal modal-dialog modal-md"><div class="modal-content"><div class="modal-header"><button type="button" class="close" aria-hidden="true">×</button><h4 class="modal-title" id="mySmallModalLabel">Tab Configuration</h4> </div><div class="modal-body">' + config + '</div></div></div>';
  var containerTab = $('<div id="tab'+ tabId +'" tab-id="" class="tab tab-control active">' + modal + '</div>');

  $('.form-holder nav ul').append(navTab);
  $('.form-holder .tabs-holder').append(containerTab);

  if(obj != null){
    $('#tab' + tabId).attr('tab-id', obj.tabId);
  }

  //Update Tab Title
  $('.modal').find('.tab-title').change(function(){
    var id = $(this).closest('.tab').attr('id');
    $('a[href="#' + id + '"]').text($(this).val());
  });

  // remove tab
  $('.tab-remove').click(function(){
    var nav = $(this).closest('li');
    var tabId = getHash(nav);
    $('.form-holder .tabs-holder ' + tabId).remove();
    nav.remove();
  });

  // open config modal
  $('.tab-config').click(function(){
    var nav = $(this).closest('li');
    var tabId = getHash(nav);
    $(tabId + ' .modal').show();
  });

  // close config modal
  $('.modal button.close').click(function(){
    $(this).closest('.modal').hide();
  });

  var tabs = $('#drag-container .tab');
  [].forEach.call(tabs, function(tab) {
    tab.addEventListener('dragover', handleDragOverList, false);
    tab.addEventListener('drop', handleDrop, false);
  });;
}

// Adds options to field groups
function addOption(type, node, label, value){
      var html;
      if(type == 'checkbox-group'){
        html = '<div class="checkbox checkbox-success"><input type="checkbox" value="' + value + '"><label>'+ label + '</label></div>'
        $(html).appendTo(node.find('.drag-input .checkbox-group'));
      }else if(type == 'radio-group'){
        html = '<div class="radio radio-success"> <input type="radio" name="radio-group" value="'+ value +'"> <label> '+ label +' </label> </div>';
        $(html).appendTo(node.find('.drag-input .form-group'));
      }else if(type == 'select'){
        var html = '<option value="' + value + '">'+ label + '</option>';
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

function appendComment(user, msg, node){
  node.find('.comments').append('<li><p><span class="username">'+ user +'</span><span class="message">'+ msg +'</span></p></li>');
}

function ordenateFields(){
  fields = $('.form-holder .draggable-input');
  fields.each(function(index){
    $(this).find('.ordenation').text('(' + (index+1) + ') ' );
  })
}
