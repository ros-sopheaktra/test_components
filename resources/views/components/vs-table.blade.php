<div class="{{ isset($component_wrapper_class) ? $component_wrapper_class : '' }}">
    @if ( isset($has_filter) && $has_filter )
        <div class="{{ isset($filter_wrapper_class) ? $filter_wrapper_class : '' }}">
            <input 
                type        = "text" 
                id          = "{{ isset($table_filter_id) ? $table_filter_id : '' }}" 
                class       = "{{ isset($table_filter_class) ? $table_filter_class : '' }}" 
                placeholder = "type to search..."
            >
        </div>
    @endif

    <div class="{{ isset($component_table_wrapper_class) ? $component_table_wrapper_class : '' }}">
        <table 
            id    = "{{ isset($table_id) ? $table_id : '' }}" 
            class = "table {{ isset($table_wrapper_class) ? $table_wrapper_class : '' }}"
        >
            <thead 
                id    = "thead-component-{{$thead_id}}-id" 
                class = "{{ isset($thead_wrapper_class) ? $thead_wrapper_class : '' }}"
            ></thead>
            <tbody id="tbody-component-{{$tbody_id}}-id"></tbody>
        </table>
    </div>
</div>
