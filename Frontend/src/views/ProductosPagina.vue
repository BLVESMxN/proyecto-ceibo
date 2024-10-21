<template>
    <div class="container mt-5">
      <div class="header text-center mb-4">
        <h1>Administración de Productos</h1>
        <p>Gestione los productos del sistema.</p>
      </div>
  
      <!-- Tabla de Productos -->
      <div class="admin-table">
        <h2>Lista de Productos</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio</th>
              <th>Stock</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="producto in productos" :key="producto.id">
              <td>{{ producto.id }}</td>
              <td>{{ producto.nombre }}</td>
              <td>{{ producto.descripcion }}</td>
              <td>{{ producto.precio }}</td>
              <td>{{ producto.stock }}</td>
              <td>
                <button @click="eliminarProducto(producto.id)" class="btn btn-danger">Eliminar</button>
                <button @click="cargarProductoParaActualizar(producto)" class="btn btn-warning">Actualizar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Formulario para agregar/actualizar un producto -->
      <div class="admin-form">
        <h2>{{ actualizando ? 'Actualizar Producto' : 'Agregar Producto' }}</h2>
        <form @submit.prevent="agregarOActualizarProducto">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" v-model="nuevoProducto.nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" v-model="nuevoProducto.descripcion" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" id="precio" v-model="nuevoProducto.precio" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" id="stock" v-model="nuevoProducto.stock" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-pink">{{ actualizando ? 'Actualizar' : 'Agregar' }}</button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        productos: [],
        nuevoProducto: {
          id: null,
          nombre: '',
          descripcion: '',
          precio: '',
          stock: '',
        },
        actualizando: false
      };
    },
    mounted() {
      this.obtenerProductos();
    },
    methods: {
      obtenerProductos() {
        axios.get('http://localhost/CEIBO/proyecto-ceibo/Backend/productos.php?action=obtenerProductos')
          .then(res => {
            this.productos = res.data;
          })
          .catch(error => {
            console.error('Error al obtener productos:', error);
          });
      },
      agregarOActualizarProducto() {
        const action = this.actualizando ? 'actualizarProducto' : 'crearProducto';
        const productoData = {
          ...this.nuevoProducto,
          ...(this.actualizando && { id: this.nuevoProducto.id }),
        };
        axios.post(`http://localhost/CEIBO/proyecto-ceibo/Backend/productos.php?action=${action}`, productoData, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => {
            this.obtenerProductos();
            this.limpiarFormulario();
          })
          .catch(error => {
            console.error(`Error al ${this.actualizando ? 'actualizar' : 'agregar'} producto:`, error);
          });
      },
      eliminarProducto(idProducto) {
        axios.post('http://localhost/CEIBO/proyecto-ceibo/Backend/productos.php?action=eliminarProducto', { id: idProducto }, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => {
            this.obtenerProductos();
          })
          .catch(error => {
            console.error('Error al eliminar producto:', error);
          });
      },
      cargarProductoParaActualizar(producto) {
        this.nuevoProducto = { ...producto };
        this.actualizando = true;
      },
      limpiarFormulario() {
        this.nuevoProducto = {
          id: null,
          nombre: '',
          descripcion: '',
          precio: '',
          stock: '',
        };
        this.actualizando = false;
      }
    }
  };
  </script>
  
  <style scoped>
  .container {
    padding: 40px 20px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .header {
    text-align: center;
    margin-bottom: 40px;
  }
  
  .header h1 {
    font-size: 3rem;
    color: #ff69b4;
    font-weight: bold;
  }
  
  .header p {
    font-size: 1.5rem;
    color: #dc1b88;
  }
  
  .admin-table, .admin-form {
    margin-top: 20px;
  }
  
  .admin-table h2, .admin-form h2 {
    margin-bottom: 10px;
    color: #ff69b4;
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #dc1b88;
  }
  
  .form-control {
    width: 100%;
    padding: 10px;
    border: 2px solid #dc1b88;
    border-radius: 10px;
    box-sizing: border-box;
  }
  
  .table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  
  .table th,
  .table td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }
  
  .table th {
    background-color: #f2f2f2;
    color: #333333;
  }
  
  .btn {
    padding: 5px 10px;
    font-size: 0.9rem;
    border: none;
    border-radius: 5px;
    margin-right: 5px;
    cursor: pointer;
  }
  
  .btn-danger {
    background-color: #dc3545;
    color: white;
  }
  
  .btn-warning {
    background-color: #ffc107;
    color: white;
  }
  
  .btn-pink {
    background-color: #ff69b4;
    color: white;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 50px;
    text-transform: uppercase;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
  }
  
  .btn-pink:hover {
    background-color: #dc1b88;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }
  </style>
  