/**
 * On component checkbox clicked, active the custom checkbox 
 * method based on given unique checkbox element id and checkbox method. 
 * @param [Html_Element_Id] checkboxId 
 * @param [JS_Method] callBackFunction 
 */
function vsCheckBoxCheck( checkboxIdOrClass, callBackFunction ){
    $(checkboxIdOrClass).on( 'click', callBackFunction );
};