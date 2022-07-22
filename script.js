//variables
var cardsaction = 'database';		//	localstorage (aka the browser cache/session), database (aka ajax call to cards.php)
var databasecards = null;

let cardBeignDragged;
let dropzones = document.querySelectorAll('.dropzone');
let priorities;
// let cards = document.querySelectorAll('.kanbanCard');
//	let dataColors comes from index.php now
let defColor = dataColors[0]['color'];
let dataCards = {
    config:{
        maxid:0
    },
    cards:[]
};
let theme="light";
//initialize

$(document).ready(()=>{
	init();
	
	//	Adding event listeners...
    $('#add').click(()=>{
        const title = $('#titleInput').val()!==''?$('#titleInput').val():null;
        const description = $('#descriptionInput').val()!==''?$('#descriptionInput').val():null;
        // $('#titleInput').val('');
        // $('#descriptionInput').val('');
        if(title) {// && description){
            //let id = dataCards.config.maxid+1;
            let id = (dataCards.cards).length

			//	Overwrite the ID if it's set (via edit)...
			let existingid = $('.form-inline>#primaryidInput').val();
			let existingposition = ((existingid) ? $('.form-inline>#positionInput').val() : defColor);
			let saveaction = ((existingid) ? 'update' : 'add');

			id = ((existingid) ? existingid : id);

            const newCard = {
                id,
                title,
                description,
                position:existingposition,
                priority: false
            }
            dataCards.cards.push(newCard);
            dataCards.config.maxid = id;
            save(saveaction, id);
            //appendComponents(newCard);
            initializeCards();

			//	Now to clear the fields...
			$("#clear").trigger('click');
        }
    });
    $("#deleteAll").click(()=>{
        dataCards.cards = [];
		
        save('deleteall', '');
    });
    $("#clear").click(()=>{
		$('.form-inline>#titleInput').val('');
		$('.form-inline>#descriptionInput').val('');
		$('.form-inline>#primaryidInput').val('');
		$('.form-inline>#positionInput').val('');
		return false;
    });
    $("#theme-btn").click((e)=>{
        e.preventDefault();
        $("body").toggleClass("darkmode");
        if(theme){
        	if(cardsaction == 'localstorage') {
            	localStorage.setItem("@kanban:theme", `${theme==="light"?"darkmode":""}`)
			} else {
				//	Execute DB call...
				//	Execute DB call...
				var getstring = '';
				getstring += '&theme=' + `${theme==="light"?"darkmode":"light"}`;
				execDBCall('savetheme', '', getstring);
			}
        }
        else{
        	if(cardsaction == 'localstorage') {
            	localStorage.setItem("@kanban:theme", "darkmode")
			} else {
				//	Execute DB call...
				var getstring = '';
				getstring += '&theme=darkmode';
				execDBCall('savetheme', '', getstring);
			}
        }
    });
});

function init() {
	//	Creating CSS elements/classes to the body of the email to handle the dynamic coloring...
	var css = '<style>';
	dataColors.forEach(item=>{
		css += '.' + item.color + ' { border-left: 5px solid ' + item.color + '; } ';
	});
	css += '</style>';
	$('body').append(css);
	
	if(cardsaction == 'localstorage') {
		$("#loadingScreen").css("display", "none");
		theme = localStorage.getItem('@kanban:theme');
		if(theme){
			$("body").addClass(`${theme==="light"?"light":"darkmode"}`);
		}
		initializeBoards();
		if(JSON.parse(localStorage.getItem('@kanban:data'))){
			dataCards = JSON.parse(localStorage.getItem('@kanban:data'));
//	        console.log(dataCards);
			initializeComponents(dataCards);
		}
		
		initializeCards();
		
	} else if(cardsaction == 'database') {
		//	Converting buttons to type="button"...
		$('#add').prop('type', 'button');
		$('#deleteAll').prop('type', 'button');
		$('#theme-btn').prop('type', 'button');
		$('#invisibleBtn').prop('type', 'button');
		execDBCall('getdata', 'databaseInit', '');
	}
}

function reload() {
	$("#boardsHeader").html('');
	$("#boardsContainer").html('');
	
	dataCards = {
		config:{
			maxid:0
		},
		cards:[]
	};
	init();
}

function databaseInit() {
//	console.log("function databaseInit() {...");
	
	$("#loadingScreen").css("display", "none");
	theme = databasecards.data.theme;
	if(theme){
	    $("body").addClass(`${theme==="light"?"light":"darkmode"}`);
	}
	initializeBoards();
	
	var dbarray = [];
	dbarray['config'] = [];
	dbarray['config']['maxid'] = (databasecards.data.cards).length;
	dbarray['cards'] = [];
	
	for(var a = 0; a < (databasecards.data.cards).length; a++) {
//		console.log(databasecards.data.cards[a]);
		dbarray['cards'][a] = databasecards.data.cards[a];
		dbarray['cards'][a]['priority'] = ((dbarray['cards'][a]['priority'] == '0') ? false : true);
	}
	dataCards = dbarray;
	initializeComponents(dataCards);
	
    initializeCards();
}

function execDBCall(dbaction, callbackfnc, getstring) {
	console.log("function execDBCall("+dbaction+", "+callbackfnc+") {...");
	//	Now executing ajax to get the content necessary for this modal...
	var payload = 'cards.php?action=' + escape(dbaction) + '&' + getstring;
	
	$.ajax({
		url: payload,
		async: false,
		fail: function() {
			return false;
		},
		success: function(result) {
			console.log(JSON.parse(result));
			databasecards = JSON.parse(result);
			
			if(callbackfnc != '') {
				window[callbackfnc].apply(this);
			}
		}
	});
}

//functions
function initializeBoards(){    
    dataColors.forEach(item=>{
    	let headerString = `
        <div class="board">
            <h3 class="text-center">${item.title.toUpperCase()}</h3>
        </div>
        `
        let htmlString = `
        <div class="board">
            <div class="dropzone" id="${item.color}">
            </div>
        </div>
        `
        $("#boardsHeader").append(headerString)
        $("#boardsContainer").append(htmlString)
    });
    let dropzones = document.querySelectorAll('.dropzone');
    dropzones.forEach(dropzone=>{
        dropzone.addEventListener('dragenter', dragenter);
        dropzone.addEventListener('dragover', dragover);
        dropzone.addEventListener('dragleave', dragleave);
        dropzone.addEventListener('drop', drop);
    });
}

function initializeCards(){
    cards = document.querySelectorAll('.kanbanCard');
    
    cards.forEach(card=>{
        card.addEventListener('dragstart', dragstart);
        card.addEventListener('drag', drag);
        card.addEventListener('dragend', dragend);
    });
}

function initializeComponents(dataArray){
    //create all the stored cards and put inside of the todo area
    dataArray.cards.forEach(card=>{
        appendComponents(card); 
    })
}

function appendComponents(card){
//	console.log("appendComponents()");
//	console.log(card);
    //creates new card inside of the todo area
    let htmlString = `
        <div id=${card.id.toString()} class="kanbanCard ${card.position}" draggable="true">
            <div class="content">               
                <h4 class="title">${card.title}</h4>
                ` + ((card.description) ? `<p class="description">${card.description}</p>` : '') +
            `</div>
            <form class="row mx-auto justify-content-between">
            	<!--
				<div class="col-xs-6 text-left">
	                <span id="span-${card.id.toString()}" onclick="togglePriority(event)" class="material-icons priority ${card.priority? "is-priority": ""}">
	                    star
	                </span>
                </div>
				-->
            	<div class="col-xs-6 text-left">
					<span class="material-icons edit" onclick="editCard(${card.id.toString()})">
						edit
					</span>
                </div>
                <div class="col-xs-6 text-right">
	                <button class="invisibleBtn">
	                    <span class="material-icons delete" onclick="deleteCard(${card.id.toString()})">
	                        remove_circle
	                    </span>
	                </button>
                </div>
            </form>
        </div>
    `
    $(`#${card.position}`).append(htmlString);
    priorities = document.querySelectorAll(".priority");
}

function togglePriority(event){
    event.target.classList.toggle("is-priority");
    var updateid = event.target.id.split('-')[1];
    dataCards.cards.forEach(card=>{
        if(updateid === card.id.toString()){
            card.priority=card.priority?false:true;
        }
    })
//	console.log("togglePriority(event)...: " + updateid);
    save('update', updateid);
}

function editCard(id){
    dataCards.cards.forEach(card=>{
        if(card.id == id){
            let index = dataCards.cards.indexOf(card);
//            console.log(index)
            dataCards.cards.splice(index, 1);
            console.log(card);
			$('.form-inline>#titleInput').val(card.title);
			$('.form-inline>#descriptionInput').val(card.description);
			$('.form-inline>#primaryidInput').val(card.id);
			$('.form-inline>#positionInput').val(card.position);
            //save('delete', id);
        }
    })
    //save('update', updateid);
}

function deleteCard(id){
    dataCards.cards.forEach(card=>{
        if(card.id == id){
            let index = dataCards.cards.indexOf(card);
//            console.log(index)
            dataCards.cards.splice(index, 1);
//            console.log(dataCards.cards);
            save('delete', id);
        }
    })
}


function removeClasses(cardBeignDragged, color){
	dataColors.forEach(item=>{
		cardBeignDragged.classList.remove(item.color);
	});
//    cardBeignDragged.classList.remove('red');
//    cardBeignDragged.classList.remove('blue');
//    cardBeignDragged.classList.remove('purple');
//    cardBeignDragged.classList.remove('green');
//    cardBeignDragged.classList.remove('yellow');
    cardBeignDragged.classList.add(color);
    position(cardBeignDragged, color);
}

function save(action, id){
//	console.log("function save("+action+", "+id+"){");
	if(cardsaction == 'localstorage') {
    	localStorage.setItem('@kanban:data', JSON.stringify(dataCards));
	} else {
		var getstring = '';
		
		if(action == 'add') {
			getstring += '&id=' + escape(id);//escape(dataCards.cards[id].id);
			getstring += '&title=' + escape(dataCards.cards[id].title);
			getstring += '&description=' + escape((dataCards.cards[id].description) ? dataCards.cards[id].description : '');
			getstring += '&position=' + escape(dataCards.cards[id].position);
			getstring += '&priority=' + escape(dataCards.cards[id].priority);
			
			//	Execute DB call...
//			console.log("Executing execDBCall("+action+", '', "+getstring+")");
			execDBCall(action, 'reload', getstring);
			
		} else if(action == 'deleteall') {
			
			//	Execute DB call...
//			console.log("Executing execDBCall("+action+", '', "+getstring+")");
			execDBCall(action, 'reload', getstring);
			
		} else if(action == 'delete') {
			getstring += '&id=' + escape(id);
			
			//	Execute DB call...
//			console.log("Executing execDBCall("+action+", '', "+getstring+")");
			execDBCall(action, 'reload', getstring);
			
		} else if(action == 'update') {
//			console.log(dataCards.cards);
			getstring += '&id=' + escape(dataCards.cards[id].id);
			getstring += '&title=' + escape(dataCards.cards[id].title);
			getstring += '&description=' + escape((dataCards.cards[id].description) ? dataCards.cards[id].description : '');
			getstring += '&position=' + escape(dataCards.cards[id].position);
			getstring += '&priority=' + escape(dataCards.cards[id].priority);
			
			//	Execute DB call...
//			console.log("Executing execDBCall("+action+", '', "+getstring+")");
			execDBCall(action, 'reload', getstring);
		} else {
			//	Do NOT execute the execDBCall just yet otherwise it'll do it over and over on every mouse move...
			
		}
		
	}
}

function position(cardBeignDragged, color){
	//console.log("function position("+cardBeignDragged+", "+color+"){");
	if(cardsaction == 'localstorage') {
		const index = dataCards.cards.findIndex(card => card.id === parseInt(cardBeignDragged.id));
		dataCards.cards[index].position = color;
		save('update', index);
	} else if(cardsaction == 'database') {
		//	Do not save here...
		const index = dataCards.cards.findIndex(card => card.id === parseInt(cardBeignDragged.id));
		//console.log(cardBeignDragged.id);
		dataCards.cards[cardBeignDragged.id].position = color;
	}
}

//cards
function dragstart(){
//	console.log("function dragstart(){...");
    dropzones.forEach( dropzone=>dropzone.classList.add('highlight'));
    this.classList.add('is-dragging');
}

function drag(){
    
}

function dragend(event){
//	console.log("function dragend(" + event + "){...");
    dropzones.forEach( dropzone=>dropzone.classList.remove('highlight'));
    this.classList.remove('is-dragging');
		
	save('update', event.target.id);
}

// Release cards area
function dragenter(){

}

function dragover({target}){
    this.classList.add('over');
    cardBeignDragged = document.querySelector('.is-dragging');
	dataColors.forEach(item=>{
	    if(this.id ===item.color){
	        removeClasses(cardBeignDragged, item.color);
	    }
	});
	
//    if(this.id ==="yellow"){
//        removeClasses(cardBeignDragged, "yellow");
//    }
//    else if(this.id ==="green"){
//        removeClasses(cardBeignDragged, "green");
//    }
//    else if(this.id ==="blue"){
//        removeClasses(cardBeignDragged, "blue");
//    }
//    else if(this.id ==="purple"){
//        removeClasses(cardBeignDragged, "purple");
//    }
//    else if(this.id ==="red"){
//        removeClasses(cardBeignDragged, "red");
//    }
    
    this.appendChild(cardBeignDragged);
}

function dragleave(){
  
    this.classList.remove('over');
}

function drop(){
    this.classList.remove('over');
}
