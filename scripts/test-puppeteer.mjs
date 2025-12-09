import puppeteer from 'puppeteer';

console.log('=== PRUEBA PUPPETEER SIMPLE ===');

async function testSimple() {
    console.log('1. Iniciando Puppeteer...');
    
    let browser;
    try {
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        console.log('2. Browser iniciado');
        
        const page = await browser.newPage();
        console.log('3. Página creada');
        
        // Ir a Google para probar
        await page.goto('https://www.google.com', { waitUntil: 'networkidle0' });
        console.log('4. Navegado a Google');
        
        const title = await page.title();
        console.log('5. Título obtenido:', title);
        
        await browser.close();
        console.log('6. Browser cerrado');
        
        return {
            success: true,
            title: title,
            message: 'Prueba exitosa'
        };
        
    } catch (error) {
        console.error('❌ ERROR:', error.message);
        
        if (browser) {
            try {
                await browser.close();
            } catch (e) {}
        }
        
        return {
            success: false,
            error: error.message
        };
    }
}

// Ejecutar prueba
testSimple().then(result => {
    console.log(JSON.stringify(result, null, 2));
    process.exit(result.success ? 0 : 1);
});