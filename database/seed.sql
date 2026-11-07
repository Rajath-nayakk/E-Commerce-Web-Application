-- ============================================================================
-- E-Commerce Web Application - Seed Data (Premium Indian Electronics Store)
-- Currency: Indian Rupees (INR - ₹)
-- Realistic Indian retail pricing, original MRPs, real product photography
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
(1, 'Laptops & Electronics', 'electronics'),
(2, 'Audio & Headphones', 'audio-headphones'),
(3, 'Wearables & Smartwatches', 'wearables-smartwatches'),
(4, 'Computer Accessories', 'computer-accessories'),
(5, 'Photography & Cameras', 'photography-cameras'),
(6, 'Smart Home & IoT', 'smart-home-iot')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ----------------------------------------------------------------------------
-- 3. Insert 24 Seed Products with Realistic Indian Retail Prices (₹)
-- ----------------------------------------------------------------------------
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `original_price`, `brand`, `rating`, `review_count`, `is_featured`, `is_deal`, `stock`, `image`, `is_active`, `created_at`) VALUES
-- Category 1: Laptops & Electronics
(1, 1, 'UltraSlim 4K OLED Monitor 27"', 'ultraslim-4k-oled-monitor-27', 'Breathtaking 4K UHD OLED display with 144Hz refresh rate, 99% DCI-P3 professional color gamut, 0.03ms response time, and USB-C 90W Power Delivery. Perfect for high-end creative work and cinematic PC gaming.', 54990.00, 69990.00, 'NovaTech Pro', 4.9, 142, 1, 1, 15, 'monitor-4k-oled.jpg', 1, NOW()),
(2, 1, 'Thunderbolt 4 Pro Dock Station', 'thunderbolt-4-pro-dock-station', 'Universal 14-in-1 desktop workstation hub featuring dual 4K 60Hz DisplayPort/HDMI outputs, Gigabit Ethernet, SD 4.0 card reader, and 96W upstream fast charging for laptops.', 14990.00, 19990.00, 'NovaTech', 4.8, 88, 1, 0, 24, 'docking-station.jpg', 1, NOW()),
(3, 1, 'Portable Power Bank 26800mAh 100W', 'portable-power-bank-26800mah-100w', 'Airline-approved high-capacity fast charging power bank with dual USB-C Power Delivery 3.0 ports. Capable of charging modern MacBooks, Windows laptops, iPads, and Android smartphones at full speed.', 4999.00, 7499.00, 'NovaCharge', 4.8, 310, 1, 1, 45, 'powerbank.jpg', 1, NOW()),
(4, 1, 'GaN Fast Charger 120W Triple Port', 'gan-fast-charger-120w-triple-port', 'Next-generation Gallium Nitride wall adapter with intelligent dynamic power allocation, foldable Indian pins, and multi-stage surge protection.', 3499.00, 4999.00, 'NovaCharge', 4.7, 195, 0, 1, 60, 'gan-charger.jpg', 1, NOW()),

-- Category 2: Audio & Headphones
(5, 2, 'AcousticPro Wireless ANC Headphones', 'acousticpro-wireless-anc-headphones', 'Engineered with custom 40mm titanium drivers, hybrid active noise cancellation up to 42dB, transparency mode, LDAC Hi-Res Wireless codec, and an exceptional 55-hour battery life.', 18990.00, 24990.00, 'NovaSound', 4.9, 412, 1, 1, 18, 'headphones-anc.jpg', 1, NOW()),
(6, 2, 'TrueSound Spatial Earbuds Pro', 'truesound-spatial-earbuds-pro', 'Compact true wireless earbuds with dynamic head-tracking 3D spatial audio, IPX7 sweat resistance, low-latency gaming mode, and Qi wireless charging case.', 8499.00, 12999.00, 'NovaSound', 4.7, 260, 1, 1, 32, 'earbuds-pro.jpg', 1, NOW()),
(7, 2, 'StudioMaster Desktop Reference Monitors', 'studiomaster-desktop-reference-monitors', 'Pair of nearfield acoustic studio speakers with balanced TRS/XLR inputs, Kevlar woofers, and silk dome tweeters for pristine music production and audio mastering.', 16499.00, 21990.00, 'StudioMaster', 4.8, 94, 0, 0, 12, 'studio-monitors.jpg', 1, NOW()),
(8, 2, 'SoundBar Cinema 3.1 with Wireless Subwoofer', 'soundbar-cinema-3-1-wireless-subwoofer', 'Dolby Atmos compatible home theater soundbar delivering 420W peak audio power, crystal-clear dialogue vocal enhancement, HDMI eARC, and seamless Bluetooth 5.3 streaming.', 22990.00, 31990.00, 'NovaSound', 4.8, 135, 1, 1, 9, 'soundbar.jpg', 1, NOW()),

-- Category 3: Wearables & Smartwatches
(9, 3, 'Apex Ultra GPS Multisport Smartwatch', 'apex-ultra-gps-multisport-smartwatch', 'Aerospace Grade 5 titanium casing, sapphire crystal display, dual-frequency GNSS navigation, optical heart rate & ECG sensors, and up to 21 days battery endurance.', 32990.00, 42990.00, 'NovaFit', 4.9, 180, 1, 1, 14, 'smartwatch-apex.jpg', 1, NOW()),
(10, 3, 'PulseFit Health & Fitness Tracker', 'pulsefit-health-fitness-tracker', 'Lightweight slim smart band with vibrant 1.47" AMOLED screen, 24/7 continuous SpO2 blood oxygen monitoring, sleep phase tracking, and 50+ workout modes.', 3499.00, 5499.00, 'NovaFit', 4.6, 520, 0, 1, 50, 'fitness-tracker.jpg', 1, NOW()),
(11, 3, 'Smart Ring BioTracker Horizon', 'smart-ring-biotracker-horizon', 'Discreet aerospace titanium smart ring measuring circadian rhythm, recovery index, skin temperature variations, and movement metrics with 7-day battery life.', 19990.00, 26990.00, 'NovaRing', 4.7, 75, 1, 0, 8, 'smart-ring.jpg', 1, NOW()),
(12, 3, 'Classic Elite Leather Hybrid Watch', 'classic-elite-leather-hybrid-watch', 'Analog mechanical watch hands overlaid on an invisible high-contrast e-ink display. Features subtle vibration alerts, step counting, and 30-day battery life.', 11999.00, 15999.00, 'ChronoCraft', 4.7, 104, 0, 0, 20, 'hybrid-watch.jpg', 1, NOW()),

-- Category 4: Computer Accessories
(13, 4, 'ErgoMech Wireless Mechanical Keyboard', 'ergomech-wireless-mechanical-keyboard', 'Premium 75% mechanical keyboard with hot-swappable tactile switches, sound-dampening gasket mount, programmable OLED display, and PBT keycaps.', 9999.00, 14499.00, 'NovaGear', 4.9, 290, 1, 1, 22, 'mechanical-keyboard.jpg', 1, NOW()),
(14, 4, 'Precision Master Vertical Wireless Mouse', 'precision-master-vertical-wireless-mouse', 'Scientifically designed 57-degree natural handshake ergonomic angle to eliminate wrist strain, dual Bluetooth/2.4G connectivity, and thumb scroll wheel.', 4499.00, 6999.00, 'NovaGear', 4.8, 175, 0, 1, 35, 'gaming-mouse.jpg', 1, NOW()),
(15, 4, 'Alloy Pro Adjustable Laptop Stand', 'alloy-pro-adjustable-laptop-stand', 'Heavy-duty CNC machined aircraft-grade aluminum riser with dual-axis 360-degree rotation and heat dissipation vents. Supports laptops up to 17 inches.', 2499.00, 3999.00, 'NovaAlloy', 4.7, 140, 0, 0, 55, 'monitor-arm.jpg', 1, NOW()),
(16, 4, 'Dual-Sided Eco Leather Desk Mat (90x40cm)', 'dual-sided-eco-leather-desk-mat', 'Waterproof, scratch-resistant vegan leather desk blotter with anti-slip backing. Provides smooth mouse glide and protects desk surfaces.', 1299.00, 1999.00, 'NovaMat', 4.6, 380, 0, 1, 80, 'cat-accessories.jpg', 1, NOW()),

-- Category 5: Photography & Cameras
(17, 5, 'Lumixor 4K Creator Mirrorless Camera', 'lumixor-4k-creator-mirrorless-camera', 'Compact 24.2MP APS-C sensor camera with unlimited 4K 60p recording, real-time eye-tracking AI autofocus, and articulated touch screen for creators and vloggers.', 74990.00, 89990.00, 'Lumixor', 4.9, 68, 1, 0, 6, 'webcam-4k.jpg', 1, NOW()),
(18, 5, 'SteadyGimbal 3-Axis Smartphone Stabilizer', 'steadygimbal-3-axis-smartphone-stabilizer', 'Foldable handheld gimbal with magnetic quick-mount clamp, AI object tracking, built-in extension selfie rod, and companion cinematic video effects app.', 9999.00, 13999.00, 'SteadyPro', 4.7, 115, 0, 1, 28, 'graphics-card.jpg', 1, NOW()),
(19, 5, 'SpectraStudio Bi-Color LED Video Key Light', 'spectrastudio-bi-color-led-video-key-light', 'Professional 45W soft edge-lit panel with 2500K-8500K color temperature control, 98+ CRI rating, desk mount clamp, and Wi-Fi smartphone app adjustment.', 7499.00, 10999.00, 'SpectraStudio', 4.8, 82, 0, 1, 16, 'monitor-lightbar.jpg', 1, NOW()),
(20, 5, 'StreamVoice XLR Dynamic Broadcast Mic', 'streamvoice-xlr-dynamic-broadcast-mic', 'Cardioid broadcast microphone engineered for podcasting and vocal streaming, featuring internal pop filter and electromagnetic hum shielding.', 11499.00, 15999.00, 'StreamVoice', 4.9, 130, 1, 1, 19, 'broadcast-mic.jpg', 1, NOW()),

-- Category 6: Smart Home & IoT
(21, 6, 'AuraGlow Smart Ambient Light Bar Duo', 'auraglow-smart-ambient-light-bar-duo', 'Synchronizes 16 million vibrant RGB colors with your PC screen and audio. Compatible with Alexa, Google Home, and Matter protocol.', 5999.00, 8499.00, 'NovaSmart', 4.7, 210, 1, 1, 30, 'ambient-light.jpg', 1, NOW()),
(22, 6, 'NestGuard Smart Video Doorbell 2K', 'nestguard-smart-video-doorbell-2k', 'Dual-camera video doorbell with package detection, two-way audio talk, color night vision, and tamper alarm with local storage option.', 9990.00, 13990.00, 'NestGuard', 4.6, 90, 0, 0, 17, 'cat-monitors.jpg', 1, NOW()),
(23, 6, 'AeroPure Smart HEPA Air Purifier', 'aeropure-smart-hepa-air-purifier', 'High-efficiency 3-stage filtration capturing 99.97% of airborne allergens, real-time PM2.5 air quality display, and ultra-quiet 22dB sleep mode.', 12990.00, 16990.00, 'AeroPure', 4.8, 145, 1, 1, 11, 'cat-laptops.jpg', 1, NOW()),
(24, 6, 'ThermoSmart Pro Wi-Fi Thermostat', 'thermosmart-pro-wifi-thermostat', 'Learns your schedule, automatically optimizes energy consumption, and provides detailed monthly HVAC efficiency analytics via mobile app.', 8499.00, 11990.00, 'ThermoSmart', 4.7, 72, 0, 0, 25, 'cat-audio.jpg', 1, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `price` = VALUES(`price`), `original_price` = VALUES(`original_price`), `brand` = VALUES(`brand`), `rating` = VALUES(`rating`), `review_count` = VALUES(`review_count`), `image` = VALUES(`image`), `is_featured` = VALUES(`is_featured`), `is_deal` = VALUES(`is_deal`);
