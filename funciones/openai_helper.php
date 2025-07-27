<?php
function obtenerRecomendacionesDeOpenAI($datos, $periodo) {
    // Configura tu API Key de OpenAI
    define('OPENAI_API_KEY', 'tu_api_key_aqui');
    
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
                    'content' => 'Eres un asistente especializado en gestión de turnos para salones de belleza. Proporciona recomendaciones concisas y prácticas basadas en datos. al final de cada recomendacion poner en que datos se basa dicha recomendacion'
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