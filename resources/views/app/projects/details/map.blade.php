<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        @if($project->lat and $project->lng)
            <x-information-alert
                type="info"
                title="Modification des coordonnées GPS."
                message="Vous pouvez modifier en déplaçant le pin sur la carte. Cela ne modifiera pas l'adresse du projet, seulement la latitude et longitude."
            />
            <div id='map' class="h-[800px] rounded-md w-full"></div>
        @else
            <x-empty-model
                content="Vous devez définir une adresse dans le formulaire du projet pour afficher la carte"
                :model="new \App\Models\Project()"
                class="col-span-4"
                height="48"
            />
        @endif

        @push('styles')
            <link href='{{ asset('css/mapbox-gl-export.css') }}' rel='stylesheet' />
        @endpush

        @if($project->lat and $project->lng)
            @push('scripts')

                <link href='{{ asset('css/mapbox-gl.css') }}' rel='stylesheet' />
                <script src='{{ asset('js/mapbox-gl.js') }}'></script>
                <script src="{{ asset('js/mapbox-gl-export.js') }}"></script>

                <script>
                    mapboxgl.accessToken = 'pk.eyJ1IjoiZWxiYXlsb3QiLCJhIjoiY2syNHg0eWlsMjZ6YTNjbXZvbjZvaWZjbCJ9.n5ZLe2_JA9wA5JonWQP6oA';

                    var map = new mapboxgl.Map({
                        container: 'map',
                        style: 'mapbox://styles/mapbox/outdoors-v12',
                        center: [{{ $project->lng }}, {{ $project->lat }}],
                        zoom: 13
                    });

                    map.on('load', function () {
                        const marker = new mapboxgl.Marker({
                            draggable: true
                        })
                            .setLngLat([{{ $project->lng }}, {{ $project->lat }}])
                            .addTo(map);

                        map.resize();
                        marker.on('dragend', function () {
                            const lngLat = marker.getLngLat();

                            const url = '{{ route('projects.update.coordinates', ['project' => $project]) }}';
                            const data = {
                                lat: lngLat.lat,
                                lng: lngLat.lng,
                            };

                            const response = fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify(data),
                            }).then(function () {
                                new FilamentNotification()
                                    .title('Coordonnées du projet mises à jour.')
                                    .body("Attention : les coordonnées GPS du projet peuvent être différentes de l'adresse affichée du projet.")
                                    .success()
                                    .send()
                            }).catch(function () {
                                new FilamentNotification()
                                    .title('Impossible de mettre à jour les coordonnées.')
                                    .danger()
                                    .send()
                            });

                        });

                        map.addControl(new mapboxgl.FullscreenControl());

                        map.addControl(new MapboxExportControl({
                            PageSize: Size.A4,
                            PageOrientation: PageOrientation.Portrait,
                            Format: Format.PNG,
                            DPI: DPI[300],
                            Crosshair: true,
                            PrintableArea: true,
                        }), 'top-right');

                    });
                </script>
            @endpush
        @endif
    </x-slot>
</x-pages.projects.details-base>
