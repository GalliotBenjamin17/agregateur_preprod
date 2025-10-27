<x-pages.settings.details-base
    :page-name="$emailTemplate->name"
>
    <x-slot:actions>
        <x-button onclick="submitForm()" type="success" icon>
            <span>Sauvegarder</span>
            <x-icon.chevron_droite class="h-5 w-5" />
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div>
            <div id="editor" style="height: calc(100vh - 56px)"></div>

            <div id="basePage" class="loader-overlay">
                <img class="mx-auto h-12" src="{{ asset('img/loader.svg') }}" />
                <h2 class="text-center text-white mt-5 text-xl font-semibold">Génération de l'image</h2>
                <p class="w-1/3 text-center text-white">Cela peut prendre quelques secondes, ne fermez pas cette page.</p>
            </div>

        </div>

        <form id="submitHtmlContent" style="display: none" action="{{ route('settings.emails-templates.update', ['emailTemplate' => $emailTemplate->slug]) }}" method="POST">
            @csrf
            <input style="display: none" type="text" id="html_content" name="html_content" value=""/>
            <input style="display: none" type="text" id="json_content" name="json_content" value=""/>
        </form>

        <script src="https://editor.unlayer.com/embed.js"></script>
        @push('scripts')
            <script>
                unlayer.init({
                    id: 'editor',
                    projectId: {{ setting('unlayerProjectId') }},
                    displayMode: 'web',
                    locale: 'fr-FR',
                    appearance: {
                        theme: 'light',
                        panels: {
                            tools: {
                                dock: 'left'
                            }
                        }
                    },
                    features: {
                        textEditor: {
                            tables: true
                        },
                        colorPicker: {
                            presets: ['#D9E3F0', '#F47373', '#697689', '#37D67A', '#2CCCE4', '#555555', '#DCE775']
                        },
                        audit: true
                    }
                });

                var design = @js(json_decode($emailTemplate->json_content, true));
                unlayer.loadDesign(design);

                function submitForm() {
                    var baseLoader = document.getElementById('basePage');
                    baseLoader.style.display = 'flex';

                    setTimeout(() => {
                        unlayer.exportHtml(function (data) {
                            var json = data.design; // design json
                            var html = data.html; // final html

                            document.getElementById('html_content').value = html;
                            document.getElementById('json_content').value = JSON.stringify(json);
                            document.forms["submitHtmlContent"].submit();
                        })
                    }, 5000);
                }
            </script>
        @endpush
    </x-slot>
</x-pages.settings.details-base>
