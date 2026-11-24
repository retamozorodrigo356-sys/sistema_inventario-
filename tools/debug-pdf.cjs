const puppeteer = require('puppeteer-core');
const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

(async () => {
  const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new' });
  const page = await browser.newPage();
  await page.goto('file:///c:/xampp/htdocs/sistema_inventario/MANUAL_DE_USUARIO.pdf');
  await new Promise(r => setTimeout(r, 2000));
  
  // Let's take screenshot of page where Section 11 is
  // Or let's see how many pages and capture
  console.log('PDF opened');
  await browser.close();
})();
