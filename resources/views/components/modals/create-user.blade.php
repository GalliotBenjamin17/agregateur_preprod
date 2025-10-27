<x-modal id="{{ $id }}">
    <x-modal.header>
        <div>
            <div class="font-semibold text-gray-700">
                {{ $title }}
            </div>
        </div>
        <x-modal.close-button/>
    </x-modal.header>

    <form autocomplete="off" action="{{ route('users.store') }}" method="POST">
        @csrf
        <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">

            <div>
                <x-label value="Prénom : " required />
                <x-input type="text" name="first_name" :value="old('first_name')" placeholder="Eliott" required />
                <x-error-message :errors="$errors" key="first_name" />
            </div>

            <div>
                <x-label value="Nom : " required />
                <x-input type="text" name="last_name" :value="old('last_name')" placeholder="Baylot" required />
                <x-error-message :errors="$errors" key="last_name" />
            </div>

            <div class="sm:col-span-2">
                <x-label value="Email : " required />
                <x-input type="text" name="email" :value="old('email')" placeholder="eliott.baylot@ooh-insight.fr" required />
                <x-error-message :errors="$errors" key="email" />
            </div>

            @if($organization)
                <input hidden name="organization_id" value="{{ $organization->id }}" />
            @elseif($partner)
                <input hidden name="partner_id" value="{{ $partner->id }}" />
            @elseif($role)
                <input hidden name="role" value="{{ $role }}" />
            @else
                <div class="sm:col-span-2">
                    <x-label value="Rôle : " />
                    <x-select name="role" required>
                        @foreach(\App\Enums\Roles::toSelect() as $key => $value)
                            <option @selected(old('role') == $key) value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </x-select>
                    <x-error-message :errors="$errors" key="role" />
                </div>
            @endif

            @if($organization)
            @elseif($partner)
            @elseif($role == \App\Enums\Roles::Admin)
            @elseif($currentTenant)
                <input hidden name="tenant_id" value="{{ $currentTenant->id }}" />
            @else
                <div class="sm:col-span-2">
                    <x-label value="Antenne locale : " />
                    <x-select name="tenant_id" required>
                        @foreach($tenants as $tenant)
                            <option @selected(old('tenant_id') == $tenant->id) value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </x-select>
                    <x-error-message :errors="$errors" key="tenant_id" />
                </div>
            @endif
        </x-modal.body>

        <x-modal.footer>
            <x-button data-bs-dismiss="modal">
                Fermer
            </x-button>
            <x-button submit type="success">
                Ajouter
            </x-button>
        </x-modal.footer>
    </form>
</x-modal>
