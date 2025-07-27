<?php
function obtenerRecomendacionesDeOpenAICompras($datos, $periodo) {
    // Configura tu API Key de OpenAI
    define('OPENAI_API_KEY', 'tuapi');
    
    // Validar que la API key esté configurada
    if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
        throw new Exception("API key de OpenAI no configurada");
    }

    // Validar si no hay compras
    $comprasTotales = $datos['comprasHoy']['total'] ?? 0;
    $gastosTotales = $datos['gastosCompras']['total'] ?? 0;
    
    if ($comprasTotales == 0 && $gastosTotales == 0) {
        return "¡Hola! Soy Hachi, tu asistente virtual. Analizando los datos de compras, veo que no hay compras registradas en este período.\n\n" .
               "💡 Recomendaciones para gestión de compras:\n" .
               "1. Revisa el inventario para identificar productos que necesitan reposición\n" .
               "2. Evalúa relaciones con proveedores para mejores condiciones de pago\n" .
               "3. Considera compras programadas para optimizar costos\n" .
               "4. Analiza productos con bajo movimiento para ajustar compras futuras\n\n" .
               "¿Necesitas ayuda para diseñar alguna de estas estrategias?";
    }

    // Generar el prompt basado en los datos
    $prompt = generarPromptRecomendacionesCompras($datos, $periodo);
    
    // Configurar la solicitud a la API con timeout más largo
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres "Hachi", el asistente virtual inteligente para peluquerías caninas. Tu objetivo es ayudar al dueño del negocio a optimizar las compras basadas en datos. 

                                ## Instrucciones específicas:
                                1. **Presentación**: Siempre comienza con: "¡Hola! Soy Hachi, tu asistente virtual de [Nombre del Negocio]. Analizando los datos de compras, tengo estos consejos para ti:"

                                2. **Tono y estilo**:
                                - Usa un lenguaje profesional y analítico
                                - Sé objetivo, destacando oportunidades de optimización
                                - Usa emojis relevantes 📦💰📉 de forma moderada
                                - Organiza la información en secciones claras

                                3. **Estructura de respuesta**:
                                a) **Resumen ejecutivo**: 2-3 frases destacando lo más importante
                                b) **Análisis por área**: Breve interpretación de cada métrica
                                c) **Recomendaciones accionables**: 3-5 consejos específicos
                                d) **Meta sugerida**: Una pequeña meta alcanzable para el próximo período

                                4. **Datos que recibirás**:
                                - Variación en cantidad de compras vs período anterior (%)
                                - Variación en gastos vs período anterior (%)
                                - Artículos más comprados
                                - Compras por proveedor (monto)
                                - Evolución de compras
                                - Frecuencia de compras por proveedor

                                ## Ejemplo de respuesta ideal:

                                "¡Hola! Soy Hachi, tu asistente virtual de PeloCan. Analizando los datos de compras, tengo estos consejos para ti:

                                📊 **Resumen**: 
                                Este mes realizaste 15 compras (20% más que el mes pasado) con un gasto total de $2,500 (+15%). El artículo más comprado fue "Shampoo Hidratante" con 45 unidades.

                                🔍 **Análisis detallado**:
                                - 📈 **Crecimiento**: Buen aumento en compras, especialmente con el proveedor "PetSupplies".
                                - 🛍️ **Artículos**: El "Shampoo Hidratante" representa el 30% de tus compras.
                                - 🏭 **Proveedores**: "PetSupplies" es tu principal proveedor con $1,200 en compras.
                                - 📅 **Temporalidad**: Las compras se concentran los primeros días del mes.

                                💡 **Recomendaciones**:
                                1. Negocia descuentos por volumen con "PetSupplies" para el Shampoo Hidratante
                                2. Programa compras estratégicas para aprovechar mejores precios
                                3. Diversifica proveedores para artículos clave
                                4. Revisa inventario para evitar compras innecesarias

                                🎯 **Meta sugerida**: 
                                Reducir el costo promedio del Shampoo Hidratante en un 10% este mes mediante negociación con proveedores.

                                ¿Qué te parece si empezamos con un análisis de proveedores alternativos? 🐶💼"

                                ## Reglas importantes:
                                - Nunca inventes datos que no se te hayan proporcionado
                                - Si no hay suficientes datos, sugiere qué información adicional sería útil recolectar
                                - Mantén cada análisis en máximo 15 líneas
                                - Usa comparaciones comprensibles ("equivalente a 4 shampoos más comprados que el mes pasado")
                                - Destaca oportunidades de optimización de costos
                                - Incluye una llamada a la acción simple al final'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 500
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY
        ],
        CURLOPT_TIMEOUT => 30, // Aumentar timeout a 30 segundos
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        throw new Exception("Error de conexión con OpenAI: " . $error_msg);
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        throw new Exception("OpenAI API returned HTTP code: " . $httpCode);
    }

    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        throw new Exception("Error de OpenAI: " . ($data['error']['message'] ?? 'Error desconocido'));
    }

    if (!isset($data['choices'][0]['message']['content'])) {
        throw new Exception("La respuesta de OpenAI no tiene el formato esperado");
    }

    return $data['choices'][0]['message']['content'];
}

function generarPromptRecomendacionesCompras($datos, $periodo) {
    // Si no hay compras, devolver un prompt especial
    if (($datos['comprasHoy']['total'] ?? 0) == 0) {
        return "No hay compras registradas en el período $periodo. Por favor proporciona recomendaciones generales para optimizar la gestión de compras en una peluquería canina, enfocadas en estrategias para realizar compras más inteligentes y ahorrar costos.";
    }

    $prompt = "Analiza estos datos de compras para un salón de belleza canina (período: $periodo) y proporciona recomendaciones profesionales para optimizar las compras:\n\n";
    
    // Datos de compras
    $prompt .= "Resumen:\n";
    $prompt .= "- Compras totales: " . ($datos['comprasHoy']['total'] ?? 0) . "\n";
    $prompt .= "- Gastos totales: $" . ($datos['gastosCompras']['total'] ?? 0) . "\n\n";
    
    // Artículos más comprados
    if (isset($datos['articulosChart'])) {
        $prompt .= "Artículos más comprados:\n";
        foreach ($datos['articulosChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['articulosChart']['series'][$index] ?? 0) . " unidades\n";
        }
        $prompt .= "\n";
    }
    
    // Compras por proveedor
    if (isset($datos['proveedoresChart'])) {
        $prompt .= "Compras por proveedor (monto total):\n";
        foreach ($datos['proveedoresChart']['labels'] as $index => $label) {
            $prompt .= "- $label: $" . ($datos['proveedoresChart']['series'][$index] ?? 0) . "\n";
        }
        $prompt .= "\n";
    }
    
    // Evolución de compras
    if (isset($datos['evolucionChart'])) {
        $prompt .= "Evolución de compras:\n";
        foreach ($datos['evolucionChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['evolucionChart']['series'][$index] ?? 0) . " unidades\n";
        }
        $prompt .= "\n";
    }
    
    // Frecuencia de compras por proveedor
    if (isset($datos['frecuenciaChart'])) {
        $prompt .= "Frecuencia de compras por proveedor:\n";
        foreach ($datos['frecuenciaChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['frecuenciaChart']['series'][$index] ?? 0) . " compras\n";
        }
        $prompt .= "\n";
    }
    
    $prompt .= "Proporciona:\n";
    $prompt .= "1. 3-5 recomendaciones específicas para optimizar compras\n";
    $prompt .= "2. Análisis de oportunidades de ahorro\n";
    $prompt .= "3. Sugerencias para negociación con proveedores\n";
    $prompt .= "4. Ideas para mejorar la gestión de inventario\n";
    $prompt .= "Formato: Markdown con encabezados claros";
    
    return $prompt;
}
?>