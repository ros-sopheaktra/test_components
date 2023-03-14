<div class="{{isset($main_class_wrapper) ? $main_class_wrapper : ''}}">
    @if ($is_active_search == true || $is_active_search == 'true')
    <div class="{{isset($div_search_wrapper) ? $div_search_wrapper : ''}}">
        <input type="text" placeholder="search..." id="{{isset($input_id_search) ? $input_id_search : ''}}" class="{{isset($input_search_class) ? $input_search_class : ''}}">
    </div>
    @endif
    <div class="{{isset($component_table_wrapper_class) ? $component_table_wrapper_class : ''}}">
        <table id="{{isset($table_id) ? $table_id : ''}}" class="table {{isset($table_wrapper_class) ? $table_wrapper_class : ''}}">
            <thead id="thead-component-{{$thead_unique_element_id}}-id" class="{{isset($thead_wrapper_class) ? $thead_wrapper_class : ''}}">
            
            </thead>
            <tbody id="tbody-component-{{$tbody_unique_element_id}}-id">
                
            </tbody>
        </table>
    </div>
</div>