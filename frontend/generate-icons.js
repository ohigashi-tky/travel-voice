const sharp = require('sharp');
const fs = require('fs');

async function generateIcons() {
  const svgBuffer = fs.readFileSync('./public/app-icon.svg');
  
  // Standard icons
  await sharp(svgBuffer)
    .resize(192, 192)
    .png()
    .toFile('./public/icon-192x192.png');
    
  await sharp(svgBuffer)
    .resize(512, 512)
    .png()
    .toFile('./public/icon-512x512.png');
  
  // Maskable icons (with padding for better appearance)
  const maskableSvg = `
    <svg width="512" height="512" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:#0891b2;stop-opacity:1" />
          <stop offset="50%" style="stop-color:#2563eb;stop-opacity:1" />
          <stop offset="100%" style="stop-color:#7c3aed;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="iconGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.9" />
          <stop offset="100%" style="stop-color:#f1f5f9;stop-opacity:0.8" />
        </linearGradient>
      </defs>
      
      <!-- Background circle for maskable -->
      <circle cx="256" cy="256" r="256" fill="url(#bgGradient)"/>
      
      <!-- Japanese house icon (scaled and centered) -->
      <g transform="translate(156, 176) scale(0.8)">
        <!-- Roof -->
        <path d="M156 80 L50 150 L262 150 Z" fill="url(#iconGradient)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
        <!-- House body -->
        <rect x="80" y="150" width="152" height="120" fill="url(#iconGradient)" stroke="rgba(255,255,255,0.3)" stroke-width="2" rx="8"/>
        <!-- Door -->
        <rect x="130" y="200" width="52" height="70" fill="rgba(255,255,255,0.7)" rx="4"/>
        <!-- Windows -->
        <rect x="95" y="170" width="25" height="25" fill="rgba(255,255,255,0.7)" rx="2"/>
        <rect x="192" y="170" width="25" height="25" fill="rgba(255,255,255,0.7)" rx="2"/>
      </g>
      
      <!-- Sound waves -->
      <g transform="translate(106, 106) scale(0.8)">
        <circle cx="380" cy="120" r="30" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="3"/>
        <circle cx="380" cy="120" r="50" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
        <circle cx="380" cy="120" r="70" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
      </g>
      
      <!-- Mountain silhouette -->
      <g transform="translate(0, 356) scale(0.8)">
        <path d="M0 100 L100 40 L200 60 L300 20 L400 50 L512 30 L512 212 L0 212 Z" 
              fill="rgba(255,255,255,0.15)"/>
      </g>
    </svg>
  `;
  
  await sharp(Buffer.from(maskableSvg))
    .resize(192, 192)
    .png()
    .toFile('./public/icon-maskable-192x192.png');
    
  await sharp(Buffer.from(maskableSvg))
    .resize(512, 512)
    .png()
    .toFile('./public/icon-maskable-512x512.png');
    
  // Generate favicon
  await sharp(svgBuffer)
    .resize(32, 32)
    .png()
    .toFile('./public/favicon-32x32.png');
    
  await sharp(svgBuffer)
    .resize(16, 16)
    .png()
    .toFile('./public/favicon-16x16.png');
  
  // Apple touch icon
  await sharp(Buffer.from(maskableSvg))
    .resize(180, 180)
    .png()
    .toFile('./public/apple-touch-icon.png');
  
  console.log('✅ All PWA icons generated successfully!');
}

generateIcons().catch(console.error);