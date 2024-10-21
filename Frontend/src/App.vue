<template>
  <div id="app">
      <nav>
          <router-link to="/">Home</router-link>
          <router-link to="/about">About</router-link>
          <router-link to="/usuarios-pagina">Usuarios</router-link>
          <router-link to="/ventas-pagina">Ventas</router-link>
          <router-link to="/pagos-pagina">Pagos</router-link>
          <router-link to="/productos-pagina">Productos</router-link>
      </nav>
      <router-view></router-view> <!-- Aquí se cargarán las vistas -->
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style>
nav {
  margin-bottom: 20px;
}

router-link {
  margin-right: 10px;
}
</style>
