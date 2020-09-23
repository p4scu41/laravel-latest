require('./bootstrap');

import Vue from 'vue';
import VueMeta from 'vue-meta';
import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';

Vue.config.productionTip = false;
Vue.mixin({methods: {route: window.route}});
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);

const app = document.getElementById('app');

new Vue({
    metaInfo: {
        titleTemplate: (title) => title ? `${title} - Laravel` : 'Laravel',
    },
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => import(`@/Pages/${name}`).then(module => module.default),
            },
        }),
}).$mount(app);
