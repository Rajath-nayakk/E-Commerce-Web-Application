<?php
declare(strict_types=1);

/**
 * Generates 24 modern SVG product illustrations for seed products
 */

$products = [
    1 => ['name' => 'OLED Monitor 27"', 'cat' => 'Electronics', 'icon' => 'monitor', 'color1' => '#3b82f6', 'color2' => '#1d4ed8'],
    2 => ['name' => 'Thunderbolt 4 Dock', 'cat' => 'Electronics', 'icon' => 'hub', 'color1' => '#6366f1', 'color2' => '#4338ca'],
    3 => ['name' => 'Power Bank 26800mAh', 'cat' => 'Electronics', 'icon' => 'battery', 'color1' => '#0ea5e9', 'color2' => '#0369a1'],
    4 => ['name' => 'GaN Charger 120W', 'cat' => 'Electronics', 'icon' => 'zap', 'color1' => '#f59e0b', 'color2' => '#d97706'],
    5 => ['name' => 'ANC Headphones Pro', 'cat' => 'Audio', 'icon' => 'headphones', 'color1' => '#8b5cf6', 'color2' => '#6d28d9'],
    6 => ['name' => 'Spatial Earbuds', 'cat' => 'Audio', 'icon' => 'earbuds', 'color1' => '#ec4899', 'color2' => '#be185d'],
    7 => ['name' => 'Studio Monitors Pair', 'cat' => 'Audio', 'icon' => 'speaker', 'color1' => '#10b981', 'color2' => '#047857'],
    8 => ['name' => 'SoundBar Cinema 3.1', 'cat' => 'Audio', 'icon' => 'soundbar', 'color1' => '#14b8a6', 'color2' => '#0f766e'],
    9 => ['name' => 'Apex Ultra GPS Watch', 'cat' => 'Wearables', 'icon' => 'watch', 'color1' => '#f97316', 'color2' => '#c2410c'],
    10 => ['name' => 'PulseFit Tracker', 'cat' => 'Wearables', 'icon' => 'band', 'color1' => '#06b6d4', 'color2' => '#0e7490'],
    11 => ['name' => 'Smart Ring BioTracker', 'cat' => 'Wearables', 'icon' => 'ring', 'color1' => '#e11d48', 'color2' => '#9f1239'],
    12 => ['name' => 'Hybrid Classic Watch', 'cat' => 'Wearables', 'icon' => 'watch_classic', 'color1' => '#64748b', 'color2' => '#334155'],
    13 => ['name' => 'ErgoMech Keyboard', 'cat' => 'Accessories', 'icon' => 'keyboard', 'color1' => '#a855f7', 'color2' => '#7e22ce'],
    14 => ['name' => 'Vertical Wireless Mouse', 'cat' => 'Accessories', 'icon' => 'mouse', 'color1' => '#2563eb', 'color2' => '#1e40af'],
    15 => ['name' => 'Alloy Laptop Stand', 'cat' => 'Accessories', 'icon' => 'stand', 'color1' => '#475569', 'color2' => '#1e293b'],
    16 => ['name' => 'Eco Leather Desk Mat', 'cat' => 'Accessories', 'icon' => 'mat', 'color1' => '#059669', 'color2' => '#065f46'],
    17 => ['name' => 'Lumixor 4K Camera', 'cat' => 'Photography', 'icon' => 'camera', 'color1' => '#dc2626', 'color2' => '#991b1b'],
    18 => ['name' => 'SteadyGimbal 3-Axis', 'cat' => 'Photography', 'icon' => 'gimbal', 'color1' => '#d97706', 'color2' => '#b45309'],
    19 => ['name' => 'SpectraStudio LED Light', 'cat' => 'Photography', 'icon' => 'light', 'color1' => '#ca8a04', 'color2' => '#854d0e'],
    20 => ['name' => 'StreamVoice Broadcast Mic', 'cat' => 'Photography', 'icon' => 'mic', 'color1' => '#4f46e5', 'color2' => '#3730a3'],
    21 => ['name' => 'AuraGlow Light Bar Duo', 'cat' => 'Smart Home', 'icon' => 'lightbar', 'color1' => '#7c3aed', 'color2' => '#5b21b6'],
    22 => ['name' => 'NestGuard Video Doorbell', 'cat' => 'Smart Home', 'icon' => 'doorbell', 'color1' => '#0284c7', 'color2' => '#0369a1'],
    23 => ['name' => 'AeroPure HEPA Purifier', 'cat' => 'Smart Home', 'icon' => 'purifier', 'color1' => '#0d9488', 'color2' => '#115e59'],
    24 => ['name' => 'ThermoSmart Wi-Fi', 'cat' => 'Smart Home', 'icon' => 'thermostat', 'color1' => '#e11d48', 'color2' => '#be123c'],
];

$outDir = dirname(__DIR__) . '/public/uploads/products';
if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

foreach ($products as $id => $p) {
    $c1 = $p['color1'];
    $c2 = $p['color2'];
    $name = htmlspecialchars($p['name']);
    $cat = htmlspecialchars($p['cat']);

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bg-grad-{$id}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="100%" stop-color="#1e293b" />
    </linearGradient>
    <linearGradient id="accent-grad-{$id}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$c1}" />
      <stop offset="100%" stop-color="{$c2}" />
    </linearGradient>
    <filter id="glow-{$id}" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="24" result="blur" />
      <feComposite in="SourceGraphic" in2="blur" operator="over" />
    </filter>
  </defs>

  <!-- Background Card -->
  <rect width="600" height="450" fill="url(#bg-grad-{$id})" rx="16" />
  
  <!-- Subtle Grid Pattern -->
  <g stroke="#334155" stroke-width="0.5" opacity="0.3">
    <line x1="0" y1="90" x2="600" y2="90" />
    <line x1="0" y1="180" x2="600" y2="180" />
    <line x1="0" y1="270" x2="600" y2="270" />
    <line x1="0" y1="360" x2="600" y2="360" />
    <line x1="120" y1="0" x2="120" y2="450" />
    <line x1="240" y1="0" x2="240" y2="450" />
    <line x1="360" y1="0" x2="360" y2="450" />
    <line x1="480" y1="0" x2="480" y2="450" />
  </g>

  <!-- Glowing Aura -->
  <circle cx="300" cy="200" r="110" fill="{$c1}" opacity="0.25" filter="url(#glow-{$id})" />

  <!-- Center Icon Graphic -->
  <g transform="translate(230, 130)">
    <rect width="140" height="140" rx="28" fill="url(#accent-grad-{$id})" opacity="0.9" filter="url(#glow-{$id})" />
    <circle cx="70" cy="70" r="45" fill="none" stroke="#ffffff" stroke-width="4" opacity="0.8" />
    <path d="M 50 70 L 65 85 L 92 55" fill="none" stroke="#ffffff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
  </g>

  <!-- Category Pill Badge -->
  <g transform="translate(40, 40)">
    <rect width="130" height="32" rx="16" fill="#1e293b" stroke="#334155" stroke-width="1.5" />
    <circle cx="20" cy="16" r="5" fill="{$c1}" />
    <text x="34" y="21" fill="#94a3b8" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="600">{$cat}</text>
  </g>

  <!-- Product ID Badge -->
  <g transform="translate(500, 40)">
    <rect width="60" height="32" rx="16" fill="#1e293b" stroke="#334155" stroke-width="1.5" />
    <text x="30" y="21" fill="#cbd5e1" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="700" text-anchor="middle">#{$id}</text>
  </g>

  <!-- Product Name & Showcase Label -->
  <text x="300" y="340" fill="#f8fafc" font-family="system-ui, -apple-system, sans-serif" font-size="22" font-weight="700" text-anchor="middle">{$name}</text>
  <text x="300" y="375" fill="#94a3b8" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="500" text-anchor="middle">Premium Official Hardware | In Stock</text>
</svg>
SVG;

    file_put_contents("{$outDir}/product-{$id}.svg", $svg);
}

echo "Generated 24 SVG product images in {$outDir}\n";

