-- ============================================================================
-- E-Commerce Web Application - Seed Data
-- 1 Admin (admin@store.com / Admin@123)
-- 1 Customer (user@store.com / User@123)
-- 6 Categories & 24 Featured Products with inventory and pricing
-- ============================================================================

USE `ecommerce_db`;

-- ----------------------------------------------------------------------------
-- 1. Insert Seed Users
-- Passwords hashed using PASSWORD_BCRYPT (Cost 10)
-- Admin: admin@store.com / Admin@123
-- User:  user@store.com  / User@123
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@store.com', '$2y$10$zLKugGG8Tpdstd02jOuS9.Oxko/A88dcydX7MdVV5fq.l7bpKh98.', 'admin', NOW()),
(2, 'Jane Customer', 'user@store.com', '$2y$10$SJM78enmqpFrXgQvdbclFeXHQGBiKWmWxbG8qJY6Arc1OISxa8wDG', 'user', NOW())
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- ----------------------------------------------------------------------------
-- 2. Insert Seed Categories
-- ----------------------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Electronics', 'electronics'),
(2, 'Audio & Headphones', 'audio-headphones'),
(3, 'Wearables & Smartwatches', 'wearables-smartwatches'),
(4, 'Computer Accessories', 'computer-accessories'),
(5, 'Photography & Cameras', 'photography-cameras'),
(6, 'Smart Home & IoT', 'smart-home-iot')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ----------------------------------------------------------------------------
-- 3. Insert 24 Seed Products (4 per category)
-- ----------------------------------------------------------------------------
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `stock`, `image`, `is_active`, `created_at`) VALUES
-- Category 1: Electronics
(1, 1, 'UltraSlim 4K OLED Monitor 27"', 'ultraslim-4k-oled-monitor-27', 'Breathtaking 4K UHD display with 144Hz refresh rate, 99% DCI-P3 color gamut, and USB-C 90W power delivery. Perfect for creative professionals and high-performance gaming.', 599.99, 15, 'product-1.svg', 1, NOW()),
(2, 1, 'Thunderbolt 4 Pro Dock Station', 'thunderbolt-4-pro-dock-station', 'Universal 14-in-1 desktop docking station featuring dual 4K 60Hz outputs, Gigabit Ethernet, SD 4.0 card reader, and 96W upstream charging.', 189.50, 24, 'product-2.svg', 1, NOW()),
(3, 1, 'Portable Power Bank 26800mAh 100W', 'portable-power-bank-26800mah-100w', 'Airline-approved high capacity battery bank with dual USB-C Power Delivery 3.0 ports. Capable of charging modern laptops, tablets, and smartphones simultaneously.', 79.99, 45, 'product-3.svg', 1, NOW()),
(4, 1, 'GaN Fast Charger 120W Triple Port', 'gan-fast-charger-120w-triple-port', 'Next-generation Gallium Nitride wall charger with intelligent power allocation, foldable prongs, and comprehensive surge protection.', 49.99, 60, 'product-4.svg', 1, NOW()),

-- Category 2: Audio & Headphones
(5, 2, 'AcousticPro Wireless ANC Headphones', 'acousticpro-wireless-anc-headphones', 'Engineered with custom 40mm titanium drivers, hybrid active noise cancellation, transparency mode, and an incredible 50-hour battery life.', 249.99, 18, 'product-5.svg', 1, NOW()),
(6, 2, 'TrueSound Spatial Earbuds Pro', 'truesound-spatial-earbuds-pro', 'Compact true wireless earbuds with dynamic head-tracking spatial audio, IPX7 sweat resistance, and Qi wireless charging case.', 129.00, 32, 'product-6.svg', 1, NOW()),
(7, 2, 'StudioMaster Desktop Reference Monitors', 'studiomaster-desktop-reference-monitors', 'Pair of nearfield acoustic studio speakers with balanced TRS inputs, Kevlar woofers, and silk dome tweeters for pristine audio mixing.', 199.95, 12, 'product-7.svg', 1, NOW()),
(8, 2, 'SoundBar Cinema 3.1 with Wireless Subwoofer', 'soundbar-cinema-3-1-wireless-subwoofer', 'Dolby Atmos compatible soundbar system delivering 380W peak audio power, crystal clear vocal enhancement, and seamless Bluetooth 5.3 streaming.', 279.00, 9, 'product-8.svg', 1, NOW()),

-- Category 3: Wearables & Smartwatches
(9, 3, 'Apex Ultra GPS Multisport Smartwatch', 'apex-ultra-gps-multisport-smartwatch', 'Rugged titanium casing, sapphire crystal glass, multi-band GNSS tracking, ECG sensor, and up to 21 days battery longevity in standard smartwatch mode.', 449.00, 14, 'product-9.svg', 1, NOW()),
(10, 3, 'PulseFit Health & Fitness Tracker', 'pulsefit-health-fitness-tracker', 'Lightweight slim smart band with 24/7 continuous heart rate monitoring, SpO2 sensor, sleep phase analysis, and 30+ dedicated workout modes.', 69.99, 50, 'product-10.svg', 1, NOW()),
(11, 3, 'Smart Ring BioTracker Horizon', 'smart-ring-biotracker-horizon', 'Discreet aerospace titanium smart ring tracking circadian rhythm, recovery score, skin temperature variations, and movement metrics.', 299.00, 8, 'product-11.svg', 1, NOW()),
(12, 3, 'Classic Elite Leather Hybrid Watch', 'classic-elite-leather-hybrid-watch', 'Analog mechanical watch hands overlaid on an invisible e-ink display. Features subtle vibration alerts, step counting, and 30-day battery life.', 179.50, 20, 'product-12.svg', 1, NOW()),

-- Category 4: Computer Accessories
(13, 4, 'ErgoMech Wireless Mechanical Keyboard', 'ergomech-wireless-mechanical-keyboard', 'Split ergonomic 75% mechanical keyboard with hot-swappable tactile switches, programmable OLED screen, and acoustic dampening foam.', 159.99, 22, 'product-13.svg', 1, NOW()),
(14, 4, 'Precision Master Vertical Wireless Mouse', 'precision-master-vertical-wireless-mouse', 'Scientifically designed 57-degree natural handshake angle to reduce muscle strain, dual Bluetooth/2.4G connectivity, and thumb scroll wheel.', 74.99, 35, 'product-14.svg', 1, NOW()),
(15, 4, 'Alloy Pro Adjustable Laptop Stand', 'alloy-pro-adjustable-laptop-stand', 'Heavy-duty CNC machined aluminum alloy riser with dual-axis 360 rotation and heat dissipation vents. Supports laptops up to 17 inches.', 39.99, 55, 'product-15.svg', 1, NOW()),
(16, 4, 'Dual-Sided Eco Leather Desk Mat (90x40cm)', 'dual-sided-eco-leather-desk-mat', 'Waterproof, scratch-resistant vegan leather desk pad with anti-slip backing. Provides smooth mouse glide and protects desk surfaces.', 24.99, 80, 'product-16.svg', 1, NOW()),

-- Category 5: Photography & Cameras
(17, 5, 'Lumixor 4K Creator Mirrorless Camera', 'lumixor-4k-creator-mirrorless-camera', 'Compact 24.2MP APS-C sensor camera with unlimited 4K 60p recording, real-time eye tracking autofocus, and articulated touch screen for vloggers.', 849.00, 6, 'product-17.svg', 1, NOW()),
(18, 5, 'SteadyGimbal 3-Axis Smartphone Stabilizer', 'steadygimbal-3-axis-smartphone-stabilizer', 'Foldable handheld gimbal with magnetic quick-mount, AI object tracking, built-in extension rod, and companion cinematic video effects.', 119.00, 28, 'product-18.svg', 1, NOW()),
(19, 5, 'SpectraStudio Bi-Color LED Video Key Light', 'spectrastudio-bi-color-led-video-key-light', 'Professional 45W soft edge-lit panel with 2500K-8500K color temperature control, 98+ CRI, desk mount clamp, and Wi-Fi app adjustment.', 139.99, 16, 'product-19.svg', 1, NOW()),
(20, 5, 'StreamVoice XLR Dynamic Broadcast Mic', 'streamvoice-xlr-dynamic-broadcast-mic', 'Cardioid broadcast microphone engineered for podcasting and vocal streaming, featuring internal pop filter and electromagnetic shielding.', 169.00, 19, 'product-20.svg', 1, NOW()),

-- Category 6: Smart Home & IoT
(21, 6, 'AuraGlow Smart Ambient Light Bar Duo', 'auraglow-smart-ambient-light-bar-duo', 'Synchronizes 16 million vibrant colors with your display and gaming audio. Works with Alexa, Google Home, and Matter protocol.', 89.99, 30, 'product-21.svg', 1, NOW()),
(22, 6, 'NestGuard Smart Video Doorbell 2K', 'nestguard-smart-video-doorbell-2k', 'Dual-camera video doorbell with package detection, two-way audio talk, color night vision, and tamper alarm with local storage option.', 149.00, 17, 'product-22.svg', 1, NOW()),
(23, 6, 'AeroPure Smart HEPA Air Purifier', 'aeropure-smart-hepa-air-purifier', 'High-efficiency 3-stage filtration capturing 99.97% of airborne allergens, real-time PM2.5 air quality sensor, and ultra-quiet 22dB sleep mode.', 189.99, 11, 'product-23.svg', 1, NOW()),
(24, 6, 'ThermoSmart Pro Wi-Fi Thermostat', 'thermosmart-pro-wifi-thermostat', 'Learns your schedule, automatically optimizes energy consumption, and provides detailed monthly HVAC efficiency analytics.', 129.95, 25, 'product-24.svg', 1, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
