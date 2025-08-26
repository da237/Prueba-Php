<template>
  <div class="container">
    <h2>Gestión de Productos</h2>

    <form @submit.prevent="crearProducto">
      <input v-model="nuevo.name" placeholder="Nombre" required />
      <input v-model.number="nuevo.price" placeholder="Precio" required type="number" />
      <button type="submit">Agregar</button>
    </form>

    <p v-if="mensaje" class="mensaje">{{ mensaje }}</p>

    <ul>
      <li v-for="p in productosFiltrados" :key="p.id">
        <strong>{{ p.name }}</strong> - ${{ p.price }}
        <button @click="eliminarProducto(p.id)">Eliminar</button>
        <button @click="editarProducto(p)">Editar</button>
      </li>
    </ul>

    <div v-if="editando">
      <h3>Editar Producto</h3>
      <form @submit.prevent="actualizarProducto">
        <input v-model="editando.name" required />
        <input v-model.number="editando.price" required type="number" />
        <button type="submit">Actualizar</button>
        <button @click="cancelarEdicion">Cancelar</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ProductList',
  data() {
    return {
      productos: [],
      nuevo: { name: '', price: null },
      editando: null,
      mensaje: '',
      API: 'http://localhost:8000/products'
    };
  },


  computed: {
    productosFiltrados() {
      return Array.isArray(this.productos)
        ? this.productos.filter(p => p.name && Number(p.price) > 0)
        : [];
    }
  },
  methods: {
    cargarProductos() {
      axios.get(this.API)
        .then(res => this.productos = res.data)
        .catch(err => console.error('Error al cargar productos:', err));
    },
    crearProducto() {
      if (!this.nuevo.name || this.nuevo.price === null || this.nuevo.price <= 0) {
        this.mensaje = 'Por favor ingresa un nombre y un precio válido';
        return;
      }

      axios.post(this.API, this.nuevo)
        .then(() => {
          this.mensaje = 'Producto agregado correctamente';
          this.nuevo = { name: '', price: null };
          this.cargarProductos();
        })
        .catch(err => {
          this.mensaje = 'Error al crear producto';
          console.error(err);
        });
    },
    eliminarProducto(id) {
      axios.delete(`${this.API}/${id}`)
        .then(() => {
          this.mensaje = 'Producto eliminado';
          this.cargarProductos();
        })
        .catch(err => {
          this.mensaje = 'Error al eliminar producto';
          console.error(err);
        });
    },
    editarProducto(p) {
      this.editando = { ...p };
    },
    actualizarProducto() {
      if (!this.editando.name || this.editando.price === null || this.editando.price <= 0) {
        this.mensaje = 'Nombre y precio válidos son requeridos';
        return;
      }

      axios.put(`${this.API}/${this.editando.id}`, this.editando)
        .then(() => {
          this.mensaje = 'Producto actualizado';
          this.editando = null;
          this.cargarProductos();
        })
        .catch(err => {
          this.mensaje = 'Error al actualizar producto';
          console.error(err);
        });
    },
    cancelarEdicion() {
      this.editando = null;
      this.mensaje = '';
    }
  },
  mounted() {
    this.cargarProductos();
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: auto;
  font-family: sans-serif;
}

input {
  margin: 5px;
}

button {
  margin: 5px;
}

ul {
  list-style: none;
  padding: 0;
}

li {
  margin-bottom: 10px;
}

.mensaje {
  margin: 10px 0;
  color: green;
  font-weight: bold;
}
</style>