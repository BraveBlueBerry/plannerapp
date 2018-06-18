import Vue from 'vue'
import VueRouter from 'vue-router'

import App from './views/App'
import Home from './views/Home'
import TasksIndex from './views/TasksIndex'

require('./bootstrap');
Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/tasks',
            name: 'tasks.index',
            component: TasksIndex,
        },
    ],
});

const app = new Vue ({
    el: '#app',
    components: { App },
    router,
});
