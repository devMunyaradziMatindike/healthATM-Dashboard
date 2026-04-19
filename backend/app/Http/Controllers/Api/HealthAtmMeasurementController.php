<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Measurement;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HealthAtmMeasurementController extends Controller
{
    private const DEFAULT_EQUIP_ID = 'UNKNOWN-EQUIP';

    public function store(Request $request)
    {
        $originalData = $request->all();

        if (!is_array($originalData)) {
            throw ValidationException::withMessages([
                'payload' => 'Invalid JSON payload.',
            ]);
        }

        $data = $this->normalizeHealthAtmPayload($originalData);

        $validated = validator($data, [
            'equip_id' => ['required', 'string', 'max:50'],
            'serial_number' => ['nullable', 'integer'],
            'equip_model' => ['nullable', 'string', 'max:20'],
            'equip_number' => ['nullable', 'string', 'max:20'],
            'token' => ['nullable', 'string', 'max:50'],

            'card_id' => ['nullable', 'string', 'max:50'],
            'qr_code' => ['nullable', 'string', 'max:100'],
            'cell_phone' => ['nullable', 'string', 'max:30'],
            'name' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'integer'],
            'age' => ['nullable', 'integer'],
            'birth' => ['nullable', 'string', 'max:10'],
            'nation' => ['nullable', 'integer'],

            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'utc' => ['nullable', 'integer'],
        ])->validate();

        $endTime = isset($validated['end_time'])
            ? Carbon::parse($validated['end_time'])
            : now();

        $measureDate = $endTime->toDateString();

        DB::transaction(function () use ($validated, $data, $endTime, $measureDate, $originalData) {
            $device = Device::query()->updateOrCreate(
                ['equip_id' => $validated['equip_id']],
                [
                    'equip_model' => $validated['equip_model'] ?? null,
                    'equip_number' => $validated['equip_number'] ?? null,
                    'last_seen_at' => now(),
                ],
            );

            $patient = $this->resolvePatient($data);

            Measurement::query()->create(array_merge(
                [
                    'device_id' => $device->id,
                    'patient_id' => $patient?->id,
                    'equip_id' => $validated['equip_id'],
                    'equip_model' => $validated['equip_model'] ?? null,
                    'equip_number' => $validated['equip_number'] ?? null,
                    'serial_number' => $validated['serial_number'] ?? null,
                    'token' => $validated['token'] ?? null,
                    'start_time' => isset($validated['start_time'])
                        ? Carbon::parse($validated['start_time'])
                        : null,
                    'end_time' => isset($validated['end_time'])
                        ? Carbon::parse($validated['end_time'])
                        : null,
                    'utc' => $validated['utc'] ?? null,
                    'measure_date' => $measureDate,
                ],
                collect($data)->only([
                    'card_id',
                    'card_type',
                    'name',
                    'gender',
                    'age',
                    'birth',
                    'nation',
                    'coin',
                    'qr_code',
                    'cell_phone',
                    'height',
                    'weight',
                    'body_temperature',
                    'systolic_bp',
                    'diastolic_bp',
                    'pulse_per_minute',
                    'blood_oxygen_saturation',
                    'fat_rate',
                    'fat_mass',
                    'basal_metabolism',
                    'body_moisture_rate',
                    'body_moisture_rate_core',
                    'skeletal_muscle',
                    'skeletal_muscle_score',
                    'visceral_fat_index',
                    'visceral_fat_index_core',
                    'bone_mineral_content',
                    'bone_mineral_content_score',
                    'extracellular_fluid',
                    'intracellular_fluid',
                    'moisture',
                    'protein',
                    'inorganic_salts',
                    'physical_age',
                    'overall_rating',
                    'blood_sugar',
                    'alcohol',
                    'alcohol_result',
                    'face_code',
                    'face_photo',
                    'tag1',
                    'tag2',
                    'tag3',
                    'tag4',
                    'tag5',
                    'tag6',
                    'tag7',
                    'tag8',
                    'tag9',
                    'tag10',
                ])->all(),
                [
                    'raw_payload' => $originalData,
                ],
            ));
        });

        return response()->json([
            'code' => 0,
            'msg' => 'success',
        ]);
    }

    /**
     * Apply defaults for missing/empty values and coerce types the Health ATM often sends as strings or ISO dates.
     */
    private function normalizeHealthAtmPayload(array $data): array
    {
        $equip = $data['equip_id'] ?? null;
        if ($equip === null || $equip === '' || (is_string($equip) && trim($equip) === '')) {
            $data['equip_id'] = self::DEFAULT_EQUIP_ID;
        } else {
            $data['equip_id'] = is_string($equip) ? trim($equip) : (string) $equip;
            if (strlen($data['equip_id']) > 50) {
                $data['equip_id'] = substr($data['equip_id'], 0, 50);
            }
        }

        foreach (['serial_number', 'gender', 'age', 'utc', 'nation'] as $key) {
            if (!array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
                continue;
            }
            if (is_string($data[$key]) && is_numeric($data[$key])) {
                $data[$key] = (int) $data[$key];
            }
        }

        foreach (['start_time', 'end_time'] as $key) {
            if (!array_key_exists($key, $data) || $data[$key] === null) {
                continue;
            }
            if (is_string($data[$key])) {
                $trimmed = trim($data[$key]);
                if ($trimmed === '') {
                    $data[$key] = null;

                    continue;
                }
                $data[$key] = str_replace('T', ' ', preg_replace('/\.\d{3}Z?$/', '', str_replace('Z', '', $trimmed)));
            }
        }

        $endEmpty = !isset($data['end_time']) || $data['end_time'] === null
            || (is_string($data['end_time']) && trim((string) $data['end_time']) === '');
        $startEmpty = !isset($data['start_time']) || $data['start_time'] === null
            || (is_string($data['start_time']) && trim((string) $data['start_time']) === '');

        if ($endEmpty) {
            $data['end_time'] = now()->format('Y-m-d H:i:s');
        }
        if ($startEmpty) {
            $data['start_time'] = Carbon::parse($data['end_time'])->copy()->subMinutes(2)->format('Y-m-d H:i:s');
        }

        return $data;
    }

    private function resolvePatient(array $data): ?Patient
    {
        $cardId = isset($data['card_id']) && is_string($data['card_id']) && $data['card_id'] !== '' ? $data['card_id'] : null;
        $qrCode = isset($data['qr_code']) && is_string($data['qr_code']) && $data['qr_code'] !== '' ? $data['qr_code'] : null;
        $idcard = isset($data['idcard']) && is_string($data['idcard']) && $data['idcard'] !== '' ? $data['idcard'] : null;
        $phone = isset($data['cell_phone']) && is_string($data['cell_phone']) && $data['cell_phone'] !== '' ? $data['cell_phone'] : null;

        if (!$cardId && !$qrCode && !$idcard && !$phone) {
            return null;
        }

        $patientQuery = Patient::query();
        $patientQuery->where(function ($q) use ($cardId, $qrCode, $idcard, $phone) {
            if ($cardId) {
                $q->orWhere('card_id', $cardId);
            }
            if ($qrCode) {
                $q->orWhere('qr_code', $qrCode);
            }
            if ($idcard) {
                $q->orWhere('idcard', $idcard);
            }
            if ($phone) {
                $q->orWhere('phone', $phone);
            }
        });

        $patient = $patientQuery->first();

        $attributes = [
            'card_id' => $cardId,
            'qr_code' => $qrCode,
            'idcard' => $idcard,
            'phone' => $phone,
            'name' => isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            'gender' => isset($data['gender']) ? (is_numeric($data['gender']) ? (int) $data['gender'] : null) : null,
            'age' => isset($data['age']) ? (is_numeric($data['age']) ? (int) $data['age'] : null) : null,
            'birth' => isset($data['birth']) && is_string($data['birth']) ? $data['birth'] : null,
            'nation' => isset($data['nation']) ? (is_numeric($data['nation']) ? (int) $data['nation'] : null) : null,
            'face_code' => isset($data['face_code']) && is_string($data['face_code']) ? $data['face_code'] : null,
            'face_photo' => isset($data['face_photo']) && is_string($data['face_photo']) ? $data['face_photo'] : null,
        ];

        if ($patient) {
            $patient->fill(array_filter($attributes, fn ($v) => $v !== null));
            $patient->save();
            return $patient;
        }

        return Patient::query()->create(array_filter($attributes, fn ($v) => $v !== null));
    }
}
