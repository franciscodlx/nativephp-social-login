# Contribuir

## Preparar el entorno

PHP 8.3+, Composer, Node 22+ (para los mocks y backends), Xcode, y Android SDK con JDK 17.

```sh
cd packages/social-login
composer install
composer test
```

## Correr la demo contra los mocks

1. Proveedor local: `node tools/mock-idp/server.mjs`. Con `MOCK_IDP_AUTO=approve` redirige sin mostrar la pantalla de consentimiento; con `MOCK_IDP_AUTO=deny` siempre rechaza.
2. Backends, cada uno en su puerto y en modo mock si no tiene `.env`: `node server.mjs` en cada carpeta de `tools/*-backend`.

   | Backend                      | Puerto | Proveedores                    |
   | ---------------------------- | ------ | ------------------------------ |
   | `tools/linkedin-backend`     | 4100   | LinkedIn                       |
   | `tools/mercadolibre-backend` | 4200   | Mercado Libre                  |
   | `tools/twitch-backend`       | 4300   | Twitch                         |
   | `tools/presets-backend`      | 4400   | GitHub, Discord, Slack, Strava |
   | `tools/telegram-backend`     | 4500   | Telegram                       |
   | `tools/tiktok-backend`       | 4600   | TikTok                         |

   X no necesita backend: es cliente público.
3. App, desde `examples/demo`:

   ```sh
   composer install
   cp .env.example .env && php artisan key:generate
   php artisan native:install        # una vez (descarga el runtime de PHP)
   php artisan native:run ios        # o android
   ```

En el emulador de Android la demo llega a los mocks por `10.0.2.2`; en el simulador de iOS, por `localhost`.

**Si cambias algo y la app no lo refleja**: NativePHP solo vuelve a extraer el código de la app cuando cambia la versión. Desinstala la app del simulador o emulador, o sube `NATIVEPHP_APP_VERSION`.

## Probar con cuentas reales

1. En `examples/demo/.env`, llena el client ID del proveedor (`DEMO_LINKEDIN_CLIENT_ID`, …) y la URL pública del túnel de su backend (`DEMO_LINKEDIN_BACKEND_URL`, …). X no usa backend. Ver `config/social-demo.php`.
2. Copia `tools/<proveedor>-backend/.env.example` a `.env` y llena el secret. Nunca lo subas.
3. Levanta el backend y un túnel: `cloudflared tunnel --url http://localhost:<puerto>`. Registra `<URL del túnel>/auth/<proveedor>/callback` en el proveedor (la URL cambia en cada arranque).

La demo usa los mismos esquemas que los ejemplos de React Native y Flutter (`linkedinexample://`, `xloginexample://`, …), así que los `APP_CALLBACK` de los backends y los redirect ya registrados siguen sirviendo. En Android, eso significa que solo una de las apps demo de la familia puede estar instalada a la vez: si hay dos, Android pregunta con cuál abrir el redirect.

## Qué probar en dispositivo antes de abrir un PR

| Caso                            | Resultado esperado                             |
| ------------------------------- | ---------------------------------------------- |
| "Sign in with …" y aprobar      | `SIGNED IN (<proveedor>)` con `id` y `email`   |
| Cerrar el navegador sin aprobar | `ERROR CANCELLED: The user closed the browser` |
| Pulsar "Deny" en el mock        | `ERROR CANCELLED: The user denied the request` |

## Antes de enviar

- `composer test` en `packages/social-login`.
- Commits con [Conventional Commits](https://www.conventionalcommits.org/): `feat: …`, `fix(android): …`.
- Código y comentarios en inglés; documentación en español.
- Un cambio de comportamiento lleva su prueba.

## Publicar

Packagist toma el paquete de un repositorio con `composer.json` en la raíz, así que `packages/social-login` se publica a través de un espejo de solo lectura: [`nativephp-social-login-plugin`](https://github.com/franciscodlx/nativephp-social-login-plugin). No hagas commits ahí.

1. En tu PR, agrega la versión nueva arriba de `packages/social-login/CHANGELOG.md` (`## 0.1.1`) con sus cambios. El job "Version that would be released" del CI avisa si el merge va a publicar.
2. Al hacer merge, cuando el CI de `main` pasa, el workflow `Release` hace `git subtree split` del paquete y lo empuja a `main` del espejo (`dev-main` en Packagist). Si la versión del CHANGELOG no tiene tag en el espejo, también empuja `v<versión>` y crea el release de GitHub aquí.
3. Packagist se actualiza con el webhook del espejo.
