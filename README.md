# Webhook Handler para Pagos en Criptomonedas

Este proyecto implementa un manejador de webhooks para procesar pagos en criptomonedas, específicamente Bitcoin. Incluye un simulador para probar el webhook localmente.

## Características

- Verificación de firmas HMAC SHA256 para seguridad
- Procesamiento de pagos en Bitcoin
- Simulador de webhook para pruebas locales
- Documentación detallada del formato de datos
- Manejo de errores y logging

## Requisitos

- PHP 7.4 o superior
- Node.js 14 o superior
- npm (incluido con Node.js)

## Instalación

1. Clona el repositorio:
```bash
git clone [URL_DEL_REPOSITORIO]
cd [NOMBRE_DEL_DIRECTORIO]
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

## Uso

### Iniciar el Servidor PHP

```bash
npm run start
```

### Simular un Webhook

```bash
npm run simulate
```

### Ejecutar Todo en un Solo Comando

```bash
npm run dev
```

## Formato del Webhook

### Payload Esperado

```json
{
  "orderId": "string",
  "status": "string",
  "depositAddress": "string",
  "usdAmount": "string",
  "btcAmount": "string",
  "paidAmount": {
    "api": "string",
    "confirmedBalance": number,
    "pendingBalance": number,
    "totalReceived": number,
    "totalSpent": number,
    "unconfirmedTxCount": number,
    "availableBalance": number,
    "hasPendingTransactions": boolean
  },
  "createdAt": "string",
  "updatedAt": "string",
  "statusMessage": "string",
  "warningMessage": "string|null",
  "paidPercentage": "string",
  "remainingAmount": "string",
  "transactionDetails": []
}
```

### Headers Requeridos

- `X-Signature`: Firma HMAC SHA256 del payload
- `Content-Type`: application/json

### Respuestas

#### Éxito (200)
```json
{
  "success": true,
  "message": "Webhook procesado correctamente",
  "orderId": "string",
  "status": "string",
  "usdAmount": "string",
  "btcAmount": "string"
}
```

#### Error (400/401)
```json
{
  "error": "string"
}
```

## Seguridad

- Se utiliza HMAC SHA256 para verificar la autenticidad de los webhooks
- El secreto debe mantenerse seguro y no compartirse
- Se recomienda usar HTTPS en producción

## Desarrollo

Para contribuir al proyecto:

1. Haz fork del repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Haz commit de tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles. 