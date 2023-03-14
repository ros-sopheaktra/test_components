/**
 * Set dropdown select component unique id based on component 
 * structure in order to prevent future duplicate id by accident.
 * @param [string] selectId
 * @return [string] uniqueSelectId
 */
function set_unique_table_id(selectId, option = ''){
    const uniqueSelectId = `#${option}${selectId}-id`;

    return uniqueSelectId;
}

/**
 * 
 */
function generate_thead_table( theadId, dataObjects){
    const getTheadId = this.set_unique_table_id(theadId, 'thead-component-');
    
    const tableTheadId = $(getTheadId);
    let trThead = $('<tr></tr>');
    for( object in dataObjects ){
        let th = $('<th></th>')
            .text(capitalizeText(dataObjects[object].label))
            .appendTo(trThead);
    }

    trThead.appendTo(tableTheadId);
}

/**
 * 
 */
function generate_tbody_table( tbodyId, dataObjects){
    const tableTbodyId = this.set_unique_table_id(tbodyId, 'tbody-component-');

    const tbody = $(tableTbodyId);
    for( object in dataObjects ){
        let tr = $('<tr></tr>');

        let tdId = $('<td></td>')
        .text(dataObjects[object].id)
        .appendTo(tr);

        let tdName = $('<td></td>')
        .text(dataObjects[object].name)
        .appendTo(tr);

        let tdAlertQuantity = $('<td></td>')
        .text(dataObjects[object].alert_quantity)
        .appendTo(tr);

        let tdProductPoint = $('<td></td>')
        .text(dataObjects[object].product_point)
        .appendTo(tr);

        tr.appendTo(tbody);
    }
}