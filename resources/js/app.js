import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { messageSuccess, error500, error422 } from '@/Hooks/useErrorsForm';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#96b5e1ff',
    },
});

router.on('navigate', (event) => {
    handleFlashMessages(event);
});

router.on('success', (event) => {
    handleFlashMessages(event);
});

router.on('error', (event) => {
    handleFlashMessages(event);
});

router.on('warning', (event) => {
    handleFlashMessages(event);
});

// Función helper para manejar flash messages
function handleFlashMessages(event) {
    // En Inertia v2, los datos pueden estar directamente en event.detail
    const page = event.detail?.page || event.detail;
    
    if (page && page.props) {
        // Manejar mensajes de success/error directos
        if (page.props.success) {
            messageSuccess(page.props.success);
        } else if (page.props.error) {
            error500(page.props.error);
        }
        
        // Manejar flash messages
        if (page.props.flash) {
            if (page.props.flash.success) {
                messageSuccess(page.props.flash.success);
            } else if (page.props.flash.error) {
                error500(page.props.flash.error);
            } else if (page.props.flash.info) {
                error422(page.props.flash.info);
            }
        }
    }
}

router.on('exception', (errors) => {
    console.log('Inertia exception event:', errors); // Debug log
    
    // Manejar errores de validación 422
    if (errors.detail && errors.detail.errors) {
        error422('Por favor, revisa los campos del formulario.');
    } else {
        error500('Ocurrió un error inesperado.');
    }
});
