import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../views/HomePage.vue'; // Asegúrate de crear esta vista
import AboutPage from '../views/AboutPage.vue'; // Asegúrate de crear esta vista

const routes = [
    {
        path: '/',
        name: 'Home',
        component: HomePage,
    },
    {
        path: '/about',
        name: 'About',
        component: AboutPage,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
