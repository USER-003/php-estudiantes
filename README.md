# Registro de Estudiantes (App 1 · PHP)

CRUD completo de estudiantes en **PHP 8.3 nativo** (PDO) sobre **PostgreSQL
administrado**, desplegado en **Kompilo PaaS** desde GitHub con despliegue
automatico en cada `git push`.

| Ruta | Operacion |
|---|---|
| `GET /` | Listar y buscar (`?q=`) |
| `GET /nuevo` · `POST /guardar` | Crear |
| `GET /editar?id=N` · `POST /guardar` | Actualizar |
| `POST /eliminar` | Eliminar |
| `GET /healthz` | Sonda de salud (consulta la BD) |

## Variables de entorno

Ninguna credencial esta en el codigo. Kompilo las inyecta desde un Secret de
Kubernetes al crear la base en el panel:

| Variable | Origen |
|---|---|
| `DATABASE_URL` | Kompilo (complemento PostgreSQL) |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | Kompilo (alternativa si no hay URL) |
| `PORT` | Kompilo (puerto configurado en la app) |

## Despliegue en Kompilo

1. En https://kompilo.site: **Desplegar desde GitHub** > este repositorio, rama `main`.
2. Puerto `8080`, ruta de salud `/healthz`, **despliegue automatico** activado.
3. En el detalle de la app: **Base de datos > Crear base PostgreSQL**.
4. Kompilo construye la imagen con el `Dockerfile` del repo (Kaniko) y la publica
   en `https://<nombre>.kompilo.site` con HTTPS.

Cada `git push` a `main` dispara el webhook de GitHub y Kompilo reconstruye y
redespliega la app sin intervencion manual.

## Probar en local

```bash
docker run -d --name pg -e POSTGRES_PASSWORD=clave -p 5432:5432 postgres:16-alpine
docker build -t php-estudiantes .
docker run --rm -p 8080:8080 -e DATABASE_URL=postgresql://postgres:clave@host.docker.internal:5432/postgres php-estudiantes
```
