<?php

/**
 * Script para generar archivos .md para Statamic desde CSV
 * Uso: php generate_migrants_md.php
 */

// Configuración
$csvFile = 'migrants_nz_form 1d0e6bda722f8059a014ce7528f8c5bf_all.csv';
$outputDir = 'content/collections/migrants';
$blueprintName = 'migrants';
$defaultUpdatedBy = 'b702c7e5-e21a-41a0-a013-e48f0ff5b708'; // Usar tu ID de usuario

// Función para generar UUID v4
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Función para limpiar texto y crear slug
function createSlug($text) {
    // Convertir a minúsculas
    $text = strtolower($text);
    // Reemplazar espacios y caracteres especiales con guiones
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    // Eliminar guiones al inicio y final
    $text = trim($text, '-');
    return $text;
}

// Función para limpiar el email
function cleanEmail($email) {
    $email = trim($email);
    if (empty($email)) {
        return '';
    }
    // Validar formato de email básico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '';
    }
    return $email;
}

// Función para procesar el avatar
function processAvatar($avatar) {
    if (empty($avatar)) {
        return '';
    }
    
    // Extraer solo el nombre del archivo
    $filename = basename($avatar);
    
    // Decodificar URL encode si es necesario
    $filename = urldecode($filename);
    
    return $filename;
}

// Función para convertir fecha a timestamp
function convertToTimestamp($dateString) {
    if (empty($dateString)) {
        return time();
    }
    
    $timestamp = strtotime($dateString);
    return $timestamp !== false ? $timestamp : time();
}

// Verificar que existe el archivo CSV
if (!file_exists($csvFile)) {
    die("Error: No se encontró el archivo CSV: $csvFile\n");
}

// Crear directorio de salida si no existe
if (!is_dir($outputDir)) {
    if (!mkdir($outputDir, 0755, true)) {
        die("Error: No se pudo crear el directorio: $outputDir\n");
    }
}

// Función para detectar el separador del CSV
function detectCSVDelimiter($csvFile, $checkLines = 2) {
    $delimiters = [',', ';', '\t', '|'];
    $results = [];
    
    $handle = fopen($csvFile, 'r');
    if (!$handle) return ',';
    
    for ($i = 0; $i < $checkLines; $i++) {
        $line = fgets($handle);
        if (!$line) break;
        
        foreach ($delimiters as $delimiter) {
            $regExp = '/[' . preg_quote($delimiter, '/') . ']/';
            $fields = preg_split($regExp, $line);
            if (count($fields) > 1) {
                if (!isset($results[$delimiter])) {
                    $results[$delimiter] = 0;
                }
                $results[$delimiter] += count($fields);
            }
        }
    }
    fclose($handle);
    
    if (empty($results)) return ',';
    
    return array_search(max($results), $results);
}

// Detectar el separador del CSV
$delimiter = detectCSVDelimiter($csvFile);
echo "Separador detectado: '$delimiter'\n";

// Leer el archivo CSV
$csvData = [];
if (($handle = fopen($csvFile, 'r')) !== FALSE) {
    // Leer la primera línea (headers)
    $headers = fgetcsv($handle, 0, $delimiter);
    
    if (!$headers) {
        die("Error: No se pudieron leer los headers del CSV\n");
    }
    
    // Limpiar headers (quitar BOM y espacios)
    $headers = array_map(function($header) {
        // Quitar BOM si existe
        $header = str_replace("\xEF\xBB\xBF", '', $header);
        return trim($header);
    }, $headers);
    
    echo "Headers encontrados: " . implode(', ', $headers) . "\n";
    
    // Verificar que existe la columna Status
    if (!in_array('Status', $headers)) {
        echo "Headers disponibles: " . print_r($headers, true);
        die("Error: No se encontró la columna 'Status' en el CSV\n");
    }
    
    // Procesar cada línea
    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        if (count($data) === count($headers)) {
            $csvData[] = array_combine($headers, $data);
        }
    }
    fclose($handle);
} else {
    die("Error: No se pudo abrir el archivo CSV\n");
}

echo "Procesando " . count($csvData) . " registros...\n";

$processedCount = 0;
$skippedCount = 0;

foreach ($csvData as $row) {
    // Verificar que el array tiene las claves necesarias
    if (!isset($row['Status']) || !isset($row['Name'])) {
        echo "Saltando registro - faltan campos requeridos\n";
        $skippedCount++;
        continue;
    }
    
    // Solo procesar registros con Status "Publish"
    $status = trim($row['Status'] ?? '');
    if ($status !== 'Publish') {
        echo "Saltando registro (Status: '$status'): " . trim($row['Name'] ?? '') . "\n";
        $skippedCount++;
        continue;
    }
    
    // Validar datos requeridos
    $name = trim($row['Name'] ?? '');
    if (empty($name)) {
        echo "Saltando registro sin nombre\n";
        $skippedCount++;
        continue;
    }
    
    // Generar ID único
    $id = generateUUID();
    
    // Crear slug para el nombre del archivo
    $slug = createSlug($name);
    
    // Preparar datos para el archivo .md
    $title = trim($row['title'] ?? '');
    $position = !empty($title) ? $title : 'Position not specified';
    $url = trim($row['url'] ?? '');
    $email = cleanEmail($row['Email'] ?? '');
    $tags = trim($row['Tags'] ?? '');
    $location = trim($row['location'] ?? '');
    $avatar = processAvatar($row['avatar'] ?? '');
    $createdTime = convertToTimestamp($row['Created time'] ?? '');
    
    // Contenido del archivo markdown
    $content = "---\n";
    $content .= "id: $id\n";
    $content .= "blueprint: $blueprintName\n";
    $content .= "title: '$name'\n";
    $content .= "position: '$position'\n";
    
    if (!empty($url)) {
        $content .= "url: '$url'\n";
    }
    
    if (!empty($email)) {
        $content .= "email: $email\n";
    }
    
    if (!empty($tags)) {
        $content .= "tags: '$tags'\n";
    }
    
    if (!empty($location)) {
        $content .= "location: '$location'\n";
    }
    
    if (!empty($avatar)) {
        $content .= "image:
  -  '$avatar'\n";
    }
    
    $content .= "updated_by: $defaultUpdatedBy\n";
    $content .= "updated_at: $createdTime\n";
    $content .= "---\n\n";
    
    // Nombre del archivo
    $filename = "$outputDir/$slug.md";
    
    // Verificar si el archivo ya existe
    if (file_exists($filename)) {
        $counter = 1;
        do {
            $filename = "$outputDir/$slug-$counter.md";
            $counter++;
        } while (file_exists($filename));
    }
    
    // Escribir el archivo
    if (file_put_contents($filename, $content) !== false) {
        echo "✓ Creado: $filename\n";
        $processedCount++;
    } else {
        echo "✗ Error al crear: $filename\n";
    }
}

echo "\n--- Resumen ---\n";
echo "Registros procesados: $processedCount\n";
echo "Registros saltados: $skippedCount\n";
echo "Total de registros: " . count($csvData) . "\n";
echo "Archivos creados en: $outputDir\n";

?>