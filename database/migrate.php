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
        original_price NUMERIC,
        brand TEXT,
        rating NUMERIC DEFAULT 4.5,
        review_count INTEGER DEFAULT 0,
        is_featured INTEGER DEFAULT 0,
        is_deal INTEGER DEFAULT 0,
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
        // Category 1: Laptops & Electronics
        [1, 1, 'UltraSlim 4K OLED Monitor 27"', 'ultraslim-4k-oled-monitor-27', 'Breathtaking 4K UHD OLED display with 144Hz refresh rate, 99% DCI-P3 professional color gamut, 0.03ms response time, and USB-C 90W Power Delivery. Perfect for high-end creative work and cinematic PC gaming.', 54990.00, 69990.00, 'NovaTech Pro', 4.9, 142, 1, 1, 15, 'monitor-4k-oled.jpg', 1],
        [2, 1, 'Thunderbolt 4 Pro Dock Station', 'thunderbolt-4-pro-dock-station', 'Universal 14-in-1 desktop workstation hub featuring dual 4K 60Hz DisplayPort/HDMI outputs, Gigabit Ethernet, SD 4.0 card reader, and 96W upstream fast charging for laptops.', 14990.00, 19990.00, 'NovaTech', 4.8, 88, 1, 0, 24, 'docking-station.jpg', 1],
        [3, 1, 'Portable Power Bank 26800mAh 100W', 'portable-power-bank-26800mah-100w', 'Airline-approved high-capacity fast charging power bank with dual USB-C Power Delivery 3.0 ports. Capable of charging modern MacBooks, Windows laptops, iPads, and Android smartphones at full speed.', 4999.00, 7499.00, 'NovaCharge', 4.8, 310, 1, 1, 45, 'powerbank.jpg', 1],
        [4, 1, 'GaN Fast Charger 120W Triple Port', 'gan-fast-charger-120w-triple-port', 'Next-generation Gallium Nitride wall adapter with intelligent dynamic power allocation, foldable Indian pins, and multi-stage surge protection.', 3499.00, 4999.00, 'NovaCharge', 4.7, 195, 0, 1, 60, 'gan-charger.jpg', 1],

        // Category 2: Audio & Headphones
        [5, 2, 'AcousticPro Wireless ANC Headphones', 'acousticpro-wireless-anc-headphones', 'Engineered with custom 40mm titanium drivers, hybrid active noise cancellation up to 42dB, transparency mode, LDAC Hi-Res Wireless codec, and an exceptional 55-hour battery life.', 18990.00, 24990.00, 'NovaSound', 4.9, 412, 1, 1, 18, 'headphones-anc.jpg', 1],
        [6, 2, 'TrueSound Spatial Earbuds Pro', 'truesound-spatial-earbuds-pro', 'Compact true wireless earbuds with dynamic head-tracking 3D spatial audio, IPX7 sweat resistance, low-latency gaming mode, and Qi wireless charging case.', 8499.00, 12999.00, 'NovaSound', 4.7, 260, 1, 1, 32, 'earbuds-pro.jpg', 1],
        [7, 2, 'StudioMaster Desktop Reference Monitors', 'studiomaster-desktop-reference-monitors', 'Pair of nearfield acoustic studio speakers with balanced TRS/XLR inputs, Kevlar woofers, and silk dome tweeters for pristine music production and audio mastering.', 16499.00, 21990.00, 'StudioMaster', 4.8, 94, 0, 0, 12, 'studio-monitors.jpg', 1],
        [8, 2, 'SoundBar Cinema 3.1 with Wireless Subwoofer', 'soundbar-cinema-3-1-wireless-subwoofer', 'Dolby Atmos compatible home theater soundbar delivering 420W peak audio power, crystal-clear dialogue vocal enhancement, HDMI eARC, and seamless Bluetooth 5.3 streaming.', 22990.00, 31990.00, 'NovaSound', 4.8, 135, 1, 1, 9, 'soundbar.jpg', 1],

        // Category 3: Wearables & Smartwatches
        [9, 3, 'Apex Ultra GPS Multisport Smartwatch', 'apex-ultra-gps-multisport-smartwatch', 'Aerospace Grade 5 titanium casing, sapphire crystal display, dual-frequency GNSS navigation, optical heart rate & ECG sensors, and up to 21 days battery endurance.', 32990.00, 42990.00, 'NovaFit', 4.9, 180, 1, 1, 14, 'smartwatch-apex.jpg', 1],
        [10, 3, 'PulseFit Health & Fitness Tracker', 'pulsefit-health-fitness-tracker', 'Lightweight slim smart band with vibrant 1.47" AMOLED screen, 24/7 continuous SpO2 blood oxygen monitoring, sleep phase tracking, and 50+ workout modes.', 3499.00, 5499.00, 'NovaFit', 4.6, 520, 0, 1, 50, 'fitness-tracker.jpg', 1],
        [11, 3, 'Smart Ring BioTracker Horizon', 'smart-ring-biotracker-horizon', 'Discreet aerospace titanium smart ring measuring circadian rhythm, recovery index, skin temperature variations, and movement metrics with 7-day battery life.', 19990.00, 26990.00, 'NovaRing', 4.7, 75, 1, 0, 8, 'smart-ring.jpg', 1],
        [12, 3, 'Classic Elite Leather Hybrid Watch', 'classic-elite-leather-hybrid-watch', 'Analog mechanical watch hands overlaid on an invisible high-contrast e-ink display. Features subtle vibration alerts, step counting, and 30-day battery life.', 11999.00, 15999.00, 'ChronoCraft', 4.7, 104, 0, 0, 20, 'hybrid-watch.jpg', 1],

        // Category 4: Computer Accessories
        [13, 4, 'ErgoMech Wireless Mechanical Keyboard', 'ergomech-wireless-mechanical-keyboard', 'Premium 75% mechanical keyboard with hot-swappable tactile switches, sound-dampening gasket mount, programmable OLED display, and PBT keycaps.', 9999.00, 14499.00, 'NovaGear', 4.9, 290, 1, 1, 22, 'mechanical-keyboard.jpg', 1],
        [14, 4, 'Precision Master Vertical Wireless Mouse', 'precision-master-vertical-wireless-mouse', 'Scientifically designed 57-degree natural handshake ergonomic angle to eliminate wrist strain, dual Bluetooth/2.4G connectivity, and thumb scroll wheel.', 4499.00, 6999.00, 'NovaGear', 4.8, 175, 0, 1, 35, 'gaming-mouse.jpg', 1],
        [15, 4, 'Alloy Pro Adjustable Laptop Stand', 'alloy-pro-adjustable-laptop-stand', 'Heavy-duty CNC machined aircraft-grade aluminum riser with dual-axis 360-degree rotation and heat dissipation vents. Supports laptops up to 17 inches.', 2499.00, 3999.00, 'NovaAlloy', 4.7, 140, 0, 0, 55, 'monitor-arm.jpg', 1],
        [16, 4, 'Dual-Sided Eco Leather Desk Mat (90x40cm)', 'dual-sided-eco-leather-desk-mat', 'Waterproof, scratch-resistant vegan leather desk pad with anti-slip backing. Provides smooth mouse glide and protects desk surfaces.', 1299.00, 1999.00, 'DeskPro', 4.8, 480, 0, 1, 80, 'desk-mat.jpg', 1],

        // Category 5: Photography & Cameras
        [17, 5, 'Lumixor 4K Creator Mirrorless Camera', 'lumixor-4k-creator-mirrorless-camera', 'Compact 24.2MP APS-C sensor camera with unlimited 4K 60p recording, real-time eye tracking autofocus, and articulated touch screen for vloggers.', 69990.00, 84990.00, 'Lumixor', 4.9, 65, 1, 1, 6, 'camera-mirrorless.jpg', 1],
        [18, 5, 'SteadyGimbal 3-Axis Smartphone Stabilizer', 'steadygimbal-3-axis-smartphone-stabilizer', 'Foldable handheld gimbal with magnetic quick-mount, AI object tracking, built-in extension rod, and companion cinematic video effects.', 8999.00, 13999.00, 'SteadyGimbal', 4.7, 190, 1, 1, 28, 'camera-gimbal.jpg', 1],
        [19, 5, 'SpectraStudio Bi-Color LED Video Key Light', 'spectrastudio-bi-color-led-video-key-light', 'Professional 45W soft edge-lit panel with 2500K-8500K color temperature control, 98+ CRI, desk mount clamp, and Wi-Fi app adjustment.', 6499.00, 9999.00, 'SpectraStudio', 4.8, 110, 0, 0, 16, 'studio-light.jpg', 1],
        [20, 5, 'StreamVoice XLR Dynamic Broadcast Mic', 'streamvoice-xlr-dynamic-broadcast-mic', 'Cardioid broadcast microphone engineered for podcasting and vocal streaming, featuring internal pop filter and electromagnetic shielding.', 12499.00, 16999.00, 'StreamVoice', 4.8, 145, 1, 0, 19, 'broadcast-mic.jpg', 1],

        // Category 6: Smart Home & IoT
        [21, 6, 'AuraGlow Smart Ambient Light Bar Duo', 'auraglow-smart-ambient-light-bar-duo', 'Synchronizes 16 million vibrant colors with your display and gaming audio. Works with Alexa, Google Home, and Matter protocol.', 5499.00, 7999.00, 'AuraGlow', 4.7, 215, 1, 1, 30, 'smart-light-bar.jpg', 1],
        [22, 6, 'NestGuard Smart Video Doorbell 2K', 'nestguard-smart-video-doorbell-2k', 'Dual-camera video doorbell with package detection, two-way audio talk, color night vision, and tamper alarm with local storage option.', 9999.00, 14999.00, 'NestGuard', 4.6, 98, 0, 1, 17, 'smart-doorbell.jpg', 1],
        [23, 6, 'AeroPure Smart HEPA Air Purifier', 'aeropure-smart-hepa-air-purifier', 'High-efficiency 3-stage filtration capturing 99.97% of airborne allergens, real-time PM2.5 air quality sensor, and ultra-quiet 22dB sleep mode.', 12999.00, 17999.00, 'AeroPure', 4.8, 160, 1, 1, 11, 'air-purifier.jpg', 1],
        [24, 6, 'ThermoSmart Pro Wi-Fi Thermostat', 'thermosmart-pro-wifi-thermostat', 'Learns your schedule, automatically optimizes energy consumption, and provides detailed monthly HVAC efficiency analytics.', 8499.00, 11999.00, 'ThermoSmart', 4.7, 85, 0, 0, 25, 'smart-thermostat.jpg', 1]
    ];

    $stmtProd = $pdo->prepare("INSERT INTO products (id, category_id, name, slug, description, price, original_price, brand, rating, review_count, is_featured, is_deal, stock, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
    $driver = strtolower(trim((string) ($config['default'] ?? 'mysql')));

    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--driver=')) {
            $driver = strtolower(trim(substr($arg, 9)));
        }
    }

    if ($driver === 'sqlite') {
        echo "Mode: Explicit SQLite Database\n";
        $dbPath = $config['connections']['sqlite']['database'];
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0777, true);
        }
        $pdo = new PDO("sqlite:{$dbPath}", null, null, $config['connections']['sqlite']['options']);
        runSqliteMigration($pdo, true);
        echo "\n[SUCCESS] SQLite database migrated & seeded at: {$dbPath}\n";
    } elseif ($driver === 'mysql') {
        echo "Mode: Primary MySQL Database\n";
        try {
            runMysqlMigration($config, true);
            echo "\n[SUCCESS] MySQL database migrated & seeded successfully!\n";
        } catch (\Throwable $e) {
            echo "\n[ERROR] MySQL Migration Failed: " . $e->getMessage() . "\n";
            echo "Troubleshooting:\n";
            echo " 1. Make sure MySQL server is running on {$config['connections']['mysql']['host']}:{$config['connections']['mysql']['port']}\n";
            echo " 2. If using XAMPP/WAMP, verify MySQL is started in the control panel\n";
            echo " 3. Check credentials in .env (DB_HOST, DB_PORT, DB_USER, DB_PASS)\n";
            echo " 4. To explicitly opt-in to SQLite standalone mode instead, pass: php database/migrate.php --driver=sqlite\n\n";
            exit(1);
        }
    } else {
        echo "\n[ERROR] Unsupported database driver: '{$driver}'. Use 'mysql' or 'sqlite'.\n";
        exit(1);
    }
}

