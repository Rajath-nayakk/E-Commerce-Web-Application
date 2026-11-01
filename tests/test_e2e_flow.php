<?php
declare(strict_types=1);

/**
 * End-to-End Browser Simulation Integration Test
 * Simulates real user and admin flows over HTTP on http://localhost:8000
 */

$baseUrl = 'http://localhost:8000';
$cookieFileUser = sys_get_temp_dir() . '/cookie_user_' . uniqid() . '.txt';
$cookieFileAdmin = sys_get_temp_dir() . '/cookie_admin_' . uniqid() . '.txt';

function httpReq(string $url, string $method = 'GET', array $data = [], string $cookieFile = '', array $headers = []): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    if ($cookieFile !== '') {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    return [
        'code' => $httpCode,
        'headers' => $headerStr,
        'body' => $body,
        'url' => $effectiveUrl
    ];
}

function extractCsrf(string $html): string
{
    if (preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    if (preg_match('/content="([a-f0-9]{64})"/', $html, $m)) {
        return $m[1];
    }
    return '';
}

echo "=========================================================\n";
echo " Running End-to-End Web Flow Test\n";
echo "=========================================================\n";

// 1. Customer visits login page and signs in
echo "1. Customer logs in (user@store.com)...\n";
$loginPage = httpReq("{$baseUrl}/login", 'GET', [], $cookieFileUser);
$csrf = extractCsrf($loginPage['body']);
if (empty($csrf)) {
    echo "FAILED: Could not extract CSRF token from login page\n";
    exit(1);
}

$loginPost = httpReq("{$baseUrl}/login", 'POST', [
    'csrf_token' => $csrf,
    'email'      => 'user@store.com',
    'password'   => 'User@123'
], $cookieFileUser);

if (!str_contains($loginPost['body'], 'Jane Customer') && !str_contains($loginPost['body'], 'Sign Out')) {
    echo "FAILED: Login response did not contain user session indicators\n";
    exit(1);
}
echo "   [OK] Customer successfully authenticated.\n";

// 2. Customer adds item to cart
echo "2. Customer adds product #3 to cart...\n";
$prodPage = httpReq("{$baseUrl}/products", 'GET', [], $cookieFileUser);
$csrf = extractCsrf($prodPage['body']);

$addCart = httpReq("{$baseUrl}/cart/add", 'POST', [
    'csrf_token' => $csrf,
    'product_id' => 3,
    'quantity'   => 2
], $cookieFileUser);

$cartPage = httpReq("{$baseUrl}/cart", 'GET', [], $cookieFileUser);
if (!str_contains($cartPage['body'], 'Portable Power Bank 26800mAh') || !str_contains($cartPage['body'], '$159.98')) {
    echo "FAILED: Cart page did not reflect added product\n";
    exit(1);
}
echo "   [OK] Product #3 in cart with quantity 2.\n";

// 3. Customer proceeds to checkout
echo "3. Customer completes checkout with Cash on Delivery...\n";
$checkoutPage = httpReq("{$baseUrl}/checkout", 'GET', [], $cookieFileUser);
$csrf = extractCsrf($checkoutPage['body']);

$checkoutPost = httpReq("{$baseUrl}/checkout/process", 'POST', [
    'csrf_token'       => $csrf,
    'shipping_name'    => 'Jane Customer',
    'shipping_phone'   => '+1 (555) 987-6543',
    'shipping_address' => '456 Silicon Avenue, Cupertino, CA 95014',
    'payment_method'   => 'cod'
], $cookieFileUser);

if (!str_contains($checkoutPost['url'], '/orders/confirmation/') || !str_contains($checkoutPost['body'], 'Order Confirmed!')) {
    echo "FAILED: Checkout process did not redirect to confirmation page\n";
    exit(1);
}

preg_match('/Order #(\d+) has been placed/', $checkoutPost['body'], $ordMatch);
$orderId = isset($ordMatch[1]) ? (int) $ordMatch[1] : 0;
echo "   [OK] Order #{$orderId} created atomically!\n";

// 4. Customer views Order Timeline
echo "4. Customer verifies initial timeline for Order #{$orderId}...\n";
$trackPage = httpReq("{$baseUrl}/orders/{$orderId}", 'GET', [], $cookieFileUser);
echo "   Response Code: {$trackPage['code']}, URL: {$trackPage['url']}\n";
if (!str_contains($trackPage['body'], 'Order Placed') || !str_contains($trackPage['body'], 'pending')) {
    echo "FAILED: Order timeline did not display initial Pending milestone. Body preview:\n";
    echo substr($trackPage['body'], 0, 500) . "\n";
    exit(1);
}
echo "   [OK] Timeline active at Stage 1: Order Placed (Pending).\n";

// 5. Admin logs in
echo "5. Admin logs in (admin@store.com)...\n";
$adminLoginPage = httpReq("{$baseUrl}/login", 'GET', [], $cookieFileAdmin);
$adminCsrf = extractCsrf($adminLoginPage['body']);

$adminLoginPost = httpReq("{$baseUrl}/login", 'POST', [
    'csrf_token' => $adminCsrf,
    'email'      => 'admin@store.com',
    'password'   => 'Admin@123'
], $cookieFileAdmin);

if (!str_contains($adminLoginPost['body'], 'Admin Console') && !str_contains($adminLoginPost['body'], 'Dashboard Overview')) {
    echo "FAILED: Admin authentication failed\n";
    exit(1);
}
echo "   [OK] Admin dashboard reached.\n";

// 6. Admin advances order status: Pending -> Processing -> Shipped
echo "6. Admin updates Order #{$orderId} to 'Shipped' with carrier tracking note...\n";
$adminOrderPage = httpReq("{$baseUrl}/admin/orders/{$orderId}", 'GET', [], $cookieFileAdmin);
$adminCsrf = extractCsrf($adminOrderPage['body']);

$adminUpdate = httpReq("{$baseUrl}/admin/orders/{$orderId}/status", 'POST', [
    'csrf_token' => $adminCsrf,
    'status'     => 'shipped',
    'note'       => 'Carrier tracking #NVT-7890218 via Express Post'
], $cookieFileAdmin);

echo "   [OK] Admin updated status to 'shipped'.\n";

// 7. Customer checks timeline again to see updated status
echo "7. Customer verifies updated timeline reflects 'Shipped' and tracking note...\n";
$trackPageUpdated = httpReq("{$baseUrl}/orders/{$orderId}", 'GET', [], $cookieFileUser);

if (!str_contains($trackPageUpdated['body'], 'Shipped') || !str_contains($trackPageUpdated['body'], 'NVT-7890218')) {
    echo "FAILED: Customer tracking page did not reflect the admin's Shipped status or note\n";
    exit(1);
}
echo "   [OK] Customer timeline immediately reflects 'Shipped' and carrier note!\n";

echo "=========================================================\n";
echo " ALL END-TO-END FLOWS VERIFIED SUCCESSFULLY!\n";
echo "=========================================================\n";

// Clean up cookies
@unlink($cookieFileUser);
@unlink($cookieFileAdmin);
