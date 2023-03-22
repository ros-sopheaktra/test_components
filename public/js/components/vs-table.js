/**
 * Set table component unique id based on component 
 * structure in order to prevent future duplicate id by accident.
 * @param [string] tableId
 * @param [string] options [ thead, tbody ]
 * @return [string] uniqueTableId
 */
function set_unique_table_id( tableId, options ){
    const attributeOptions = {
        thead: 'thead-component-',
        tbody: 'tbody-component-',
    };

    const uniqueTableId = `#${attributeOptions[options]}${tableId}-id`;

    return uniqueTableId;
}

/**
 * Generate table header content and bind into table heading 
 * based on given unique table element id and table header 
 * content in the form of object passed as parameters.
 * @param [string] theadId
 * @param [Object] headerContents
 * 
 * @return void
 */
function generate_table_thead( theadId, headerContents ){
    const uniqueTheadId = $(this.set_unique_table_id( theadId, 'thead' ));
    
    const tr_thead = $('<tr></tr>');
    for( index in headerContents ){
        $('<th></th>')
            .text( capitalizeText( headerContents[index].label ) )
        .appendTo(tr_thead);
    }

    tr_thead.appendTo(uniqueTheadId);
}

/**
 * Generate table body content and bind into table body 
 * based on given unique table element id and table body 
 * content in the form of object passed as parameters.
 * @param [string] tbodyId
 * @param [Object] bodyContents
 * 
 * @return void
 */
function generate_tbody_table( tbodyId, bodyContents ){
    const uniqueTbodyId = this.set_unique_table_id( tbodyId, 'tbody' );

    const tbody = $(uniqueTbodyId);
    for( index in bodyContents ){
        const rowData = bodyContents[index],
              tr = $('<tr></tr>');
        
        // always append first for table number
        $('<td></td>')
            .text( parseInt(index)+1 )
        .appendTo(tr);

        // generate table contents
        for( index_2 in rowData ){
            let td = $("<td></td>")
                .text(rowData[index_2])
            .appendTo(tr);
        }

        tr.appendTo(tbody);
    }
}
