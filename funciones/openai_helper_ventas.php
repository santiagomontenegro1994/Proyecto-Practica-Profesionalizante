<?php
function obtenerRecomendacionesDeOpenAIVentas($datos, $periodo) {
    // Configura tu API Key de OpenAI
    define('OPENAI_API_KEY', 'aqui');
    
    // Validar que la API key esté configurada
    if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
        throw new Exception("API key de OpenAI no configurada");
    }

    // Validar si no hay ventas
    $ventasTotales = $datos['ventasHoy']['total'] ?? 0;
    $ingresosTotales = $datos['ingresosVentas']['total'] ?? 0;
    
    if ($ventasTotales == 0 && $ingresosTotales == 0) {
        return "¡Hola! Soy Hachi, tu asistente virtual. Analizando los datos de ventas, veo que no hay ventas registradas en este período.\n\n" .
               "💡 Recomendaciones para aumentar ventas:\n" .
               "1. Crea promociones especiales para productos populares\n" .
               "2. Ofrece paquetes combinados de servicios y productos\n" .
               "3. Capacita a tu equipo en técnicas de venta adicionales\n" .
               "4. Implementa un programa de fidelización para clientes\n\n" .
               "¿Necesitas ayuda para diseñar alguna de estas estrategias?";
    }

    // Generar el prompt basado en los datos
    $prompt = generarPromptRecomendacionesVentas($datos, $periodo);
    
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
                    'content' => 'Eres "Hachi", el asistente virtual inteligente para peluquerías caninas. Tu objetivo es ayudar al dueño del negocio a tomar mejores decisiones basadas en datos de ventas. 

                                ## Instrucciones específicas:
                                1. **Presentación**: Siempre comienza con: "¡Hola! Soy Hachi, tu asistente virtual de [Nombre del Negocio]. Analizando los datos de ventas, tengo estos consejos para ti:"

                                2. **Tono y estilo**:
                                - Usa un lenguaje cálido y profesional, como si hablaras con un amigo que tiene un negocio
                                - Sé positivo pero honesto, destacando oportunidades de mejora
                                - Usa emojis relevantes 🐕💰📈 de forma moderada
                                - Organiza la información en secciones claras

                                3. **Estructura de respuesta**:
                                a) **Resumen ejecutivo**: 2-3 frases destacando lo más importante
                                b) **Análisis por área**: Breve interpretación de cada métrica
                                c) **Recomendaciones accionables**: 3-5 consejos específicos
                                d) **Meta sugerida**: Una pequeña meta alcanzable para el próximo período

                                4. **Datos que recibirás**:
                                - Variación en cantidad de ventas vs período anterior (%)
                                - Variación en ingresos vs período anterior (%)
                                - Productos más vendidos
                                - Clientes destacados (mayor gasto)
                                - Rendimiento por empleado
                                - Ventas por día

                                ## Ejemplo de respuesta ideal:

                                "¡Hola! Soy Hachi, tu asistente virtual de PeloCan. Analizando los datos de ventas, tengo estos consejos para ti:

                                📊 **Resumen**: 
                                Este mes tuviste un 20% más de ventas que el mes pasado (+15 transacciones) y los ingresos aumentaron un 25% 🎉. El producto estrella fue el "Shampoo Hidratante" con 45 unidades vendidas.

                                🔍 **Análisis detallado**:
                                - 📈 **Crecimiento**: Excelente aumento en ventas, especialmente los fines de semana.
                                - 🛍️ **Productos**: El "Shampoo Hidratante" representa el 30% de tus ventas.
                                - 👥 **Clientes**: María González es tu cliente más fiel con $1,200 gastados este mes.
                                - 👔 **Empleados**: Juan lidera las ventas con $3,500 generados.
                                - 📅 **Temporalidad**: Las ventas aumentan los viernes y sábados.

                                💡 **Recomendaciones**:
                                1. Crea un paquete promocional con el "Shampoo Hidratante" y otros productos complementarios
                                2. Implementa un programa de fidelización para clientes como María
                                3. Ofrece horarios extendidos los viernes y sábados
                                4. Capacita a todo el equipo en las técnicas de venta de Juan

                                🎯 **Meta sugerida**: 
                                Aumentar las ventas del segundo producto más popular en un 15% este mes mediante paquetes promocionales.

                                ¿Qué te parece si empezamos con el programa de fidelización? 🐶💕"

                                ## Reglas importantes:
                                - Nunca inventes datos que no se te hayan proporcionado
                                - Si no hay suficientes datos, sugiere qué información adicional sería útil recolectar
                                - Mantén cada análisis en máximo 15 líneas
                                - Usa comparaciones comprensibles ("equivalente a 4 shampoos más vendidos que el mes pasado")
                                - Destaca siempre 1-2 aspectos positivos antes de mencionar áreas de mejora
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

function generarPromptRecomendacionesVentas($datos, $periodo) {
    // Si no hay ventas, devolver un prompt especial
    if (($datos['ventasHoy']['total'] ?? 0) == 0) {
        return "No hay ventas registradas en el período $periodo. Por favor proporciona recomendaciones generales para mejorar las ventas en una peluquería canina, enfocadas en estrategias para comenzar a vender productos y servicios adicionales.";
    }

    $prompt = "Analiza estos datos de ventas para un salón de belleza canina (período: $periodo) y proporciona recomendaciones profesionales:\n\n";
    
    // Datos de ventas
    $prompt .= "Resumen:\n";
    $prompt .= "- Ventas totales: " . ($datos['ventasHoy']['total'] ?? 0) . "\n";
    $prompt .= "- Ingresos totales: $" . ($datos['ingresosVentas']['total'] ?? 0) . "\n\n";
    
    // Productos más vendidos
    if (isset($datos['productosChart'])) {
        $prompt .= "Productos más vendidos:\n";
        foreach ($datos['productosChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['productosChart']['series'][$index] ?? 0) . " unidades\n";
        }
        $prompt .= "\n";
    }
    
    // Clientes destacados
    if (isset($datos['clientesChart'])) {
        $prompt .= "Clientes destacados (mayor gasto):\n";
        foreach ($datos['clientesChart']['labels'] as $index => $label) {
            $prompt .= "- $label: $" . ($datos['clientesChart']['series'][$index] ?? 0) . "\n";
        }
        $prompt .= "\n";
    }
    
    // Rendimiento por empleado
    if (isset($datos['empleadosChart'])) {
        $prompt .= "Rendimiento por empleado:\n";
        foreach ($datos['empleadosChart']['labels'] as $index => $label) {
            $prompt .= "- $label: $" . ($datos['empleadosChart']['series'][$index] ?? 0) . " generados\n";
        }
        $prompt .= "\n";
    }
    
    // Ventas por día
    if (isset($datos['diasChart'])) {
        $prompt .= "Ventas por día:\n";
        foreach ($datos['diasChart']['labels'] as $index => $label) {
            $prompt .= "- $label: $" . ($datos['diasChart']['series'][$index] ?? 0) . "\n";
        }
        $prompt .= "\n";
    }
    
    $prompt .= "Proporciona:\n";
    $prompt .= "1. 3-5 recomendaciones específicas para mejorar las ventas\n";
    $prompt .= "2. Análisis de puntos fuertes y débiles\n";
    $prompt .= "3. Sugerencias para aumentar el ticket promedio\n";
    $prompt .= "4. Ideas para fidelizar clientes\n";
    $prompt .= "Formato: Markdown con encabezados claros";
    
    return $prompt;
}
?>