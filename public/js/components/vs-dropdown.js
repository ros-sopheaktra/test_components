/**
 * On component dropdown options changed, active the custom dropdown 
 * selection method based on given unique id and dropdown selection method. 
 * @param [Html_Element_Id] selectId 
 * @param [JS_Method] callBackFunction 
 */
function vsSelectOnChange( selectId, callBackFunction ){
    const selectionId = this.set_unique_select_id(selectId);

    $(selectionId).on('change', callBackFunction);
};

/**
 * Set dropdown select component unique id based on component 
 * structure in order to prevent future duplicate id by accident.
 * @param [string] selectId
 * @return [string] uniqueSelectId
 */
function set_unique_select_id(selectId){
    const uniqueSelectId = `#select-component-${selectId}-id`;

    return uniqueSelectId;
}

/**
 * Set hidden input dynamic unique id based on component 
 * structure in order to prevent future duplicate id by accident.
 * @param [string] selectId
 * @return [string] uniqueHiddenDynamicId
 */
function get_hidden_input_dynamic_id(selectId){
    const uniqueHiddenDynamicId = `#hidden-dynamic-selected-component-${selectId}-id`;

    return uniqueHiddenDynamicId;
}

/**
 * Get the hidden input dynamic options value and decode from json 
 * into ready to use object based on given unique id pass as paramter.
 * @param [string] selectId
 * @return [Object] input_hiddenOptions;
 */
function get_hidden_dynamic_options(selectId){
    const input_hiddenOptions = $(this.get_hidden_input_dynamic_id(selectId)).val();

    return JSON.parse(input_hiddenOptions)
}

/**
 * Generate selection options and bind into the component based 
 * on given component unique id and a options object that 
 * contained array of data pass as paramters.
 * @param [Html_Element_Id] selectElementId
 * @param [Object] options
 */
function generate_selection_options( selectElementId, options ){
    const selectionId = this.set_unique_select_id(selectElementId);

    const select = $(selectionId);
    const hasExtraAttributes = "extraAttributes" in options[0];
    for( object in options ){
        const option = $("<option></option>")
            .attr( 'value', options[object].value )
            .text( capitalizeText(options[object].label) );

        if( hasExtraAttributes ){
            const extraAttributes = options[object].extraAttributes;
            for( attribute in extraAttributes ){
                option.attr( attribute, extraAttributes[attribute] );
            }
        }

        option.appendTo(select);
    }
}
