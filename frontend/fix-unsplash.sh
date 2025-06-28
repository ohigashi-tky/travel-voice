#!/bin/bash

# List of files that use UnsplashImage
files=(
    "pages/aichi.vue"
    "pages/fukuoka.vue" 
    "pages/kyoto.vue"
    "pages/hiroshima.vue"
    "pages/tokushima.vue"
    "pages/spots/[id].vue"
    "pages/ehime.vue"
    "pages/niigata.vue"
    "pages/kagoshima.vue"
    "pages/fukushima.vue"
    "pages/yamaguchi.vue"
    "pages/saitama.vue"
    "pages/hokkaido.vue"
    "pages/osaka.vue"
    "pages/category.vue"
    "pages/index.vue"
)

echo "🔄 Replacing UnsplashImage with PlacePhotoImage in ${#files[@]} files..."

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "📝 Processing: $file"
        
        # Replace UnsplashImage component with PlacePhotoImage
        sed -i '' 's/UnsplashImage/PlacePhotoImage/g' "$file"
        
        # Replace import statement
        sed -i '' 's/import UnsplashImage from/import PlacePhotoImage from/g' "$file"
        sed -i '' 's|~/components/UnsplashImage.vue|~/components/PlacePhotoImage.vue|g' "$file"
        
        # Replace spot-name prop with both spot-name and place-id props
        sed -i '' 's/:spot-name="spot\.name"/:spot-name="spot.name" :place-id="spot.place_id"/g' "$file"
        sed -i '' 's/:spot-name="currentSpot\.name"/:spot-name="currentSpot.name" :place-id="currentSpot.place_id"/g' "$file"
        sed -i '' 's/:spot-name="currentSpot\?.name"/:spot-name="currentSpot?.name" :place-id="currentSpot?.place_id"/g' "$file"
        
        echo "✅ Completed: $file"
    else
        echo "⚠️  File not found: $file"
    fi
done

echo "🎉 All files processed!"