const crypto = require('crypto');
const axios = require('axios');

// El mismo secreto que está en webhook.php
const webhookSecret = 'ernIsmoNYOpoSCARyNtFUnkLeNTyPHeleweaNoRYOnoXEnTATE';

// Payload del webhook
const payload = {
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
  },
  "createdAt": "2025-04-24T01:38:51.179Z",
  "updatedAt": "2025-04-24T02:09:01.405Z",
  "statusMessage": "La orden está pendiente de pago",
  "warningMessage": null,
  "paidPercentage": "0.00",
  "remainingAmount": "0.00010718",
  "transactionDetails": []
};

// Convertir el payload a string
const payloadString = JSON.stringify(payload);

// Generar la firma HMAC SHA256
const signature = crypto
  .createHmac('sha256', webhookSecret)
  .update(payloadString)
  .digest('hex');

console.log('Firma generada:', signature);

// URL de tu endpoint PHP local
const webhookUrl = 'http://127.0.0.1:8000/webhook.php';

// Enviar el webhook
async function sendWebhook() {
  try {
    console.log('Intentando enviar webhook a:', webhookUrl);
    console.log('Payload a enviar:', payloadString);
    console.log('Headers:', {
      'Content-Type': 'application/json',
      'X-Signature': signature
    });

    const response = await axios.post(webhookUrl, payload, {
      headers: {
        'Content-Type': 'application/json',
        'X-Signature': signature
      },
      timeout: 5000 // 5 segundos de timeout
    });

    console.log('Respuesta del servidor:', response.data);
  } catch (error) {
    console.error('Error al enviar el webhook:');
    if (error.response) {
      // El servidor respondió con un código de estado fuera del rango 2xx
      console.error('Status:', error.response.status);
      console.error('Data:', error.response.data);
    } else if (error.request) {
      // La solicitud fue hecha pero no se recibió respuesta
      console.error('No se recibió respuesta del servidor');
      console.error('Request:', error.request);
    } else {
      // Ocurrió un error al configurar la solicitud
      console.error('Error:', error.message);
    }
  }
}

sendWebhook(); 