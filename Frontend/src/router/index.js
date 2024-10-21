import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../views/HomePage.vue'; // Asegúrate de crear esta vista
import AboutPage from '../views/AboutPage.vue';
import UsuariosPagina from '../views/UsuariosPagina.vue'; // Asegúrate de crear esta vista
import VentasPagina from '../views/VentasPagina.vue';
import PagosPagina from '../views/PagosPagina.vue';
import ProductosPagina from '../views/ProductosPagina.vue';


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
    {
        path: '/usuarios-pagina',
        name: 'Usuarios',
        component: UsuariosPagina,
    },
    {
        path: '/ventas-pagina',
        name: 'Ventas',
        component: VentasPagina,
    },
    {
        path: '/pagos-pagina',
        name: 'Pagos',
        component: PagosPagina,
    },
    {
        path: '/productos-pagina',
        name: 'Productos',
        component: ProductosPagina,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
