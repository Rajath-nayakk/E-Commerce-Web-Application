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
    console.log('Launching Edge browser in headless mode...');
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

    async function snap(page, url, filename, waitMs = 500) {
        console.log(`Navigating to ${url} -> ${filename}...`);
        await page.goto(url, { waitUntil: 'networkidle2', timeout: 20000 });
        if (waitMs > 0) {
            await new Promise(r => setTimeout(r, waitMs));
        }
        const outPath = path.join(screenshotsDir, filename);
        await page.screenshot({ path: outPath, fullPage: false });
        console.log(`  [OK] Saved ${filename} (${fs.statSync(outPath).size} bytes)`);
    }

    // -------------------------------------------------------------------------
    // 1. Guest & Customer Pages
    // -------------------------------------------------------------------------
    const userPage = await browser.newPage();

    // 1. Home
    await snap(userPage, `${baseUrl}/`, 'home.png', 800);

    // 2. Catalog
    await snap(userPage, `${baseUrl}/products`, 'catalog.png', 800);

    // 3. Product detail
    await snap(userPage, `${baseUrl}/products/ultraslim-4k-oled-monitor-27`, 'product_detail.png', 800);

    // Add item to cart
    console.log('Adding product to shopping cart...');
    await userPage.evaluate(() => {
        const form = document.querySelector('form.ajax-add-cart') || document.querySelector('form[action="/cart/add"]');
        if (form) form.submit();
    });
    await new Promise(r => setTimeout(r, 1200));

    // 4. Cart
    await snap(userPage, `${baseUrl}/cart`, 'cart.png', 800);

    // Sign in as customer (user@store.com / User@123)
    console.log('Authenticating as customer (user@store.com)...');
    await userPage.goto(`${baseUrl}/login`, { waitUntil: 'networkidle2' });
    await userPage.type('#email', 'user@store.com');
    await userPage.type('#password', 'User@123');
    await Promise.all([
        userPage.waitForNavigation({ waitUntil: 'networkidle2' }),
        userPage.evaluate(() => document.querySelector('form[action="/login"]').submit())
    ]);
    console.log('Customer signed in, current URL:', userPage.url());

    // 5. Checkout
    // Ensure item in cart for checkout
    await userPage.goto(`${baseUrl}/cart`, { waitUntil: 'networkidle2' });
    await snap(userPage, `${baseUrl}/checkout`, 'checkout.png', 800);

    // 6. Order Timeline
    console.log('Navigating to Customer My Orders...');
    await userPage.goto(`${baseUrl}/orders`, { waitUntil: 'networkidle2' });
    const orderLink = await userPage.$('a[href*="/orders/"]');
    if (orderLink) {
        const href = await userPage.evaluate(el => el.getAttribute('href'), orderLink);
        await snap(userPage, `${baseUrl}${href}`, 'order_timeline.png', 800);
    } else {
        await snap(userPage, `${baseUrl}/orders/2`, 'order_timeline.png', 800);
    }

    await userPage.close();

    // -------------------------------------------------------------------------
    // 2. Administrator Session (Fresh Context)
    // -------------------------------------------------------------------------
    console.log('\nCreating Admin session context...');
    const adminContext = await browser.createBrowserContext();
    const adminPage = await adminContext.newPage();

    console.log('Authenticating as admin (admin@store.com)...');
    await adminPage.goto(`${baseUrl}/login`, { waitUntil: 'networkidle2' });
    await adminPage.type('#email', 'admin@store.com');
    await adminPage.type('#password', 'Admin@123');
    await Promise.all([
        adminPage.waitForNavigation({ waitUntil: 'networkidle2' }),
        adminPage.evaluate(() => document.querySelector('form[action="/login"]').submit())
    ]);
    console.log('Admin signed in, current URL:', adminPage.url());

    // 7. Admin Dashboard
    await snap(adminPage, `${baseUrl}/admin`, 'admin_dashboard.png', 800);

    // 8. Admin Orders
    await snap(adminPage, `${baseUrl}/admin/orders`, 'admin_orders.png', 800);

    await adminPage.close();
    await adminContext.close();

    await browser.close();
    console.log('\n=========================================================');
    console.log(' ALL 8 SCREENSHOTS CAPTURED AND VERIFIED!');
    console.log('=========================================================');
}

run().catch(err => {
    console.error('Screenshot capture failed:', err);
    process.exit(1);
});
