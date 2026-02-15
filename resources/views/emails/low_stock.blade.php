<!DOCTYPE html>
<html>
<head>
    @include('emails.includes._style')
</head>
<body>
<div class="main" style="padding: 20px;">
    <h2 class="headTitle">Low Stock Alert</h2>
    <p>Item: {{ $item->name }} (SKU: {{ $item->sku }})</p>
    <p>Warehouse: {{ $warehouse->name }} - {{ $warehouse->location }}</p>
    <p>Current quantity: <strong>{{ $stock->quantity }}</strong></p>
    <p>Please restock when possible.</p>
</div>
</body>
</html>
