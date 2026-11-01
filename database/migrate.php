<?php
declare(strict_types=1);

/**
 * ----------------------------------------------------------------------------
 * Database Migration & Seeding CLI Tool
 * ----------------------------------------------------------------------------
 * Handles setup for both MySQL/MariaDB (via schema.sql & seed.sql)
 * and SQLite for zero-configuration standalone testing.
 * 
 * Usage:
 *   php database/migrate.php
 *   php database/migrate.php --seed
 *   php database/migrate.php --driver=sqlite
 */

require_once dirname(__DIR__) . '/app/config/config.php';

function runSqliteMigration(PDO $pdo, bool $withSeed = true): void
{
    echo " [SQLite] Initializing database schema...\n";

    $pdo->exec("PRAGMA foreign_keys = OFF;");
    $pdo->exec("DROP TABLE IF EXISTS password_resets;");
    $pdo->exec("DROP TABLE IF EXISTS order_status_history;");
    $pdo->exec("DROP TABLE IF EXISTS order_items;");
    $pdo->exec("DROP TABLE IF EXISTS orders;");
    $pdo->exec("DROP TABLE IF EXISTS cart_items;");
    $pdo->exec("DROP TABLE IF EXISTS products;");
    $pdo->exec("DROP TABLE IF EXISTS categories;");
    $pdo->exec("DROP TABLE IF EXISTS users;");
    $pdo->exec("PRAGMA foreign_keys = ON;");

    $schemaSql = <<<SQL
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('user', 'admin')),
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE
    );

    CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE RESTRICT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT,
        price NUMERIC NOT NULL,
        stock INTEGER NOT NULL DEFAULT 0,
        image TEXT,
        is_active INTEGER NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE cart_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
        quantity INTEGER NOT NULL DEFAULT 1,
        UNIQUE (user_id, product_id)
    );

    CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        total NUMERIC NOT NULL,
        status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled')),
        payment_method TEXT NOT NULL,
        shipping_name TEXT NOT NULL,
        shipping_address TEXT NOT NULL,
        shipping_phone TEXT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
        product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
        price_at_purchase NUMERIC NOT NULL,
        quantity INTEGER NOT NULL
    );

    CREATE TABLE order_status_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
        status TEXT NOT NULL,
        note TEXT,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        token_hash TEXT NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );
    SQL;

    $pdo->exec($schemaSql);
    echo " [SQLite] Schema created successfully!\n";

    if ($withSeed) {
        seedDatabase($pdo, 'sqlite');
    }
}

function runMysqlMigration(array $config, bool $withSeed = true): void
{
    $mc = $config['connections']['mysql'];
    echo " [MySQL] Connecting to {$mc['host']}:{$mc['port']}...\n";

    // Connect without dbname to ensure database exists
    $dsnNoDb = "mysql:host={$mc['host']};port={$mc['port']};charset={$mc['charset']}";
    $pdo = new PDO($dsnNoDb, $mc['username'], $mc['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$mc['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$mc['database']}`;");

    echo " [MySQL] Executing schema.sql...\n";
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);
        echo " [MySQL] Schema applied successfully!\n";
    }

    if ($withSeed) {
        echo " [MySQL] Executing seed.sql...\n";
        $seedFile = __DIR__ . '/seed.sql';
        if (file_exists($seedFile)) {
            $sql = file_get_contents($seedFile);
            $pdo->exec($sql);
            echo " [MySQL] Seed data imported successfully!\n";
        }
    }
}

function seedDatabase(PDO $pdo, string $driver): void
{
    echo " [{$driver}] Seeding default users (admin, customer)...\n";
    
    // Admin & Customer
    $adminPass = password_hash('Admin@123', PASSWORD_BCRYPT);
    $userPass = password_hash('User@123', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (id, name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([1, 'Administrator', 'admin@store.com', $adminPass, 'admin']);
    $stmt->execute([2, 'Jane Customer', 'user@store.com', $userPass, 'user']);

    echo " [{$driver}] Seeding 6 categories...\n";
    $categories = [
        [1, 'Electronics', 'electronics'],
        [2, 'Audio & Headphones', 'audio-headphones'],
        [3, 'Wearables & Smartwatches', 'wearables-smartwatches'],
        [4, 'Computer Accessories', 'computer-accessories'],
        [5, 'Photography & Cameras', 'photography-cameras'],
        [6, 'Smart Home & IoT', 'smart-home-iot']
    ];

    $stmtCat = $pdo->prepare("INSERT INTO categories (id, name, slug) VALUES (?, ?, ?)");
    foreach ($categories as $cat) {
        $stmtCat->execute($cat);
    }

    echo " [{$driver}] Seeding 24 products...\n";
    $products = [
        // Category 1: Electronics
        [1, 1, 'UltraSlim 4K OLED Monitor 27"', 'ultraslim-4k-oled-monitor-27', 'Breathtaking 4K UHD display with 144Hz refresh rate, 99% DCI-P3 color gamut, and USB-C 90W power delivery. Perfect for creative professionals and high-performance gaming.', 599.99, 15, 'product-1.svg', 1],
        [2, 1, 'Thunderbolt 4 Pro Dock Station', 'thunderbolt-4-pro-dock-station', 'Universal 14-in-1 desktop docking station featuring dual 4K 60Hz outputs, Gigabit Ethernet, SD 4.0 card reader, and 96W upstream charging.', 189.50, 24, 'product-2.svg', 1],
        [3, 1, 'Portable Power Bank 26800mAh 100W', 'portable-power-bank-26800mah-100w', 'Airline-approved high capacity battery bank with dual USB-C Power Delivery 3.0 ports. Capable of charging modern laptops, tablets, and smartphones simultaneously.', 79.99, 45, 'product-3.svg', 1],
        [4, 1, 'GaN Fast Charger 120W Triple Port', 'gan-fast-charger-120w-triple-port', 'Next-generation Gallium Nitride wall charger with intelligent power allocation, foldable prongs, and comprehensive surge protection.', 49.99, 60, 'product-4.svg', 1],

        // Category 2: Audio & Headphones
        [5, 2, 'AcousticPro Wireless ANC Headphones', 'acousticpro-wireless-anc-headphones', 'Engineered with custom 40mm titanium drivers, hybrid active noise cancellation, transparency mode, and an incredible 50-hour battery life.', 249.99, 18, 'product-5.svg', 1],
        [6, 2, 'TrueSound Spatial Earbuds Pro', 'truesound-spatial-earbuds-pro', 'Compact true wireless earbuds with dynamic head-tracking spatial audio, IPX7 sweat resistance, and Qi wireless charging case.', 129.00, 32, 'product-6.svg', 1],
        [7, 2, 'StudioMaster Desktop Reference Monitors', 'studiomaster-desktop-reference-monitors', 'Pair of nearfield acoustic studio speakers with balanced TRS inputs, Kevlar woofers, and silk dome tweeters for pristine audio mixing.', 199.95, 12, 'product-7.svg', 1],
        [8, 2, 'SoundBar Cinema 3.1 with Wireless Subwoofer', 'soundbar-cinema-3-1-wireless-subwoofer', 'Dolby Atmos compatible soundbar system delivering 380W peak audio power, crystal clear vocal enhancement, and seamless Bluetooth 5.3 streaming.', 279.00, 9, 'product-8.svg', 1],

        // Category 3: Wearables & Smartwatches
        [9, 3, 'Apex Ultra GPS Multisport Smartwatch', 'apex-ultra-gps-multisport-smartwatch', 'Rugged titanium casing, sapphire crystal glass, multi-band GNSS tracking, ECG sensor, and up to 21 days battery longevity in standard smartwatch mode.', 449.00, 14, 'product-9.svg', 1],
        [10, 3, 'PulseFit Health & Fitness Tracker', 'pulsefit-health-fitness-tracker', 'Lightweight slim smart band with 24/7 continuous heart rate monitoring, SpO2 sensor, sleep phase analysis, and 30+ dedicated workout modes.', 69.99, 50, 'product-10.svg', 1],
        [11, 3, 'Smart Ring BioTracker Horizon', 'smart-ring-biotracker-horizon', 'Discreet aerospace titanium smart ring tracking circadian rhythm, recovery score, skin temperature variations, and movement metrics.', 299.00, 8, 'product-11.svg', 1],
        [12, 3, 'Classic Elite Leather Hybrid Watch', 'classic-elite-leather-hybrid-watch', 'Analog mechanical watch hands overlaid on an invisible e-ink display. Features subtle vibration alerts, step counting, and 30-day battery life.', 179.50, 20, 'product-12.svg', 1],

        // Category 4: Computer Accessories
        [13, 4, 'ErgoMech Wireless Mechanical Keyboard', 'ergomech-wireless-mechanical-keyboard', 'Split ergonomic 75% mechanical keyboard with hot-swappable tactile switches, programmable OLED screen, and acoustic dampening foam.', 159.99, 22, 'product-13.svg', 1],
        [14, 4, 'Precision Master Vertical Wireless Mouse', 'precision-master-vertical-wireless-mouse', 'Scientifically designed 57-degree natural handshake angle to reduce muscle strain, dual Bluetooth/2.4G connectivity, and thumb scroll wheel.', 74.99, 35, 'product-14.svg', 1],
        [15, 4, 'Alloy Pro Adjustable Laptop Stand', 'alloy-pro-adjustable-laptop-stand', 'Heavy-duty CNC machined aluminum alloy riser with dual-axis 360 rotation and heat dissipation vents. Supports laptops up to 17 inches.', 39.99, 55, 'product-15.svg', 1],
        [16, 4, 'Dual-Sided Eco Leather Desk Mat (90x40cm)', 'dual-sided-eco-leather-desk-mat', 'Waterproof, scratch-resistant vegan leather desk pad with anti-slip backing. Provides smooth mouse glide and protects desk surfaces.', 24.99, 80, 'product-16.svg', 1],

        // Category 5: Photography & Cameras
        [17, 5, 'Lumixor 4K Creator Mirrorless Camera', 'lumixor-4k-creator-mirrorless-camera', 'Compact 24.2MP APS-C sensor camera with unlimited 4K 60p recording, real-time eye tracking autofocus, and articulated touch screen for vloggers.', 849.00, 6, 'product-17.svg', 1],
        [18, 5, 'SteadyGimbal 3-Axis Smartphone Stabilizer', 'steadygimbal-3-axis-smartphone-stabilizer', 'Foldable handheld gimbal with magnetic quick-mount, AI object tracking, built-in extension rod, and companion cinematic video effects.', 119.00, 28, 'product-18.svg', 1],
        [19, 5, 'SpectraStudio Bi-Color LED Video Key Light', 'spectrastudio-bi-color-led-video-key-light', 'Professional 45W soft edge-lit panel with 2500K-8500K color temperature control, 98+ CRI, desk mount clamp, and Wi-Fi app adjustment.', 139.99, 16, 'product-19.svg', 1],
        [20, 5, 'StreamVoice XLR Dynamic Broadcast Mic', 'streamvoice-xlr-dynamic-broadcast-mic', 'Cardioid broadcast microphone engineered for podcasting and vocal streaming, featuring internal pop filter and electromagnetic shielding.', 169.00, 19, 'product-20.svg', 1],

        // Category 6: Smart Home & IoT
        [21, 6, 'AuraGlow Smart Ambient Light Bar Duo', 'auraglow-smart-ambient-light-bar-duo', 'Synchronizes 16 million vibrant colors with your display and gaming audio. Works with Alexa, Google Home, and Matter protocol.', 89.99, 30, 'product-21.svg', 1],
        [22, 6, 'NestGuard Smart Video Doorbell 2K', 'nestguard-smart-video-doorbell-2k', 'Dual-camera video doorbell with package detection, two-way audio talk, color night vision, and tamper alarm with local storage option.', 149.00, 17, 'product-22.svg', 1],
        [23, 6, 'AeroPure Smart HEPA Air Purifier', 'aeropure-smart-hepa-air-purifier', 'High-efficiency 3-stage filtration capturing 99.97% of airborne allergens, real-time PM2.5 air quality sensor, and ultra-quiet 22dB sleep mode.', 189.99, 11, 'product-23.svg', 1],
        [24, 6, 'ThermoSmart Pro Wi-Fi Thermostat', 'thermosmart-pro-wifi-thermostat', 'Learns your schedule, automatically optimizes energy consumption, and provides detailed monthly HVAC efficiency analytics.', 129.95, 25, 'product-24.svg', 1]
    ];

    $stmtProd = $pdo->prepare("INSERT INTO products (id, category_id, name, slug, description, price, stock, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $prod) {
        $stmtProd->execute($prod);
    }

    echo " [{$driver}] 24 products seeded successfully!\n";
}

// CLI Execution Entry Point
if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === realpath(__FILE__)) {
    echo "========================================================\n";
    echo " NovaTech E-Commerce Database Setup\n";
    echo "========================================================\n";

    $config = require dirname(__DIR__) . '/app/config/database.php';
    $driver = 'mysql';

    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--driver=')) {
            $driver = substr($arg, 9);
        }
    }

    if ($driver === 'sqlite') {
        $dbPath = $config['connections']['sqlite']['database'];
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0777, true);
        }
        $pdo = new PDO("sqlite:{$dbPath}", null, null, $config['connections']['sqlite']['options']);
        runSqliteMigration($pdo, true);
        echo "\n[SUCCESS] SQLite database migrated & seeded at: {$dbPath}\n";
    } else {
        try {
            runMysqlMigration($config, true);
            echo "\n[SUCCESS] MySQL database migrated & seeded!\n";
        } catch (\Throwable $e) {
            echo "\n[WARNING] MySQL connection failed: " . $e->getMessage() . "\n";
            echo "Falling back to SQLite for instant local operation...\n";
            $dbPath = $config['connections']['sqlite']['database'];
            if (!is_dir(dirname($dbPath))) {
                mkdir(dirname($dbPath), 0777, true);
            }
            $pdo = new PDO("sqlite:{$dbPath}", null, null, $config['connections']['sqlite']['options']);
            runSqliteMigration($pdo, true);
            echo "\n[SUCCESS] SQLite fallback database migrated & seeded!\n";
        }
    }
}

