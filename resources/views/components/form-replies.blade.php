@props([
    /** @var \Illuminate\Support\Collection */
    'repliesGrouped',
    /** @var \App\Models\Form */
    'form'
])

<div {{ $attributes->class(['space-y-10']) }}>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    @foreach($repliesGrouped as $key => $values)
        @if($form->formFields->where('id', $key)->first()['type'] == 'text')
            <div class="flex flex-col">
                <div class="pb-1 border-b border-gray-300 flex items-center justify-between">
                    <span class="font-semibold">{{ $form->formFields->where('id', $key)->first()['question'] }}</span>
                    <span class="text-sm">{{ sizeof($values->whereNotNull('text_value')) }} @choice('réponse|réponses', sizeof($values->whereNotNull('text_value')))</span>
                </div>
                <div class="mt-3 space-y-2">
                    @foreach($values as $reply)
                        @continue($reply->text_value == null)
                        <div class="block flex items-start space-x-2 w-full text-left px-3 py-2 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer tippy" data-tippy-content="<center>Réponse ajoutée @datetime($reply->created_at).</center>">
                            @if($reply->is_public)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                     class="w-4 h-4 mt-1 text-gray-500 flex-shrink-0 tippy"
                                     data-tippy-content="<center>Cette réponse est publique.</center>">
                                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-wrap">{{ $reply->text_value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($form->formFields->where('id', $key)->first()['type'] == 'stars')
            @php
                $values = $values->whereNotNull('numeric_value');
                $average = round($values->avg('numeric_value'), 1);
            @endphp
            <div class="flex flex-col">
                <div class="pb-1 border-b border-gray-300 flex items-center justify-between">
                    <span class="font-semibold">{{ $form->formFields->where('id', $key)->first()['question'] }}</span>
                    <span class="text-sm">{{ sizeof($values->whereNotNull('numeric_value')) }} @choice('réponse|réponses', sizeof($values->whereNotNull('numeric_value')))</span>
                </div>
                <div class="mt-3 space-y-2">
                    <div class="grid grid-cols-1 md:grid-cols-10">
                        <div class="md:col-span-4">
                            <div class="mt-3 flex items-center">
                                <div>
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 h-5 w-5 @if($average > 1) text-yellow-400 @else text-gray-300 @endif"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        <svg class="flex-shrink-0 h-5 w-5 @if($average > 2) text-yellow-400 @else text-gray-300 @endif"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        <svg class="flex-shrink-0 h-5 w-5 @if($average > 3) text-yellow-400 @else text-gray-300 @endif"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        <svg class="flex-shrink-0 h-5 w-5 @if($average > 4) text-yellow-400 @else text-gray-300 @endif"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        <svg class="flex-shrink-0 h-5 w-5 @if($average >= 4.5) text-yellow-400 @else text-gray-300 @endif"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-2 pt-1 text-sm text-gray-900">{{ $average }} en moyenne sur 5 étoiles</p>
                            </div>

                            <div class="mt-6">
                                <dl class="space-y-3">
                                    <div class="flex items-center text-sm">
                                        <dt class="flex flex-1 items-center">
                                            <p class="w-3 font-medium text-gray-900">5<span class="sr-only"> star
                                                    reviews</span></p>
                                            <div aria-hidden="true" class="ml-1 flex flex-1 items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-yellow-400"
                                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                     fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                                <div class="relative ml-3 flex-1">
                                                    <div class="h-3 rounded-full border border-gray-200 bg-gray-100"></div>
                                                    @if($values->where('numeric_value', 5)->count() > 0)
                                                        <div style="width: {{ ($values->where('numeric_value', 5)->count() / $values->count()) * 100 }}%"
                                                             class="absolute inset-y-0 rounded-full border border-yellow-400 bg-yellow-400"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dt>
                                        <dd class="ml-3 w-10 text-right text-sm tabular-nums text-gray-900">{{ round(($values->where('numeric_value', 5)->count() / $values->count()) * 100, 1)  }}%</dd>
                                    </div>

                                    <div class="flex items-center text-sm">
                                        <dt class="flex flex-1 items-center">
                                            <p class="w-3 font-medium text-gray-900">4<span class="sr-only"> star
                                                    reviews</span></p>
                                            <div aria-hidden="true" class="ml-1 flex flex-1 items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-yellow-400"
                                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                     fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                          clip-rule="evenodd"></path>
                                                </svg>

                                                <div class="relative ml-3 flex-1">
                                                    <div class="h-3 rounded-full border border-gray-200 bg-gray-100"></div>
                                                    @if($values->where('numeric_value', 4)->count() > 0)
                                                        <div style="width: {{ ($values->where('numeric_value', 4)->count() / $values->count()) * 100 }}%"
                                                             class="absolute inset-y-0 rounded-full border border-yellow-400 bg-yellow-400"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dt>
                                        <dd class="ml-3 w-10 text-right text-sm tabular-nums text-gray-900">{{ round(($values->where('numeric_value', 4)->count() / $values->count()) * 100, 1)  }}%</dd>
                                    </div>

                                    <div class="flex items-center text-sm">
                                        <dt class="flex flex-1 items-center">
                                            <p class="w-3 font-medium text-gray-900">3<span class="sr-only"> star
                                                    reviews</span></p>
                                            <div aria-hidden="true" class="ml-1 flex flex-1 items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-yellow-400"
                                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                     fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                          clip-rule="evenodd"></path>
                                                </svg>

                                                <div class="relative ml-3 flex-1">
                                                    <div class="h-3 rounded-full border border-gray-200 bg-gray-100"></div>
                                                    @if($values->where('numeric_value', 3)->count() > 0)
                                                        <div style="width: {{ ($values->where('numeric_value', 3)->count() / $values->count()) * 100 }}%"
                                                             class="absolute inset-y-0 rounded-full border border-yellow-400 bg-yellow-400"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dt>
                                        <dd class="ml-3 w-10 text-right text-sm tabular-nums text-gray-900">{{ round(($values->where('numeric_value', 3)->count() / $values->count()) * 100, 1)  }}%</dd>
                                    </div>

                                    <div class="flex items-center text-sm">
                                        <dt class="flex flex-1 items-center">
                                            <p class="w-3 font-medium text-gray-900">2<span class="sr-only"> star
                                                    reviews</span></p>
                                            <div aria-hidden="true" class="ml-1 flex flex-1 items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-yellow-400"
                                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                     fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                          clip-rule="evenodd"></path>
                                                </svg>

                                                <div class="relative ml-3 flex-1">
                                                    <div class="h-3 rounded-full border border-gray-200 bg-gray-100"></div>
                                                    @if($values->where('numeric_value', 2)->count() > 0)
                                                        <div style="width: {{ ($values->where('numeric_value', 2)->count() / $values->count()) * 100 }}%"
                                                             class="absolute inset-y-0 rounded-full border border-yellow-400 bg-yellow-400"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dt>
                                        <dd class="ml-3 w-10 text-right text-sm tabular-nums text-gray-900">{{ round(($values->where('numeric_value', 2)->count() / $values->count()) * 100, 1)  }}%</dd>
                                    </div>

                                    <div class="flex items-center text-sm">
                                        <dt class="flex flex-1 items-center">
                                            <p class="w-3 font-medium text-gray-900">1<span class="sr-only"> star
                                                    reviews</span></p>
                                            <div aria-hidden="true" class="ml-1 flex flex-1 items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-yellow-400"
                                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                     fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                          clip-rule="evenodd"></path>
                                                </svg>

                                                <div class="relative ml-3 flex-1">
                                                    <div class="h-3 rounded-full border border-gray-200 bg-gray-100"></div>
                                                    @if($values->where('numeric_value', 1)->count() > 0)
                                                        <div style="width: {{ ($values->where('numeric_value', 1)->count() / $values->count()) * 100 }}%"
                                                             class="absolute inset-y-0 rounded-full border border-yellow-400 bg-yellow-400"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dt>
                                        <dd class="ml-3 w-10 text-right text-sm tabular-nums text-gray-900">{{ round(($values->where('numeric_value', 1)->count() / $values->count()) * 100, 1)  }}%</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array($form->formFields->where('id', $key)->first()['type'], ['unique_choice', 'multiple_choices']))
            @php
                $formField = $form->formFields->where('id', $key)->first();
                $choices = $formField['choices'];
                $choicesKeyValue = [];
                array_map(function ($choice) use (&$choicesKeyValue){
                    $choicesKeyValue[$choice['id']]=$choice['value'];
                }, $choices);
                $groupedCount = array_count_values($values->pluck('options')->collapse()->toArray());
            @endphp
            <div class="flex flex-col">
                <div class="pb-1 border-b border-gray-300 flex items-center justify-between">
                    <span class="font-semibold">{{ $formField['question'] }}</span>
                    <span class="text-sm">{{ sizeof($values) }} @choice('réponse|réponses', sizeof($values))</span>
                </div>
                <div class="mt-3 space-y-2">
                    <div id="chart_{{ $formField->id }}" style=" height: 300px;"></div>
                </div>
            </div>
            <script type="text/javascript">
                google.charts.load("current", {packages:["corechart"]});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {
                    var chart = new google.visualization.PieChart(document.getElementById('chart_{{ $formField->id }}'));
                    chart.draw(
                        google.visualization.arrayToDataTable([
                            ['Réponse', 'Nombre de réponses'],
                            @foreach($groupedCount as $key => $value)
                                ["{{ $choicesKeyValue[$key] }}", {{ $value }}],
                            @endforeach
                    ]), {
                        pieHole: 0.4,
                    });
                }
            </script>
        @endif
    @endforeach
</div>
