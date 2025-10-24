import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from '@/Hooks/useErrorsForm';

export function useAppointment(props = {}) {
    const { setLoading } = useLoading();
    // Usar props.appointment directamente, no props.appointment.data
    const appointment = props.appointment || {};

    const form = useForm({
        _method: appointment.id ? 'patch' : 'post',
        patient_id: appointment.patient_id ?? null,
        doctor_id: appointment.doctor_id ?? null,
        date: appointment.date ?? null,
        time: appointment.time ?? null,
        modality: appointment.modality ?? null,
        reason: appointment.reason ?? null,
        additional_notes: appointment.additional_notes ?? null,
        video_call_link: appointment.video_call_link ?? null,
        appointment_status_id: appointment.appointment_status_id ?? null,
    });

    provide('form', form);
    provide('statusList', props.statusList);
    provide('doctors', props.doctors);
    provide('patients', props.patients);

    const saveForm = () => {
        form.post(route(`${props.routeName}store`), {
            onBefore: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const updateForm = () => {
        form.post(route(`${props.routeName}update`, { appointment: appointment.id }), {
            onBefore: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const destroyForm = () => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { appointment: appointment.id }));
            }
        });
    };

    const deleteForm = (appointmentId) => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, appointmentId));
            }
        });
    };

    return {
        processing: computed(() => form.processing),
        saveForm,
        updateForm,
        destroyForm,
        deleteForm,
        form,
    };
}
