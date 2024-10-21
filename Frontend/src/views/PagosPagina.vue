<template>
    <div class="container mt-5">
      <div class="header text-center mb-4">
        <h1>Administración de Pagos</h1>
        <p>Gestione los pagos del sistema.</p>
      </div>
  
      <!-- Tabla de Pagos -->
      <div class="admin-table">
        <h2>Lista de Pagos</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Venta ID</th>
              <th>Método de Pago</th>
              <th>Monto</th>
              <th>Fecha de Pago</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pago in pagos" :key="pago.id">
              <td>{{ pago.id }}</td>
              <td>{{ pago.venta_id }}</td>
              <td>{{ pago.metodo_pago }}</td>
              <td>{{ pago.monto }}</td>
              <td>{{ pago.fecha_pago }}</td>
              <td>
                <button @click="eliminarPago(pago.id)" class="btn btn-danger">Eliminar</button>
                <button @click="cargarPagoParaActualizar(pago)" class="btn btn-warning">Actualizar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Formulario para agregar/actualizar un pago -->
      <div class="admin-form">
        <h2>{{ actualizando ? 'Actualizar Pago' : 'Agregar Pago' }}</h2>
        <form @submit.prevent="agregarOActualizarPago">
          <div class="form-group">
            <label for="venta_id">Venta ID</label>
            <input type="number" id="venta_id" v-model="nuevoPago.venta_id" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="metodo_pago">Método de Pago</label>
            <input type="text" id="metodo_pago" v-model="nuevoPago.metodo_pago" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="monto">Monto</label>
            <input type="number" id="monto" v-model="nuevoPago.monto" class="form-control" required>
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
        pagos: [],
        nuevoPago: {
          id: null,
          venta_id: '',
          metodo_pago: '',
          monto: '',
        },
        actualizando: false
      };
    },
    mounted() {
      this.obtenerPagos();
    },
    methods: {
      obtenerPagos() {
        axios.get('http://localhost/CEIBO/proyecto-ceibo/Backend/pagos.php?action=obtenerPagos')
          .then(res => {
            this.pagos = res.data;
          })
          .catch(error => {
            console.error('Error al obtener pagos:', error);
          });
      },
      agregarOActualizarPago() {
        const action = this.actualizando ? 'actualizarPago' : 'crearPago';
        const pagoData = {
          ...this.nuevoPago,
          ...(this.actualizando && { id: this.nuevoPago.id }),
        };
        axios.post(`http://localhost/CEIBO/proyecto-ceibo/Backend/pagos.php?action=${action}`, pagoData, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => {
            this.obtenerPagos();
            this.limpiarFormulario();
          })
          .catch(error => {
            console.error(`Error al ${this.actualizando ? 'actualizar' : 'agregar'} pago:`, error);
          });
      },
      eliminarPago(idPago) {
        axios.post('http://localhost/CEIBO/proyecto-ceibo/Backend/pagos.php?action=eliminarPago', { id: idPago }, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => {
            this.obtenerPagos();
          })
          .catch(error => {
            console.error('Error al eliminar pago:', error);
          });
      },
      cargarPagoParaActualizar(pago) {
        this.nuevoPago = { ...pago };
        this.actualizando = true;
      },
      limpiarFormulario() {
        this.nuevoPago = {
          id: null,
          venta_id: '',
          metodo_pago: '',
          monto: '',
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
  