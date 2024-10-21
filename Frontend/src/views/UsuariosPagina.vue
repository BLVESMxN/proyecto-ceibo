<template>
    <div class="container mt-5">
      <div class="header text-center mb-4">
        <h1>Administración de Usuarios</h1>
        <p>Gestione los usuarios del sistema.</p>
      </div>
  
      <!-- Tabla de Usuarios -->
      <div class="admin-table">
        <h2>Lista de Usuarios</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Fecha de Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="usuario in usuarios" :key="usuario.id">
              <td>{{ usuario.id }}</td>
              <td>{{ usuario.nombre }}</td>
              <td>{{ usuario.email }}</td>
              <td>{{ usuario.rol }}</td>
              <td>{{ usuario.fecha_creacion }}</td>
              <td>
                <button @click="eliminarUsuario(usuario.id)" class="btn btn-danger">Eliminar</button>
                <button @click="cargarUsuarioParaActualizar(usuario)" class="btn btn-warning">Actualizar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Formulario para agregar/actualizar un usuario -->
      <div class="admin-form">
        <h2>{{ actualizando ? 'Actualizar Usuario' : 'Agregar Usuario' }}</h2>
        <form @submit.prevent="agregarOActualizarUsuario">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" v-model="nuevoUsuario.nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="nuevoUsuario.email" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" v-model="nuevoUsuario.password" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="rol">Rol</label>
            <select id="rol" v-model="nuevoUsuario.rol" class="form-control">
              <option value="cliente">Cliente</option>
              <option value="vendedor">Vendedor</option>
              <option value="proveedor">Proveedor</option>
              <option value="admin">Admin</option>
            </select>
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
        usuarios: [],
        nuevoUsuario: {
          id: null,
          nombre: '',
          email: '',
          password: '', // Cambiado de contrasenia a password
          rol: 'cliente', // Valor por defecto
        },
        actualizando: false // Variable para saber si estamos actualizando o agregando un usuario
      };
    },
    mounted() {
      this.obtenerUsuarios();
    },
    methods: {
      obtenerUsuarios() {
        axios.get('http://localhost/CEIBO/proyecto-ceibo/Backend/usuarios.php?action=obtenerUsuarios')
          .then(res => {
            this.usuarios = res.data; // Cambié "response" por "res"
          })
          .catch(error => {
            console.error('Error al obtener usuarios:', error);
          });
      },
      agregarOActualizarUsuario() {
        const action = this.actualizando ? 'actualizarUsuario' : 'crearUsuario';
        const usuarioData = {
          ...this.nuevoUsuario,
          // Si se está actualizando, incluir el ID
          ...(this.actualizando && { id: this.nuevoUsuario.id }),
        };
        axios.post(`http://localhost/CEIBO/proyecto-ceibo/Backend/usuarios.php?action=${action}`, usuarioData, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => { // Eliminé "response" aquí también
            this.obtenerUsuarios();
            this.limpiarFormulario();
          })
          .catch(error => {
            console.error(`Error al ${this.actualizando ? 'actualizar' : 'agregar'} usuario:`, error);
          });
      },
      eliminarUsuario(idUsuario) {
        axios.post('http://localhost/CEIBO/proyecto-ceibo/Backend/usuarios.php?action=eliminarUsuario', { id: idUsuario }, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .then(() => { // Eliminé "response" aquí también
            this.obtenerUsuarios();
          })
          .catch(error => {
            console.error('Error al eliminar usuario:', error);
          });
      },
      cargarUsuarioParaActualizar(usuario) {
        this.nuevoUsuario = { ...usuario }; // Copiar los datos del usuario al formulario
        this.actualizando = true; // Cambiar el estado a "actualizando"
      },
      limpiarFormulario() {
        this.nuevoUsuario = {
          id: null,
          nombre: '',
          email: '',
          password: '', // Cambiado de contrasenia a password
          rol: 'cliente', // Resetear a valor por defecto
        };
        this.actualizando = false; // Resetear el estado a "agregar"
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
  