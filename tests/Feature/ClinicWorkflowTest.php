<?php
namespace Tests\Feature;
use App\Models\{User,Doctor,Patient,Bed,Admission,Invoice,DiagnosticTest,LabOrder,TestParameter};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class ClinicWorkflowTest extends TestCase
{
    use RefreshDatabase;
    private function admin(): User
    {
        return User::create(['name'=>'Test Admin','email'=>'admin@example.test','password'=>Hash::make('LongTestPassword123!'),'role'=>'admin','active'=>1]);
    }
    public function test_doctor_crud_and_role_permissions(): void
    {
        $this->actingAs($this->admin());
        $data=['name'=>'Test Doctor','specialty'=>'Medicine','fee'=>500,'followup_fee'=>300,'active'=>1];
        $this->post('/doctors',$data)->assertRedirect();
        $doctor=Doctor::firstOrFail();
        $this->get('/doctors/'.$doctor->id)->assertOk()->assertSee('Test Doctor');
        $this->put('/doctors/'.$doctor->id,$data+[])->assertRedirect();
        $this->delete('/doctors/'.$doctor->id)->assertRedirect();
        $this->assertDatabaseMissing('doctors',['id'=>$doctor->id]);
        $staff=User::create(['name'=>'Reception','email'=>'reception@example.test','password'=>Hash::make('LongTestPassword123!'),'role'=>'reception','active'=>1]);
        $this->actingAs($staff)->get('/settings')->assertForbidden();
        $this->get('/doctors/create')->assertForbidden();
    }
    public function test_admission_lab_payment_and_discharge(): void
    {
        $this->actingAs($this->admin());
        $patient=Patient::create(['name'=>'Patient','age_years'=>30,'gender'=>'female','phone'=>'01700000001']);
        $doctor=Doctor::create(['name'=>'Doctor','specialty'=>'Medicine','fee'=>500,'followup_fee'=>300,'active'=>1]);
        $bed=Bed::create(['name'=>'Test Cabin','ward'=>'Cabin','type'=>'cabin','daily_rate'=>1500,'state'=>'available']);
        $this->post('/admissions',['patient_id'=>$patient->id,'doctor_id'=>$doctor->id,'admitted_at'=>now()->subHour()->toDateTimeString(),'reason'=>'Test','bed_id'=>$bed->id,'advance'=>2000,'method'=>'cash'])->assertRedirect()->assertSessionHasNoErrors();
        $admission=Admission::firstOrFail();$invoice=$admission->invoice;
        $test=DiagnosticTest::create(['name'=>'CBC','sample_type'=>'Blood','price'=>500,'turnaround_hours'=>4,'active'=>1]);
        $parameter=TestParameter::create(['test_id'=>$test->id,'name'=>'Hemoglobin','unit'=>'g/dL','reference_range'=>'Verified range','sort_order'=>1]);
        $this->post('/lab_orders',['patient_id'=>$patient->id,'admission_id'=>$admission->id,'test_id'=>$test->id,'ordered_at'=>now()->toDateTimeString()])->assertRedirect()->assertSessionHasNoErrors();
        $order=LabOrder::firstOrFail();
        foreach(['collected','processing'] as $status)$this->post('/lab_orders/'.$order->id.'/actions/lab_status',['status'=>$status])->assertRedirect()->assertSessionHasNoErrors();
        $this->post('/lab_orders/'.$order->id.'/actions/lab_results',['results'=>[$parameter->id=>'12.3']])->assertRedirect()->assertSessionHasNoErrors();
        $this->post('/lab_orders/'.$order->id.'/actions/lab_status',['status'=>'review'])->assertRedirect()->assertSessionHasNoErrors();
        $this->post('/lab_orders/'.$order->id.'/actions/approve_lab')->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lab_orders',['id'=>$order->id,'status'=>'approved']);
        $this->post('/admissions/'.$admission->id.'/actions/discharge',['discharged_at'=>now()->toDateTimeString(),'treatment_summary'=>'Test summary','discharge_type'=>'regular'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('invoices',['id'=>$invoice->id,'status'=>'finalized']);
        $this->assertDatabaseHas('beds',['id'=>$bed->id,'state'=>'cleaning']);
    }
    public function test_all_module_pages_render(): void
    {
        $this->actingAs($this->admin());
        foreach(['/','/patients','/doctors','/visits','/admissions','/beds','/nursing_records','/lab_orders','/invoices','/discharge','/expenses','/doctor_ledgers','/reports','/users','/settings','/tests','/test_parameters'] as $path)$this->get($path)->assertOk();
    }
}
