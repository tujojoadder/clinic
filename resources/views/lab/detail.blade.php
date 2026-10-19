@php
$results=$record->results->keyBy('parameter_name');
$editable=in_array(auth()->user()->role,['admin','lab']) && !in_array($record->status,['approved','cancelled']);
$parameters=$record->status==='approved' ? $record->results->map(fn($r)=>(object)['id'=>$r->id,'name'=>$r->parameter_name,'unit'=>$r->unit,'reference_range'=>$r->reference_range]) : $record->test->parameters()->orderBy('sort_order')->get();
@endphp
<section class="panel">
<div class="panelhead">
<h2>LAB-{{ $record->id }} · {{ $record->test->name }}</h2>
<span class="badge">{{ $record->status }}</span>
</div>
<div class="panelbody">
@if($editable)<form method="post" action="{{ route('workflows.action',['lab_orders',$record->id,'lab_results']) }}">@csrf 
@endif
<div class="tablewrap">
<table>
<tr>
<th>প্যারামিটার</th>
<th>ফলাফল</th>
<th>ইউনিট</th>
<th>রেফারেন্স রেঞ্জ</th>
</tr>
@foreach($parameters as $parameter)<tr>
<td>{{ $parameter->name }}</td>
<td>
@if($editable)<input name="results[{{ $parameter->id }}]" value="{{ old('results.'.$parameter->id, $results->get($parameter->name)?->result_value) }}">@else{{ $results->get($parameter->name)?->result_value ?? '—' }}
@endif
</td>
<td>{{ $results->get($parameter->name)?->unit ?? $parameter->unit }}</td>
<td>{{ $results->get($parameter->name)?->reference_range ?? $parameter->reference_range }}</td>
</tr>
@endforeach
</table>
</div>
@if($editable)
@include('components.field',['name'=>'report_notes','field'=>['রিপোর্ট নোট','textarea',false],'value'=>$record->report_notes])
<div class="actions">
<button class="btn primary">ফলাফল সংরক্ষণ</button>
</div>
</form>
@else<p>{{ $record->report_notes }}</p>
@endif
@if($record->status==='approved')<p>অনুমোদিত: {{ $record->approved_at }} · {{ \App\Models\User::find($record->approved_by)?->name }}</p>
<button class="btn noprint" data-print>রিপোর্ট প্রিন্ট</button>
@endif
</div>
</section>
<div class="quick noprint">
@if(in_array(auth()->user()->role,['admin','lab']))
@php $next=['ordered'=>'collected','collected'=>'processing','processing'=>'review'][$record->status]??null; @endphp
@if($next)<form method="post" action="{{ route('workflows.action',['lab_orders',$record->id,'lab_status']) }}">@csrf<input type="hidden" name="status" value="{{ $next }}">
<button class="btn">পরবর্তী ধাপ: {{ $next }}</button>
</form>
@endif
@endif
@if($record->status==='review' && auth()->user()->role==='admin')<form method="post" action="{{ route('workflows.action',['lab_orders',$record->id,'approve_lab']) }}">@csrf<button class="btn primary">অনুমোদন</button>
</form>
@endif
@if($record->status==='approved' && auth()->user()->role==='admin')<form method="post" action="{{ route('workflows.action',['lab_orders',$record->id,'reopen_lab']) }}">@csrf<input name="reason" required placeholder="সংশোধনের কারণ">
<button class="btn">সংশোধনের জন্য খুলুন</button>
</form>
@endif
@if(!in_array($record->status,['approved','cancelled']) && \App\Support\Registry::can('lab_orders',true))<form method="post" action="{{ route('workflows.action',['lab_orders',$record->id,'cancel_lab']) }}" data-confirm="টেস্ট বাতিল?">@csrf<button class="btn">টেস্ট বাতিল</button>
</form>
@endif
</div>
