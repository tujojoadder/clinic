@foreach(['visits'=>'ভিজিট','admissions'=>'ভর্তি','lab_orders'=>'টেস্ট','invoices'=>'বিল'] as $related => $label)
    
@if(\App\Support\Registry::can($related))
        <section class="panel">
<div class="panelhead">
<h2>{{ $label }} ইতিহাস</h2>
</div>
<div class="panelbody">
            
@foreach(\App\Support\Registry::model($related)->where('patient_id', $record->id)->latest('id')->get() as $item)<a class="btn" href="{{ route('entities.show', [$related, $item->id]) }}">{{ $label }} #{{ $item->id }}</a>
@endforeach
        </div>
</section>
    
@endif
@endforeach
