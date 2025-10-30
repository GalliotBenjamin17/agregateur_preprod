<div class="fi-ta-top-without-corner fi-ta-extra-thin">


    {{ $this->table }}
</div>

@push('styles')
    <style>
        /* Hide only filters trigger; keep Columns toggle visible */
        .fi-ta-header-toolbar .fi-ta-filters-modal,
        .fi-ta-header-toolbar .fi-ta-filters-dropdown { display: none !important; }
        .fi-dd label { cursor: pointer; }
    </style>
@endpush

@push('scripts')
<script>
    window.FI_TABLE_FILTER_OPTIONS = {
        certification: @json(\App\Models\Certification::pluck('name','id')),
        method: @json(\App\Models\MethodForm::pluck('name','id')),
        state: @json(\App\Enums\Models\Projects\ProjectStateEnum::toArray()),
        segmentation: @json(\App\Models\Segmentation::pluck('name','id')),
    };
    (function(){
      const PARAMS_MAP = { certification: 'cf_id', method: 'mf_id', state: 'st', segmentation: 'sg_id' };
      const LABELS = { certification: 'certification', method: 'méthode', state: 'statut', segmentation: 'segmentation' };
      const OPTIONS = window.FI_TABLE_FILTER_OPTIONS || {};
      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-fi-funnel]');
        if(!btn) return;
        e.preventDefault(); e.stopPropagation();
        const key = btn.getAttribute('data-fi-funnel');
        if(!PARAMS_MAP[key]) return;
        // Build dropdown
        let dd = document.createElement('div');
        dd.className = 'fi-dd absolute z-[1000] mt-2 w-64 rounded-md border border-gray-200 bg-white p-2 text-sm shadow-lg';
        dd.style.position = 'absolute';
        dd.style.top = (btn.getBoundingClientRect().bottom + window.scrollY) + 'px';
        dd.style.left = (btn.getBoundingClientRect().left + window.scrollX) + 'px';
        const currentUrl = new URL(window.location.href);
        const param = PARAMS_MAP[key];
        const selectedRaw = currentUrl.searchParams.get(param) || '';
        const selected = selectedRaw ? selectedRaw.split(',') : [];
        const options = OPTIONS[key] || {};
        const items = Object.entries(options).map(function(entry){
          var id = String(entry[0]); var label = entry[1];
          var checked = selected.includes(id) ? ' checked' : '';
          return '<label class="flex items-center gap-2 py-1"><input type="checkbox" value="'+id+'"'+checked+'> <span>'+label+'</span></label>';
        }).join('');
        dd.innerHTML = '<div class="mb-2 font-semibold">Filtrer par '+(LABELS[key]||key)+'</div>'+
          '<div class="max-h-64 overflow-auto pr-1">'+ items +'</div>'+
          '<div class="mt-2 flex gap-2">'+
          '<button type="button" class="px-2 py-1 border rounded bg-blue-600 text-white" data-action="apply">Appliquer</button>'+
          '<button type="button" class="px-2 py-1 border rounded" data-action="clear">Effacer</button>'+
          '</div>';
        document.body.appendChild(dd);
        dd.querySelector('[data-action="apply"]').addEventListener('click', function(){
          const url = new URL(window.location.href);
          const values = Array.from(dd.querySelectorAll('input[type=checkbox]:checked')).map(function(o){return o.value;});
          if(values.length){ url.searchParams.set(param, values.join(',')); } else { url.searchParams.delete(param); }
          url.searchParams.set('page','1');
          window.location.href = url.toString();
        });
        dd.querySelector('[data-action="clear"]').addEventListener('click', function(){
          const url = new URL(window.location.href);
          url.searchParams.delete(param);
          url.searchParams.set('page','1');
          window.location.href = url.toString();
        });
        const close = function(ev){ if(ev && dd.contains(ev.target)) return; dd.remove(); document.removeEventListener('click', close); };
        setTimeout(function(){ document.addEventListener('click', close); }, 0);
      });
    })();
</script>
@endpush
