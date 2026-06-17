<?php
// 1. INICIALIZAR VARIABLES (Para que no tiren error antes de enviar el formulario)
$costo_base = 0;
$porcentaje_ganancia = 0;
$comision_plataforma = 0;
$costo_envio = 0;

$precio_venta = 0;
$ganancia_neta = 0;
$mostrar_resultado = false;

// 2. PROCESAR EL FORMULARIO CUANDO SE ENVÍA (Método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tomamos los datos del formulario y los convertimos a números (float) por seguridad
    $costo_base = (float)$_POST['costo_base'];
    $porcentaje_ganancia = (float)$_POST['porcentaje_ganancia'];
    $comision_plataforma = (float)$_POST['comision_plataforma'];
    $costo_envio = (float)$_POST['costo_envio'];

// --- FÓRMULAS MATEMÁTICAS REALES ---
    
    // 1. Calculamos el costo total sumando el costo del producto + el margen que querés ganar libre
    $costo_con_margen = $costo_base * (1 + ($porcentaje_ganancia / 100));
    
    // 2. Aplicamos la fórmula para que el precio final absorba la comisión de la plataforma y el envío
    // Si la comisión es del 3%, dividimos por 0.97 para que al descontar el 3% nos dé el número exacto.
    if ($comision_plataforma < 100) {
        $precio_venta = ($costo_con_margen + $costo_envio) / (1 - ($comision_plataforma / 100));
    } else {
        $precio_venta = 0; // Evita división por cero si ponen 100%
    }

    // 3. La ganancia neta es lo que te queda en el bolsillo: 
    // Precio de venta menos lo que te saca la plataforma, menos el envío, menos el costo del producto.
    $descuento_plataforma = $precio_venta * ($comision_plataforma / 100);
    $ganancia_neta = $precio_venta - $descuento_plataforma - $costo_envio - $costo_base;
    
    $mostrar_resultado = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Márgenes - E-commerce</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Calculadora de Márgenes</h1>

        <form method="POST" action="index.php" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Costo Base del Producto ($)</label>
                <input type="number" step="0.01" name="costo_base" value="<?php echo $costo_base; ?>" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Margen de Ganancia Deseado (%)</label>
                <input type="number" step="0.1" name="porcentaje_ganancia" value="<?php echo $porcentaje_ganancia; ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comisión de Plataforma / Impuesto (%)</label>
                <input type="number" step="0.1" name="comision_plataforma" value="<?php echo $comision_plataforma; ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Costo de Envío / Logística ($)</label>
                <input type="number" step="0.01" name="costo_envio" value="<?php echo $costo_envio; ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition duration-200">
                Calcular Precios
            </button>
        </form>

        <?php if ($mostrar_resultado): ?>
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200 space-y-2">
                <h3 class="font-semibold text-blue-900 text-lg border-b border-blue-200 pb-1">Resultados:</h3>
                <p class="text-gray-700">Precio de Venta Sugerido: <strong class="text-gray-900">$<?php echo number_format($precio_venta, 2, ',', '.'); ?></strong></p>
                <p class="text-gray-700">Ganancia Neta Real: <strong class="text-green-600">$<?php echo number_format($ganancia_neta, 2, ',', '.'); ?></strong></p>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>