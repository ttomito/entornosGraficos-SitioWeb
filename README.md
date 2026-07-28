# Sistema de Vuelos

El sitio web corresponde a una agencia de vuelos que permite buscar y reservar pasajes. Incluye tres tipos de usuario con permisos distintos, registro con validación por correo electrónico, recuperación de contraseña y un panel de administración.

## Funcionalidades

### Visitante (sin cuenta)

- Ver el listado de vuelos disponibles con filtros por origen, destino y fecha
- Consultar promociones vigentes y precios con descuento aplicado
- Enviar consultas por el formulario de contacto
- Registrarse como cliente o como CEO de aerolínea

### Cliente

- Todo lo anterior
- Reservar vuelos y consultar el estado de sus reservas
- Editar su perfil

### CEO de aerolínea

- Gestionar los vuelos de su aerolínea
- Cargar promociones, que deberán ser aprobadas por un administrador

### Administrador

- Aprobar o rechazar las solicitudes de registro de los CEOs
- Aprobar o rechazar las promociones cargadas
- Gestionar aerolíneas y usuarios

## Tecnologías

| Lenguaje | PHP 8 |
| Base de datos | MySQL / MariaDB (extensión `mysqli`) |
| Estilos | Bootstrap 5 |
| Envío de correos | PHPMailer sobre SMTP de Gmail |
| Alertas | SweetAlert2 |
| Dependencias | Composer |

## Estructura de carpetas

entornosGraficos-SitioWeb/
│
├── admin/              Panel de administración (gestión de CEOs, promociones, aerolíneas)
├── assets/             Hojas de estilo
├── auth/               Registro, login, validación de cuenta y recuperación de contraseña
├── ceo/                Panel del CEO de aerolínea (vuelos y promociones propias)
├── cliente/            Listado de vuelos y gestión de reservas
├── database/           Script SQL para crear la base y cargar datos de prueba
├── includes/           Archivos compartidos por todo el sitio
├── perfil/             Edición de los datos de la cuenta
├── Sobrenosotros/      Página institucional
├── uploads/            Imágenes subidas desde el sitio
├── vendor/             Dependencias
│
├── contacto.php        Formulario de contacto
├── enviarContacto.php  Procesa y envía el mensaje de contacto
├── index.php           Página de inicio
└── mapa.php            Mapa del sitio

### Registro de un cliente

1. Completa el formulario en `auth/registro.php`
2. La cuenta se crea como `PENDIENTE` con `aprobadoAdmin = 'SI'`
3. Recibe un correo con un enlace hacia `auth/validar.php`
4. Al hacer clic, la cuenta pasa a `ACTIVA` y ya puede iniciar sesión

### Registro de un CEO

Los primeros pasos son iguales, con la diferencia de que la cuenta se crea con `aprobadoAdmin = 'NO'`. Después de validar el correo, la solicitud aparece en el panel del administrador, que puede aprobarla o rechazarla. En ambos casos el CEO recibe un correo con la respuesta. Recién con `estadoCuenta = 'ACTIVA'` y `aprobadoAdmin = 'SI'` puede ingresar

### Recuperación de contraseña

Desde `auth/recuperar.php` se pide un enlace, que llega por correo y **vence a los 60 minutos**. El enlace lleva a `auth/restablecer.php`, donde se define la contraseña nueva. El token se anula apenas se usa, así que cada enlace sirve una sola vez.

Por seguridad, el formulario responde siempre el mismo mensaje exista o no una cuenta con ese correo: si respondiera distinto, cualquiera podría averiguar qué direcciones están registradas probando de a una.

## Autores

- Cravero Tomás - 53038
- Rojtberg Ramiro - 52107
- Toscan Emiliano - 55971

Materia: Entornos Gráficos
Docentes: Daniela Díaz - Julián Butti
Año: 2026