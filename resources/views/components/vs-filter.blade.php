<div class="form-row justify-content-end">
    <div class="form-group input-group-sm d-flex align-items-center">
        <p class="my-0 mr-3">Search</p>
        <input 
            type="text" 
            id="module-filter-{{$unique_element_id}}"
            class="module-input-filter-box form-control input-sm" 
            value=""
            moduleEntityName="{{$module_entity}}"
            filterBy="{{$filter_by}}"
        >
    </div>
    <input 
        type="hidden" 
        id="module-filter-{{$unique_element_id}}-response" 
        value="" 
        filterStatus="" 
        filterMessage="" 
        filterDetailMessage="" 
    >
</div>
