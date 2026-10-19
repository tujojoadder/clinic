<section class="panel">
<div class="panelhead">
<h2>বেডের ইতিহাস</h2>
<span class="badge">{{ $record->status }}</span>
</div>
<div class="tablewrap">
<table>
<tr>
<th>বেড</th>
<th>শুরু</th>
<th>শেষ</th>
<th>দৈনিক ভাড়া</th>
</tr>
@foreach($record->stays()->with('bed')->get() as $stay)<tr>
<td>{{ $stay->bed->name }}</td>
<td>{{ $stay->started_at }}</td>
<td>{{ $stay->ended_at ?? 'চলমান' }}</td>
<td>{{ \App\Support\Format::money($stay->daily_rate) }}</td>
</tr>
@endforeach
</table>
</div>
</section>
@if(\App\Support\Registry::can('invoices'))<a class="btn primary" href="{{ route('entities.show',['invoices',$record->invoice->id]) }}">ভর্তি বিল</a>
@endif
@if($record->status === 'admitted' && \App\Support\Registry::can('admissions',true))
<section class="panel noprint">
<div class="panelhead">
<h2>বেড পরিবর্তন</h2>
</div>
<div class="panelbody">
<form method="post" action="{{ route('workflows.action',['admissions',$record->id,'transfer']) }}">@csrf<div class="formgrid">
@include('components.field',['name'=>'bed_id','field'=>['নতুন বেড','fk',true,'beds'],'value'=>null])
@include('components.field',['name'=>'at','field'=>['পরিবর্তনের সময়','datetime-local',true],'value'=>null])
</div>
<div class="actions">
<button class="btn primary">পরিবর্তন</button>
</div>
</form>
</div>
</section>
<form method="post" action="{{ route('workflows.action',['admissions',$record->id,'cancel_admission']) }}" data-confirm="ভর্তি বাতিল করবেন?">@csrf<button class="btn">ভুল ভর্তি বাতিল</button>
</form>
@endif
@if($record->status === 'admitted' && \App\Support\Registry::can('discharge',true)) 
@include('discharge.form') 
@endif
@if($record->status === 'discharged')
<section class="panel">
<div class="panelhead">
<h2>ডিসচার্জ সামারি</h2>
<button class="btn noprint" data-print>প্রিন্ট</button>
</div>
<div class="panelbody">
<p>ছাড়পত্র: {{ $record->discharged_at }}</p>
<h2>চিকিৎসার সারসংক্ষেপ</h2>
<p>{{ $record->treatment_summary }}</p>
<h2>পরবর্তী নির্দেশনা</h2>
<p>{{ $record->followup_notes }}</p>
</div>
</section>
@endif
