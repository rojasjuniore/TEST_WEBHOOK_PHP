<?php
/**
 * Webhook Handler para Procesamiento de Pagos en Criptomonedas
 * 
 * Este script maneja webhooks de pagos en criptomonedas, verificando la autenticidad
 * mediante firmas HMAC SHA256 y procesando la información de la transacción.
 * 
 * Estructura del Payload Esperado:
 * @example
 * {
 *   "orderId": string,        // UUID de la orden (ej: "da8972b7-6471-4a8d-a05a-1bdfafeadfa4")
 *   "status": string,         // Estado de la orden (ej: "pending", "partial", "completed")
 *   "depositAddress": string, // Dirección BTC para el depósito
 *   "usdAmount": string,      // Monto en USD (ej: "10.00")
 *   "btcAmount": string,      // Monto en BTC (ej: "0.00010718")
 *   "paidAmount": {
 *     "api": string,          // API utilizada (ej: "blockstream")
 *     "confirmedBalance": number,    // Balance confirmado
 *     "pendingBalance": number,      // Balance pendiente
 *     "totalReceived": number,       // Total recibido
 *     "totalSpent": number,          // Total gastado
 *     "unconfirmedTxCount": number,  // Número de transacciones sin confirmar
 *     "availableBalance": number,    // Balance disponible
 *     "hasPendingTransactions": boolean // Indica si hay transacciones pendientes
 *   },
 *   "createdAt": string,     // Fecha de creación (ISO 8601)
 *   "updatedAt": string,     // Fecha de actualización (ISO 8601)
 *   "statusMessage": string,  // Mensaje descriptivo del estado
 *   "warningMessage": string|null, // Mensaje de advertencia si existe
 *   "paidPercentage": string,      // Porcentaje pagado (ej: "0.00")
 *   "remainingAmount": string,      // Monto restante en BTC
 *   "transactionDetails": [         // Detalles de transacciones
 *     {
 *       "transactionHash": string,  // Hash de la transacción
 *       "amount": number,           // Monto de la transacción
 *       "timestamp": string,        // Fecha de la transacción (ISO 8601)
 *       "status": string,          // Estado de la transacción
 *       "confirmations": number,   // Número de confirmaciones
 *       "blockHeight": number      // Altura del bloque
 *     }
 *   ]
 * }
 * 
 * Headers Requeridos:
 * - X-Signature: Firma HMAC SHA256 del payload
 * - Content-Type: application/json
 * 
 * Respuestas:
 * - 200: Webhook procesado correctamente
 * - 400: JSON inválido
 * - 401: Firma no proporcionada o inválida
 */

/**
 * Verifica la firma HMAC SHA256 de un webhook.
 *
 * @param string $payload El cuerpo de la solicitud (raw string).
 * @param string $signature La firma recibida en el header X-Signature.
 * @param string $secret El secreto compartido utilizado para generar la firma.
 * @return bool Devuelve true si la firma es válida, false en caso contrario.
 */
function verifyWebhookSignature($payload, $signature, $secret)
{
    // Calcula la firma HMAC SHA256 esperada utilizando el payload y el secreto.
    // Se utiliza trim() en el secreto para eliminar posibles espacios en blanco.
    $expectedSignature = hash_hmac('sha256', $payload, trim($secret));

    // Registra el payload, la firma esperada y la firma recibida para debugging.
    error_log("Payload recibido: " . $payload);
    error_log("Firma esperada: " . $expectedSignature);
    error_log("Firma recibida: " . $signature);

    // Compara de forma segura la firma esperada con la firma recibida.
    // hash_equals() es resistente a ataques de tiempo.
    return hash_equals($expectedSignature, $signature);
}

// --- Configuración ---

// Secreto del webhook. Debe coincidir con el secreto configurado en el servicio que envía el webhook.
$webhookSecret = 'ernIsmoNYOpoSCARyNtFUnkLeNTyPHeleweaNoRYOnoXEnTATE';

/*
 * Ejemplo de la estructura del payload JSON esperado (basado en el ejemplo de prueba):
 * {
 *   "orderId": "37b93772-c3c9-4ed7-bc80-6d95308680f7",
 *   "status": "partial",
 *   "depositAddress": "bc1qvervepgggtj4wtraw2q2zg6yna3w8nevxx4sf6",
 *   "usdAmount": "20.00",
 *   "btcAmount": "0.00022004",
 *   "paidAmount": {
 *     "api": "blockstream",
 *     "confirmedBalance": 0.00008,
 *     "pendingBalance": 0,
 *     "totalReceived": 0.00008,
 *     "totalSpent": 0,
 *     "unconfirmedTxCount": 0,
 *     "availableBalance": 0.00008,
 *     "hasPendingTransactions": false
 *   },
 *   "createdAt": "2025-04-22T17:08:36.422Z",
 *   "updatedAt": "2025-04-22T17:32:01.629Z",
 *   "statusMessage": "La orden está parcialmente pagada (36.36%)",
 *   "warningMessage": "Falta por pagar 0.00014004 BTC (63.64%)",
 *   "paidPercentage": "36.36",
 *   "remainingAmount": "0.00014004",
 *   "transactionDetails": [
 *     {
 *       "transactionHash": "65627cace5a7cf3864773cf9ffa5e0620adee6ce802ba9865b00135477c69956",
 *       "amount": 0.00008,
 *       "timestamp": "2025-04-22T17:31:51.000Z",
 *       "status": "completed",
 *       "confirmations": 1,
 *       "blockHeight": 893536
 *     }
 *   ],
 *   "transactions": [
 *     {
 *       "hash": null,
 *       "amount": 0.00008,
 *       "timestamp": "2025-04-22T17:32:01.628Z",
 *       "status": "partial"
 *     }
 *   ],
 *   "currentBalance": 0.00008,
 *   "requiredAmount": 0.00022004
 * }
 */

// --- Procesamiento de la Solicitud ---

// Obtiene el cuerpo completo (raw) de la solicitud POST.
$payload = file_get_contents('php://input');
// Obtiene la firma del header HTTP 'X-Signature'. Si no existe, se asigna un string vacío.
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

// Registra el payload recibido y la firma para debugging inicial.
error_log("Webhook recibido - Payload: " . $payload);
error_log("Webhook recibido - Firma: " . $signature);
error_log("Headers recibidos: " . print_r($_SERVER, true));

// Verifica si la firma fue proporcionada. Si no, devuelve un error 401.
if (empty($signature)) {
    error_log("Error: Firma no proporcionada en el webhook");
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Firma no proporcionada']);
    exit; // Termina la ejecución del script.
}

// --- Verificación de la Firma y Procesamiento del Payload ---

// Llama a la función para verificar la firma.
if (verifyWebhookSignature($payload, $signature, $webhookSecret)) {
    // La firma es válida.
    
    // Decodifica el payload JSON en un array asociativo.
    $data = json_decode($payload, true);
    
    // Verifica si hubo errores al decodificar el JSON.
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error: Payload JSON inválido - " . json_last_error_msg());
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Payload JSON inválido']);
        exit;
    }

    // Registra que el webhook fue procesado exitosamente, incluyendo el ID de la orden si está disponible.
    error_log("Webhook procesado exitosamente para orden: " . ($data['orderId'] ?? 'desconocido'));
    error_log("Estado de la orden: " . ($data['status'] ?? 'desconocido'));
    error_log("Monto USD: " . ($data['usdAmount'] ?? 'desconocido'));
    error_log("Monto BTC: " . ($data['btcAmount'] ?? 'desconocido'));
    
    /**
     * Respuesta exitosa del webhook
     * @return array {
     *   success: boolean,     // Siempre true para respuestas exitosas
     *   message: string,      // Mensaje descriptivo del resultado
     *   orderId: string,      // ID de la orden procesada
     *   status: string,       // Estado actual de la orden
     *   usdAmount: string,    // Monto en USD
     *   btcAmount: string     // Monto en BTC
     * }
     */
    echo json_encode([
        'success' => true, 
        'message' => 'Webhook procesado correctamente',
        'orderId' => $data['orderId'] ?? null,
        'status' => $data['status'] ?? null,
        'usdAmount' => $data['usdAmount'] ?? null,
        'btcAmount' => $data['btcAmount'] ?? null
    ]);
} else {
    /**
     * Respuesta de error por firma inválida
     * @return array {
     *   error: string        // Mensaje de error
     * }
     */
    error_log("Error: Firma inválida en el webhook");
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Firma inválida']);
}
