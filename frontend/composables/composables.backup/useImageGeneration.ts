export const useImageGeneration = () => {
  // SVGベースの高品質な観光地画像を生成
  const generateTouristSpotImage = (spotName: string, category: string): string => {
    const imageId = `spot-${spotName.replace(/\s+/g, '-').toLowerCase()}`
    
    // 観光地タイプ別の色とアイコン
    const getSpotTheme = (name: string, cat: string) => {
      if (name.includes('スカイツリー') || name.includes('タワー')) {
        return {
          gradient: 'from-sky-400 via-blue-500 to-indigo-600',
          icon: 'M12 2L22 12L12 22L2 12Z', // Tower shape
          bgPattern: 'M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,133.3C672,117,768,107,864,112C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'
        }
      } else if (name.includes('寺') || name.includes('神宮') || name.includes('神社')) {
        return {
          gradient: 'from-red-400 via-orange-500 to-yellow-600',
          icon: 'M12 2L15 9H22L17 14L19 22L12 18L5 22L7 14L2 9H9Z', // Shrine shape
          bgPattern: 'M0,64L48,69.3C96,75,192,85,288,90.7C384,96,480,96,576,101.3C672,107,768,117,864,106.7C960,96,1056,64,1152,58.7C1248,53,1344,75,1392,85.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'
        }
      } else if (name.includes('動物園') || name.includes('水族館')) {
        return {
          gradient: 'from-green-400 via-emerald-500 to-teal-600',
          icon: 'M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 7V9C15 10.1 15.9 11 17 11S19 10.1 19 11H21ZM3 9V7L9 7V9C9 10.1 8.1 11 7 11S5 10.1 5 11H3Z', // Animal shape
          bgPattern: 'M0,160L48,144C96,128,192,96,288,96C384,96,480,128,576,138.7C672,149,768,139,864,133.3C960,128,1056,128,1152,138.7C1248,149,1344,171,1392,181.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'
        }
      } else if (name.includes('庭園') || name.includes('公園') || name.includes('御苑')) {
        return {
          gradient: 'from-emerald-400 via-green-500 to-lime-600',
          icon: 'M12 2L13.09 8.26L22 9L13.09 9.74L12 22L10.91 9.74L2 9L10.91 8.26Z', // Garden/nature shape
          bgPattern: 'M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,213.3C672,224,768,224,864,208C960,192,1056,160,1152,154.7C1248,149,1344,171,1392,181.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'
        }
      } else if (name.includes('城') || name.includes('宮殿') || name.includes('皇居')) {
        return {
          gradient: 'from-purple-400 via-violet-500 to-indigo-600',
          icon: 'M12 2L22 7L22 17L12 22L2 17L2 7Z', // Castle shape
          bgPattern: 'M0,128L48,138.7C96,149,192,171,288,165.3C384,160,480,128,576,117.3C672,107,768,117,864,133.3C960,149,1056,171,1152,165.3C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'
        }
      } else {
        return {
          gradient: 'from-cyan-400 via-blue-500 to-purple-600',
          icon: 'M12 2L14 9H21L16 14L18 21L12 17L6 21L8 14L3 9H10Z', // Default star shape
          bgPattern: 'M0,192L48,181.3C96,171,192,149,288,149.3C384,149,480,171,576,181.3C672,192,768,192,864,176C960,160,1056,128,1152,122.7C1248,117,1344,139,1392,149.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'
        }
      }
    }
    
    const theme = getSpotTheme(spotName, category)
    
    return `data:image/svg+xml;base64,${btoa(`
      <svg width="400" height="300" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="bg-${imageId}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
            <stop offset="50%" style="stop-color:#8b5cf6;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:1" />
          </linearGradient>
          
          <linearGradient id="overlay-${imageId}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.1" />
            <stop offset="100%" style="stop-color:#000000;stop-opacity:0.3" />
          </linearGradient>
          
          <filter id="glow-${imageId}">
            <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
            <feMerge> 
              <feMergeNode in="coloredBlur"/>
              <feMergeNode in="SourceGraphic"/>
            </feMerge>
          </filter>
          
          <pattern id="pattern-${imageId}" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
            <circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/>
          </pattern>
        </defs>
        
        <!-- Background -->
        <rect width="400" height="300" fill="url(#bg-${imageId})"/>
        
        <!-- Pattern overlay -->
        <rect width="400" height="300" fill="url(#pattern-${imageId})"/>
        
        <!-- Geometric shapes for visual interest -->
        <circle cx="350" cy="50" r="30" fill="rgba(255,255,255,0.1)" opacity="0.7"/>
        <circle cx="50" cy="250" r="40" fill="rgba(255,255,255,0.05)" opacity="0.5"/>
        <rect x="300" y="200" width="60" height="60" fill="rgba(255,255,255,0.08)" opacity="0.6" transform="rotate(45 330 230)"/>
        
        <!-- Main icon -->
        <g transform="translate(200, 150)">
          <circle r="45" fill="rgba(255,255,255,0.2)" filter="url(#glow-${imageId})"/>
          <path d="${theme.icon}" fill="white" opacity="0.9" transform="scale(2) translate(-12, -12)"/>
        </g>
        
        <!-- Text area -->
        <rect x="0" y="220" width="400" height="80" fill="rgba(0,0,0,0.4)"/>
        
        <!-- Spot name -->
        <text x="200" y="250" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="24" font-weight="bold">
          ${spotName}
        </text>
        
        <!-- Category -->
        <text x="200" y="275" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="14">
          ${category}
        </text>
        
        <!-- Overlay gradient -->
        <rect width="400" height="300" fill="url(#overlay-${imageId})"/>
      </svg>
    `)}`
  }
  
  // 都道府県別のテーマ画像を生成
  const generatePrefectureImage = (prefecture: string): string => {
    const prefectureId = `pref-${prefecture.replace(/\s+/g, '-').toLowerCase()}`
    
    const getPrefectureTheme = (pref: string) => {
      if (pref === '東京都') {
        return {
          gradient: ['#ff6b6b', '#4ecdc4', '#45b7d1'],
          landmarks: ['🗼', '🏙️', '🚄'],
          title: 'TOKYO'
        }
      } else if (pref === '北海道') {
        return {
          gradient: ['#74b9ff', '#0984e3', '#6c5ce7'],
          landmarks: ['🏔️', '❄️', '🦀'],
          title: 'HOKKAIDO'
        }
      } else if (pref === '大阪府') {
        return {
          gradient: ['#fd79a8', '#fdcb6e', '#e17055'],
          landmarks: ['🏯', '🍜', '🎭'],
          title: 'OSAKA'
        }
      } else if (pref === '京都府') {
        return {
          gradient: ['#fd79a8', '#fdcb6e', '#e84393'],
          landmarks: ['⛩️', '🌸', '🎋'],
          title: 'KYOTO'
        }
      } else {
        return {
          gradient: ['#81ecec', '#74b9ff', '#a29bfe'],
          landmarks: ['🏔️', '🌊', '🌺'],
          title: pref.replace(/[都道府県]/g, '').toUpperCase()
        }
      }
    }
    
    const theme = getPrefectureTheme(prefecture)
    
    return `data:image/svg+xml;base64,${btoa(`
      <svg width="600" height="400" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="bg-${prefectureId}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:${theme.gradient[0]};stop-opacity:1" />
            <stop offset="50%" style="stop-color:${theme.gradient[1]};stop-opacity:1" />
            <stop offset="100%" style="stop-color:${theme.gradient[2]};stop-opacity:1" />
          </linearGradient>
          
          <filter id="shadow-${prefectureId}">
            <feDropShadow dx="0" dy="4" stdDeviation="8" flood-opacity="0.3"/>
          </filter>
        </defs>
        
        <!-- Background -->
        <rect width="600" height="400" fill="url(#bg-${prefectureId})"/>
        
        <!-- Decorative elements -->
        <circle cx="100" cy="100" r="60" fill="rgba(255,255,255,0.1)"/>
        <circle cx="500" cy="300" r="80" fill="rgba(255,255,255,0.05)"/>
        <rect x="450" y="50" width="100" height="100" fill="rgba(255,255,255,0.08)" transform="rotate(45 500 100)"/>
        
        <!-- Main title -->
        <text x="300" y="200" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="48" font-weight="900" filter="url(#shadow-${prefectureId})">
          ${theme.title}
        </text>
        
        <!-- Landmarks -->
        <text x="150" y="150" font-size="40">${theme.landmarks[0]}</text>
        <text x="300" y="100" font-size="40">${theme.landmarks[1]}</text>
        <text x="450" y="150" font-size="40">${theme.landmarks[2]}</text>
        
        <!-- Subtitle -->
        <text x="300" y="240" text-anchor="middle" fill="rgba(255,255,255,0.9)" font-family="Arial, sans-serif" font-size="18" font-weight="600">
          ${prefecture}の魅力を発見しよう
        </text>
      </svg>
    `)}`
  }
  
  return {
    generateTouristSpotImage,
    generatePrefectureImage
  }
}