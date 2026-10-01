<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrescriptionPdfController extends Controller
{
    public function generate(Request $request, Consultation $consultation)
    {
        // Validate signed URL
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid or expired link.');
        }

        // Load all needed relationships
        $consultation->load([
            'doctor:id,first_name,last_name,specialization,prc_id',
            'patient' => fn ($q) => $q->withTrashed()->select('id', 'first_name', 'last_name', 'gender', 'birthdate', 'address'),
            'clinic' => fn ($q) => $q->withTrashed()->select('id', 'clinic_name', 'address', 'phone_number'),
            'prescriptions.generic:id,generic_name',
            'prescriptions.brand:id,brand_name',
        ]);

        $doctor = $consultation->doctor;
        $patient = $consultation->patient;
        $clinic = $consultation->clinic;

        // Calculate patient age
        $age = $patient->birthdate
            ? Carbon::parse($patient->birthdate)->age
            : null;

        // Gender display
        $gender = $patient->gender
            ? strtoupper(substr($patient->gender, 0, 1))
            : '';

        // Build prescriptions array
        $prescriptions = collect($consultation->prescriptions)->map(function ($rx) {
            return [
                'generic_name' => $rx->generic_name_snapshot ?? $rx->generic?->generic_name ?? 'Unknown',
                'brand_name'   => $rx->brand_name_snapshot ?? $rx->brand?->brand_name ?? 'Unknown',
                'dosage'       => $rx->dosage,
                'frequency'    => $rx->frequency,
                'duration'     => $rx->duration,
                'instructions' => $rx->instructions ?? null,
            ];
        });

        $data = [
            'doctor' => [
                'name'           => $doctor->first_name . ' ' . $doctor->last_name,
                'specialization' => $doctor->specialization ?? 'General Practitioner',
                'prc_id'         => $doctor->prc_id ?? 'N/A',
            ],
            'patient' => [
                'name'    => $patient->last_name . ', ' . $patient->first_name,
                'age'     => $age ?? '—',
                'gender'  => $gender,
                'address' => $patient->address ?? null,
            ],
            'clinic' => [
                'name'    => $clinic->clinic_name,
                'address' => $clinic->address ?? null,
                'phone'   => $clinic->phone_number ?? null,
            ],
            'consultation' => [
                'date' => Carbon::parse($consultation->consultation_date)
                    ->format('m/d/Y'),
            ],
            'prescriptions' => $prescriptions,
        ];

        // FIX: was 'letter' — this was overriding the CSS @page A5 size
        // entirely, since dompdf ignores @page size and uses setPaper().
        $pdf = Pdf::loadView('prescription', $data)
            ->setPaper('a5', 'portrait');

        $filename = 'prescription_' . $patient->last_name . '_' .
            Carbon::parse($consultation->consultation_date)->format('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }

    public function generateSignedUrl(Request $request, Consultation $consultation)
    {
        // Only the attending doctor or clinic staff can generate the link
        $user = $request->user();
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        abort_if((int) $consultation->clinic_id !== (int) $request->active_clinic_id, 403, 'This record does not belong to your active clinic.');

        $url = \URL::temporarySignedRoute(
            'prescription.pdf',
            now()->addMinutes(15),
            ['consultation' => $consultation->id]
        );

        return response()->json(['url' => $url]);
    }
}