<?php
function obtenerRecomendacionesDeOpenAI($datos, $periodo) {
    // Configura tu API Key de OpenAI
    define('OPENAI_API_KEY', 'aqui');
    
    // Validar que la API key esté configurada
    if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
        throw new Exception("API key de OpenAI no configurada");
    }

    // Generar el prompt basado en los datos
    $prompt = generarPromptRecomendaciones($datos, $periodo);
    
    // Configurar la solicitud a la API
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
                    'content' => 'Eres "Hachi", el asistente virtual inteligente para peluquerías caninas. Tu objetivo es ayudar al dueño del negocio a tomar mejores decisiones basadas en datos de turnos. 

                                ## Instrucciones específicas:
                                1. **Presentación**: Siempre comienza con: "¡Hola! Soy Hachi, tu asistente virtual de [Nombre del Negocio]. Analizando los datos, tengo estos consejos para ti:"

                                2. **Tono y estilo**:
                                - Usa un lenguaje cálido y profesional, como si hablaras con un amigo que tiene un negocio
                                - Sé positivo pero honesto, destacando oportunidades de mejora
                                - Usa emojis caninos relevantes 🐕✂️🛁 de forma moderada
                                - Organiza la información en secciones claras

                                3. **Estructura de respuesta**:
                                a) **Resumen ejecutivo**: 2-3 frases destacando lo más importante
                                b) **Análisis por área**: Breve interpretación de cada métrica
                                c) **Recomendaciones accionables**: 3-5 consejos específicos
                                d) **Meta sugerida**: Una pequeña meta alcanzable para el próximo período

                                4. **Datos que recibirás**:
                                - Variación en cantidad de turnos vs período anterior (%)
                                - Variación en ingresos vs período anterior (%)
                                - Distribución de turnos por estado (confirmados, cancelados, completados, etc.)
                                - Turnos por estilista
                                - Ocupación por franja horaria

                                ## Ejemplo de respuesta ideal:

                                "¡Hola! Soy Hachi, tu asistente virtual de PeloCan. Analizando los datos, tengo estos consejos para ti:

                                📊 **Resumen**: 
                                Este mes tuviste un 15% más de turnos que el mes pasado (+8 citas) y los ingresos aumentaron un 22% 🎉. Sin embargo, hubo un 20% de cancelaciones.

                                🔍 **Análisis detallado**:
                                - 📈 **Crecimiento**: ¡Buen trabajo! El aumento en turnos muestra que tu marketing está funcionando.
                                - ❌ **Cancelaciones**: 8 de 40 turnos se cancelaron (20%), principalmente los fines de semana.
                                - ✂️ **Estilistas**: María atendió el 45% de los turnos, mientras que Juan solo el 30%.
                                - 🕒 **Horarios**: Las franjas de 10-12 AM y 3-5 PM fueron las más solicitadas.

                                💡 **Recomendaciones**:
                                1. Implementa un recordatorio automático 24h antes para reducir cancelaciones
                                2. Balancea la carga entre estilistas para evitar sobrecargar a María
                                3. Ofrece descuentos en horarios menos concurridos (9-10 AM)
                                4. Crea un paquete Spa Canino para aumentar el ticket promedio

                                🎯 **Meta sugerida**: 
                                Reducir cancelaciones al 10% este mes mediante recordatorios.

                                ¿Qué te parece si empezamos con el sistema de recordatorios? 🐶💕"

                                ## Reglas importantes:
                                - Nunca inventes datos que no se te hayan proporcionado
                                - Si no hay suficientes datos, sugiere qué información adicional sería útil recolectar
                                - Mantén cada análisis en máximo 15 líneas
                                - Usa comparaciones comprensibles ("equivalente a 4 baños completos más que el mes pasado")
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
        CURLOPT_TIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception("Error de conexión con OpenAI: " . curl_error($ch));
    }
    
    curl_close($ch);

    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        throw new Exception("Error de OpenAI: " . ($data['error']['message'] ?? 'Error desconocido'));
    }

    return $data['choices'][0]['message']['content'] ?? 'No se recibieron recomendaciones';
}

function generarPromptRecomendaciones($datos, $periodo) {
    $prompt = "Analiza estos datos de turnos para un salón de belleza (período: $periodo) y proporciona recomendaciones profesionales:\n\n";
    
    // Datos de turnos
    $prompt .= "Resumen:\n";
    $prompt .= "- Turnos totales: " . ($datos['turnosHoy']['total'] ?? 0) . "\n";
    $prompt .= "- Ingresos totales: $" . ($datos['ingresosTurnos']['total'] ?? 0) . "\n\n";
    
    // Distribución por estados
    if (isset($datos['estadoChart'])) {
        $prompt .= "Estados de turnos:\n";
        foreach ($datos['estadoChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['estadoChart']['series'][$index] ?? 0) . " turnos\n";
        }
        $prompt .= "\n";
    }
    
    // Turnos por estilista
    if (isset($datos['estilistaChart'])) {
        $prompt .= "Turnos por estilista:\n";
        foreach ($datos['estilistaChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['estilistaChart']['series'][$index] ?? 0) . " turnos\n";
        }
        $prompt .= "\n";
    }
    
    // Ocupación por horario
    if (isset($datos['horarioChart'])) {
        $prompt .= "Ocupación por horario:\n";
        foreach ($datos['horarioChart']['labels'] as $index => $label) {
            $prompt .= "- $label: " . ($datos['horarioChart']['series'][$index] ?? 0) . " turnos\n";
        }
        $prompt .= "\n";
    }
    
    $prompt .= "Proporciona:\n";
    $prompt .= "1. 3-5 recomendaciones específicas para mejorar\n";
    $prompt .= "2. Análisis de puntos fuertes y débiles\n";
    $prompt .= "3. Sugerencias para balancear carga de trabajo\n";
    $prompt .= "4. Ideas para optimizar horarios\n";
    $prompt .= "Formato: Markdown con encabezados claros";
    
    return $prompt;
}
?>