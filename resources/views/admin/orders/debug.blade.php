<!DOCTYPE html>
<html>
<head>
    <title>Order Debug</title>
</head>
<body>
    <h1>Order Debug Page</h1>
    <p>If you can see this, the route is working.</p>
    
    <h2>Order Data:</h2>
    <pre>{{ json_encode($order->toArray(), JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Order Items:</h2>
    <pre>{{ json_encode($order->items->toArray(), JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Session Data:</h2>
    <pre>{{ json_encode(session()->all(), JSON_PRETTY_PRINT) }}</pre>
</body>
</html>
