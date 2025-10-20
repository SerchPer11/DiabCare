import { computed, ref, onMounted, watch } from "vue";
import { messageConfirm } from "./useErrorsForm";

export const useImage = (form, attribute = "photos") => {
    const totalFiles = computed(() => form[attribute].length);
    const isDragging = ref(false);

    const fileCountText = computed(() => {
        const count = totalFiles.value;
        return `${count} imagen${count !== 1 ? "es" : ""} seleccionada${count !== 1 ? "s" : ""}`;
    });

    const cleanFileName = (fileName) => {
        return fileName
            .replace(/\.(pdf|txt|docx)$/i, "")
            .replace(/[_-]/g, " ")
            .trim();
    };

    const clearErrors = () => {
        if (!form.errors) return;
        const errorKeys = Object.keys(form.errors);

        errorKeys.forEach(key => {
            if (key.startsWith(`${attribute}.`)) {
                delete form.errors[key];
            }
        });
    };

    const reindexErrors = () => {
        form[attribute].forEach((item, newIndex) => {
            item.index = newIndex;
        });
    };

    const handleFileInput = (event) => {
        isDragging.value = false;
        const files = event.target?.files || event.dataTransfer?.files;

        if (!files || files.length === 0) return;

        const fileList = Array.from(files);

        fileList.forEach((file) => {
            const newFile = {
                index: form[attribute].length,
                title: cleanFileName(file.name),
                description: null,
                file,
                path: URL.createObjectURL(file),
            };

            form[attribute].push(newFile);
        });
    };

    const removeFile = (index) => {
        const file = form[attribute][index];
        if (file?.path && file.path.startsWith("blob:")) {
            URL.revokeObjectURL(file.path);
        }
        clearErrors(index);
        form[attribute].splice(index, 1);
    };

    const removeAllFiles = () => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form[attribute].forEach((file) => {
                    if (file?.path && file.path.startsWith("blob:")) {
                        URL.revokeObjectURL(file.path);
                    }
                });
                clearErrors();
                form[attribute] = [];
            }
        });
    };

    onMounted(() => {
        reindexErrors();
    });

    watch(() => form[attribute], () => reindexErrors(),
        { deep: true }
    );

    return {
        // attributes
        isDragging,
        totalFiles,
        fileCountText,
        // methods
        handleFileInput,
        removeFile,
        removeAllFiles,
    };
};

export const useComments = () => {
    const showFile = ref(false);
    const selectedFile = ref({});

    const openModal = (file) => {
        showFile.value = true;
        selectedFile.value = file;
    };

    const closeModal = () => {
        showFile.value = false;
        selectedFile.value = {};
    };

    return {
        showFile,
        selectedFile,
        openModal,
        closeModal,
    };
};