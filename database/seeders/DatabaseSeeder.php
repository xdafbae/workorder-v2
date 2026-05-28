<?php

namespace Database\Seeders;

use App\Models\DamageIndication;
use App\Models\Device;
use App\Models\Rule;
use App\Models\Symptom;
use App\Models\SystemNotification;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\RuleEngineService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $units = collect([
            ['name' => 'ICU Lantai 2', 'floor' => '2', 'building' => 'Gedung A'],
            ['name' => 'NICU', 'floor' => '2', 'building' => 'Gedung A'],
            ['name' => 'IGD', 'floor' => '1', 'building' => 'Gedung B'],
            ['name' => 'HCU', 'floor' => '3', 'building' => 'Gedung A'],
        ])->mapWithKeys(fn ($unit) => [$unit['name'] => Unit::query()->updateOrCreate(['name' => $unit['name']], $unit)]);

        $users = collect([
            ['name' => 'Ns. Rina Marlina', 'email' => 'perawat@rs.test', 'role' => 'nurse', 'unit_id' => $units['ICU Lantai 2']->id],
            ['name' => 'Teguh Prasetyo', 'email' => 'teknisi@rs.test', 'role' => 'technician', 'unit_id' => null],
            ['name' => 'Supervisor IPSRS', 'email' => 'admin@rs.test', 'role' => 'admin', 'unit_id' => null],
            ['name' => 'IT Super Admin', 'email' => 'superadmin@rs.test', 'role' => 'super_admin', 'unit_id' => null],
        ])->mapWithKeys(fn ($user) => [$user['role'] => User::query()->updateOrCreate(
            ['email' => $user['email']],
            $user + ['password' => Hash::make('password')]
        )]);

        $devices = collect([
            ['name' => 'Syringe Pump Terumo TE-331', 'type' => 'Syringe Pump', 'model' => 'TE-331', 'serial_number' => 'SN-TE331-24019', 'inventory_number' => 'INV-IP-ICU-024', 'status' => 'repair', 'purchased_at' => '2023-01-10', 'unit_id' => $units['ICU Lantai 2']->id],
            ['name' => 'Syringe Pump B. Braun Perfusor', 'type' => 'Syringe Pump', 'model' => 'Perfusor Space', 'serial_number' => 'SN-BB-11029', 'inventory_number' => 'INV-IP-NICU-011', 'status' => 'repair', 'purchased_at' => '2022-07-15', 'unit_id' => $units['NICU']->id],
            ['name' => 'Infusion Pump Mindray BeneFusion', 'type' => 'Infusion Pump', 'model' => 'BeneFusion VP5', 'serial_number' => 'SN-MR-88006', 'inventory_number' => 'INV-IP-IGD-006', 'status' => 'active', 'purchased_at' => '2024-03-18', 'unit_id' => $units['IGD']->id],
            ['name' => 'Syringe Pump Fresenius Injectomat', 'type' => 'Syringe Pump', 'model' => 'Injectomat Agilia', 'serial_number' => 'SN-FR-72019', 'inventory_number' => 'INV-IP-HCU-019', 'status' => 'active', 'purchased_at' => '2021-11-04', 'unit_id' => $units['HCU']->id],
        ])->mapWithKeys(function ($device) {
            $savedDevice = Device::query()->firstOrNew(['inventory_number' => $device['inventory_number']]);
            $savedDevice->fill($device);
            $savedDevice->save();

            return [$device['inventory_number'] => $savedDevice];
        });

        $symptoms = collect([
            ['code' => 'PWR-01', 'name' => 'Alat tidak menyala', 'category' => 'power_check'],
            ['code' => 'PWR-02', 'name' => 'Indikator baterai tidak muncul', 'category' => 'power_check'],
            ['code' => 'ALM-01', 'name' => 'Alarm occlusion aktif', 'category' => 'alarm_check'],
            ['code' => 'ALM-02', 'name' => 'Alarm air bubble aktif', 'category' => 'alarm_check'],
            ['code' => 'ALM-03', 'name' => 'Alarm door open aktif', 'category' => 'alarm_check'],
            ['code' => 'PRF-01', 'name' => 'Cairan tidak keluar', 'category' => 'performa_check'],
            ['code' => 'PRF-02', 'name' => 'Flow rate tidak stabil', 'category' => 'performa_check'],
            ['code' => 'SNS-01', 'name' => 'Sensor tidak merespons', 'category' => 'sensor_check'],
            ['code' => 'SNS-02', 'name' => 'Error sensor tekanan', 'category' => 'sensor_check'],
            ['code' => 'MTR-01', 'name' => 'Suara motor tidak normal', 'category' => 'mekanik_motor_check'],
            ['code' => 'MTR-02', 'name' => 'Plunger tidak bergerak', 'category' => 'mekanik_motor_check'],
            ['code' => 'SFW-01', 'name' => 'Tampilan hang', 'category' => 'software_check'],
            ['code' => 'SFW-02', 'name' => 'Kode error sistem muncul', 'category' => 'software_check'],
        ])->mapWithKeys(fn ($symptom) => [$symptom['code'] => Symptom::query()->updateOrCreate(['code' => $symptom['code']], $symptom)]);

        $indications = collect([
            ['code' => 'DMG-PWR', 'name' => 'Sistem Power', 'severity' => 'high', 'suggestions' => ['Cek sumber daya: kabel, adaptor, baterai, sekering, power board, tombol, dan konektor internal.']],
            ['code' => 'DMG-ALM', 'name' => 'Alarm Aktif', 'severity' => 'medium', 'suggestions' => ['Lakukan tindakan sesuai panduan alarm di user manual dan catat kode alarm yang muncul.']],
            ['code' => 'DMG-PRF', 'name' => 'Performa Flow', 'severity' => 'medium', 'suggestions' => ['Periksa gelembung udara, sumbatan selang, ukuran syringe, dan konektor sebelum kalibrasi lanjutan.']],
            ['code' => 'DMG-SNS', 'name' => 'Kerusakan Sensor', 'severity' => 'medium', 'suggestions' => ['Bersihkan sensor dan periksa kabel konektor sensor tekanan, udara, atau posisi plunger.']],
            ['code' => 'DMG-MTR', 'name' => 'Motor / Driver / Mekanik', 'severity' => 'high', 'suggestions' => ['Periksa motor, lead screw, mekanisme penggerak, pelumasan, dan driver motor.']],
            ['code' => 'DMG-SFW', 'name' => 'Software / System', 'severity' => 'low', 'suggestions' => ['Restart alat, dokumentasikan kode error, lalu siapkan update atau instal ulang firmware bila berulang.']],
        ])->mapWithKeys(function ($item) {
            $suggestions = $item['suggestions'];
            unset($item['suggestions']);

            $indication = DamageIndication::query()->updateOrCreate(['code' => $item['code']], $item);
            $indication->suggestions()->delete();
            foreach ($suggestions as $index => $suggestion) {
                $indication->suggestions()->create([
                    'step_order' => $index + 1,
                    'action_text' => $suggestion,
                ]);
            }

            return [$item['code'] => $indication];
        });

        $this->rule('Power failure basic', 95, ['PWR-01', 'PWR-02'], ['DMG-PWR'], $symptoms, $indications);
        $this->rule('Alarm handling path', 70, ['ALM-01'], ['DMG-ALM', 'DMG-SNS'], $symptoms, $indications);
        $this->rule('Occlusion flow path', 82, ['ALM-01', 'PRF-02'], ['DMG-SNS', 'DMG-PRF'], $symptoms, $indications);
        $this->rule('Motor drive suspect', 88, ['MTR-01', 'MTR-02'], ['DMG-MTR'], $symptoms, $indications);
        $this->rule('Firmware error', 78, ['SFW-01', 'SFW-02'], ['DMG-SFW'], $symptoms, $indications);

        $this->workOrder(
            'WO-2026-0008',
            $devices['INV-IP-ICU-024'],
            $users['nurse'],
            null,
            'pending',
            ['PWR-01', 'PWR-02'],
            'Alat mati saat akan digunakan di bed 3.',
            $symptoms
        );

        $this->workOrder(
            'WO-2026-0007',
            $devices['INV-IP-NICU-011'],
            $users['nurse'],
            $users['technician'],
            'in_progress',
            ['ALM-01', 'PRF-02'],
            'Alarm occlusion berulang dan flow tidak stabil.',
            $symptoms
        );

        $this->workOrder(
            'WO-2026-0006',
            $devices['INV-IP-IGD-006'],
            $users['nurse'],
            $users['technician'],
            'completed',
            ['ALM-03'],
            'Door open alarm aktif setelah syringe diganti.',
            $symptoms
        );
    }

    private function rule(string $name, int $weight, array $symptomCodes, array $indicationCodes, $symptoms, $indications): void
    {
        $rule = Rule::query()->updateOrCreate(['name' => $name], [
            'weight' => $weight,
            'is_active' => true,
        ]);

        $rule->symptoms()->sync(collect($symptomCodes)->map(fn ($code) => $symptoms[$code]->id));
        $rule->indications()->sync(collect($indicationCodes)->map(fn ($code) => $indications[$code]->id));
    }

    private function workOrder(string $number, Device $device, User $reporter, ?User $technician, string $status, array $symptomCodes, string $description, $symptoms): void
    {
        $workOrder = WorkOrder::query()->updateOrCreate(['wo_number' => $number], [
            'device_id' => $device->id,
            'reporter_id' => $reporter->id,
            'technician_id' => $technician?->id,
            'status' => $status,
            'description' => $description,
            'processed_at' => $status !== 'pending' ? now()->subHours(2) : null,
            'completed_at' => in_array($status, ['completed', 'closed'], true) ? now()->subHour() : null,
        ]);

        $symptomIds = collect($symptomCodes)->map(fn ($code) => $symptoms[$code]->id)->all();
        $workOrder->symptoms()->sync($symptomIds);

        $engine = app(RuleEngineService::class);
        $workOrder->indications()->sync($engine->evaluate($symptomIds)->mapWithKeys(fn ($indication) => [
            $indication->id => ['source' => 'system', 'score' => $indication->score ?? 0],
        ])->all());

        $workOrder->updates()->delete();
        $workOrder->updates()->create([
            'user_id' => $reporter->id,
            'status' => 'pending',
            'notes' => 'Work Order dibuat dari laporan perawat.',
            'created_at' => now()->subHours(3),
            'updated_at' => now()->subHours(3),
        ]);

        if ($status !== 'pending') {
            $workOrder->updates()->create([
                'user_id' => $technician?->id,
                'status' => $status,
                'final_diagnosis' => $workOrder->indications()->first()?->name,
                'notes' => 'Teknisi mulai menangani Work Order.',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ]);
        }

        SystemNotification::query()
            ->where('user_id', $reporter->id)
            ->where('type', 'work_order_status_changed')
            ->delete();

        SystemNotification::query()->create([
            'user_id' => $reporter->id,
            'type' => 'work_order_status_changed',
            'data' => ['wo_number' => $number, 'status' => $status],
        ]);
    }
}
