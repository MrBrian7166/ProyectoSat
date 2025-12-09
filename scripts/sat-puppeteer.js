const puppeteer = require('puppeteer');

async function obtenerFormularioSAT() {
    console.log('🚀 Iniciando Puppeteer para SAT...');
    
    const browser = await puppeteer.launch({
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-web-security',
            '--disable-features=IsolateOrigins,site-per-process'
        ]
    });
    
    try {
        const page = await browser.newPage();
        
        // Configurar headers como navegador real
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        
        // URL del formulario SAT
        const url = 'https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge%3DkeBew7q19naJxaTIXK27KNtB886H1Z0u8eFGqIhuzyQ%26code_challenge_method%3DS256%26response_mode%3Dform_post%26nonce%3D639007739736089596.MGFhODJjMjItYzAyNS00OWQyLWJiZGQtNThlYjY5YWQyOTczZDNjODM3ZDYtZjRhOS00OTlkLThmNDktNzA2MmUxZTNiYmI2%26state%3DCfDJ8OSJ4mcSBN1Cm3RmDyDIwh5laxT7GJezGjNNWp5rLUOmge3BVJ2lSo0iR6gplpJf8-U-owHI9VY6xrUgsTm_yTmf8XCsLZXuSIcVW4BS6o0Rrf6CtPvioLi94nxd0rLKXaLyQsdCr8OzqWjH-h62nT7XGVII_b0kdXbu75vXsp_BH2ft9SY4_iA-1uhPsNN28OdD5rS7i7yg4qdsSFtx-oxADQXNDCuz2ZkvErTexKptOTppvg4pfQWzOe6YjKacZntsMoVc6TGgW1b5pIViKzjlpUNd9Sebw2LTAXRhmlmcVj5RTpyUq467ogC03gIkmV9pvo-N-kVXWxb0_L-1olyrlEUF36NL60PgRmgsMp4f3MxkZjwy7scjnLVWTR0JLsGYk6x1HjlnzCRoMB4-722vtYC5PsNbPeeMGuHiG9wUAUuQwRWQ1VeHrstx-mgfDA%26x-client-SKU%3DID_NETSTANDARD2_0%26x-client-ver%3D6.10.0.0';
        
        console.log(`🌐 Navegando a: ${url.substring(0, 100)}...`);
        
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 60000
        });
        
        // Esperar a que cargue el JavaScript
        await page.waitForTimeout(5000);
        
        // Verificar si ya estamos en la página con formulario
        const html = await page.content();
        
        // Guardar HTML para análisis
        const fs = require('fs');
        fs.writeFileSync('sat_formulario_puppeteer.html', html);
        
        console.log('📄 HTML obtenido:', html.length, 'bytes');
        
        // Buscar campos específicos
        const campos = await page.evaluate(() => {
            const resultados = {};
            
            // Buscar campos que nos interesan
            const buscarCampo = (name) => {
                const input = document.querySelector(`input[name="${name}"]`);
                return input ? {
                    encontrado: true,
                    name: input.name,
                    type: input.type,
                    id: input.id,
                    value: input.value,
                    placeholder: input.placeholder
                } : { encontrado: false };
            };
            
            resultados.fileCertificate = buscarCampo('fileCertificate');
            resultados.txtCertificate = buscarCampo('txtCertificate');
            resultados.filePrivately = buscarCampo('filePrivately');
            resultados.txtPrivately = buscarCampo('txtPrivately');
            resultados.privatelyPassword = buscarCampo('privatelyPassword');
            resultados.rfc = buscarCampo('rfc');
            resultados.submit = buscarCampo('submit');
            
            // Contar formularios
            resultados.formularios = document.querySelectorAll('form').length;
            
            return resultados;
        });
        
        console.log('🔍 Campos encontrados:', JSON.stringify(campos, null, 2));
        
        // Si no encontramos los campos, esperar más tiempo
        if (!campos.fileCertificate.encontrado && !campos.filePrivately.encontrado) {
            console.log('⚠️ Campos no encontrados. Esperando más tiempo...');
            await page.waitForTimeout(10000);
            
            // Intentar nuevamente
            const html2 = await page.content();
            const campos2 = await page.evaluate(() => {
                const inputs = document.querySelectorAll('input');
                return Array.from(inputs).map(input => ({
                    name: input.name,
                    type: input.type,
                    id: input.id
                }));
            });
            
            console.log('🔍 Todos los inputs encontrados:', campos2);
        }
        
        // Tomar screenshot para debug
        await page.screenshot({ path: 'sat_screenshot.png', fullPage: true });
        console.log('📸 Screenshot guardado: sat_screenshot.png');
        
        await browser.close();
        
        return {
            success: true,
            html_length: html.length,
            campos: campos,
            url: url
        };
        
    } catch (error) {
        console.error('❌ Error:', error);
        await browser.close();
        
        return {
            success: false,
            error: error.message
        };
    }
}

// Ejecutar si se llama directamente
if (require.main === module) {
    obtenerFormularioSAT().then(resultado => {
        console.log(JSON.stringify(resultado, null, 2));
        process.exit(resultado.success ? 0 : 1);
    });
}

module.exports = { obtenerFormularioSAT };