/**
 * On component button clicked, active the custom button 
 * method based on given unique button element id and button method. 
 * @param [Html_Element_Id] buttonId 
 * @param [JS_Method] callBackFunction 
 */
function vsButtonOnClick( buttonId, callBackFunction ){
    const uniqueButtonId = this.set_unique_button_id(buttonId);

    $(uniqueButtonId).on( 'click', callBackFunction );
};

/**
 * Set button component unique id based on component 
 * structure in order to prevent future duplicate id by accident.
 * @param [string] buttonId
 * @return [string] uniqueButtonId
 */
function set_unique_button_id(buttonId){
    const uniqueButtonId = `#button-component-${buttonId}-id`;

    return uniqueButtonId;
}
