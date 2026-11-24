const puppeteer = require('puppeteer-core');
const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

(async () => {
  const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1200, height: 1600 });
  await page.goto('file:///c:/xampp/htdocs/sistema_inventario/MANUAL_DE_USUARIO.pdf');
  await new Promise(r => setTimeout(r, 2000));
  await page.screenshot({ path: 'tools/pdf-roles-check.png' });
  await browser.close();
  console.log('PDF check complete');
})();
