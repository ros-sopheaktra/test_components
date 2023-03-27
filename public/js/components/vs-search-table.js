/**
 * Filter fron end in the table
 * @param {*} inputId 
 * @param {*} tableId 
 */
function filterInTable(inputId, tableId){
    $(`#${inputId}`).on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(`#${tableId} tbody tr`).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
}