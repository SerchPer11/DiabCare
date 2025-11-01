import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const cancelColor = "#2F8AD4";
const confirmColor = "#2F8AD4";
const warningColor = "#F23030";
const confirmMessage = "Aceptar";
const warningMessage = "Sí";
const cancelMessage = "No";

export default function (errors) {
    return Object.keys(errors).reduce((acc, key) => {
        acc[key] = errors[key][0] ?? null; // Solo toma el primer error de cada campo
        return acc;
    }, {});
}

export function error422(message = "Al parecer hay campos inválidos, por favor revísalos con cuidado.") {
    Swal.fire({
        title: "Ups!",
        text: message,
        icon: "info",
        customClass: {
            icon: "swal-info-icon"
        },
        confirmButtonColor: confirmColor,
        confirmButtonText: confirmMessage,
        returnFocus: false,
    });
}

export function error500(message = "Ocurrió un error inesperado, intentalo más tarde.") {
    Swal.fire({
        title: "Error",
        text: message,
        icon: "error",
        customClass: {
            icon: "swal-error-icon"
        },
        confirmButtonColor: confirmColor,
        confirmButtonText: confirmMessage,
        returnFocus: false,
    });
}

export function messageSuccess(message = "La operación se realizo con éxito.") {
    Swal.fire({
        title: "¡Operación Éxitosa!",
        text: message,
        icon: "success",
        customClass: {
            icon: "swal-success-icon"
        },
        confirmButtonColor: confirmColor,
        confirmButtonText: confirmMessage,
        returnFocus: false,
    });
}

export function messageConfirm(message = "Esta acción no se puede revertir") {
    return Swal.fire({
        title: "¿Está seguro?",
        text: message,
        icon: "warning",
        customClass: {
            icon: "swal-warning-icon"
        },
        showCancelButton: true,
        cancelButtonText: cancelMessage,
        cancelButtonColor: cancelColor,
        confirmButtonColor: warningColor,
        confirmButtonText: warningMessage,
        returnFocus: false,
    });
}

export function messageConfirmGood(message = "Se enviará la respuesta y no podrá ser cambiada") {
    return Swal.fire({
        title: "¿Está seguro?",
        text: message,
        icon: "warning",
        customClass: {
            icon: "swal-warning-icon"
        },
        showCancelButton: true,
        cancelButtonText: cancelMessage,
        cancelButtonColor: warningColor,
        confirmButtonColor: cancelColor,
        confirmButtonText: warningMessage,
        returnFocus: false,
    });
}