# Sistema de gestión académica HabilProf UCSC

Este proyecto fue desarrollado para optimizar el proceso de asignación, seguimiento y administración de los alumnos que estén cursando su habilitación profesional en la UCSC en distintas modalidades (Proyecto de título, Práctica profesional, Práctica tutelada)

El objetivo es crear una plataforma integral que permita la administración de los datos de alumnos, profesores, ayudantes, tutores de práctica y otros datos relacionados a la habilitación profesional del estudiante como parte de su formación académica. Todo el  sistema fue diseñado teniendo en cuenta la fiabilidad y el rendimiento además de un fuerte enfoque en la usabilidad.

---

## Tecnologías usadas

Laravel, MySQL, JavaScript, Blade, Composer (PHP), NPM (JS).

---

## Aprendizajes

En este proyecto me vi enfrentado a desafíos técnicos, de gestión y de colaboración con mi grupo de trabajo.

No solo buscamos que el sistema funcionara sino que fuera sencillo para el usuario entenderlo y utilizarlo. Nos enfocamos en la Integrabilidad y la funcionalidad.

Aporté en la integración de código de mis compañeros. Gestioné conflictos de fusión, control de versiones, documentación y trabajo en equipo.

---
---![8db34fcd-d454-49f6-a7ea-f682b8c2cb5b](https://github.com/user-attachments/assets/096086a3-233a-4734-85db-a177e78ac255)
---![5299286e-f275-4adb-899c-98425259fe63](https://github.com/user-attachments/assets/99b64c13-6f6a-4349-b730-60b412aec5b4)
---![0a2cf4f4-db2f-44c2-8912-4a35f2d51aad](https://github.com/user-attachments/assets/79a61096-bec3-49fa-b4c8-91841c76eb12)
---![716b776d-2d80-45f9-9b35-7e1db0786d04](https://github.com/user-attachments/assets/6654c4f8-d6df-4f81-89ea-d5c2c6995dcf)




---

# 🧩 Configuración del Proyecto Laravel

Sigue estos pasos para preparar y ejecutar el proyecto correctamente en tu entorno local.

## ⚙️ 1. Configurar el archivo `.env`

Abre el archivo `.env` en la raíz del proyecto y reemplaza (o descomenta) las siguientes líneas según tu configuración local:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proyectotis
DB_USERNAME=root
DB_PASSWORD=
```

> 💡 Asegúrate de que tu servidor MySQL esté en ejecución y la base de datos `proyectotis` exista.

---

## 🧱 2. Instalar dependencias

Abre una terminal en la carpeta del proyecto y ejecuta:

```bash
composer install
```

---

## 🗄️ 3. Ejecutar las migraciones

Aplica las migraciones de la base de datos con:

```bash
php artisan migrate
```

> Si Laravel solicita crear la base de datos o confirma alguna acción, acepta (escribe `yes` o `y`).

---

## 🎨 4. Instalar Bootstrap y Popper.js

Ejecuta el siguiente comando para instalar las dependencias de frontend necesarias:

```bash
npm install bootstrap @popperjs/core
```

---

## 🔑 5. Generar la clave de aplicación

Crea la clave única para tu aplicación con:

```bash
php artisan key:generate
```

---

## 🚀 6. Iniciar el servidor de desarrollo

Finalmente, levanta el servidor local con:

```bash
php artisan serve
```

Luego abre tu navegador en la dirección que se indique (por defecto [http://127.0.0.1:8000](http://127.0.0.1:8000)).

---

## 🧩 Notas adicionales (opcional)

- Si aparece un error de migración, asegúrate de haber creado la base de datos en **phpMyAdmin** o mediante MySQL.
- Si el comando `composer install` no funciona, asegúrate de tener **Composer** correctamente instalado y agregado al PATH.
- Si `npm install` muestra errores, elimina la carpeta `node_modules` y ejecuta de nuevo:
  ```bash
  npm install
  ```
- Si `php artisan serve` no inicia, verifica que no haya otro servidor ejecutándose en el puerto 8000.
