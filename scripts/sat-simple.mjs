import puppeteer from 'puppeteer';
import { writeFileSync } from 'fs';

console.log('🚀 INICIANDO PUPPETEER PARA SAT');

async function main() {
    let browser;
    
    try {
        // Configuración básica
        browser = await puppeteer.launch({
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-web-security'
            ]
        });
        
        const page = await browser.newPage();
        
        // Configurar User-Agent
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        
        // URL del SAT
        const satUrl = 'https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge_method%3DS256%26response_mode%3Dform_post';
        
        console.log(`🌐 Navegando a: ${satUrl.substring(0, 80)}...`);
        
        // Navegar al SAT
        await page.goto(satUrl, {
            waitUntil: 'networkidle0',
            timeout: 60000
        });
        
        // Esperar 5 segundos
        await new Promise(resolve => setTimeout(resolve, 5000));
        
        // Obtener URL actual
        const currentUrl = page.url();
        console.log(`📍 URL actual: ${currentUrl}`);
        
        // Obtener HTML
        const html = await page.content();
        
        // Guardar HTML
        writeFileSync('sat_test.html', html);
        console.log(`📄 HTML guardado (${html.length} bytes)`);
        
        // Tomar screenshot
        await page.screenshot({ path: 'sat_test.png', fullPage: false });
        console.log('📸 Screenshot guardado');
        
        // Buscar elementos clave
        const elementos = await page.evaluate(() => {
            const results = {
                forms: [],
                inputs: [],
                textos: []
            };
            
            // Formularios
            document.querySelectorAll('form').forEach((form, i) => {
                results.forms.push({
                    index: i,
                    id: form.id || `form-${i}`,
                    action: form.action || '',
                    method: form.method || 'GET'
                });
            });
            
            // Inputs
            document.querySelectorAll('input, textarea, select').forEach((input, i) => {
                results.inputs.push({
                    index: i,
                    tag: input.tagName,
                    name: input.name || '',
                    type: input.type || '',
                    id: input.id || '',
                    placeholder: input.placeholder || ''
                });
            });
            
            // Textos que podrían indicar el formulario
            const textosBuscar = ['certificado', 'llave', 'contraseña', 'password', 'rfc', 'fiel'];
            const bodyText = document.body.innerText.toLowerCase();
            
            textosBuscar.forEach(texto => {
                if (bodyText.includes(texto)) {
                    results.textos.push(texto);
                }
            });
            
            return results;
        });
        
        await browser.close();
        
        // Resultado final
        const resultado = {
            success: true,
            urlInicial: satUrl,
            urlFinal: currentUrl,
            htmlLength: html.length,
            htmlSaved: 'sat_test.html',
            screenshotSaved: 'sat_test.png',
            formsCount: elementos.forms.length,
            inputsCount: elementos.inputs.length,
            textosEncontrados: elementos.textos,
            algunosInputs: elementos.inputs.slice(0, 10),
            timestamp: new Date().toISOString()
        };
        
        console.log('\n✅ PROCESO COMPLETADO');
        console.log(JSON.stringify(resultado, null, 2));
        
        return resultado;
        
    } catch (error) {
        console.error('\n❌ ERROR:', error.message);
        
        if (browser) {
            try {
                await browser.close();
            } catch (e) {
                console.error('Error cerrando browser:', e.message);
            }
        }
        
        const errorResult = {
            success: false,
            error: error.message,
            stack: error.stack,
            timestamp: new Date().toISOString()
        };
        
        console.log(JSON.stringify(errorResult, null, 2));
        
        return errorResult;
    }
}

// Ejecutar
main();