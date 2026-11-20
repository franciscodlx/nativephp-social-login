# nativephp-social-login

Login social para [NativePHP Mobile](https://nativephp.com): OAuth 2.0 + PKCE en el navegador del sistema, sin `client_secret` en la app y con la misma API para todos los proveedores. Es la versión para NativePHP de [rn-social-login](https://github.com/franciscodlx/rn-social-login), [flutter_social_login](https://github.com/franciscodlx/flutter_social_login), [capacitor-social-login](https://github.com/franciscodlx/capacitor-social-login) y [nativescript-social-login](https://github.com/franciscodlx/nativescript-social-login).

```php
SocialLogin::signIn(SocialLogin::linkedin(), ['clientId' => $id, 'redirectUri' => $uri]);
// #[On(AuthSessionCompleted::class)] → SocialLogin::complete($url)
```

## Paquete

| Paquete                                                                  | Qué hace                                                                 | Estado           |
| ------------------------------------------------------------------------ | ------------------------------------------------------------------------ | ---------------- |
| [`franciscodlxdev/nativephp-social-login`](packages/social-login)         | Plugin de NativePHP (Swift + Kotlin) y los 10 proveedores en PHP          | Probado con mock |

"Probado con mock": iOS (simulador) y Android (emulador) contra `tools/mock-idp` y los backends de `tools/`. El flujo es el mismo que ya se probó con cuentas reales en React Native y Flutter.

A diferencia de las versiones en TypeScript, aquí todo va en un solo paquete de Composer. Un plugin de NativePHP es un paquete, y en PHP los proveedores son solo configuración (endpoints, scopes y mapeo del perfil), como los drivers de Socialite. Entra External ID (native auth) todavía no está en esta versión.

## Estructura

```
packages/social-login       plugin: PHP (flujo y proveedores), Swift, Kotlin, manifiesto nativephp.json
examples/demo               app Laravel + NativePHP 4 (pantalla nativa) con todos los proveedores
tools/mock-idp              proveedor OAuth/OIDC local, sin credenciales reales
tools/<proveedor>-backend   backends de ejemplo: redirect https y canje con el secret
```

## Desarrollo

Requiere PHP 8.3+, Composer, Node 22+ (mocks y backends), Xcode, y Android SDK con JDK 17.

```sh
cd packages/social-login && composer install && composer test
```

Probar el flujo completo, sin cuenta en ningún proveedor: ver [CONTRIBUTING.md](CONTRIBUTING.md). Vulnerabilidades: [SECURITY.md](SECURITY.md).

## Licencia

MIT
