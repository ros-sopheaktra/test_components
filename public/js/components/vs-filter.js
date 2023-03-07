/**
 * Activate ajax call to backend through api and filter module records 
 * based on given custom parameters available in the listing bellow, 
 * then function then get the response data and replace it to the hidden 
 * input in the form of encoded json format come with the component it self, 
 * access by perform element id selection in structure of 
 * '#module-filter-yourCustomId-response' to get response data.
 * 
 * @param [Api_Url] ApiUrl
 * @param [Api_Token] ApiToken
 * @param [String] Method
 * @param [Key_Value_Pair_Object] CustomRequests (default []) 
 * 
 * @return void
 */
function filter_component_activateModuleFilter( ApiUrl, ApiToken, Method, CustomRequests = [] ){
    $('.module-input-filter-box').off().on('keypress', function(event){
        // listing only enter key code (13)
        if( event.which == 13 ){
            const State        = $(this),
                    Id         = State.attr('id'),
                    Value      = State.val().length != 0 ? State.val() : 'default_queries',
                    FilterBy   = State.attr('filterBy'),
                    EntityName = State.attr('moduleEntityName');

            const MainUrl = `${ApiUrl}/${Value}/filter`;
            $.ajax({
                url: MainUrl,
                type: Method,
                data: { 
                    _token: ApiToken,
                    entityName: EntityName,
                    filterBy: FilterBy,
                    requests: CustomRequests,
                },
                dataType: 'JSON',
                error: function(response){
                    alert(response.message);
                    console.log(response);
                },
                success: function(response){
                    _updateResponseDataToInputHidden( Id, response );
                },
            });
        }
    });

    function _updateResponseDataToInputHidden( InputFilterElementId, filterResults ){
        const   Data          = filterResults.data,
                Status        = filterResults.status,
                Message       = filterResults.message,
                DetailMessage = filterResults.detailMessage;

        // bind the response data to element
        const input_filterResponseHiddenElement = $(`#${InputFilterElementId}-response`);
        input_filterResponseHiddenElement.val( JSON.stringify(Data) );
        input_filterResponseHiddenElement.attr('filterStatus', Status);
        input_filterResponseHiddenElement.attr('filterMessage', Message);
        input_filterResponseHiddenElement.attr('filterDetailMessage', DetailMessage);

        // this trigger change need to make the onChange event work, 
        // if you find any other way to make it work please modify it. :)
        input_filterResponseHiddenElement.trigger('change');
    }
}

/**
 * Form amd return filter hidden element id name based on given parameter.
 * @param [String] elementId
 * @return [String] elementIdName
 */
function filter_component_getHiddenInputResponseElement( elementId ){
    return `#module-filter-${elementId}-response`;
}
