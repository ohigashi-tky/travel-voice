const sharp = require('sharp');
const fs = require('fs');

async function generateScreenshots() {
  const screenshotSvg = fs.readFileSync('./public/screenshot-placeholder.svg');
  
  // Narrow form factor (mobile)
  await sharp(screenshotSvg)
    .resize(640, 1136)
    .png()
    .toFile('./public/screenshot-narrow.png');
    
  // Wide form factor (tablet landscape)
  const wideScreenshotSvg = `
    <svg width="1024" height="640" viewBox="0 0 1024 640" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Background -->
      <rect width="1024" height="640" fill="#f8fafc"/>
      
      <!-- Header -->
      <rect width="1024" height="80" fill="url(#headerGradient)"/>
      <defs>
        <linearGradient id="headerGradient" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" style="stop-color:#0891b2;stop-opacity:1" />
          <stop offset="50%" style="stop-color:#2563eb;stop-opacity:1" />
          <stop offset="100%" style="stop-color:#7c3aed;stop-opacity:1" />
        </linearGradient>
      </defs>
      
      <!-- Title -->
      <text x="512" y="50" text-anchor="middle" fill="white" font-size="32" font-weight="bold">おうち旅行</text>
      
      <!-- Main content in two columns -->
      <!-- Left column - Popular spots -->
      <text x="60" y="130" fill="#1e293b" font-size="20" font-weight="600">人気スポット</text>
      
      <rect x="60" y="150" width="400" height="100" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <rect x="80" y="170" width="80" height="60" fill="#ddd6fe" rx="8"/>
      <text x="180" y="190" fill="#1e293b" font-size="16" font-weight="600">東京スカイツリー</text>
      <text x="180" y="210" fill="#64748b" font-size="12">高さ634mの世界最高クラスの電波塔</text>
      <text x="180" y="230" fill="#3b82f6" font-size="10">東京都 • 展望台</text>
      
      <rect x="60" y="270" width="400" height="100" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <rect x="80" y="290" width="80" height="60" fill="#fecaca" rx="8"/>
      <text x="180" y="310" fill="#1e293b" font-size="16" font-weight="600">大阪城</text>
      <text x="180" y="330" fill="#64748b" font-size="12">豊臣秀吉が築城した名城</text>
      <text x="180" y="350" fill="#3b82f6" font-size="10">大阪府 • 歴史建造物</text>
      
      <!-- Right column - Prefectures -->
      <text x="520" y="130" fill="#1e293b" font-size="20" font-weight="600">都道府県から探す</text>
      
      <rect x="520" y="150" width="100" height="80" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <text x="570" y="175" text-anchor="middle" font-size="24">🗼</text>
      <text x="570" y="205" text-anchor="middle" fill="#1e293b" font-size="12">東京都</text>
      
      <rect x="640" y="150" width="100" height="80" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <text x="690" y="175" text-anchor="middle" font-size="24">🏯</text>
      <text x="690" y="205" text-anchor="middle" fill="#1e293b" font-size="12">大阪府</text>
      
      <rect x="760" y="150" width="100" height="80" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <text x="810" y="175" text-anchor="middle" font-size="24">⛩️</text>
      <text x="810" y="205" text-anchor="middle" fill="#1e293b" font-size="12">京都府</text>
      
      <rect x="880" y="150" width="100" height="80" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <text x="930" y="175" text-anchor="middle" font-size="24">🐄</text>
      <text x="930" y="205" text-anchor="middle" fill="#1e293b" font-size="12">北海道</text>
      
      <rect x="520" y="250" width="100" height="80" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <text x="570" y="275" text-anchor="middle" font-size="24">🏭</text>
      <text x="570" y="305" text-anchor="middle" fill="#1e293b" font-size="12">愛知県</text>
      
      <rect x="640" y="250" width="100" height="80" fill="white" rx="12" stroke="#e2e8f0" stroke-width="1"/>
      <text x="690" y="275" text-anchor="middle" font-size="24">🏮</text>
      <text x="690" y="305" text-anchor="middle" fill="#1e293b" font-size="12">福岡県</text>
    </svg>
  `;
  
  await sharp(Buffer.from(wideScreenshotSvg))
    .resize(1024, 640)
    .png()
    .toFile('./public/screenshot-wide.png');
  
  console.log('✅ PWA screenshots generated successfully!');
}

generateScreenshots().catch(console.error);