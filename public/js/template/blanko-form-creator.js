
var isEditable = true;
var fieldCounter = 0; // FIELD ID
var tabCounter = 0; //TAB ID
var isClientView;
var isUserClient;

var tabHolderFrag = document.createDocumentFragment();

var filesArray = new Array();

// Temporary variables
var tempContainers;
var tempFields;
var clones = new Array();

document.addEventListener('keypress', function(e){
  if(e.which == 13) {
    if(e.target.nodeName !== 'TEXTAREA' && e.target.className !== "help-text" && e.target.className !== "form-control paragraph-content" ) {
      e.preventDefault()
    }
    return false;
  }
});

var container = GetElement('#drag-container')[0];

// Creates tabs from json
// Uses createFields
function createTabs(json, clientView, isClient, report){
  if(json.trim() == ""){
    json = "[]";
  }

  /*
  * Some texts have been created with this special characters, maybe, because copied and pasted from another software.
  * So, replace it to avoid bug.
  * */
  json = json.replace(/\/g, '');

  var objs = JSON.parse(json);
  
  if(objs.length <= 0){
      objs = [{"config":{"_id":"00","title":"New Page","tabId":""},"fields":[]}];
  }

  isUserClient = isClient;
  isClientView = clientView;

  GetElement('.help .icon').toggle(false);
  GetElement('.tab-control').toggle(false);
  //$('.tab-control').remove();

    for(var i = 0; i < objs.length; i++){
      var obj = objs[i];
      clones = [];
      var t = addTab(obj.config);
      if(obj.fields !== undefined && obj.fields !== null){
        for(var j = 0; j < obj.fields.length; j++){
          var objeto = obj.fields[j];
          createFields(objeto, clientView);
        }
      }

      clones.sort(function(a, b){
        //console.log(a);
        var a =  $(a).attr('ordenation');
        var b =  parseInt($(b).attr('ordenation'));
        //console.log(a);
        return a - b;
      });

      var fragment = document.createDocumentFragment();

      for(var k = 0; k < clones.length; k++){
        var clone = clones[k];
        fragment.appendChild(clone[0]);
      }

      t.append(fragment);

      tabHolderFrag.appendChild(t[0]);
      
    }

  var tabsHolder = document.getElementById('tabs-holder');
  tabsHolder.appendChild(tabHolderFrag);

  var fields = $('.form-holder .draggable-input');

  for(var i = 0; i < fields.length; i++){
    var field = fields[i];
    var old_order = $(field).attr('ordenation');
    var index = i + 1;
    //$(field).find('.drag-heading .ordenation').text('(' + (fieldIndex) + ')' );
    field.setAttribute('ordenation', index);
    var fieldId = $(field).attr('id').split('__')[1];
  }

  var rules = $('.rules .field-id');
  
  for (var j =  0; j < rules.length; j++) {
    var rule = rules[j];
    var ruleOrder = rule.getAttribute('ordenation');
    var field = $('.draggable-input[ordenation="'+ ruleOrder +'"]')[0];
    var id = field.getAttribute('field-id');
    rule.setAttribute('field-id', id)
  } 

  for(var i = 0; i < objs.length; i++){
    var obj = objs[i];
    if(obj.fields != undefined){
      for(var j = 0; j < obj.fields.length; j++){
        var field = obj.fields[j];
        if(field.isEditable && clientView) {
          activateRule(field.setting.ordenate, field.setting.rule.ruleAction, field.setting.rule.ruleTarget, field.setting.rule.conditions);
        }
      }
    }
  }


  $('.required-field').remove();
  $('.tab-control').removeClass('active');
  $('.tab-control:first-of-type').addClass('active');

  $('#drag-container').find('a:not(.btn)').attr('target', '_blank');

   
  if(isClientView){

    container.classList.add('client-view');
    GetElement('.drag-options').toggle(false);
    $('.draggable-input').removeClass('panel');
    GetElement('.tabs-options #addTab').toggle(false);
    $('.drag-heading li:not(:first-of-type)').toggle(false);
    GetElement('#list-container').toggle(false);
    GetElement('.tab .modal').toggle(false)
    GetElement('nav .tab-control .fa').toggle(false);
    $('.help .comment-icon').html($('<i>', {
      class : 'fa fa-comments toggle-comments',
      click : function(){
        $(this).closest('.draggable-input').find('.drag-comments').toggleClass('hidden');
      }
    }));

    GetElement('#save-changes').toggle(true);
    
    var fields = $('.draggable-input');
    for(var i = 0; i < fields.length; i++){
      var field = fields[i];
      var ordenation = $(field).find('.drag-heading .ordenation').clone();
      $(field).find('.drag-heading .ordenation').hide();
      $(field).find('.drag-label').prepend(ordenation);
    }

    //$('.drag-label').each(function(){
      //var heading = $(this).closest('.draggable-input').find('.ordenation');
      //$(this).insertAfter(heading);
    //});

  }else{
    $('.filter').hide();
    $('.drag-validate').hide();
    $('#save-changes').hide();
    $('.drag-input.dropzone').hide();
  }
  
  if(isClient){
    $('.internal-comments').hide();
    $('.open-internal').hide();
  }

  if(isClient || !clientView){
    $('.drag-validate').hide();
  }
  
    $('.preview-form .filter').hide();
    $('.preview-form #save-changes').hide();
    $('.preview-form .drag-validate').hide();
    $('.preview-form .tabs-options').hide();

}

function createFields(obj, clientView){
  //var clone = $('#input-types #' + obj.type).clone();
  var clone = new Field(obj);
  clone = $(clone);
  configureField(clone, obj.setting, obj.type, obj._id);
  clones.splice(obj.setting.ordenate, 0, clone);


  if(obj.type == 'file-upload'){
    filesArray[obj._id] = obj.setting.value;
  }

  if(obj.type == 'checkbox-group' || obj.type == 'radio-group' || obj.type == 'select'){
    for(var i = 0; i < obj.setting.options.length; i++){
      var option = obj.setting.options[i];
      addOption(obj.type, clone, option.label, option.value, option.prop, obj._id);
    }
  }

  if(obj.comments != null){
      for(var i = 0; i < obj.comments.length; i++){
        var comment = obj.comments[i];
        appendComment(comment.username, comment.msg, comment.type, $(clone), comment._id);
      }
  }

  addEvents(clone[0], obj._id, obj.setting.signature);

  //rules
  clone.find('.rule-action').val(obj.setting.rule.ruleAction);
  clone.find('.rule-action').attr('rule-action-id', obj._id);
  clone.find('.rule-target').val(obj.setting.rule.ruleTarget);
  clone.find('.rule-target').attr('rule-target-id', obj._id);


  for(var i = 0; i < obj.setting.rule.conditions.length; i++){
    var condition = obj.setting.rule.conditions[i];
    var page = condition.page;
    var field = condition.field;
    var comparison = condition.comparison;
    var value = condition.value;
    addRule( clone.find('.rules'), page, field, comparison, value);
  }

}

function configureField(node, options, type, id){

  if(type != 'paragraph'){
    node.find('.update-label').text(options.label);
    node.find('.update-label').val(options.label);
  }else{
    node.find('.update-paragraph').html(options.label);
    node.find('.paragraph-content').html(options.label);
  }

  node.attr('ordenation', options.ordenate);
  //console.log(node);

  node.find('.help + .text').html(options.help);
  node.find('.help-text').html(options.help);

  var text = options.help;
 
  if(text == '') {
    node.find('.help .icon').hide();
  }else{
    node.find('.help .icon').show();
  }

  //Size of the field
  node.addClass(options.class);

  node.find('.drag-validate input[value="'+ options.error +'"]').prop('checked', true);

  //other attributes
  node.find('.drag-input input').attr({
    'min' : options.min,
    'max' : options.max,
    'value' : options.value,
    'step' : options.step,
    'placeholder' : options.placeholder
  });

  if(options.mask != null){
     node.find('.drag-input input').mask(options.mask);
     node.find('.drag-input input').attr('mask', options.mask);
     node.find('.mask').val(options.mask);
  }

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
  node.find('.update-value').attr('placeholder', options.placeholder);

  /*Options*/
  node = node.find('.drag-options');
  node.find('.is-required').prop('checked', options.isRequired);
  node.find('.label-text').val(options.label);
  node.find('.help-text').html(options.help);

  node.find('.value').val(options.placeholder);
  node.find('.min-value').val(options.min);
  node.find('.max-value').val(options.max);
  node.find('.step-value').val(options.step);
  node.find('input[value="' + options.type+'"]' ).prop('checked', true);
}

//activateRule(action, target, page, field, comparison, value)
function activateRule(obj_id, ruleAction, ruleTarget, conditions) {
  var cond = "";
  var changes = "'";
  var ev = "";
  var ev2 = "";
  var fields = [];
  var cont = 0;
  if(conditions.length >0){
    var i = 0;
    conditions.forEach(function( condition){
      var is_last_item = (i == (conditions.length - 1));
      
      var page = condition.page;
      var field = condition.field;
      var comparison = condition.comparison;
      var value = condition.value;

      var jQfield = $('[ordenation="' + field.index +'"]');
      var jQFieldId = jQfield.attr('field-id');
      var fieldType = jQfield.attr('field-type');
      
      if(fieldType === 'radio-group'){
        changes += "[name=\"radio-group__"+ jQFieldId +"\"]"
        cond += " " + "$('[name=\"radio-group__"+ jQFieldId +"\"]:checked').val() " + comparison.value + "'" + value.value + "'";
      }else if(fieldType == 'checkbox-group'){
        changes += "[name=\"checkbox-group__"+ jQFieldId +"\"]"
        cond += "1"
        //cond += " " + "$('[name=\"checkbox-group__" + jQFieldId + "\"]:checked').val() " + comparison.value + "'" + value.value + "'";
        fields.push( "[ordenation=\"" + field.index + "\"]");
        //console.log(fields);
        ev += "opts[j].value " + comparison.value+" '"+value.value + "' ";
        if(!is_last_item) ev += "||";
        cont+= 1;
      }else{
        changes += "[ordenation=\"" + field.index + "\"] .drag-input .form-control";
        //changes += "'[data-id=\""+field._id+"\"] .drag-input .form-control'";
        cond += " " + "$('[ordenation=\"" + field.index + "\"] .drag-input .form-control').val() " + comparison.value + "'" + value.value + "'";
        //cond += " " + "$('[data-id=\""+ field._id + "\"] .drag-input .form-control').val() " + comparison.value + "'" + value.value + "'";
      }
      
      if(conditions.length>1 && !is_last_item){
        cond += (ruleTarget == "all" ? " && " : " || " );
        changes += ", ";
      }
      i++;
    });

    changes += "'";

    //console.log(changes);

    $(eval(changes)).change(function() {

      evaluate(obj_id, cond, ruleAction, ev, fields, cont);
    });

    evaluate(obj_id, cond, ruleAction, ev, fields, cont);
  }
}

function evaluate(obj_id, cond, ruleAction, ev, fields, cont){
  if(ev == ""){
      if(eval(cond)){
          if(ruleAction === "show"){
            //$("[ordenation=\"" + obj_id + "\"]").show();
            GetElement('[ordenation="'+ obj_id +'"]').toggle(true);
          }else{
            //$("[ordenation=\"" + obj_id + "\"]").hide();
            GetElement('[ordenation="'+ obj_id +'"]').toggle(false);
          }
      }else{
          if(ruleAction === "show"){
            //$("[ordenation=\"" + obj_id + "\"]").hide();
            GetElement('[ordenation="'+ obj_id +'"]').toggle(false);
          }else{
            GetElement('[ordenation="'+ obj_id +'"]').toggle(true);
            //$("[ordenation=\"" + obj_id + "\"]").show();
          }
      }
  }else{
    for(var i = 0; i < fields.length; i++){
      field = $(fields[i]);
      var opts = field.find('.drag-input :checked');
      var a = [];
      for(var j = 0; j < opts.length; j++){
        //console.log(opts[j].value);
        if(eval(ev)){
          //console.log(ev);
          a.push(opts[j].value);
          //console.log(a);
        }
      }
      if(a.length >= cont){
        if(eval(cond)){
          //console.log('succ');
            if(ruleAction === "show"){
              //$("[ordenation=\"" + obj_id + "\"]").show();
              GetElement('[ordenation="'+ obj_id +'"]').toggle(true);
            }else{
              //$("[ordenation=\"" + obj_id + "\"]").hide();
              GetElement('[ordenation="'+ obj_id +'"]').toggle(false);
            }
        }else{
          //console.log('err');
            if(ruleAction === "show"){
              //$("[ordenation=\"" + obj_id + "\"]").hide();
              GetElement('[ordenation="'+ obj_id +'"]').toggle(false);
            }else{
              GetElement('[ordenation="'+ obj_id +'"]').toggle(true);
              //$("[ordenation=\"" + obj_id + "\"]").show();
            }
        }
      }else{
        //console.log('menor')
        if(ruleAction === "show"){
          //$("[ordenation=\"" + obj_id + "\"]").hide();
          GetElement('[ordenation="'+ obj_id +'"]').toggle(false);
        }else{
          GetElement('[ordenation="'+ obj_id +'"]').toggle(true);
          //$("[ordenation=\"" + obj_id + "\"]").show();
        }
      }
    }
  }
}

function checkFieldValue(id, value, options, isIncorrect, file){
  if( isClientView ){
    var elem = $('.draggable-input[data-id="'+id+'"]');

    var tabs = $('.tab')
    for(var i = 0; i < tabs.length; i++){
    var tab = tabs[i];
    
      var l = $(tab).find('.required-fail').length;
      if( l <= 0 ){
        var tabid = $(tab).attr('id');
        $('[href="#'+ tabid +'"]').removeClass('tab-fail');
      }
    }

    $('#save-changes').removeClass('btn-default').addClass('btn-save').html('<i class="fa fa-check m-r-20"></i> Save Changes');

    var type = elem.attr('field-type');

    var obj = {
      _id : id,
      setting : {
      }
    };

    if(value != null){
      if(type == 'signature'){
        obj.setting.signature = value;
      }else{
        obj.setting.value = value;
      }
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
    }


    var sequence = { _token: window.Laravel.csrfToken, field: JSON.stringify(obj) };

    $.ajax({
      url: '/workflow/updateFormField',
      dataType: "json",
      method: 'POST',
      data: sequence,
      success: function (data) {
        console.log('Success!');
      },
      error: function (data) {
      console.log('Error!');
      }
    });

    return obj;
  }
}

function getComments(id){
  var elem = $('.draggable-input[data-id="'+id+'"]');
  var result = new Array();
  var comments = $(elem).find('.comments li');

  for(var i = 0; i < comments.length; i++){
    var com = comments[i];
    var comment = {
        _id : $(com).attr('comment-id'),
        fieldId : id,
        username : $(com).find('span.username').text(),
        msg : $(com).find('.message').text(),
        type : $(com).attr('comment-type')
      };
    result.push(comment);
  }

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
      console.log(result.commentId);
    },
    error: function (data) {
      console.log("No comments added");
    }
  });
}


// Transform fields in objects
function toFieldObject(){

  console.log($(this));
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
  // console.log($(this).find('.evaluation').prop('checked'));
  obj.setting.showEvaluation = $(this).find('.evaluation').prop('checked');
  obj.setting.error = $('[name="incorrect__'+ obj._id +'"]:checked').val();
  obj.setting.ordenate = parseInt(this.getAttribute('ordenation'));
  obj.setting.isRequired = true;
  if(obj.type != 'paragraph'){
    obj.setting.label = $(this).find('.update-label').text();
  }else{
    obj.setting.label = $(this).find('.update-paragraph').html();
  }

  console.log(1, obj.setting.label = $(this).find('.update-label').text());
  console.log(2, obj.setting.label = $(this).find('.update-paragraph').html());

  obj.setting.help = $(this).find('.help-text').html();
  if(obj.type != 'file-upload'){
    obj.setting.value = $(this).find('.update-value').val();
  }

  console.log(3, $(this).find('.help-text').html());
  console.log(4, $(this).find('.update-value').val());

  obj.setting.placeholder = $(this).find('.update-value').attr('placeholder');
  obj.setting.checked = $(this).find('.update-value').prop('checked');
  obj.setting.min = $(this).find('.update-min').attr('min');
  obj.setting.max = $(this).find('.update-max').attr('max');
  obj.setting.step = $(this).find('.step-value').val();
  obj.setting.type = $(this).find('[type=radio]:checked').val();
  obj.setting.class = ($(this).hasClass('half-row')) ? 'half-row' : '';

  console.log(5, $(this).find('.step-value').val());
  console.log(6, $(this).find('[type=radio]:checked').val());
  console.log('\n');

  // &#10;

  if(obj.type == 'phone-field'){
    //obj.setting.mask =  $(this).find('.draggable-input').val();
    obj.setting.mask = $(this).find('[mask]').attr('mask');
  }

  if(obj.type == 'signature'){
    for(var i = 0; i < canvasArray.length; i++){
      var sign = canvasArray[i];
      if(sign.field == obj._id){
        obj.setting.signature = sign.signature.toDataURL();
      }
    }
  }

  obj.comments = getComments(obj._id);

  var rules = $(this).find('.rules tr:not(:first-of-type)');

  for(var i = 0; i < rules.length; i++){
    var r = rules[i];
    var rule = {
      page : {
        _id : $(r).find('td.page-id').attr('page-id'),
        label : $(r).find('td.page-id').text()
      },
      field : {
        _id : $(r).find('td.field-id').attr('field-id'),
        index : $(r).find('td.field-id .ordenation').text().replace('(', '').replace(')',''),
        label : $(r).find('td.field-id .field-label').text()
      },
      comparison : {
        value : $(r).find('td.comparison').attr('value'),
        label : $(r).find('td.comparison').text()
      },
      value : {
        value : $(r).find('td.value').attr('value'),
        label : $(r).find('td.value').text()
      }
    }
    obj.setting.rule.conditions.push(rule);
  }

  switch(obj.type){
    case 'select' : 
      var options = $(this).find('.drag-input select option');
      
      for(var i = 0; i < options.length; i++){
        var opt = options[i];
        var option = {
            label : $(opt).text(),
            value : $(opt).val(),
            prop : $(opt).prop('selected')
        };
        obj.setting.options.push(option);
      }
      break;
    case 'radio-group' :
      var options = $(this).find('.drag-input .radio');

      for(var i = 0; i < options.length; i++){
        var opt = options[i];
        var option = {
          label : $(opt).find('label').text(),
          value : $(opt).find('input').val(),
          prop : $(opt).find('input').prop('checked')

        };
        obj.setting.options.push(option);
      }
      break;
    case 'checkbox-group' :
      var options = $(this).find('.drag-input .checkbox');

      for(var i = 0; i < options.length; i++){
        var opt = options[i];
        var option = {
          label : $(opt).find('label').text(),
          value : $(opt).find('input').val(),
          prop : $(opt).find('input').prop('checked')
        };
        //console.log($(opt).find('label').val())
        obj.setting.options.push(option);
      }

      break;
  }
  tempFields.push(obj);
}

// Save current tabs and fields in Json
function toJson(){  
  tempContainers = new Array();
  var listContainers = $('.tab');

  for(var i = 0; i < listContainers.length; i++){

    console.log("tojson", listContainers);

    var container = listContainers[i];
    var id = $(container).attr('id').replace("tab", "");
    var index = $(container).attr('id');
    var tabId = $(container).attr('tab-id');
    var title = $('a[href="#' + index + '"]').text();

    tempFields = new Array();
    var index = $(container).closest('.tab').index();
    var tab = {
      config : {
        _id : id,
        title : title,
        tabId : tabId
      },
      fields : []
    }
    var listFields = $(container).find('.draggable-input');

    listFields.each(toFieldObject);

    tab.fields = tempFields;
    tempContainers.push(tab);
  }

  var fieldArray = new Array();
  
  return JSON.stringify(tempContainers);
}


function GetElement(query){
    if(this === window) {
        return new GetElement(query);
    }

    this.element = document.querySelectorAll(query);

    for(var i = 0; i < this.element.length; i++){
        this.element[i].toggleClass = function(className, toggle){
            var element = this;
            if(toggle !== undefined && toggle !== null){
                if( toggle === true ) {
                    element.classList.remove(className);
                }else{
                    element.classList.add(className);
                }
            }else{
                if( element.classList.contains(className) ) {
                    element.classList.remove(className);
                }else{
                    element.classList.add(className);
                }
            }
            return this;
        };
        this.element[i].toggle = function(toggle){
            if(toggle !== null && toggle !== undefined){
                if(toggle === false){
                    this.style.display = 'none';
                }else{
                    this.style.display = null;
                }
            }else{
                if(this.style.display === 'none'){
                    this.style.display = null;
                }else{
                    this.style.display = 'none';
                }
            }
            return this;
        };
        this.element[i].remove = function(element){
            element.parentNode.removeChild(element);
        };
    }

    this.element.toggleClass = function(className, toggle){
        for(var i = 0; i < this.length; i++){
            this[i].toggleClass(className, toggle);
        }
        return this;
    };
    this.element.toggle = function(toggle){
        for(var i = 0; i < this.length; i++){
            this[i].toggle(toggle);
        }
        return this;
    };
    this.element.remove = function(element){
        for(var i = 0; i < this.length; i++){
            this[i].remove(element);
        }
    };

    return this.element ;
}


function Field(obj){
  var type = obj.type;
  var label = obj.setting.label;
  var settings = obj.setting;
  var mask = obj.setting.mask;
  var id = obj._id || fieldCounter;

  var field = create('div');
  field.setAttribute('id', type);
  field.setAttribute('data-id', '');
  field.setAttribute('field-type', type);
  field.setAttribute('field-id', id);
  //console.log(id);
  field.classList.add('draggable-input', 'panel');

  var heading = new Heading(type);

  var body = create('div');
  body.classList.add('panel-body');
  
  var dragInput = create('div');
  dragInput.classList.add('drag-input', 'form-group');

  switch(type){
    case 'header' :
      dragInput.appendChild( new Header() );
      break;
    case 'paragraph' :
      body.appendChild( new DragLabel(true) );
      dragInput.appendChild( new Paragraph() );
      break;
    case 'file-upload' :
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new FileUpload() );
      break;
    case 'text-area' :
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new TextArea() );
      break;
    case 'signature' :
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new Signature() );
      break;
    case 'checkbox-group':
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new Checkbox() );
      break;
    case 'radio-group':
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new Radio() );
      break;
    case 'select':
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new Select() );
      break;
    default :
      body.appendChild( new DragLabel() );
      dragInput.appendChild( new DragInput(type, mask) );
  }

  body.appendChild(dragInput);
  
  if(isClientView){
    if(type !== 'header' && type !== 'paragraph'){
      body.appendChild( new DragComments() );
      if(obj.setting.showEvaluation){
         body.appendChild( new DragValidate() );
      }
    }
  }else{
    body.appendChild( new DragOptions(type, settings) );
  }

  field.appendChild(heading);
  field.appendChild(body);

  return field;
}

//Field Heading
function Heading(type){

  var heading = create('div');
  heading.classList.add('drag-heading');
  heading.setAttribute('draggable', true);

  var headingH4 = create('h4');
  headingH4.textContent = type.replace('-group', "").replace('-field', "");

  var headingMenu = create('ul');
  var expandField = create('li');

  var expandFieldIcon = create('i');
  expandFieldIcon.classList.add('fa', 'expand-field', 'fa-compress');
  var cloneField = create('li');
  var cloneFieldIcon = create('i');
  cloneFieldIcon.classList.add('fa', 'fa-clone');
  var configField = create('li');
  var configFieldIcon = create('i');
  configFieldIcon.classList.add('fa', 'fa-cog');
  var deleteField = create('li');
  var deleteFieldIcon = create('i');
  deleteFieldIcon.classList.add('fa', 'fa-times');

  expandField.appendChild(expandFieldIcon);
  cloneField.appendChild(cloneFieldIcon);
  configField.appendChild(configFieldIcon);
  deleteField.appendChild(deleteFieldIcon);

  headingMenu.appendChild(expandField);
  headingMenu.appendChild(cloneField);
  headingMenu.appendChild(configField);
  headingMenu.appendChild(deleteField);

  heading.appendChild(headingH4);
  heading.appendChild(headingMenu);

  return heading;
}

//Field Label
function DragLabel(isParagraph){

  var dragLabel = create('div');
  dragLabel.classList.add('drag-label');

  dragLabel.label = create('label');
  dragLabel.label.classList.add('update-label');
  if(!isParagraph){
    dragLabel.label.textContent = "Label";
  }else{
    dragLabel.label.textContent = "";
  }

  dragLabel.appendChild(dragLabel.label);

  dragLabel.help = create('div');
  dragLabel.help.classList.add('help');
  
  dragLabel.help.iconHolder = create('div');
  dragLabel.help.iconHolder.classList.add('icon');
  dragLabel.help.iconHolder.icon = create('i');
  dragLabel.help.iconHolder.icon.classList.add('fa', 'fa-book');

  dragLabel.help.iconHolder.appendChild(dragLabel.help.iconHolder.icon);


  dragLabel.help.appendChild(dragLabel.help.iconHolder);
  if(!isParagraph){
    dragLabel.help.comment = create('div');
    dragLabel.help.comment.classList.add('comment-icon');
    dragLabel.help.appendChild(dragLabel.help.comment);
  }

  dragLabel.appendChild(dragLabel.help);
  
  dragLabel.helpText = create('div');
  dragLabel.helpText.classList.add('text');
  dragLabel.appendChild(dragLabel.helpText);

  return dragLabel;
}

//Create Inputs

function DragInput(type, mask){
  var input = create('input');
  input.type = type.replace('-field', "");
  input.classList.add('form-control', 'update-value', 'update-required');
  if(type == 'phone-field') {
    mask = (mask !== undefined) ? mask : "(000) 000-0000";
    input.setAttribute('mask', mask);
  }
  return input;
}

function Signature(){
  var signature = create('div');
  signature.classList.add('form-group');

  var clear = create('button');
  clear.classList.add('clear', 'btn', 'btn-default');
  clear.textContent = "Clear";

  var canvas = create('canvas');
  canvas.classList.add('update-required');

  signature.appendChild(clear);
  signature.appendChild(canvas);

  return signature;
}

function FileUpload(){
  var fragment = document.createDocumentFragment();

  var input = create('div');
  input.classList.add('file-drop', 'm-t-20');


  var fileHolderH5 = create('h5');
  fileHolderH5.classList.add('bold', 'm-t-20');
  fileHolderH5.textContent = "Files ";

  var icon = create('i');
  icon.classList.add('fa', 'fa-arrow-down');

  fileHolderH5.appendChild(icon);

  var fileHolder = create('div');
  fileHolder.classList.add('file-holder');
  
  if(isClientView){
    fragment.appendChild(input);
  }

  fragment.appendChild(fileHolderH5);
  fragment.appendChild(fileHolder);

  return fragment;
}

function TextArea(){
  var input = create('textarea');
  input.classList.add('form-control', 'update-value', 'update-required');
  return input;
}

function Header(){
  var input = create('h3');
  input.textContent = "Label";
  input.classList.add('update-label')
  return input;
}

function Paragraph(){
  var input = create('p');
  input.textContent = "Label";
  input.classList.add('update-paragraph');
  return input;
}

function Checkbox(){
  var input = create('div');
  input.classList.add('checkbox-group', 'update-required');
  return input; 
}

function Radio(){
  var input = create('div');
  input.classList.add('radio-group', 'update-required');
  return input;
}

function Select(){
  var input = create('select');
  input.classList.add('form-control', 'update-required', 'update-value');

  var opt = create('option');
  opt.value = "initial-value";
  opt.textContent = "Select One";

  input.appendChild(opt);

  return input;
}

function DragOptions(type, settings){

  var dragOptions = create('div');
  dragOptions.classList.add('drag-options', 'p-t-20', 'hidden');

  var h4 = create('h4')
  h4.textContent = "Field Configuration";

  var form = create('div');
  form.classList.add('form-horizontal');

  if(type !== 'paragraph' && type !== 'header'){
    var evaluation = new Evaluation(settings.showEvaluation);
    form.appendChild(evaluation);
  }
  
  var labelConfig = new LabelConfig(type, settings.label);
  form.appendChild(labelConfig);

  if(type == 'checkbox-group' || type === 'select' || type === 'radio-group'){
    var optionsConfig = new OptionsConfig(type, settings.options);
    form.appendChild(optionsConfig);
  }

  if(type !== 'checkbox-group' && type == 'select' && type == 'radio-group' && type !== 'paragraph' && type !== 'header' && type !== 'file-upload' && type !== 'signature' && type !== 'date-field'){
    var placeholderConfig = new PlaceholderConfig(settings.placeholder);
    form.appendChild(placeholderConfig);  
  }

  if(type == 'phone-field'){
    var maskConfig = new MaskConfig(settings.mask);
    form.appendChild(maskConfig);
  }

  if(type !== 'header'){
    var helpConfig = new HelpConfig(settings.help);
    form.appendChild(helpConfig);
  }


  dragOptions.appendChild(h4);
  dragOptions.appendChild(form);

  return dragOptions;
}

function Evaluation(showEvaluation){
  var evaluation = create('div');
  evaluation.classList.add('form-group');

  evaluation.label = create('label');
  evaluation.label.textContent = "Evaluate Field";


  evaluation.inputHolder = create('div');
  evaluation.inputHolder.classList.add('checkbox', 'checkbox-success');

  evaluation.input = create('input');
  evaluation.input.classList.add('evaluation');
  evaluation.input.type = "checkbox";
  evaluation.input.checked = showEvaluation;
  

  evaluation.inputHolder.appendChild(evaluation.input);
  evaluation.inputHolder.appendChild(evaluation.label);
  evaluation.appendChild(evaluation.inputHolder);

  return evaluation;
}

function LabelConfig(type, label){

  var labelConfig = create('div');
  labelConfig.classList.add('form-group', 'config-form');

  labelConfig.label = create('label');
  labelConfig.label.classList.add('control-label');
  labelConfig.label.textContent = "Label";
  
  labelConfig.appendChild(labelConfig.label);

  if(type !== 'paragraph'){
    labelConfig.input = create('input');
    labelConfig.input.classList.add('form-control' , 'label-text');
    labelConfig.input.type = "text";
    labelConfig.input.value = label;
  }else{
    labelConfig.input = create('div', ['full-row'])
    labelConfig.input.p = create('div');
    labelConfig.input.p.classList.add('form-control' , 'paragraph-content');
    labelConfig.input.p.setAttribute('contenteditable', true);
    labelConfig.input.p.textContent = label;

    labelConfig.input.appendChild(labelConfig.input.p);
  }


  labelConfig.appendChild(labelConfig.input);

  return labelConfig;
}

function PlaceholderConfig(placeholder){

  var placeholderConfig = create('div');
  placeholderConfig.classList.add('form-group', 'config-form');

  placeholderConfig.label = create('label');
  placeholderConfig.label.classList.add('control-label');
  placeholderConfig.label.textContent = "Placeholder"

  placeholderConfig.input = create('input');
  placeholderConfig.input.classList.add('form-control', 'value');
  placeholderConfig.input.type = "text";
  placeholderConfig.input.value = placeholder;

  placeholderConfig.appendChild(placeholderConfig.label);
  placeholderConfig.appendChild(placeholderConfig.input);

  return placeholderConfig;
}


function MaskConfig(mask){
  var maskConfig = create('div');
  maskConfig.classList.add('form-group', 'config-form');

  maskConfig.label = create('label');
  maskConfig.label.classList.add('control-label');
  maskConfig.label.textContent = "Mask"

  maskConfig.input = create('input');
  maskConfig.input.classList.add('form-control', 'mask');
  maskConfig.input.type = "text";
  maskConfig.input.value = (mask !== undefined && mask !== null) ? mask : "(000) 000-0000";

  maskConfig.appendChild(maskConfig.label);
  maskConfig.appendChild(maskConfig.input);

  return maskConfig;
}

function HelpConfig(help){
  var helpConfig = create('div');
  helpConfig.classList.add('form-group', 'config-form');

  helpConfig.label = create('label');
  helpConfig.label.classList.add('control-label');
  helpConfig.label.textContent = "Help Text"

  helpConfig.formGroup = create('div', ['form-group', 'help-holder']);

  helpConfig.formGroup.input = create('div');
  helpConfig.formGroup.input.classList.add('help-text');
  helpConfig.formGroup.input.setAttribute('contenteditable', true);
  helpConfig.formGroup.input.setAttribute('rows', 15);
  helpConfig.formGroup.input.innerHTML = help;

  helpConfig.formGroup.appendChild(helpConfig.formGroup.input);

  helpConfig.appendChild(helpConfig.label);
  helpConfig.appendChild(helpConfig.formGroup);

  return helpConfig;
}

function Commands(){
  var commands = create('div');
  commands.classList.add('commands');

  function Button(command){
    var btn = create('button');
    btn.classList.add('btn', 'btn-default');
    btn.setAttribute('data-command', command);

    var icon = create('i');
    icon.classList.add('fa');

    switch(command){
      case 'justifyLeft' :
        icon.classList.add('fa-align-left');
        break;
      case 'justifyRight' :
        icon.classList.add('fa-align-right');
        break;
      case 'justifyCenter' :
        icon.classList.add('fa-align-center');
        break;
      case 'justifyFull' :
        icon.classList.add('fa-align-justify');
        break;
      case 'insertUnorderedList' :
        icon.classList.add('fa-list-ul');
        break;
      case 'insertOrderedList' :
        icon.classList.add('fa-list-ol');
        break;
      case 'createLink' :
        icon.classList.add('fa-link');
        break;
      case 'h4' :
        icon.textContent = "H1";
        break;
      case 'h5' :
        icon.textContent = "H2";
        break;
      case 'p' :
        icon.textContent = "P";
        break;
      default :
        icon.classList.add('fa-'+ command.toLowerCase());
    }

    btn.appendChild(icon);
    return btn;
  }

  commands.appendChild( new Button('bold') );
  commands.appendChild( new Button('italic') );
  commands.appendChild( new Button('underline') );
  commands.appendChild( new Button('strikeThrough') );
    commands.appendChild( new Button('createLink') );
    commands.appendChild( new Button('unlink') );
    if(1){
  commands.appendChild( new Button('justifyLeft') );
  commands.appendChild( new Button('justifyCenter') );
  commands.appendChild( new Button('justifyRight') );
  commands.appendChild( new Button('justifyFull') );
  //commands.appendChild( new Button('indent') );
  //commands.appendChild( new Button('outdent') );
  commands.appendChild( new Button('insertUnorderedList') );
  commands.appendChild( new Button('insertOrderedList') );
  commands.appendChild( new Button('h4') );
  commands.appendChild( new Button('h5') );
  commands.appendChild( new Button('p') );
    }
  return commands;
}


function OptionsConfig(type, options){
  var optionsConfig = create('div');
  optionsConfig.classList.add('options', 'form-group', 'config-form');

  var label = create('label');
  label.classList.add('control-label');
  label.textContent = "Options";

  optionsConfig.appendChild(label);

  var addOption = create('div');
  addOption.classList.add('full-row');

  addOption.input = create('input');
  addOption.input.classList.add('form-control', 'label-input', 'col-md-4');
  addOption.input.setAttribute('placeholder', "Item");

  addOption.button = create('a')
  addOption.button.textContent = "Add";
  addOption.button.classList.add('add-options', 'btn', 'btn-success');

  addOption.appendChild(addOption.input);
  addOption.appendChild(addOption.button);

  optionsConfig.appendChild(addOption);

  if(type === 'select'){

    var tableHolder = create('div', ['m-t-20']);

    var table = create('table');
    table.classList.add('table', 'color-table', 'muted-table');

    var thead = create('thead');
    thead.innerHTML = '<tr><th>Label</th><th>Value</th><th></th></tr>';

    table.appendChild(thead);

    var tbody = create('tbody')
    tbody.classList.add('options-table');
    tbody.innerHTML = '<tr></tr>';

    table.appendChild(tbody);

    tableHolder.appendChild(table);

    addOption.appendChild(tableHolder);
    
  }


  return optionsConfig;

}

function DragComments(){
  var commentsHolder = create('div');
  commentsHolder.classList.add('p-t-20', 'drag-comments', 'hidden');

  var externalH4 = create('h4');
  externalH4.classList.add('active', 'open-external');
  externalH4.textContent = "Comments";

  var internalH4 = create('h4');
  internalH4.classList.add('open-internal');
  internalH4.textContent = "Internal Comments";

  var listExternal = create('ul');
  listExternal.classList.add('comments', 'external-comments');

  var listInternal = create('ul');
  listInternal.classList.add('comments', 'internal-comments');

  commentsHolder.appendChild(externalH4);
  commentsHolder.appendChild(internalH4);
  commentsHolder.appendChild(listExternal);
  commentsHolder.appendChild(listInternal);

  var commentInput = create('div');
  commentInput.classList.add('form-group', 'comment-input', 'row');

  var text = create('textarea');
  text.classList.add('comment-msg', 'form-control');
  text.setAttribute('comment-type', 'external');
  text.setAttribute('placeholder', 'Type your comment here')

  var sendComment = create('div');
  sendComment.classList.add('col-sm-3', 'radio', 'text-right', 'pull-right');

  var send = create('a');
  send.classList.add('btn', 'btn-default', 'add-comment');
  send.textContent = "Send";

  sendComment.appendChild(send);
  commentInput.appendChild(text);
  commentInput.appendChild(sendComment);

  commentsHolder.appendChild(commentInput);

  return commentsHolder;
}

function DragValidate(){
  var dragValidate = create('div');
  dragValidate.classList.add("drag-validate");

  function Radio(type){
    var radio = create('div');
    radio.classList.add('radio');
    var input = create('input');
    input.classList.add('is-incorrect');
    input.type = "radio"
    input.value = type;
    input.name = "incorrect";

    var label = create('label');
    label.textContent = type;

    radio.appendChild(input);
    radio.appendChild(label);

    return radio;
  }

  dragValidate.appendChild(new Radio("Pass"));
  dragValidate.appendChild(new Radio("Fail"));
  dragValidate.appendChild(new Radio("Audit"));


  return dragValidate;
}

function create(tag, classes, config){

  var element = document.createElement(tag);
  
  //add class
  if(arguments[1] != null){
    for(var i = 0; i < classes.length; i++){
      element.classList.add(classes[i]);
    }
  }

  //add attributes
  if(arguments[2] != null){
    if(config.type != null) element.type = config.type;
    if(config.text != null) element.textContent = config.text;
    if(config.value != null) element.value = config.value;
    if(config.placeholder != null) element.setAttribute('placeholder', config.placeholder);
  }
  
  return element; 
}