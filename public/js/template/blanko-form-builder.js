var isValid;


Dropzone.autoDiscover = false;

var canvas;
var canvasArray = new Array();
appendNavigation();
if($('client-view').length <= 0){ appendList(); }
//appendModel();

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
    //console.log(scrollTop);
    //console.log($('#page-wrapper').height())

    if(scrollTop >= menuTop && (scrollTop + $('#list-container').height()) < (scrollTop + $('#drag-container').height()) ){
        if( scrollTop < $('#page-wrapper').height() - $('#list-container').height() ) {
            if(1){
                $('#list-container').addClass('fixed').css({
                    top: scrollTop - menuTop
                });
            }
        }
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
    var obj = {
        type : id.replace('#', ""),
        setting : {
            label : "Label",
            placeholder : "Placeholder",
            help : "",
            mask : "(000) 000-0000",
            options : []
        }
    }

    dragSrcEl = new Field(obj);

    // Add necessary events to the source
    addEvents(dragSrcEl);
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
        setTimeout(function(){
            $(dragSrcEl).find('.label-text').focus();
            $(dragSrcEl).find('.paragraph-content').focus();
        });
    }
    $('#blankSpace').remove();
    return false;
}

// Adds all events necessary for the field
function addEvents(elem, id = null, signature = null){

    //$(elem).css('display', 'none');

    var type = elem.getAttribute('field-type');

    if(id == null){
        id = fieldCounter;
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


    if( !isClientView ){
        // Add Event Listeners for Drag and Drop
        elem.addEventListener('dragover', handleDragOver, false);
        elem.addEventListener('drop', handleDrop, false);
        elem.addEventListener('dragend', handleDragEnd, false);
        $(elem).find('.drag-heading')[0].addEventListener('dragstart', handleDragStart, false);
    }

    updateLabel(elem);
    updateHelp(elem);
    updatePlaceholder(elem);
    headingEvents(elem);
    clickForLabels(elem);
    updateIncorrect(elem);
    updateRules(elem);
    updateRequired(elem);
    fieldComments(elem);
    toggleComments(elem);
    
    switch(type){
        case 'checkbox-group':
            fieldOptions(elem);
            changeCheckbox(elem);
            break;
        case 'radio-group':
            fieldOptions(elem);
            changeRadio(elem);
            break;
        case 'select':
            fieldOptions(elem);
            changeSelect(elem);
            break;
        case 'signature':
            signatureField(elem);
            changeSignature(elem);
            break;
        case 'number-field':
            updateNumberField(elem);
            changeField(elem);
            break;
        case 'file-upload':
            updateFileUpload(elem);
            changeField(elem);
            break;
        case 'phone-field':
            updateMask(elem);
            changeField(elem);
            break;
        case 'paragraph':
            updateParagraph(elem);
            changeField(elem);
            break;
        default :
            changeField(elem);
    }

    function updateLabel(elem){
        // Update Label Text
        $(elem).find('.drag-options .label-text').keyup(function(){
            $(elem).find('.update-label').val($(this).val());
            $(elem).find('.update-label').text($(this).val());
        });
        $(elem).find('.drag-options .label-text').change(function(){
            $('[field-id="'+ id +'"] .field-label').text($(this).val());
        });
    }

    function updateHelp(elem){
        // Update Help Text
        if( $(elem).find('.help-text').html() === "" ){
            $(elem).find('.help .icon').hide();
        }

        $(elem).find('.drag-options .help-text').keyup(function(e){
            e.preventDefault();
            $(elem).find('.help + .text').html($(this).html());
            if($(this).html() == ""){
                $(elem).find('.help .icon').hide();
            }else{
                $(elem).find('.help .icon').show();
            }
        });

        $(elem).find('.help + .text').hide();

        $(elem).find('.help .icon').click(function(){
            $(this).parent().next('.text').slideToggle();
        });
    }
    function updateMask(elem){
        var input =  $(elem).find('.drag-input input');
        input.mask(input.attr('mask'));

        $(elem).find('.mask').change(function(){
            input.mask($(this).val());
            input.attr('mask', $(this).val());
            $(elem).find('.drag-options .value').mask($(this).val());
        });
    }
    function updatePlaceholder(elem){
        $(elem).find('.drag-options .value').keyup(function(){
            $(elem).find('.update-value').attr('placeholder', $(this).val());
        });
    }
    function updateNumberField(elem){
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
    }
    function updateParagraph(elem){
        $(elem).find('.drag-options .paragraph-content').keyup(function(){
            $(elem).find('.update-paragraph').html($(this).html());
        });
    }
    function updateRequired(elem){
        $(elem).find('.span-required').toggle($(elem).find('.drag-options .is-required').prop('checked'));
        $(elem).find('.drag-options .is-required').change(function(){
            var isRequired = $(this).prop('checked');
            $(elem).find('.span-required').toggle(isRequired);
            $(elem).find('.update-required').toggleClass('required', isRequired);
        });
    }
    function headingEvents(elem){
        // Configure Field
        $(elem).find('.drag-heading .fa-cog').click(function(){
            var dragOpt = $(this).closest('.draggable-input').find('.drag-options').toggleClass('hidden');
            if(type == 'paragraph' && $('.report-view').length > 0){
                if ( $(this).closest('.draggable-input').find('.drag-options').hasClass('hidden') ){
                    $(this).closest('.draggable-input').find('.update-paragraph').show();
                }else{
                    $(this).closest('.draggable-input').find('.update-paragraph').hide();

                }
            }
            if(dragOpt.hasClass('hidden')){
                dragOpt.find('.commands').remove();
            }else{
                dragOpt.find('.help-text').before(new Commands());
                helpFormatter(elem);
            }
        });

        //Expand Field
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

        // Delete Field
        $(elem).find('.drag-heading .fa-times').click(function(){
            var fieldId = $(this).closest('.draggable-input').attr('id').split('__')[1];
            var rules = $('.rules tr td')
            for(var i = 0; i < rules.length; i++){
                var rule = rules[i];
                if($(rule).attr('field-id') == fieldId ){
                    $(rule).parent().remove();
                }
            }
            $(this).closest('.draggable-input').remove();
            ordenateFields();
        });
    }
    function fieldComments(elem){
        //Comments
        var commentType = $(elem).find('input[type="radio"][name="comment-type"]');
        commentType.attr('name', commentType.attr('name') + id);
        $(commentType[1]).prop('checked', true);

        $(elem).find('.add-comment').click(function(){
            var message = $(elem).find('.comment-msg');
            var type = message.attr('comment-type');
            appendComment(username, message.val(), type, $(elem));
            saveComments(id, username, message.val(), type);
            message.val('');
        });
    }
    function toggleComments(elem){
        // Toggle Comments
        $(elem).find('.drag-heading .fa-comments-o').click(function(){
            $(this).closest('.draggable-input').find('.drag-comments').toggleClass('hidden');
        });
        $(elem).find('.external-comments').show();
        $(elem).find('.internal-comments').hide();

        toggleCommentTypes(elem);
        expandField(elem);
    }
    function toggleCommentTypes(elem){
        $(elem).find('h4.open-external').click(function(){
            $(elem).find('.external-comments').show();
            $(elem).find('.internal-comments').hide();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).closest('.draggable-input').find('.comment-msg').attr('comment-type', 'external');
        })

        $(elem).find('h4.open-internal').click(function(){
            $(elem).find('.external-comments').hide();
            $(elem).find('.internal-comments').show();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).closest('.draggable-input').find('.comment-msg').attr('comment-type', 'internal');
        })
    }
    function expandField(elem){
        if(isClientView){
            $(elem).removeClass('half-row');
            $(elem).find('.drag-heading .expand-field').addClass('fa-expand').removeClass('fa-compress');
        }else{
            if($(elem).hasClass('half-row')){
                $(elem).find('.drag-heading .expand-field').addClass('fa-expand').removeClass('fa-compress');
            }else{
                $(elem).find('.drag-heading .expand-field').addClass('fa-compress').removeClass('fa-expand');
            }
        }
    }
    function fieldOptions(elem){
        // Add Options
        $(elem).find('.drag-options .add-options').click(function(e){
            e.preventDefault();
            var field = $(this).closest('.draggable-input');
            var label = field.find('.label-input');
            if(label.val() != ""){
                addOption( type , field, label.val(), label.val(), false, id);
                label.val('');
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
    }
    function signatureField(elem){
        //Signature Pad

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

    function updateRules(elem){
        //rules
        if($('#drag-container.report-view').length <= 0){
            if( $(elem).find('.rules-container').length <= 0){
                var comparisonOptions = '<option value="==">Is</option><option value="!=">Is not</option><option value="<">Less than</option><option value="<=">Less or equal</option><option value=">">More than</option><option value=">=">More or equal</option>';
                var addRules = '<div class="rules-container"><h4>Rules  <i class="fa fa-chevron-down toggle-rules"></i></h4><div class="rules-content"><div class="row m-b-20"><select class="col-lg-2 rule-action"><option value="hide">Hide if</option><option value="show">Show if</option></select> <select class="col-lg-2 rule-target"><option value="all">All</option><option value="any">Any</option></select></div><h5>New Condition</h5><div class="form-group row rule"><div class="col-lg-2"><select class="tabs form-control"></select></div><div class="col-lg-2"><select class="fields form-control"><option value="initial">Select Field</option></select></div><div class="col-lg-2"><select class="form-control comparison">'+ comparisonOptions +'</select></div><div class="col-lg-2 input-holder"><input type="text" class="form-control" placeholder="Value"></div> <button class=" add-rule btn btn-default">Add Condition</button></div>';
                addRules += '<table class="table rules"><tr><td> Page</td><td> Field</td><td>comparison </td><td>Value </td><td class="text-right">Delete</td> </tr></table></div></div>';
                $(elem).find('.drag-options').append(addRules);
            }
        }

        $(elem).find('.rules-content').hide();

        $(elem).find('.toggle-rules').click(function(){
            $(this).closest('.drag-options').find('.rules-content').slideToggle();
            $(this).toggleClass('fa-chevron-down');
            $(this).toggleClass('fa-chevron-up');
            if( ! $(this).hasClass('fa-chevron-down') ) {
                updateRulesPages();
            }
        });

        $(elem).find('.add-rule').click(function(e){
            e.preventDefault();
            node = $(this).closest('.draggable-input').find('.rules');
            pages = {
                _id : $(this).parent().find('select.tabs').val(),
                label : $(this).parent().find('select.tabs option:selected').text()
            };

            fields = {
                _id : $(this).parent().find('select.fields').val(),
                index : $(this).parent().find('select.fields option:selected').attr('ordenation'),
                label : $(this).parent().find('select.fields option:selected').text().split(')')[1]
            };

            comparison = {
                value : $(this).parent().find('select.comparison').val(),
                label : $(this).parent().find('select.comparison option:selected').text()
            }

            var input = $(this).parent().find('.input-holder .rule-value');
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

            if( (pages._id != 'initial') && (fields._id != 'initial')){
                addRule(node, pages, fields, comparison, value);

                $(elem).find('a.remove-row').click(function(e){
                    e.preventDefault();
                    $(this).closest('tr').remove();
                });

            }
        });

        $(elem).find('.tabs').change(function(){
            var id = $(this).find(':selected').val();
            var fields = $('#tab' + id).find('.draggable-input');
            var elemId = $(this).closest('.draggable-input').attr('id').split('__')[1];
            options = '<option value="initial">Select Field</option>';
            for(var i = 0; i < fields.length; i++){
                var field = fields[i];
                var index = i;
                id = $(field).attr('id').split('__')[1];
                title = $(field).find('.drag-label label').text();
                type = field.getAttribute('field-type');
                ordenation = field.getAttribute('ordenation');
                if( ( type != "paragraph" && type != "header" && type != "signature" && type != "file-upload" ) && elemId != id ){
                    options += '<option value="'+ id +'" ordenation="'+ ordenation +'">('+ ordenation + ')' + title +'</option>';
                }
            }
            $(this).closest('.drag-options').find('.fields').html(options);
        });

        $(elem).find('.fields').change(function(){
            fieldId = $(this).find('option:selected').val();
            getOptions($(elem),fieldId);
        })
    }
    function clickForLabels(elem){
        var labels = $(elem).find('input + label');
        for(var i = 0; i < labels.length; i++){
            var label = labels[i];
            var index = i;

            var name = 'input_' + id + '__' + index;
            $(label).attr('for', name);
            $(label).prev().attr('id', name);
        }
    }
    function updateIncorrect(elem){
        //prevInput.next().attr('for', radio.attr('name'));
        //Incorrect / Correct
        var radio = $(elem).find('input[name="incorrect"]');
        var chosenRadio = $(elem).find('input[name="incorrect"]:checked');
        $(elem).find('.drag-heading').removeClass('Pass Fail Audit').addClass(chosenRadio.val());
        radio.attr('name', radio.attr('name') + '__' + id);

        radio.change(function(){
            if($(this).prop('checked') == true) {
                $(elem).find('.drag-heading').removeClass('Pass Fail Audit').addClass($(this).val());
                $(elem).find('.drag-input').removeClass('required-fail');
                $(elem).find('.required-error-message').remove()
                checkFieldValue(id, null, null, $(this).val());
            }
        });

        if(type == 'header' || type == 'paragraph'){
            $(elem).find('.drag-heading').removeClass('Pass Fail Audit');
        }
    }
    function updateFileUpload(elem){
        $(elem).find('.file-drop').addClass('dropzone').dropzone({
            url : "/upload-files",
            addRemoveLinks: true,
            acceptedFiles : "image/*,application/pdf,.psd, .docx, .mp4, .mp3",
            init: function() {
                this.on('success', function(file, response) {
                    $('.dz-success-mark').remove();
                    $('.dz-error-mark').remove();
                    var fileObj = {
                        path: response.file,
                        type: file.type,
                        name: file.name
                    }
                    $(elem).find('.drag-input').removeClass('required-fail');
                    $(elem).find('.required-error-message').remove()
                    checkFieldValue(id, null, null, null, fileObj);
                });
                this.on('error', function(file, response) {
                    $('.dz-success-mark').remove();
                    $('.dz-error-mark').remove();
                    $(file.previewElement).find('.dz-error-message').text("File could not be uploaded.");
                });
                this.on("thumbnail", function(file, dataUrl) {
                    $('#file-holder').html('<img src="'+dataUrl+'" width="200" height="200" alt="<?php echo $empNameFull; ?>">');
                });
                this.on("removedfile", function(file, dataUrl) {
                    for(var i = 0; i < filesArray[id]; i++){
                        var item = filesArray[id][i]
                        var index = i;
                        if(item.name == file.name){
                            filesArray[id].splice(index, 1);
                            checkFieldValue(id, filesArray[id]);
                        }
                    }
                });

            },
            sending : function(file, xhr, formData){
                $('.dz-success-mark').remove();
                $('.dz-error-mark').remove();
                formData.append("folder", appFolder);
                formData.append("_token", window.Laravel.csrfToken);
            }
        });
    }
    function changeSelect(elem){
        // On Change Value

        $(elem).find('.drag-input .update-value').change(function(){
            $(elem).find('.drag-heading.Fail').removeClass('Fail');
            $(elem).find('.drag-validate input[value="Fail"]').prop('checked', false);
            var optionsArray = new Array();
            var options = $(this).find('option');
            for(var i = 0; i < options.length; i++){
                var opt = options[i];
                var option = {
                    label : $(opt).text(),
                    value : $(opt).val(),
                    prop : $(opt).prop('selected')
                };
                optionsArray.push(option);
            }
            $(elem).find('.drag-input').removeClass('required-fail');
            $(elem).find('.required-error-message').remove()
            checkFieldValue(id, null, optionsArray);
        });
    }
    function changeRadio(elem){
        $(elem).find('.drag-input input[type="radio"]').change(function(){
            $(elem).find('.drag-heading.Fail').removeClass('Fail');
            $(elem).find('.drag-validate input[value="Fail"]').prop('checked', false);
            var optionsArray = new Array();
            var options = $(this).closest('.drag-input').find('.radio');
            for(var i = 0; i < options.length; i++){
                var opt = options[i];
                var option = {
                    label : $(opt).find('label').text(),
                    value : $(opt).find('input').val(),
                    prop : $(opt).find('input').prop('checked')
                };
                optionsArray.push(option);

            }
            $(elem).find('.drag-input').removeClass('required-fail');
            $(elem).find('.required-error-message').remove()
            checkFieldValue(id, null, optionsArray);
        });
    }
    function changeCheckbox(elem){
        $(elem).find('.drag-input input[type="checkbox"]').change(function(){
            $(elem).find('.drag-heading.Fail').removeClass('Fail');
            $(elem).find('.drag-validate input[value="Fail"]').prop('checked', false);
            var optionsArray = new Array();
            var options = $(this).closest('.drag-input').find('.checkbox');

            for(var i = 0; i < options.length; i++){
                var opt = options[i];
                var option = {
                    label : $(opt).find('label').text(),
                    value : $(opt).find('input').val(),
                    prop : $(opt).find('input').prop('checked')
                };
                optionsArray.push(option);
            }
            $(elem).find('.drag-input').removeClass('required-fail');
            $(elem).find('.required-error-message').remove()
            checkFieldValue(id, null, optionsArray);
        });
    }
    function changeSignature(elem){
        $(elem).find('canvas').on('mouseup', function() {
            function hasSameId(elem) {return elem.field == id;}

            var filtered = canvasArray.filter(hasSameId)[0];

            var val = filtered.signature.toDataURL();

            checkFieldValue(id, val);
        });
    }
    function changeField(elem){
        $(elem).find('.drag-input .update-value').change(function(){
            var val = $(this).val();
            $(elem).find('.drag-input').removeClass('required-fail');
            $(elem).find('.required-error-message').remove()
            checkFieldValue(id, val, []);
            $(elem).find('.drag-heading.Fail').removeClass('Fail');
            $(elem).find('.drag-validate input[value="Fail"]').prop('checked', false);
        });
    }
    function helpFormatter(elem){
        //Text Format
        //var helpFormatter = '<div class="commands"><button class="btn btn-default" data-command="bold"><i class="fa fa-bold"></i></button> <button class="btn btn-default" data-command="italic"><i class="fa fa-italic"></i></button> <button class="btn btn-default" data-command="underline"><i class="fa fa-underline"></i></button> <button class="btn btn-default" data-command="strikeThrough"><i class="fa fa-strikethrough"></i></button> <button class="btn btn-default" data-command="justifyLeft"><i class="fa fa-align-left"></i></button> <button class="btn btn-default" data-command="justifyCenter"><i class="fa fa-align-center"></i></button> <button class="btn btn-default" data-command="justifyRight"><i class="fa fa-align-right"></i></button> <button class="btn btn-default" data-command="justifyFull"><i class="fa fa-align-justify"></i></button> <button class="btn btn-default" data-command="indent"><i class="fa fa-indent"></i></button> <button class="btn btn-default" data-command="outdent"><i class="fa fa-outdent"></i></button> <button class="btn btn-default" data-command="insertUnorderedList"><i class="fa fa-list-ul"></i></button> <button class="btn btn-default" data-command="insertOrderedList"><i class="fa fa-list-ol"></i></button> <button class="btn btn-default" data-command="h4">H1</a> <button class="btn btn-default" data-command="h5">H2</a> <button class="btn btn-default" data-command="p">P</a> <button class="btn btn-default" data-command="createlink"><i class="fa fa-link"></i></button> <button class="btn btn-default" data-command="unlink"><i class="fa fa-unlink"></i></button></div>';

        //$(elem).find('.help-formatter').html(helpFormatter);

        //$(elem).find('.paragraph-content').before(helpFormatter);

        $(elem).find('.commands button').on('click', function(e) {
            e.preventDefault();

            var command = $(this).data('command');

            if (command == 'h4' || command == 'h5' || command == 'p') {
                document.execCommand('formatBlock', false, command);
            }
            if (command == 'createlink') {
                url = prompt('Enter the link here: ', 'http:\/\/');
                document.execCommand($(this).data('command'), false, url);
            } else document.execCommand($(this).data('command'), false, null);

            $(elem).find('.help-text').keyup();
            $(elem).find('.paragraph-content').keyup();

            $('#drag-container').find('a:not(.btn)').attr('target', '_blank');
        });
    }

     // setTimeout(function(){
     // if( type == 'paragraph'){
     // $(elem).find('.paragraph-content')[0].focus();
     // }else{
     // $(elem).find('.label-text')[0].focus();
     // }
     // });
        
    //window.scroll(0, elem.offsetTop);

    //end addEvents
}

// Appends field models
function appendModel(){
    var html = $('<div id="input-types" class="hidden"> <div class="draggable-input panel" data-id="" id="checkbox-group"> <div class="drag-heading" draggable="true">  <h4>Checkbox</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Checkbox </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group checkbox-group update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Checkbox"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Item"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"></tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="radio-group"> <div class="drag-heading" draggable="true">  <h4>Radio Button</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Radio Button </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group radio-group update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Radio Button"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Item"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> </tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="select"> <div class="drag-heading" draggable="true">  <h4>Select</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Select </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <select class="form-control update-required update-value"> <option value="initial-value">Select one</option> </select> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Select"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="options"> <div class="form-group"> <label class="control-label col-sm-2">Options</label> <div class="col-sm-4"> <input type="text" class="form-control label-input" placeholder="Item"> </div><a href="#" class="add-options btn btn-success">Add </a> </div><div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <table class="table color-table muted-table"> <thead> <tr> <th>Label</th> <th>Value</th> <th></th> </tr></thead> <tbody class="options-table"> <tr class="hidden"> <td>Label</td><td>Value</td><td class="text-nowrap"></td></tr></tbody> </table> </div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="date-field"> <div class="drag-heading" draggable="true">  <h4>Date Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Date Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <input type="date" class="form-control update-value update-required"> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Date Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="file-upload"> <div class="drag-heading" draggable="true">  <h4>File Upload</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">File Upload </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"></div></div><h5 class="bold">Files <i class="fa fa-arrow-down"></i></h5> <div class="file-holder"></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="File Upload"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="header"> <div class="drag-heading" draggable="true">  <h4>Static Header</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"> <h3 class="update-label">Header</h3> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Header"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="paragraph"> <div class="drag-heading" draggable="true">  <h4>Static Paragraph</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-input"> <div class="update-paragraph">Paragraph</div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <label class="control-label col-sm-2">Content</label> <div class="col-sm-10"> <div contenteditable="true" class="form-control paragraph-content">Paragraph</div></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div></div></div><div class="draggable-input panel" data-id="" id="number-field"> <div class="drag-heading" draggable="true">  <h4>Number Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Number Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <input type="number" class="form-control update-value update-required update-min update-max update-step" min="" max="" step=""> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Number Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="form-group"> <label class="control-label col-sm-2">Min</label> <div class="col-sm-10"> <input type="number" class="form-control min-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Max</label> <div class="col-sm-10"> <input type="number" class="form-control max-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Step</label> <div class="col-sm-10"> <input type="number" class="form-control step-value"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Placeholder</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="text-field"> <div class="drag-heading" draggable="true">  <h4>Text Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Text Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <input type="text" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Text Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="form-group"> <label class="control-label col-sm-2">Placeholder</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="email-field"> <div class="drag-heading" draggable="true">  <h4>E-mail Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">E-mail Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <input type="email" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="E-mail Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="form-group"> <label class="control-label col-sm-2">Placeholder</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="phone-field"> <div class="drag-heading" draggable="true">  <h4>Masked Field</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Masked Field </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <input type="text" class="form-control update-value update-required"> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Masked Field"> </div></div><div class="form-group"> <label class="control-label col-sm-2" title="Ex: (000) 000-0000">Mask</label> <div class="col-sm-10"> <input type="text" class="form-control mask" value="(000) 000-0000"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="form-group"> <label class="control-label col-sm-2">Placeholder</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="text-area"> <div class="drag-heading" draggable="true">  <h4>Text Area</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body" draggable="false"> <div class="drag-label"> <label class="update-label">Text Area </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <textarea class="form-control update-value update-required"></textarea> </div></div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Text Area"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div><div class="form-group"> <label class="control-label col-sm-2">Placeholder</label> <div class="col-sm-10"> <input type="text" class="form-control value"> </div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div><div class="draggable-input panel" data-id="" id="signature"> <div class="drag-heading" draggable="true">  <h4>Signature</h4> <ul> <li><i class="fa expand-field"></i></li><li><i class="fa fa-clone"></i></li><li><i class="fa fa-cog"></i></li><li><i class="fa fa-times"></i></li></ul> </div><div class="panel-body " draggable="false"> <div class="drag-label"> <label class="update-label">Signature </label><span class="span-required">*</span> <div class="help"> <div class="icon"><i class="fa fa-question-circle"></i></div><div class="comment-icon"></div></div><div class="text"></div></div><div class="drag-input"> <div class="form-group"> <button class="clear btn btn-default">Clear</button> </div><canvas class="update-required"></canvas> </div><div class="hidden drag-options form-horizontal p-t-20"> <h4>Options</h4> <div class="form-horizontal"> <div class="form-group"> <div class="col-sm-offset-2 col-sm-10 required-field"> <div class="checkbox checkbox-success"> <input type="checkbox" class="is-required"> <label> Required </label> </div></div></div><div class="form-group"> <label class="control-label col-sm-2">Label</label> <div class="col-sm-10"> <input type="text" class="form-control label-text" value="Signature"> </div></div><div class="form-group"> <label class="control-label col-sm-2">Help Text</label> <div class="col-sm-10"> <div class="help-formatter"> </div><p class="help-text" contenteditable="true" rows="15"> </p></div></div></div></div><div class="hidden drag-comments p-t-20"> <h4 class="active open-external">Comments</h4> <h4 class="open-internal">Internal Comments</h4> <ul class="comments external-comments"></ul> <ul class="comments internal-comments"></ul> <div class="comment-input row"> <div class="form-group"> <div class="col-sm-12"> <textarea comment-type="external" placeholder="Type your comment here" class="comment-msg form-control"></textarea> </div><div class="col-sm-3 radio text-right pull-right"> <a class="btn btn-default add-comment">Send</a> </div></div></div></div><div class="drag-validate"> <div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Pass"> <label> Pass </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Fail"> <label> Fail </label> </div><div class="radio"> <input type="radio" name="incorrect" class="is-incorrect" value="Audit"> <label> Site Audit </label> </div></div></div></div></div>');
    $("#drag-container").append(html);
    //$('.drag-heading').append($('<div>'));
}

// Appends fields menu
function appendList(){
    var html = $('<div id="list-container"> <h3>Input Types</h3> <ul class="input-list"> <li><a draggable="true" href="#header"><i class="fa m-r-10 fa-header"></i> Header</a></li><br><li><a draggable="true" href="#paragraph"><i class="fa m-r-10 fa-paragraph"></i> Paragraph</a></li><br><li><a draggable="true" href="#text-field"><i class="fa m-r-10 fa-font"></i> Text Field</a></li><br><li><a draggable="true" href="#text-area"><i class="fa m-r-10 fa-align-justify"></i> Text Area</a></li><br><li><a draggable="true" href="#number-field"><i class="fa m-r-10"><b>1</b></i> Number Field</a></li><br><li><a draggable="true" href="#email-field"><i class="fa m-r-10 fa-envelope-o"></i> Email Field</a></li><br><li><a draggable="true" href="#date-field"><i class="fa m-r-10 fa-calendar"></i> Date Field</a></li><br><li><a draggable="true" href="#phone-field"><i class="fa m-r-10 fa-puzzle-piece"></i> Masked Field</a></li><br><li><a draggable="true" href="#select"><i class="fa m-r-10 fa-caret-down"></i> Select</a></li><br><li><a draggable="true" href="#radio-group"><i class="fa m-r-10 fa-check-circle"></i> Radio Button</a></li><br><li><a draggable="true" href="#checkbox-group"><i class="fa m-r-10 fa-check-square"></i> Checkbox</a></li><br><li><a draggable="true" href="#file-upload"><i class="fa m-r-10 fa-upload"></i> File Upload</a></li><br><li><a draggable="true" href="#signature"><i class="fa m-r-10 fa-pencil"></i> Signature</a></li><br></ul></div>');
    if($('#drag-container.report-view').length > 0){
        html.find('li:not(:nth-of-type(1)):not(:nth-of-type(2))').remove()
    }
    $("#drag-container").append(html);

    var list = $('#list-container .input-list li');

    list.click(function(e){
        e.preventDefault();
        var id = getHash(this);
        //var clone = $('#input-types ' + id).clone();
        var obj = {
            type : id.replace('#', ""),
            setting : {
                label : "Label",
                placeholder : "Placeholder",
                help : "",
                mask : "(000) 000-0000",
                options : []
            }
        }
        var clone = new Field(obj);

        addEvents(clone);
        $('#drag-container .tab.active').append(clone);

        ordenateFields();
    });

    for(var i = 0; i < list.length; i++){
        var item = list[i];
        item.addEventListener('dragstart', handleDragStartList, false);
        item.addEventListener('dragend', handleDragEnd, false);
    }
}

// Appends tabs navigation
function appendNavigation(){
    var html = '<div class="form-holder"><div class="tabs-options pull-left"><a class="btn btn-custom2 m-r-20" id="addTab">Add Page</a><a class="btn btn-save m-r-20" id="save">Save Checkpoint</a><a class="btn btn-default" id="open-history">History</a></div><div class="filter pull-right">Filter by: <a class="btn btn-danger m-l-10 m-r-20 order-fields" data-order="Fail">Fail </a><a class="btn btn-save m-r-20 order-fields" data-order="Pass">Pass </a><a class="btn btn-custom2 m-r-20 order-fields" data-order="Audit">Audit </a><a class="btn btn-default m-r-20 order-fields" data-order="normal">Reset </a></div><nav style="clear: both"><ul></ul></nav><div id="tabs-holder" class="tabs-holder"></div></div><a class="btn btn-save" id="save-changes"><i class="fa fa-check m-r-20"></i> Save Changes</a>';
    $('#drag-container').append(html);

    $('#addTab').click(function(e){
        e.preventDefault();
        $('.form-holder .tabs-holder').append(addTab());
    });

    $('#save').click(function(e){
        e.preventDefault();
        $('#save').removeClass('btn-save').addClass('btn-default');
        setTimeout(function(){
            $('#save').addClass('btn-save').removeClass('btn-default');
        }, 1500);

        saveCheckpoint();
    });

    $('#save-changes').click(function(e){
        if(!$(this).hasClass('btn-default')){
            e.preventDefault();
            $(this).removeClass('btn-save').addClass('btn-default');
            var date = new Date();
            var time = date.toLocaleTimeString();
            $(this).text('Saved at '+ time);
        }
    })

    $('.order-fields').click(function(){
        var orderType = $(this).attr('data-order');
        if(orderType != "normal"){
            var fields = $('.draggable-input');
            for(var i = 0; i < fields.length; i++){
                var field = fields[i];
                if($(field).find('[value="'+ orderType +'"]').prop('checked') == true ){
                    $(field).prependTo($(field).closest('.tab'));
                }
            }
        }else{
            //createTabs(toJson(), isClientView, isUserClient);
            var tabs = $('.tab');
            for(var t = 0; t < tabs.length; t++){
                var fields = $(tabs[t]).find('.draggable-input');
                fields.sort(function(a, b){
                    var a = parseInt($(a).attr('ordenation'));
                    var b =  parseInt($(b).attr('ordenation'));
                    return a - b;
                });
                $(fields).prependTo($(tabs[t]));
            }
        }
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
    var modal = '<div class="modal modal-dialog modal-md"><div class="modal-content"><div class="modal-header"><button type="button" class="close" aria-hidden="true">×</button><h4 class="modal-title" id="mySmallModalLabel">Tab Configuration</h4> </div><div class="modal-body">' + config + '</div><div class="text-center p-b-20"><a class="btn btn-save p-l-20 p-r-20 ok">OK</a></div></div></div>';
    var containerTab = $('<div id="tab'+ tabId +'" tab-id="" class="tab tab-control active">' + modal + '</div>');
    $('.form-holder nav ul').append(navTab);
    //$('.form-holder .tabs-holder').append(containerTab);
    //tabHolderFrag.appendChild(containerTab[0]);

    if(obj != null){
        $('#tab' + tabId).attr('tab-id', obj.tabId);
    }

    //Update Tab Title
    containerTab.find('.btn.ok').click(function(){
        var $this = $(this).closest('.modal').find('.tab-title');
        var id = $this.closest('.tab').attr('id');
        var title = $this.val();
        $('a[href="#' + id + '"]').text(title);
        
        //att rules
        var tabsOpts = $('.rule .tabs option');
        for(var i = 0; i < tabsOpts.length; i++){
            if($this.val() == id.replace('tab', '') ){
                $this.text(title);
            }
        }

        var tds = $('.rules td');
        for(var i = 0; i < tds.length; i++){
            if($this.attr('page-id') == id.replace('tab', '') ){
                $this.text(title);
            }
        }
    });

    // remove tab
    $('.tab-remove').click(function(){
        var nav = $(this).closest('li');
        var tabId = getHash(nav);
        $('.form-holder .tabs-holder ' + tabId).remove();

        var tabsOpts = $('.rules .tabs option');
        for(var i = 0; i < tabsOpts.length; i++){
            var opt = tabsOpts[i];
            if( $(opt).attr('value') == tabId.replace('#tab', '')){
                $(opt).parent().siblings().find('option').remove()
                $(opt).remove();
            }
        }
        nav.remove();


        var tds = $('.rules td');
        for(var i = 0; i < tds.length; i++){
            var td = tds[i];
            if($(td).attr('page-id') == tabId.replace('#tab', '')){
                $(td).parent().remove();
            }
        }
    });

    // open config modal
    $('.tab-config').click(function(){
        var nav = $(this).closest('li');
        var tabId = getHash(nav);
        $(tabId + ' .modal').show();
    });

    
    containerTab.find('.modal-header button.close, .modal .btn.ok').click(function(){
        $(this).closest('.modal').hide();
    });
    
    containerTab[0].addEventListener('dragover', handleDragOverList, false);
    containerTab[0].addEventListener('drop', handleDrop, false);

    return containerTab;
}

// Adds options to field groups
function addOption(type, node, label, value, prop, id){

    var html;
    if(type == 'checkbox-group'){

        var option = create('div');
        option.classList.add('checkbox', 'checkbox-success');

        var inputOption = create('input');
        inputOption.type = "checkbox";
        inputOption.name = "checkbox-group__" + id;
        inputOption.value = value;
        if(prop) inputOption.setAttribute('checked', true);

        var labelOption = create('label');
        labelOption.textContent = label;

        option.appendChild(inputOption);
        option.appendChild(labelOption);

        if(!isClientView){
            var removeOption = create('a', ['m-l-20', 'remove-option']);
            removeOption.appendChild( create('i', ['fa', 'fa-times', 'text-danger']) );
            option.appendChild(removeOption);
            removeOption.addEventListener('click', function(){
                this.parentNode.remove();
            });
        }

        node.find('.drag-input .checkbox-group')[0].appendChild(option);

    }else if(type == 'radio-group'){
        var option = create('div');
        option.classList.add('radio', 'radio-success');

        var inputOption = create('input');
        inputOption.type = "radio";
        inputOption.name = "radio-group__" + id;
        inputOption.value = value;
        if(prop) inputOption.setAttribute('checked', true);

        var labelOption = create('label');
        labelOption.textContent = label;


        option.appendChild(inputOption);
        option.appendChild(labelOption);

        if(!isClientView){
            var removeOption = create('a', ['m-l-20', 'remove-option']);
            removeOption.appendChild( create('i', ['fa', 'fa-times', 'text-danger']) );
            option.appendChild(removeOption);
            removeOption.addEventListener('click', function(){
                this.parentNode.remove();
            });
        }

        node.find('.drag-input .radio-group')[0].appendChild(option);

    }else if(type == 'select'){

        prop = (prop) ? 'selected' : '';
        var html = '<option value="' + value + '" '+ prop +'>'+ label + '</option>';
        $(html).appendTo(node.find('.drag-input select'));

        node.find('.drag-options .options-table').append('<tr><td>' + label + '</td><td>' + value + '</td><td class="text-nowrap"><a href="#" data-toggle="tooltip" data-original-title="Delete" class="remove-option"> <i class="fa fa-close text-danger"></i> </a></td></tr>');
        
        node.find('.drag-options .remove-option').on('click', function(e){
            e.preventDefault();
            var index = $(this).closest('tr').index();

            node.find('.checkbox-group .checkbox:nth-of-type('+ (index+1) +')').remove();
            node.find('.radio-group .radio:nth-of-type('+ (index+1) +')').remove();
            node.find('select option:nth-of-type('+ (index+1) +')').remove();
            $(this).closest('tr').remove();
        });
    }
    

}

// Returns the hash of the child 'a' element
function getHash(elem){
    return $(elem).find('a').attr('href');
}

function appendComment(user, msg, type = 'external', node, id){
    if(msg != ''){
        var comment = '<li comment-type="'+ type +'" class="'+ ( (username == user) ? 'user-message': '' ) +'" comment-id="'+ ((id != null) ? id : '') +'"><p><span class="username">'+ user +'</span><div class="message">'+ msg +'</div></p></li>';
        if(type == 'external'){
            node.find('.external-comments').append(comment);
        }else{
            node.find('.internal-comments').append(comment);
        }
        //$('.comments:visible').scrollTop($('.comments:visible')[0].scrollHeight);
        node.find('.comment-icon').addClass('has-comments');
    }
}


function addRule(node, page, field, comparison, value) {

    var tr = document.createElement('tr');
    var td = document.createElement('td');
    td.setAttribute('page-id', page._id);

    var html =  '<tr> <td class="page-id" page-id="' + page._id + '">' + page.label + '</td> <td class="field-id" field-id="' + field._id + '"><span class="ordenation">('+ field.index + ')</span> <span class="field-label">' + field.label + '</span></td><td class="comparison" value="'+ comparison.value +'">'+ comparison.label+'</td> <td class="value" value="'+ value.value +'">'+ value.label +'</td> <td><a class="remove-row close col-lg-1">×</a></td> </tr>';
    node.append(html);

    node.find('a.remove-row').click(function(e){
        e.preventDefault();
        $(this).closest('tr').remove();
    });

    $('.rule .tabs').val('initial');
    $('.rule .fields').val('initial');
    $('.rule .input-holder').html('<input class="form-control" type="text">');
}

function ordenateFields(){
     var fields = $('.form-holder .draggable-input');

     for(var i = 0; i < fields.length; i++){
         var field = fields[i];
         var index = i;
         var fieldIndex = index + 1;
         //$(field).find('.drag-heading .ordenation').text('(' + (fieldIndex) + ')' );
         field.setAttribute('ordenation', fieldIndex);
         var fieldId = $(field).attr('id').split('__')[1];
     }

     var rules = $('.rules td');
     for(var i = 0; i < rules.length; i++){
     var rule = rules[i];
     var id = $(rule).attr('field-id');
     $(rule).find('.ordenation').html();
     }
}
function updateRulesPages(){
     var ruleOptions = '<option value="initial">Select Page</option>';
     var anchors = $('li.tab-control a');
     for(var i = 0; i < anchors.length; i++){
     var anchor = anchors[i];
     ruleOptions += '<option value="'+ $(anchor).attr('href').replace('#tab', '') +'">'+ $(anchor).text() +'</option>'
     }

     $('.rule').find('.tabs').html(ruleOptions);
}

function getOptions(node, fieldId){
    var field = $('.draggable-input[id*="'+fieldId+'"]');
    var type = field.attr('field-type');

    var clone = field.find('.drag-input').clone();

    if( type == 'checkbox-group' ){
        var html = '<select class="rule-value form-control">';
        var options = clone.find('.checkbox');
        for(var i = 0; i < options.length; i++){
            var option = options[i];
            val = $(option).find('input').attr('value');
            label = $(option).find('label').text();
            html+= '<option value="'+ val +'">'+ label +'</option>'
        }
        html += '</select>';
        node.find('.rule .input-holder').html(html);
    }else if( type == 'radio-group' ){
        var html = '<select class="rule-value form-control">';
        var options = clone.find('.radio');
        for(var i = 0; i < options.length; i++){
            var option = options[i];
            val = $(option).find('input').attr('value');
            label = $(option).find('label').text();
            html+= '<option value="'+ val +'">'+ label +'</option>'
        }
        html += '</select>';
        node.find('.rule .input-holder').html(html);
    }else if( type == 'select' ){
        var html = '<select class="rule-value form-control">';
        var options = clone.find('select option');

        for(var i = 0; i < options.length; i++){
            var option = options[i];
            val = $(option).attr('value');
            label = $(option).text();
            html+= '<option value="'+ val +'">'+ label +'</option>'
        }
        html += '</select>';
        node.find('.rule .input-holder').html(html);
    }else{
        clone.find('input').addClass('rule-value');
        clone.find('textarea').addClass('rule-value');
        node.find('.rule .input-holder').html(clone);
    }

}

function validateForm(){
    if(Script.env == 'local'){
        return true;
    }else{
        var shownFields = $('.tabs-holder .draggable-input:not([style="display: none;"])');
        for(var i = 0; i < shownFields.length; i++){
            var type = $(this).attr('id').split('__')[0];
            var id = $(this).attr('id').split('__')[1];
            var dragInput = $(this).find('.drag-input');

            switch(type){
                case 'signature' :
                function hasSameId(elem) {return elem.field == id;}
                    var sign = canvasArray.filter(hasSameId)[0];
                    if( sign.signature.isEmpty() ){
                        dragInput.addClass('required-fail');
                    }else{
                        dragInput.removeClass('required-fail');
                    }
                    break;
                case 'radio-group' :
                    if( $(this).find('.drag-input input:radio:checked').length <= 0 ){
                        dragInput.addClass('required-fail');
                    }else{
                        dragInput.removeClass('required-fail');
                    }
                    break;
                case 'checkbox-group' :
                    if( $(this).find('.drag-input input:checkbox:checked').length <= 0 ){
                        dragInput.addClass('required-fail');
                    }else{
                        dragInput.removeClass('required-fail');
                    }
                    break;
                case 'file-upload' :
                    if($(this).find('.dz-preview').length > 0 || $(this).find('.file-holder h5').length > 0){
                        dragInput.removeClass('required-fail');
                    }else{
                        dragInput.addClass('required-fail');
                    }
                    break;
                case 'select' :
                    if( $(this).find('.drag-input select').val() == 'initial-value' ){
                        dragInput.addClass('required-fail');
                    }else{
                        dragInput.removeClass('required-fail');
                    }
                    break;
                case 'email-field' :
                    isValid = validateEmail($(this).find('.drag-input input').val());
                    if( isValid){
                        dragInput.removeClass('required-fail');
                    }else{
                        dragInput.addClass('required-fail');
                        dragInput.next('.required-error-message').remove();
                        $('<p class="required-error-message m-t-10" style="color: red">Please, insert a valid e-mail.</p>').insertAfter(dragInput);
                    }
                    break;
                default :
                    if($(this).find('.drag-input input').val() == ''){
                        dragInput.addClass('required-fail');
                    }else{
                        dragInput.removeClass('required-fail');
                    }
            }
        }

        var fails = $('.required-fail, .drag-heading.Fail');

        var reproves = $('.drag-heading.Fail');

        var id = $('.tab-fail').removeClass('tab-fail');


        for(var i = 0; i < fails.length; i++){
            var fail = fails[i];
            var id = $(fail).closest('.tab').attr('id');
            $('[href="#'+id+'"]').addClass('tab-fail');
        }

        $('.fail-error').remove();
        if(reproves.length > 0){
            var holder = $('.btn-submit').parent();
            $('<p class="alert text-center alert-danger fail-error">There are fields needing review</p>').prependTo(holder);
        }

        if(fails.length > 0 || reproves.length > 0){
            return false;
        }else{
            return true;
        }
    }
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function optionsNotEmpty(){
    var errors = 0;
    
    if( $('.drag-input .checkbox-group').length > 0 ){
        $('.drag-input .checkbox-group').each(function(){
            if( $(this).find('.checkbox').length <= 0) errors++;
        });
    }

    if( $('.drag-input .radio-group').length > 0 ){
        $('.drag-input .radio-group').each(function(){
            if( $(this).find('.radio').length <= 0) errors++;
        });
    }

    if( $('.drag-input select').length > 0){
        $('.drag-input select').each(function(){
            if( $(this).find('option').length <= 1) errors++;
        });   
    }

    console.log(errors);
    return errors <= 0;
}