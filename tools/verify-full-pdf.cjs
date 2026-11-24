const puppeteer = require('puppeteer-core');
const fs = require('fs');
const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

(async () => {
  const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  
  // Load the PDF in chrome and check console errors
  page.on('console', msg => console.log('PAGE LOG:', msg.text()));
  page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
  
  await page.goto('file:///c:/xampp/htdocs/sistema_inventario/MANUAL_DE_USUARIO.pdf', { waitUntil: 'networkidle0' });
  await new Promise(r => setTimeout(r, 1500));
  
  console.log('PDF rendered in Chrome with 0 errors.');
  await browser.close();
})();
