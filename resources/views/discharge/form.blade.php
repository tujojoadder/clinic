<section class="panel noprint">
<div class="panelhead">
<h2>ডিসচার্জ সম্পন্ন করুন</h2>
</div>
<div class="panelbody">
<form method="post" action="{{ route('workflows.action',['admissions',$record->id,'discharge']) }}">@csrf<div class="formgrid">
@include('components.field',['name'=>'discharged_at','field'=>['ছাড়পত্রের সময়','datetime-local',true],'value'=>null])
@include('components.field',['name'=>'discharge_type','field'=>['ধরন','select',true,['regular'=>'নিয়মিত','transfer'=>'স্থানান্তর','own_risk'=>'নিজ দায়িত্বে']],'value'=>'regular'])
@include('components.field',['name'=>'treatment_summary','field'=>['চিকিৎসার সারসংক্ষেপ','textarea',true],'value'=>''])
@include('components.field',['name'=>'followup_notes','field'=>['পরবর্তী নির্দেশনা','textarea',false],'value'=>''])
@if(auth()->user()->role==='admin')<div>
<label>
<input class="switch" type="checkbox" name="allow_due" value="1"> বকেয়াসহ অনুমোদন</label>
</div>
@endif
</div>
<div class="actions">
<button class="btn primary">ডিসচার্জ করুন</button>
</div>
</form>
</div>
</section>
