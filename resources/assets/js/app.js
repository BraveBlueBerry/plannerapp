import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import App from './components/App'
import Home from './components/Home'
import TasksIndex from './components/TasksIndex'

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
