/*var temporaryCheckpoints = new Array();
var checkpoints = new Array();
var clicked;
var current = 0;
var lastReview = new Date();
localStorage.getItem('checkpoints');

if(localStorage.getItem('checkpoints') != null){
	checkpoints = JSON.parse(localStorage.getItem('checkpoints'));
	checkpoints.forEach(function(checkpoint, index){
		appendToHistory(index, new Date(checkpoint.time), true, checkpoint.data);
	});
}else{
	appendToHistory(current, lastReview, true);
}

setInterval( function(){
	var date = new Date();
	if(toJson() != temporaryCheckpoints[current-1] && toJson() != clicked){
		$('#checkpoints').find('a').removeClass('active');
		appendToHistory(current, date);
	}
}, 30000);

setInterval(saveCheckpoint, 240000);

function loadPonctualCheckpoint(id){
	$('#drag-container .tab-control').remove();
	console.log('cv = ' + isClientView);
	createTabs(temporaryCheckpoints[id], isClientView, isUserClient);
}

function saveCheckpoint(){
	var date = new Date();

	if(toJson() != localStorage.getItem('form')){
		checkpoints.push({
			data : toJson(),
			time : date.getTime()
		});
		localStorage.setItem('form', toJson());
		localStorage.setItem('checkpoints', JSON.stringify(checkpoints), '  ');
		appendToHistory(current, date, true);
	}
}

function appendToHistory(index, date, checkpoint = false, data = toJson()){
	temporaryCheckpoints.push(data);
	$('#checkpoints').find('a').removeClass('active');
	$('#checkpoints').prepend(
		$('<a>', {
		class : checkpoint ? 'active checkpoint' : 'active',
		'href' : index,
		text : checkpoint ? 'Checkpoint: '+ date.toLocaleDateString() + ' ' + date.toLocaleTimeString() : 'Temporary Checkpoint: '+ date.toLocaleDateString() + ' ' + date.toLocaleTimeString(),
		click : function(e){
				e.preventDefault();
				id = $(this).attr('href');
				$('#checkpoints').find('a').removeClass('active');
				$(this).addClass('active');
				loadPonctualCheckpoint(id);
				clicked = temporaryCheckpoints[id];
			}
		})
	);
	current++;
}*/