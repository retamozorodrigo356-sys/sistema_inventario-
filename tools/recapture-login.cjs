const puppeteer = require('puppeteer-core');
const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const BASE = 'http://127.0.0.1:8000';

(async () => {
  const browser = await puppeteer.launch({
    executablePath: CHROME,
    headless: 'new',
    defaultViewport: { width: 1440, height: 1000 },
    args: ['--no-sandbox']
  });
  const page = await browser.newPage();
  
  // Go to /login
  const resp = await page.goto(`${BASE}/login`, { waitUntil: 'networkidle2' });
  console.log('HTTP status:', resp.status());
  
  // Wait for login form
  await page.waitForSelector('form', { timeout: 10000 });
  
  // Wait 1 second to ensure full render
  await new Promise(r => setTimeout(r, 1000));
  
  await page.screenshot({ path: 'docs/capturas/01-login.png', fullPage: true });
  console.log('01-login.png captured successfully with login form present!');
  
  await browser.close();
})();
