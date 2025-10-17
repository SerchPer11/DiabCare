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

router.on('success', (event) => {
    const props = event.detail.page.props;

    if (props.success) {
        messageSuccess(props.success); 
    } else if (props.error) {
        error500(props.error); 
    }
});

router.on('error', (errors) => {
    const errorObject = errors.detail.errors;
    
    if (errorObject && errorObject.response) {
        if (errorObject.response.status === 500) {
            error500();
        }
    }
    
    if(errors.detail.errors.message) {
        error422();
    }
});
