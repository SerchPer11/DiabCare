import { computed, ref } from "vue";

export const useFile = (form, attribute = "files", title = "archivo") => {
    const totalFiles = computed(() => form[attribute].length);
    const isDragging = ref(false);

    const fileCountText = computed(() => {
        const count = totalFiles.value;
        return `${count} ${title}${count !== 1 ? "s" : ""} 
        cargad${title === 'archivo' ? 'o' : 'a'}${count !== 1 ? "s" : ""}`;
    });

    const cleanFileName = (fileName) => {
        return fileName
            .replace(/\.(pdf|txt|docx)$/i, "")
            .replace(/[_-]/g, " ")
            .trim();
    };

    const clearErrors = (index) => {
        form.clearErrors(
            `files.${index}.file`,
            `files.${index}.title`,
            `files.${index}.description`
        );
    };

    const reindexErrors = (removedIndex) => {
        const newErrors = {};

        Object.keys(form.errors).forEach((key) => {
            if (!key.startsWith("files.")) {
                newErrors[key] = form.errors[key];
            }
        });

        Object.keys(form.errors).forEach((key) => {
            if (key.startsWith("files.")) {
                const match = key.match(/^files\.(\d+)\.(.+)$/);
                if (match) {
                    const currentIndex = parseInt(match[1]);
                    const field = match[2];

                    if (currentIndex > removedIndex) {
                        const newKey = `files.${currentIndex - 1}.${field}`;
                        newErrors[newKey] = form.errors[key];
                    } else if (currentIndex < removedIndex) {
                        newErrors[key] = form.errors[key];
                    }
                }
            }
        });
        form.errors = newErrors;
    };

    const handleFileInput = (event) => {
        isDragging.value = false
        const files = event.target?.files || event.dataTransfer?.files

        if (!files || files.length === 0) return;

        const fileList = Array.from(files);

        fileList.forEach((file) => {
            const newFile = {
                title: cleanFileName(file.name),
                description: null,
                file: file,
                path: URL.createObjectURL(file),
            };

            form[attribute].push(newFile);
        });
    };

    const removeFile = (index) => {
        const file = form[attribute][index];
        if (file.path && file.path.startsWith("blob:")) {
            URL.revokeObjectURL(file.path);
        }
        clearErrors(index);
        form.files.splice(index, 1);
        reindexErrors(index);
    };

    const removeAllFiles = () => {
        form[attribute].forEach((file) => {
            if (file.path && file.path.startsWith("blob:")) {
                URL.revokeObjectURL(file.path)
            }
        })
        form[attribute] = []
    }

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

export const useModal = () => {
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
