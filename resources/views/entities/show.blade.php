@extends('layouts.app', ['title' => $definition['title'].' · #'.$record->id])
@section('content')
<section class="panel">
<div class="panelhead">
<h2>{{ $definition['title'] }} · #{{ $record->id }}</h2>
</div>
<div class="panelbody">
<div class="detailgrid">
        
@foreach($definition['fields'] as $name => $field)
            
@if($name !== 'password')<div>
<label>{{ $field[0] }}</label>
<strong>{{ \App\Support\Format::value($record->$name, $field) }}</strong>
</div>
@endif
        
@endforeach
    </div>
<div class="actions noprint">
<a class="btn" href="{{ route('entities.index', $module) }}">তালিকা</a>
        
@if(\App\Support\Registry::can($module, true))
            
@if($module !== 'lab_orders')<a class="btn" href="{{ route('entities.edit', [$module, $record->id]) }}">সম্পাদনা</a>
@endif
            
@if(!in_array($module, ['admissions','visits','lab_orders','invoices']))
                <form method="post" action="{{ route('entities.destroy', [$module, $record->id]) }}" data-confirm="রেকর্ড মুছবেন?">@csrf @method('DELETE')<button class="btn danger">ডিলেট</button>
</form>
            
@endif
        
@endif
    </div>
</div>
</section>
@if($module === 'patients') 
@include('patients.history') 
@endif
@if($module === 'visits') 
@include('visits.actions') 
@endif
@if($module === 'admissions') 
@include('admissions.detail') 
@endif
@if($module === 'invoices') 
@include('billing.detail') 
@endif
@if($module === 'lab_orders') 
@include('lab.detail') 
@endif
@endsection
