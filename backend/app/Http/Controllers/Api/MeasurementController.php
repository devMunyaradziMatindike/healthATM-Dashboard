<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'equip_id' => ['nullable', 'string', 'max:50'],
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 25);

        $query = Measurement::query()
            ->with(['device', 'patient'])
            ->orderByDesc('end_time')
            ->orderByDesc('id');

        if (!empty($validated['equip_id'])) {
            $query->where('equip_id', $validated['equip_id']);
        }

        if (!empty($validated['from'])) {
            $query->whereDate('end_time', '>=', $validated['from']);
        }

        if (!empty($validated['to'])) {
            $query->whereDate('end_time', '<=', $validated['to']);
        }

        if (!empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function ($sub) use ($q) {
                $sub->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('card_id', 'like', "%{$q}%")
                    ->orWhere('qr_code', 'like', "%{$q}%")
                    ->orWhere('cell_phone', 'like', "%{$q}%")
                    ->orWhere('equip_id', 'like', "%{$q}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function show(Measurement $measurement)
    {
        $measurement->load(['device', 'patient']);
        return $measurement;
    }

    public function latest(Request $request)
    {
        $validated = $request->validate([
            'equip_id' => ['nullable', 'string', 'max:50'],
        ]);

        $query = Measurement::query()
            ->with(['device', 'patient'])
            ->orderByDesc('end_time')
            ->orderByDesc('id');

        if (!empty($validated['equip_id'])) {
            $query->where('equip_id', $validated['equip_id']);
        }

        return $query->first();
    }
}
