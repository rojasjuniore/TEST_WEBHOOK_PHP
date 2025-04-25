# Webhook Handler para Pagos en Criptomonedas

> Sistema de procesamiento de pagos en Bitcoin mediante webhooks con verificación de seguridad HMAC SHA256

[![PHP Version](https://img.shields.io/badge/PHP-8.4.6-blue.svg)](https://php.net)
[![Node.js Version](https://img.shields.io/badge/Node.js-v20.17.0-green.svg)](https://nodejs.org)

## Cómo Funciona

1. **Recepción del Webhook**
   - El servidor recibe una petición POST con datos de pago en Bitcoin
   - Los datos vienen en formato JSON con información de la transacción
   - Se incluye una firma HMAC SHA256 en el header `X-Signature`

2. **Verificación de Seguridad**
   - Se verifica la firma HMAC usando el secreto compartido
   - Si la firma no coincide, se rechaza la petición
   - Se valida que el JSON sea válido

3. **Procesamiento de Datos**
   - Se extrae información importante como:
     - ID de la orden
     - Estado del pago
     - Montos en USD y BTC
     - Dirección de depósito
     - Detalles de transacciones

4. **Respuesta**
   - Si todo es correcto: devuelve éxito (200) con datos procesados
   - Si hay error: devuelve código de error (400/401) con mensaje

## Ejemplo de Uso

1. Inicia el servidor:
```bash
npm run start
```

2. Simula un webhook:
```bash
npm run simulate
```

O ejecuta todo junto:
```bash
npm run dev
```

## Formato de Datos

### Webhook Recibido
```json
{
  "orderId": "da8972b7-6471-4a8d-a05a-1bdfafeadfa4",
  "status": "pending",
  "depositAddress": "bc1qz5lmrumd8utptrkld6vm36625qfnrdedk52cvh",
  "usdAmount": "10.00",
  "btcAmount": "0.00010718",
  "paidAmount": {
    "api": "blockstream",
    "confirmedBalance": 0,
    "pendingBalance": 0,
    "totalReceived": 0,
    "totalSpent": 0,
    "unconfirmedTxCount": 0,
    "availableBalance": 0,
    "hasPendingTransactions": false
  }
}
```

### Respuesta Exitosa
```json
{
  "success": true,
  "message": "Webhook procesado correctamente",
  "orderId": "da8972b7-6471-4a8d-a05a-1bdfafeadfa4",
  "status": "pending",
  "usdAmount": "10.00",
  "btcAmount": "0.00010718"
}
```

## Características

- Verificación de firmas HMAC SHA256 para seguridad
- Procesamiento de pagos en Bitcoin
- Simulador de webhook para pruebas locales
- Documentación detallada del formato de datos
- Manejo de errores y logging

## Requisitos

- PHP 8.4.6 (con Zend OPcache)
- Node.js v20.17.0
- npm (incluido con Node.js)

## Instalación

1. Clona el repositorio:
```bash
git clone https://github.com/rojasjuniore/TEST_WEBHOOK_PHP.git
cd TEST_WEBHOOK_PHP
```

2. Instala las dependencias de Node.js:
```bash
npm install
```

## Estructura del Proyecto

```
.
├── webhook.php          # Manejador principal del webhook
├── simulate_webhook.js  # Simulador de webhook
├── package.json         # Configuración de Node.js
└── README.md           # Este archivo
```

## Seguridad

- Se utiliza HMAC SHA256 para verificar la autenticidad de los webhooks
- El secreto debe mantenerse seguro y no compartirse
- Se recomienda usar HTTPS en producción

## Desarrollo

Para contribuir al proyecto, sigue estos pasos:

1. **Prepara tu entorno**
   ```bash
   git clone https://github.com/rojasjuniore/TEST_WEBHOOK_PHP.git
   cd TEST_WEBHOOK_PHP
   npm install
   ```

2. **Crea una rama para tu feature**
   ```bash
   git checkout -b feature/nombre-de-tu-feature
   ```

3. **Haz tus cambios y prueba**
   ```bash
   npm run dev  # Para probar tus cambios
   ```

4. **Haz commit de tus cambios**
   ```bash
   git add .
   git commit -m "feat: descripción clara de tus cambios"
   ```

5. **Sube tus cambios**
   ```bash
   git push origin feature/nombre-de-tu-feature
   ```

6. **Abre un Pull Request**
   - Ve a https://github.com/rojasjuniore/TEST_WEBHOOK_PHP
   - Haz clic en "New Pull Request"
   - Selecciona tu rama
   - Describe tus cambios
   - Espera la revisión

### Convenciones de Commits
- `feat:` para nuevas características
- `fix:` para correcciones de errores
- `docs:` para cambios en la documentación
- `style:` para cambios de formato
- `refactor:` para refactorización de código
- `test:` para pruebas
- `chore:` para tareas de mantenimiento

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles. 