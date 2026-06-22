<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Symptom;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_page_and_demo_auth_are_available(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk ke sistem');

        $this->post('/demo-login/nurse')->assertRedirect('/dashboard/perawat');
    }

    public function test_role_dashboards_render(): void
    {
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();
        $technician = User::query()->where('role', 'technician')->firstOrFail();
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($nurse)->get('/dashboard/perawat')->assertOk()->assertSee('Dashboard Perawat');
        $this->actingAs($technician)->get('/dashboard/teknisi')->assertOk()->assertSee('Dashboard Teknisi');
        $this->actingAs($admin)->get('/dashboard/admin')->assertOk()->assertSee('Dashboard Admin');
    }

    public function test_nurse_can_create_work_order_and_rule_engine_attaches_indications(): void
    {
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();
        $device = Device::query()->where('inventory_number', 'INV-IP-HCU-019')->firstOrFail();
        $symptoms = Symptom::query()->whereIn('code', ['PWR-01', 'PWR-02'])->pluck('id')->all();

        $response = $this->actingAs($nurse)->post('/work-orders', [
            'device_id' => $device->id,
            'symptoms' => $symptoms,
            'description' => 'Alat mati saat dipakai.',
        ]);

        $workOrder = WorkOrder::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('work-orders.show', $workOrder));
        $this->assertSame('pending', $workOrder->status);
        $this->assertGreaterThan(0, $workOrder->indications()->count());
        $this->assertSame('repair', $device->fresh()->status);
    }

    public function test_technician_can_update_work_order_status(): void
    {
        $technician = User::query()->where('role', 'technician')->firstOrFail();
        $workOrder = WorkOrder::query()->where('status', 'pending')->firstOrFail();

        $this->actingAs($technician)->patch(route('work-orders.update', $workOrder), [
            'status' => 'in_progress',
            'final_diagnosis' => 'Sistem Power',
            'notes' => 'Mulai pemeriksaan adaptor dan fuse.',
        ])->assertRedirect();

        $workOrder->refresh();

        $this->assertSame('in_progress', $workOrder->status);
        $this->assertSame($technician->id, $workOrder->technician_id);
        $this->assertNotNull($workOrder->processed_at);
    }

    public function test_admin_can_export_report_csv(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get('/reports/export-csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertSee('Nomor WO');
    }

    public function test_admin_can_open_printable_qr_labels(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get('/devices/print-qr')
            ->assertOk()
            ->assertSee('Cetak Label QR')
            ->assertSee('<svg', false);
    }

    public function test_admin_can_download_qr_labels_as_pdf(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->get('/devices/print-qr?format=pdf');

        $response->assertOk();
        $this->assertStringStartsWith('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('label-qr-alat.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_create_device_with_random_barcode_code(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $unitId = Unit::query()->value('id');

        $this->actingAs($admin)->post('/devices', [
            'unit_id' => $unitId,
            'name' => 'Infusion Pump Random Barcode',
            'type' => 'Infusion Pump',
            'model' => 'Auto QR',
            'serial_number' => 'SN-RANDOM-001',
            'inventory_number' => 'INV-RANDOM-001',
            'status' => 'active',
            'purchased_at' => '2026-05-23',
        ])->assertRedirect();

        $device = Device::query()->where('inventory_number', 'INV-RANDOM-001')->firstOrFail();

        $this->assertMatchesRegularExpression('/^WO-QR-[A-Z0-9]{8}$/', $device->barcode_code);
    }

    public function test_report_csv_export_respects_unit_filter(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $unit = Unit::query()->where('name', 'IGD')->firstOrFail();

        $this->actingAs($admin)
            ->get('/reports/export-csv?unit_id='.$unit->id)
            ->assertOk()
            ->assertSee('Infusion Pump Mindray BeneFusion')
            ->assertDontSee('Syringe Pump Terumo TE-331');
    }

    public function test_device_stays_in_repair_until_all_open_work_orders_are_finished(): void
    {
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();
        $technician = User::query()->where('role', 'technician')->firstOrFail();
        $device = Device::query()->where('inventory_number', 'INV-IP-HCU-019')->firstOrFail();
        $symptoms = Symptom::query()->whereIn('code', ['PWR-01', 'PWR-02'])->pluck('id')->all();

        for ($count = 0; $count < 2; $count++) {
            $this->actingAs($nurse)->post('/work-orders', [
                'device_id' => $device->id,
                'symptoms' => $symptoms,
                'description' => 'Alat mati saat dipakai.',
            ])->assertRedirect();
        }

        $orders = WorkOrder::query()
            ->where('device_id', $device->id)
            ->where('status', 'pending')
            ->latest('id')
            ->take(2)
            ->get();

        $this->actingAs($technician)->patch(route('work-orders.update', $orders[0]), [
            'status' => 'completed',
            'final_diagnosis' => 'Sistem Power',
            'notes' => 'Perbaikan pertama selesai.',
        ])->assertRedirect();

        $this->assertSame('repair', $device->fresh()->status);

        $this->actingAs($technician)->patch(route('work-orders.update', $orders[1]), [
            'status' => 'completed',
            'final_diagnosis' => 'Sistem Power',
            'notes' => 'Semua laporan terbuka selesai.',
        ])->assertRedirect();

        $this->assertSame('active', $device->fresh()->status);
    }

    public function test_admin_can_create_device_with_maintenance_calibration_and_photo(): void
    {
        $disk = env('FILESYSTEM_DISK', 'public');
        \Illuminate\Support\Facades\Storage::fake($disk);

        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $unitId = Unit::query()->value('id');
        $photo = \Illuminate\Http\UploadedFile::fake()->image('device.jpg');

        $this->actingAs($admin)->post('/devices', [
            'unit_id' => $unitId,
            'name' => 'Infusion Pump Tested',
            'type' => 'Infusion Pump',
            'model' => 'IP-Test',
            'serial_number' => 'SN-TESTED-001',
            'inventory_number' => 'INV-TESTED-001',
            'status' => 'active',
            'purchased_at' => '2026-05-23',
            'last_maintenance_at' => '2026-06-10',
            'last_calibration_at' => '2026-06-15',
            'photo' => $photo,
        ])->assertRedirect();

        $device = Device::query()->where('inventory_number', 'INV-TESTED-001')->firstOrFail();

        $this->assertEquals('2026-06-10', $device->last_maintenance_at->format('Y-m-d'));
        $this->assertEquals('2026-06-15', $device->last_calibration_at->format('Y-m-d'));
        $this->assertNotNull($device->photo_path);
        \Illuminate\Support\Facades\Storage::disk($disk)->assertExists($device->photo_path);
    }
}
