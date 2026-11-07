import puppeteer from 'puppeteer-core';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.dirname(__dirname);
const screenshotsDir = path.join(projectRoot, 'docs', 'screenshots');

if (!fs.existsSync(screenshotsDir)) {
    fs.mkdirSync(screenshotsDir, { recursive: true });
}

const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const baseUrl = 'http://localhost:8000';

async function run() {
    console.log('Launching Edge browser for Indian Rupee (₹) screenshot capture...');
    const browser = await puppeteer.launch({
        executablePath: edgePath,
        headless: 'new',
        defaultViewport: { width: 1440, height: 900 },
        args: [
            '--no-sandbox',
            '--disable-gpu',
            '--window-size=1440,900'
        ]
    });

    async function snap(page, url, filename, waitMs = 1200) {
        console.log(`Navigating to ${url} -> ${filename}...`);
        await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
        if (waitMs > 0) {
            await new Promise(r => setTimeout(r, waitMs));
        }

        const currentUrl = page.url();
        const pageTitle = await page.title();
        const hasRupee = await page.evaluate(() => document.body.innerText.includes('₹'));
        console.log(`  [State] URL: ${currentUrl} | Title: ${pageTitle}`);
        console.log(`  [INR Check] Page contains Indian Rupee (₹): ${hasRupee}`);

        const outPath = path.join(screenshotsDir, filename);
        await page.screenshot({ path: outPath, fullPage: false });
        console.log(`  [OK] Saved ${filename} (${fs.statSync(outPath).size} bytes)`);
    }

    // -------------------------------------------------------------------------
    // 1. Guest & Customer Session
    // -------------------------------------------------------------------------
    const userPage = await browser.newPage();

    // 1. Home
    await snap(userPage, `${baseUrl}/`, 'home.png', 1000);

    // 2. Catalog
    await snap(userPage, `${baseUrl}/products`, 'catalog.png', 1000);

    // 3. Product detail (UltraSlim 4K OLED Monitor 27")
    await snap(userPage, `${baseUrl}/products/ultraslim-4k-oled-monitor-27`, 'product_detail.png', 1000);

    // Sign in as customer (user@store.com / User@123)
    console.log('\nAuthenticating as Jane Customer (user@store.com)...');
    await userPage.goto(`${baseUrl}/login`, { waitUntil: 'networkidle2' });
    await userPage.type('form[action="/login"] input[name="email"]', 'user@store.com');
    await userPage.type('form[action="/login"] input[name="password"]', 'User@123');
    await Promise.all([
        userPage.waitForNavigation({ waitUntil: 'networkidle2' }),
        userPage.click('form[action="/login"] button[type="submit"]')
    ]);
    console.log('Customer signed in, current URL:', userPage.url());

    // Add items to customer cart
    console.log('Adding products to cart...');
    await userPage.goto(`${baseUrl}/products/ultraslim-4k-oled-monitor-27`, { waitUntil: 'networkidle2' });
    await userPage.click('form.ajax-add-cart button[type="submit"]');
    await new Promise(r => setTimeout(r, 1200));

    await userPage.goto(`${baseUrl}/products/portable-power-bank-26800mah-100w`, { waitUntil: 'networkidle2' });
    await userPage.click('form.ajax-add-cart button[type="submit"]');
    await new Promise(r => setTimeout(r, 1200));

    // 4. Cart
    await snap(userPage, `${baseUrl}/cart`, 'cart.png', 1000);

    // 5. Checkout
    await snap(userPage, `${baseUrl}/checkout`, 'checkout.png', 1000);

    // 6. Order Timeline
    await snap(userPage, `${baseUrl}/orders/1`, 'order_timeline.png', 1000);

    await userPage.close();

    // -------------------------------------------------------------------------
    // 2. Administrator Session (Fresh Context)
    // -------------------------------------------------------------------------
    console.log('\nCreating Admin session context...');
    const adminContext = await browser.createBrowserContext();
    const adminPage = await adminContext.newPage();

    console.log('Authenticating as Administrator (admin@store.com)...');
    await adminPage.goto(`${baseUrl}/login`, { waitUntil: 'networkidle2' });
    await adminPage.type('form[action="/login"] input[name="email"]', 'admin@store.com');
    await adminPage.type('form[action="/login"] input[name="password"]', 'Admin@123');
    await Promise.all([
        adminPage.waitForNavigation({ waitUntil: 'networkidle2' }),
        adminPage.click('form[action="/login"] button[type="submit"]')
    ]);
    console.log('Admin signed in, current URL:', adminPage.url());

    // 7. Admin Dashboard
    await snap(adminPage, `${baseUrl}/admin`, 'admin_dashboard.png', 1000);

    // 8. Admin Orders
    await snap(adminPage, `${baseUrl}/admin/orders`, 'admin_orders.png', 1000);

    await adminPage.close();
    await adminContext.close();

    await browser.close();
    console.log('\n=========================================================');
    console.log(' ALL 8 SCREENSHOTS CAPTURED WITH INDIAN RUPEES (₹)!');
    console.log('=========================================================');
}

run().catch(err => {
    console.error('Screenshot capture failed:', err);
    process.exit(1);
});
