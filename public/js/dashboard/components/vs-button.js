function buttonOnClick(buttonId, callBackFunction){
    $(buttonId).on('click', callBackFunction.call());
};

function buttonOnClickTest(buttonId){
    $(buttonId).on('click', function(){
        alert('hi');
    });
};