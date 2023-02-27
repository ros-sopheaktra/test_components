/**
 * 
 * @param {*} selectId 
 * @param {*} callBackFunction 
 */
function selectOnChange(selectId, callBackFunction){
    $(selectId).on('change', callBackFunction);
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
 * 
 */
function set_static_options(optionsObject){

}

/**
 * 
 */
function generate_selection_options( selectElementId, options){
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
