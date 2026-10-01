<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Consultation;
use App\Models\Generic;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrescriptionController extends Controller
{
    // 1. CREATE (POST /api/prescriptions)
    // Adds a prescription to an existing consultation in the active clinic.
    public function store(Request $request)
    {
        if ($request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized. Only doctors can create prescriptions.'], 403);
        }

        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'generic_id'      => ['required', Rule::exists('generics', 'id')->whereNull('deleted_at')],
            'brand_id'        => ['required', Rule::exists('brands', 'id')->whereNull('deleted_at')],
            'dosage'          => 'required|string|max:255',
            'frequency'       => 'required|string|max:255',
            'duration'        => 'required|string|max:255',
            'instructions'    => 'nullable|string',
        ]);

        $consultation = Consultation::findOrFail($validated['consultation_id']);
        abort_if((int) $consultation->clinic_id !== (int) $request->active_clinic_id, 403, 'This record does not belong to your active clinic.');

        // Save the current names so printed history survives reference data edits
        $validated['generic_name_snapshot'] = Generic::find($validated['generic_id'])?->generic_name;
        $validated['brand_name_snapshot'] = Brand::find($validated['brand_id'])?->brand_name;

        $prescription = Prescription::create($validated);

        return response()->json([
            'message'      => 'Prescription created successfully',
            'prescription' => $prescription->load([
                'generic:id,generic_name',
                'brand:id,brand_name',
            ]),
        ], 201);
    }

    // 2. UPDATE (PUT/PATCH /api/prescriptions/{prescription})
    // Edits an existing prescription after a consultation has been saved.
    public function update(Request $request, Prescription $prescription)
    {
        if ($request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized. Only doctors can update prescriptions.'], 403);
        }

        abort_if((int) $prescription->consultation()->value('clinic_id') !== (int) $request->active_clinic_id, 403, 'This record does not belong to your active clinic.');

        $validated = $request->validate([
            'generic_id'   => ['sometimes', Rule::exists('generics', 'id')->whereNull('deleted_at')],
            'brand_id'     => ['sometimes', Rule::exists('brands', 'id')->whereNull('deleted_at')],
            'dosage'       => 'sometimes|string|max:255',
            'frequency'    => 'sometimes|string|max:255',
            'duration'     => 'sometimes|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        // Refresh snapshots when the medication changes
        if (isset($validated['generic_id'])) {
            $validated['generic_name_snapshot'] = Generic::find($validated['generic_id'])?->generic_name;
        }
        if (isset($validated['brand_id'])) {
            $validated['brand_name_snapshot'] = Brand::find($validated['brand_id'])?->brand_name;
        }

        $prescription->update($validated);

        return response()->json([
            'message'      => 'Prescription updated successfully',
            'prescription' => $prescription->load([
                'generic:id,generic_name',
                'brand:id,brand_name',
            ]),
        ]);
    }

    // 3. DELETE (DELETE /api/prescriptions/{prescription})
    // Removes a prescription from a consultation.
    public function destroy(Request $request, Prescription $prescription)
    {
        if ($request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized. Only doctors can delete prescriptions.'], 403);
        }

        abort_if((int) $prescription->consultation()->value('clinic_id') !== (int) $request->active_clinic_id, 403, 'This record does not belong to your active clinic.');

        $prescription->delete();

        return response()->json([
            'message' => 'Prescription removed successfully',
        ]);
    }
}
