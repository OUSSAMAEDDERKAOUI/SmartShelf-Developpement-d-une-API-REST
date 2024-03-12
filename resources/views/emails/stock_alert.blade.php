<!-- stock_alert.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Alerte de stock faible</title>
</head>
<body>
    <h1>Alerte de stock faible</h1>
    <p>Les produits suivants ont atteint un stock faible :</p>
    <ul>
        @foreach($products as $product)
            <li>{{ $product->name }} - Stock actuel: {{ $product->stock }} (Seuil: {{ $product->min_stock }})</li>
        @endforeach
    </ul>
</body>
</html>
