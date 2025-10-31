<?php

namespace App\Traits;

use App\Models\Patient\ClinicalLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait LogClinicalActivity
{
    /**
     *
     * @param Model
     * @param string
     * @param int
     * @param int
     */
    protected function logActivity(Model $loggable, string $eventType, int $patientId, int $doctorId)
    {
        if (!$loggable->exists) {
            Log::error('Intento de registrar actividad en ClinicalLog para un modelo no guardado.', [
                'model' => get_class($loggable),
                'event_type' => $eventType
            ]);
            return;
        }

        if (!$patientId || !$doctorId) {
            Log::error('Faltan patientId o doctorId al intentar registrar en ClinicalLog.', [
                'loggable_id' => $loggable->id,
                'loggable_type' => get_class($loggable),
            ]);
            return;
        }

        try {
            ClinicalLog::create([
                'patient_id'    => $patientId,
                'doctor_id'     => $doctorId,
                'loggable_id'   => $loggable->id,
                'loggable_type' => get_class($loggable), 
                'event_type'    => $eventType,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear registro en ClinicalLog: ' . $e->getMessage(), [
                'patient' => $patientId,
                'doctor' => $doctorId,
                'loggable' => $loggable->id,
            ]);
        }
    }
}
