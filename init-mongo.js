db = db.getSiblingDB('dicromo_db');

// Crear colección de usuarios con campos detallados
db.createCollection('users');
db.users.insertMany([
  {
    _id: ObjectId(),
    name: "Tomas Aguilera",
    email: "tomas@example.com",
    password: "hashed_password",  // Utiliza una función hash en tu aplicación
    phone: "+1234567890",
    address: {
      street: "123 Main St",
      city: "Anytown",
      state: "Anystate",
      zip: "12345"
    },
    date_of_birth: new Date('1990-01-01'), // Fecha de nacimiento
    profile_picture: "http://example.com/profile-pic.jpg", // URL de la imagen de perfil
    bio: "Administrador de la aplicación", // Biografía o descripción del usuario
    created_at: new Date(),
    updated_at: new Date()
  },
  {
    _id: ObjectId(),
    name: "Maria Gonzalez",
    email: "maria@example.com",
    password: "hashed_password",
    phone: "+0987654321",
    address: {
      street: "456 Another St",
      city: "Othertown",
      state: "Otherstate",
      zip: "67890"
    },
    date_of_birth: new Date('1992-05-15'), // Fecha de nacimiento
    profile_picture: "http://example.com/profile-pic2.jpg", // URL de la imagen de perfil
    bio: "Usuario de la aplicación", // Biografía o descripción del usuario
    created_at: new Date(),
    updated_at: new Date()
  }
]);

// Crear colección de tareas con campos detallados
db.createCollection('tasks');
db.tasks.insertMany([
  {
    _id: ObjectId(),
    user_id: ObjectId("..."),  // Reemplaza con un ObjectId de un usuario existente
    title: "Primera Tarea",
    description: "Descripción de la primera tarea",
    status: "pending",
    priority: "high",
    due_date: new Date(new Date().getTime() + 7*24*60*60*1000),  // Una semana a partir de hoy
    tags: ["importante", "urgente"], // Etiquetas para la tarea
    comments: [
      {
        author: "Tomas",
        message: "Comentario inicial",
        created_at: new Date()
      }
    ],
    created_at: new Date(),
    updated_at: new Date()
  },
  {
    _id: ObjectId(),
    user_id: ObjectId("..."),  // Reemplaza con un ObjectId de un usuario existente
    title: "Segunda Tarea",
    description: "Descripción de la segunda tarea",
    status: "completed",
    priority: "medium",
    due_date: new Date(new Date().getTime() + 3*24*60*60*1000),  // Tres días a partir de hoy
    tags: ["completada"], // Etiquetas para la tarea
    comments: [
      {
        author: "Maria",
        message: "Tarea finalizada",
        created_at: new Date()
      }
    ],
    created_at: new Date(),
    updated_at: new Date()
  }
]);

// Crear un usuario de base de datos sin roles
db.createUser({
  user: "admin",
  pwd: "adminpassword",
  roles: [{ role: "readWrite", db: "dicromo_db" }]
});
