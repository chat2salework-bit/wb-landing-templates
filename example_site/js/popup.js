function freeCall() {
    var freeCall = document.querySelector('#free-call-hover');
    freeCall.classList.toggle('spinner-yolo1');
}
// var timerId=setInterval(freeCall(),3000);
var timerId = setInterval(freeCall,3000);

var freeCallBlock = document.querySelector('#free-call');
freeCallBlock.addEventListener('mouseover', function() {
    var freeCallHover = document.querySelector('#free-call-hover');
    if(freeCallHover.classList.contains('spinner-yolo1') != true){
        freeCallHover.classList.add('spinner-yolo1');
    }
    clearInterval(timerId);
});
freeCallBlock.addEventListener('mouseout', function() {
    var freeCallHover = document.querySelector('#free-call-hover');
    freeCallHover.classList.remove('spinner-yolo1');
    timerId=setInterval(freeCall,3000);
});
freeCallBlock.addEventListener('click', function() {
    var overlay = document.querySelector('#overlay-modal-window-widget');
    var modalForm = document.querySelector('#modal-window-widget');
    overlay.style.display = 'block';
    modalForm.style.display = 'block';
});

var closeModalForm = document.querySelector('#modal-window-widget .close-modal-window-widget');
closeModalForm.addEventListener('click', function() {
    var overlay = document.querySelector('#overlay-modal-window-widget');
    var modalForm = document.querySelector('#modal-window-widget');
    overlay.style.display = 'none';
    modalForm.style.display = 'none';
})
