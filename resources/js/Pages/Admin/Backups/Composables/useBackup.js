import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useBackup = (props = {}) => {
    const isLoading = ref(false);

    // Form for creating backups
    const createForm = useForm({
        description: '',
    });

    // Form for restoring backups
    const restoreForm = useForm({
        confirmation: '',
    });

    // Form for deleting backups
    const deleteForm = (backup) => {
        messageConfirm('¿Está seguro de que desea eliminar este respaldo? Esta acción no se puede revertir.').then((res) => {
            if (res.isConfirmed) {
                router.delete(route(`${props.routeName || 'backups.'}destroy`, backup.id), {
                    preserveScroll: true,
                    onStart: () => isLoading.value = true,
                    onFinish: () => isLoading.value = false,
                });
            }
        });
    };

    // Create a new backup
    const createBackup = () => {
        createForm.post(route(`${props.routeName || 'backups.'}store`), {
            preserveScroll: true,
            onStart: () => isLoading.value = true,
            onFinish: () => isLoading.value = false,
            onSuccess: () => {
                createForm.reset();
            },
        });
    };

    // Restore a backup
    const restoreBackup = (backupId) => {
        restoreForm.post(route(`${props.routeName || 'backups.'}confirm-restore`, backupId), {
            preserveScroll: true,
            onStart: () => isLoading.value = true,
            onFinish: () => isLoading.value = false,
        });
    };

    // Download a backup
    const downloadBackup = (backupId) => {
        window.location.href = route(`${props.routeName || 'backups.'}download`, backupId);
    };

    // Check backup integrity
    const checkIntegrity = async (backupId) => {
        try {
            isLoading.value = true;
            // Usar fetch en lugar de axios por simplicidad
            const response = await fetch(route(`${props.routeName || 'backups.'}check-integrity`, backupId));
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error checking backup integrity:', error);
            return { valid: false, message: 'Error al verificar la integridad' };
        } finally {
            isLoading.value = false;
        }
    };

    // Format file size
    const formatFileSize = (bytes) => {
        if (!bytes || bytes === 0) return 'N/A';
        
        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        let size = bytes;
        let unitIndex = 0;
        
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        
        return `${size.toFixed(2)} ${units[unitIndex]}`;
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        
        const date = new Date(dateString);
        return date.toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    // Get status color
    const getStatusColor = (status) => {
        const colors = {
            pending: 'warning',
            completed: 'success',
            failed: 'danger',
            corrupted: 'danger',
        };
        return colors[status] || 'info';
    };

    // Get status label
    const getStatusLabel = (status) => {
        const labels = {
            pending: 'Pendiente',
            completed: 'Completado',
            failed: 'Fallido',
            corrupted: 'Corrupto',
        };
        return labels[status] || status;
    };

    return {
        createForm,
        restoreForm,
        deleteForm,
        isLoading,
        createBackup,
        restoreBackup,
        downloadBackup,
        checkIntegrity,
        formatFileSize,
        formatDate,
        getStatusColor,
        getStatusLabel,
    };
};