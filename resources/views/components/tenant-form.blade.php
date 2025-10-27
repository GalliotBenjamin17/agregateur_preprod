@if(userHasTenant())
    <input hidden name="tenant_id" value="{{ userTenantId() }}" />
@else
    <div class="sm:col-span-2">
        <x-label value="Instance locale : " required/>
        <x-select type="text" name="tenant_id">
            @foreach($tenants as $tenant)
                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
            @endforeach
        </x-select>
    </div>
@endif
