<template>
    <div class="container mt-5">
      <div class="header text-center mb-4">
        <h1>Administración de Ventas</h1>
        <p>Gestione las ventas del sistema.</p>
      </div>
  
      <!-- Tabla de Ventas -->
      <div class="admin-table">
        <h2>Lista de Ventas</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario ID</th>
              <th>Vendedor</th> <!-- Nueva columna -->
              <th>Total</th>
              <th>Fecha de Venta</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="venta in ventas" :key="venta.id">
              <td>{{ venta.id }}</td>
              <td>{{ venta.usuario_id }}</td>
              <td>{{ venta.vendedor }}</td> <!-- Muestra el nombre del vendedor -->
              <td>{{ venta.total }}</td>
              <td>{{ venta.fecha_venta }}</td>
              <td>
                <button @click="eliminarVenta(venta.id)" class="btn btn-danger">Eliminar</button>
                <button @click="cargarVentaParaActualizar(venta)" class="btn btn-warning">Actualizar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Formulario para agregar/actualizar una venta -->
      <div class="admin-form">
        <h2>{{ actualizando ? 'Actualizar Venta' : 'Agregar Venta' }}</h2>
        <form @submit.prevent="agregarOActualizarVenta">
          <div class="form-group">
            <label for="usuario_id">Usuario ID</label>
            <input type="number" id="usuario_id" v-model="nuevaVenta.usuario_id" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="total">Total</label>
            <input type="number" id="total" v-model="nuevaVenta.total" class="form-control" required>
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
        ventas: [],
        nuevaVenta: {
          id: null,
          usuario_id: '',
          total: '',
        },
        actualizando: false
      };
    },
    mounted() {
      this.obtenerVentas();
    },
    methods: {
      obtenerVentas() {
        axios.get('http://localhost/CEIBO/proyecto-ceibo/Backend/ventas.php?action=obtenerVentas')
          .then(res => {
            this.ventas = res.data;
          })
          .catch(error => {
            console.error('Error al obtener ventas:', error);
          });
      },
      agregarOActualizarVenta() {
        const action = this.actualizando ? 'actualizarVenta' : 'crearVenta';
        const ventaData = {
          ...this.nuevaVenta,
          ...(this.actualizando && { id: this.nuevaVenta.id }),
        };
        axios.post(`http://localhost/CEIBO/proyecto-ceibo/Backend/ventas.php?action=${action}`, ventaData, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => {
            this.obtenerVentas();
            this.limpiarFormulario();
          })
          .catch(error => {
            console.error(`Error al ${this.actualizando ? 'actualizar' : 'agregar'} venta:`, error);
          });
      },
      eliminarVenta(idVenta) {
        axios.post('http://localhost/CEIBO/proyecto-ceibo/Backend/ventas.php?action=eliminarVenta', { id: idVenta }, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => {
            this.obtenerVentas();
          })
          .catch(error => {
            console.error('Error al eliminar venta:', error);
          });
      },
      cargarVentaParaActualizar(venta) {
        this.nuevaVenta = { ...venta };
        this.actualizando = true;
      },
      limpiarFormulario() {
        this.nuevaVenta = {
          id: null,
          usuario_id: '',
          total: '',
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
  