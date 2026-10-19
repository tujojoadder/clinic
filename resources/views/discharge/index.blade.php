@extends('layouts.app',['title'=>'ডিসচার্জ','module'=>'discharge'])
@section('content')<section class="panel">
<div class="panelhead">
<h2>রোগী নির্বাচন</h2>
</div>
<div class="panelbody">
@foreach($admissions as $admission)<a class="btn" href="{{ route('entities.show',['admissions',$admission->id]) }}">{{ $admission->patient->name }} · ADM-{{ $admission->id }}</a>
@endforeach</div>
</section>
@endsection
