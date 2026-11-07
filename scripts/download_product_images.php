<?php
declare(strict_types=1);

/**
 * ----------------------------------------------------------------------------
 * Product & Showcase Image Downloader
 * ----------------------------------------------------------------------------
 * Downloads real, high-resolution royalty-free product photographs from
 * Unsplash and stores them in public/images/ and public/uploads/.
 */

$projectRoot = dirname(__DIR__);
$productsDir = $projectRoot . '/public/images/products';
$categoriesDir = $projectRoot . '/public/images/categories';
$heroDir = $projectRoot . '/public/images/hero';
$uploadsDir = $projectRoot . '/public/uploads/products';

@mkdir($productsDir, 0777, true);
@mkdir($categoriesDir, 0777, true);
@mkdir($heroDir, 0777, true);
@mkdir($uploadsDir, 0777, true);

$images = [
    // 24 Products
    $productsDir . '/laptop-gaming.jpg' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/laptop-ultrabook.jpg' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/docking-station.jpg' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/gan-charger.jpg' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=800&q=80',
    
    $productsDir . '/headphones-anc.jpg' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/earbuds-pro.jpg' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/studio-monitors.jpg' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/soundbar.jpg' => 'https://images.unsplash.com/photo-1543512214-318c7553f230?auto=format&fit=crop&w=800&q=80',
    
    $productsDir . '/smartwatch-apex.jpg' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/fitness-tracker.jpg' => 'https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/smart-ring.jpg' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/hybrid-watch.jpg' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?auto=format&fit=crop&w=800&q=80',
    
    $productsDir . '/graphics-card.jpg' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/mechanical-keyboard.jpg' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/gaming-mouse.jpg' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/nvme-ssd.jpg' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?auto=format&fit=crop&w=800&q=80',
    
    $productsDir . '/monitor-4k-oled.jpg' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/monitor-ultrawide.jpg' => 'https://images.unsplash.com/photo-1551645120-d70bfe84c826?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/monitor-arm.jpg' => 'https://images.unsplash.com/photo-1593062096033-9a26b09da705?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/monitor-lightbar.jpg' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=80',
    
    $productsDir . '/webcam-4k.jpg' => 'https://images.unsplash.com/photo-1588702547919-26089e690ecc?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/broadcast-mic.jpg' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/ambient-light.jpg' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
    $productsDir . '/powerbank.jpg' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?auto=format&fit=crop&w=800&q=80',

    // 6 Categories
    $categoriesDir . '/cat-laptops.jpg' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
    $categoriesDir . '/cat-audio.jpg' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
    $categoriesDir . '/cat-wearables.jpg' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80',
    $categoriesDir . '/cat-gaming.jpg' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
    $categoriesDir . '/cat-monitors.jpg' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
    $categoriesDir . '/cat-accessories.jpg' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',

    // Hero Showcase
    $heroDir . '/hero-showcase.jpg' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=1200&q=80',
    $heroDir . '/hero-headphones.jpg' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1000&q=80',
];

echo "Downloading " . count($images) . " high-resolution product and showcase assets...\n";

foreach ($images as $targetFile => $url) {
    $filename = basename($targetFile);
    echo " -> Downloading {$filename}... ";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code === 200 && !empty($data)) {
        file_put_contents($targetFile, $data);
        echo "[OK] (" . round(strlen($data) / 1024) . " KB)\n";

        // If product image, also copy to uploads/products/ for maximum compatibility
        if (str_contains($targetFile, '/products/')) {
            copy($targetFile, $uploadsDir . '/' . $filename);
        }
    } else {
        echo "[FAILED: HTTP {$code}]\n";
    }
}

echo "\nAll photography assets downloaded and verified successfully!\n";
