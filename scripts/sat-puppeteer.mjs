import puppeteer from 'puppeteer';
import { writeFileSync, existsSync } from 'fs';

async function obtenerFormularioSAT() {
    console.log('🚀 Iniciando Puppeteer para SAT...');
    
    const browser = await puppeteer.launch({
        headless: 'new', // Usar el nuevo headless
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-web-security',
            '--disable-features=IsolateOrigins,site-per-process',
            '--window-size=1920,1080'
        ],
        defaultViewport: {
            width: 1920,
            height: 1080
        }
    });
    
    try {
        const page = await browser.newPage();
        
        // Configurar como navegador real
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        
        // Configurar headers adicionales
        await page.setExtraHTTPHeaders({
            'Accept-Language': 'es-MX,es;q=0.9,en;q=0.8',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        });
        
        // URL del formulario SAT
        const url = 'https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge%3DkeBew7q19naJxaTIXK27KNtB886H1Z0u8eFGqIhuzyQ%26code_challenge_method%3DS256%26response_mode%3Dform_post%26nonce%3D639007739736089596.MGFhODJjMjItYzAyNS00OWQyLWJiZGQtNThlYjY5YWQyOTczZDNjODM3ZDYtZjRhOS00OTlkLThmNDktNzA2MmUxZTNiYmI2%26state%3DCfDJ8OSJ4mcSBN1Cm3RmDyDIwh5laxT7GJezGjNNWp5rLUOmge3BVJ2lSo0iR6gplpJf8-U-owHI9VY6xrUgsTm_yTmf8XCsLZXuSIcVW4BS6o0Rrf6CtPvioLi94nxd0rLKXaLyQsdCr8OzqWjH-h62nT7XGVII_b0kdXbu75vXsp_BH2ft9SY4_iA-1uhPsNN28OdD5rS7i7yg4qdsSFtx-oxADQXNDCuz2ZkvErTexKptOTppvg4pfQWzOe6YjKacZntsMoVc6TGgW1b5pIViKzjlpUNd9Sebw2LTAXRhmlmcVj5RTpyUq467ogC03gIkmV9pvo-N-kVXWxb0_L-1olyrlEUF36NL60PgRmgsMp4f3MxkZjwy7scjnLVWTR0JLsGYk6x1HjlnzCRoMB4-722vtYC5PsNbPeeMGuHiG9wUAUuQwRWQ1VeHrstx-mgfDA%26x-client-SKU%3DID_NETSTANDARD2_0%26x-client-ver%3D6.10.0.0';
        
        console.log(`🌐 Navegando a URL del SAT...`);
        
        // Navegar a la URL
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 90000
        });
        
        console.log('✅ Página cargada. Esperando...');
        
        // Esperar varios segundos para que el JavaScript se ejecute
        await page.waitForTimeout(8000);
        
        // Verificar si hay redirección
        const currentUrl = page.url();
        console.log(`📍 URL actual: ${currentUrl}`);
        
        // Tomar screenshot
        await page.screenshot({ path: 'sat_screenshot.png', fullPage: true });
        console.log('📸 Screenshot guardado');
        
        // Obtener HTML
        const html = await page.content();
        const htmlPath = 'sat_formulario_puppeteer.html';
        writeFileSync(htmlPath, html);
        console.log(`📄 HTML guardado: ${htmlPath} (${html.length} bytes)`);
        
        // Buscar campos específicos
        const campos = await page.evaluate(() => {
            const resultados = {
                formularios: document.querySelectorAll('form').length,
                inputs: [],
                camposEspecificos: {}
            };
            
            // Buscar todos los inputs
            document.querySelectorAll('input, textarea, select, button').forEach(element => {
                const info = {
                    tag: element.tagName.toLowerCase(),
                    name: element.name || '',
                    id: element.id || '',
                    type: element.type || '',
                    placeholder: element.placeholder || '',
                    value: element.value || '',
                    className: element.className || ''
                };
                
                resultados.inputs.push(info);
                
                // Buscar campos específicos
                const nombresEspecificos = [
                    'fileCertificate', 'txtCertificate', 'filePrivately', 
                    'txtPrivately', 'privatelyPassword', 'rfc', 'submit', 'contrasena'
                ];
                
                if (nombresEspecificos.includes(element.name)) {
                    resultados.camposEspecificos[element.name] = info;
                }
            });
            
            return resultados;
        });
        
        console.log(`🔍 Formularios encontrados: ${campos.formularios}`);
        console.log(`🔍 Inputs totales: ${campos.inputs.length}`);
        
        // Filtrar solo información relevante
        const camposFiltrados = {};
        const camposBuscados = ['fileCertificate', 'txtCertificate', 'filePrivately', 'txtPrivately', 'privatelyPassword', 'rfc', 'submit'];
        
        camposBuscados.forEach(nombre => {
            if (campos.camposEspecificos[nombre]) {
                camposFiltrados[nombre] = {
                    encontrado: true,
                    ...campos.camposEspecificos[nombre]
                };
            } else {
                camposFiltrados[nombre] = { encontrado: false };
            }
        });
        
        await browser.close();
        
        return {
            success: true,
            url_inicial: url,
            url_final: currentUrl,
            html_length: html.length,
            html_path: htmlPath,
            screenshot_path: 'sat_screenshot.png',
            formularios: campos.formularios,
            inputs_totales: campos.inputs.length,
            campos: camposFiltrados,
            todos_inputs: campos.inputs.slice(0, 20), // Primeros 20 inputs
            timestamp: new Date().toISOString()
        };
        
    } catch (error) {
        console.error('❌ Error en Puppeteer:', error);
        
        try {
            await browser.close();
        } catch (e) {
            console.error('Error cerrando browser:', e);
        }
        
        return {
            success: false,
            error: error.message,
            timestamp: new Date().toISOString()
        };
    }
}

// Ejecutar si se llama directamente
if (process.argv[1] === import.meta.url.substring(7) || process.argv[1] === new URL(import.meta.url).pathname) {
    obtenerFormularioSAT().then(resultado => {
        console.log(JSON.stringify(resultado, null, 2));
        process.exit(resultado.success ? 0 : 1);
    });
}

export { obtenerFormularioSAT };