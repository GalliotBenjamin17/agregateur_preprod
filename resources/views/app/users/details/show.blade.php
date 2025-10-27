<x-pages.users.details-base
    :user="$user"
>
    <x-slot name="cardContent">

        {{-- <x-layouts.card
            name="Organisations ({{ sizeof($user->organizations) }})"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::organizationsIcon(size: 'sm') !!}
            </x-slot:icon>

            <x-slot:content>
                <div class="p-2.5 sm:p-[1rem] grid grid-cols-1 md:grid-cols-4 gap-x-16 gap-y-4">
                    @forelse($user->organizations as $organization)
                        <x-layouts.card-tile
                            link="{{ route('organizations.show', ['organization' => $organization->slug]) }}"
                            :title="$organization->name"
                        >
                            <x-slot:tiles>
                                <x-layouts.card-tile-line
                                    title="Type"
                                    :content="$organization->organizationType?->name ?? '-'"
                                />
                                @if($organization->pivot->organization_type_link_id)
                                    <x-layouts.card-tile-line
                                        title="Type"
                                        :content="$organization->organizationTypeLinks->where('id', $organization->pivot->organization_type_link_id)->first()?->name ?? 'Inconnu'"
                                    />
                                @endif
                            </x-slot:tiles>
                        </x-layouts.card-tile>
                    @empty
                        <x-empty-model
                            content="Aucune entité sur cette organisation"
                            :model="new \App\Models\Organization()"
                            class="col-span-4 "
                            height="48"
                        />
                    @endforelse
                </div>
            </x-slot:content>
        </x-layouts.card> --}}

        <x-files-component :model="$user" />

        {{-- @push('scripts')
            <script>
               var organizationInst = mobiscroll.select('#select-organizations', {
                    display: 'anchored',
                    filter: true,
                    multiple: true,
                    inputElement: document.getElementById('input-organizations'),
                    onChange: function (event, inst) {
                        document.getElementById('organizations_ids').value = event['value'];
                    },
                });

                function remoteFilteringOrganizations(filterText) {
                    mobiscroll.util.http.getJson('{{ route('organizations.api.search') }}?search=' + encodeURIComponent(filterText), function (data) {
                        var item;
                        var organizations = [];

                        for (var i = 0; i < data.length; i++) {
                            item = data[i];
                            organizations.push({ text: item.name, value: item.id })
                        }

                        organizationInst.setOptions({ data: organizations });
                        organizationInst.setVal(@js($user->organizations->pluck('id')->toArray()))
                    }, 'json');
                }
                remoteFilteringOrganizations('');
            </script>
        @endpush --}}
    </x-slot>

    {{-- <x-slot name="colContent">
        <div class="space-y-2 sm:space-y-3">
            <x-activities-model :model="$user" />
        </div>
    </x-slot> --}}
</x-pages.users.details-base>
