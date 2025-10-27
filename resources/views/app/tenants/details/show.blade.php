<x-pages.tenants.details-base
    :tenant="$tenant"
>
    <x-slot name="cardContent">


        @push('scripts')
            <script defer>
                let selectResponsiveOptions = {
                    xsmall: {
                        display: 'bottom',
                        touchUi: true
                    },
                    small: {
                        display: 'anchored',
                        touchUi: true
                    },
                    custom: {
                        breakpoint: 800,
                        display: 'anchored',
                        touchUi: false
                    }
                };

                mobiscroll.setOptions({
                    locale: mobiscroll.localeFr,
                    theme: 'ios',
                    themeVariant: 'light',
                });

                var usersInst = mobiscroll.select('#select-users', {
                    display: 'anchored',
                    filter: true,
                    multiple: true,
                    inputElement: document.getElementById('input-users'),
                    onChange: function (event, inst) {
                        document.getElementById('users_ids').value = event['value'];
                    },
                });

                function remoteFiltering(filterText) {
                    mobiscroll.util.http.getJson('{{ route('users.api.search') }}?search=' + encodeURIComponent(filterText), function (data) {
                        var item;
                        var users = [];

                        for (var i = 0; i < data.length; i++) {
                            item = data[i];

                            users.push({ text: item.first_name + ' ' + item.last_name, value: item.id })
                        }

                        usersInst.setOptions({ data: users });
                        usersInst.setVal(@js($tenant->users->pluck('id')->toArray()))
                    }, 'json');
                }

                remoteFiltering('');
            </script>
        @endpush

        <x-files-component :model="$tenant" />

    </x-slot>

    <x-slot name="colContent">
        <div class="col-span-4 space-y-2 sm:space-y-3">
            <x-comments-card :model="$tenant" />
            <x-activities-model :model="$tenant" />
        </div>
    </x-slot>
</x-pages.tenants.details-base>
