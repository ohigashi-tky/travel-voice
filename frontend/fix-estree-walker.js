#!/usr/bin/env node

// Fix estree-walker package.json exports issue
const fs = require('fs');
const path = require('path');

console.log('🔧 Fixing estree-walker package.json exports...');

function findEstreeWalkerPackages(dir, depth = 0) {
  const packages = [];
  
  try {
    if (!fs.existsSync(dir) || depth > 3) return packages;
    
    const items = fs.readdirSync(dir);
    
    for (const item of items) {
      const itemPath = path.join(dir, item);
      
      try {
        const stat = fs.statSync(itemPath);
        
        if (item === 'estree-walker' && stat.isDirectory()) {
          const packagePath = path.join(itemPath, 'package.json');
          if (fs.existsSync(packagePath)) {
            packages.push(packagePath);
          }
        } else if (stat.isDirectory() && item !== '.bin' && item !== '.cache') {
          // Recursively check subdirectories
          const subPackages = findEstreeWalkerPackages(path.join(itemPath, 'node_modules'), depth + 1);
          packages.push(...subPackages);
        }
      } catch (statError) {
        // Skip if we can't stat the file/directory
        continue;
      }
    }
  } catch (error) {
    console.warn(`Could not scan ${dir}:`, error.message);
  }
  
  return packages;
}

function fixPackageJson(packagePath) {
  try {
    console.log(`Checking: ${packagePath}`);
    const packageJson = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
    
    let modified = false;
    
    // For v2.0.2, ensure main field exists
    if (!packageJson.main) {
      if (packageJson.version && packageJson.version.startsWith('2.')) {
        // Version 2.x uses lib/index.js
        packageJson.main = "./lib/index.js";
      } else {
        // Version 3.x uses src/index.js
        packageJson.main = "./src/index.js";
      }
      modified = true;
    }
    
    // Fix exports field
    if (!packageJson.exports) {
      if (packageJson.version && packageJson.version.startsWith('2.')) {
        // Version 2.x configuration
        packageJson.exports = {
          "./package.json": "./package.json",
          ".": {
            "import": "./lib/index.js",
            "require": "./lib/index.js",
            "default": "./lib/index.js"
          }
        };
      } else {
        // Version 3.x configuration
        packageJson.exports = {
          "./package.json": "./package.json",
          ".": {
            "types": "./types/index.d.ts",
            "import": "./src/index.js",
            "require": "./src/index.js",
            "default": "./src/index.js"
          }
        };
      }
      modified = true;
    } else if (packageJson.exports['.'] && !packageJson.exports['.'].require) {
      // Add require and default fields if missing
      const entryPoint = packageJson.version && packageJson.version.startsWith('2.') ? "./lib/index.js" : "./src/index.js";
      packageJson.exports['.'].require = entryPoint;
      packageJson.exports['.'].default = entryPoint;
      modified = true;
    }
    
    if (modified) {
      fs.writeFileSync(packagePath, JSON.stringify(packageJson, null, '\t'));
      console.log(`✅ Fixed exports in ${packagePath}`);
    } else {
      console.log(`✓ Already properly configured: ${packagePath}`);
    }
    
    return true;
  } catch (error) {
    console.warn(`⚠️  Could not fix ${packagePath}:`, error.message);
    return false;
  }
}

try {
  const packageFiles = findEstreeWalkerPackages('node_modules');
  
  if (packageFiles.length === 0) {
    console.log('No estree-walker packages found');
    process.exit(0);
  }

  console.log(`Found ${packageFiles.length} estree-walker package(s)`);
  
  let successCount = 0;
  packageFiles.forEach(file => {
    if (fixPackageJson(file)) {
      successCount++;
    }
  });
  
  console.log(`🎉 estree-walker fix completed (${successCount}/${packageFiles.length} packages processed)`);
} catch (error) {
  console.error('❌ Failed to fix estree-walker:', error.message);
  process.exit(1);
}