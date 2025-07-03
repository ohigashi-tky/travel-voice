# syntax=docker/dockerfile:1

# Base image
FROM node:18-alpine AS base

# ビルド引数を受け取る
ARG NUXT_OPENROUTER_API_KEY
ARG NUXT_OPENROUTER_MODEL
ARG NUXT_PUBLIC_GOOGLE_MAPS_API_KEY

# ビルド時の環境変数として設定
ENV NUXT_OPENROUTER_API_KEY=$NUXT_OPENROUTER_API_KEY
ENV NUXT_OPENROUTER_MODEL=$NUXT_OPENROUTER_MODEL
ENV NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=$NUXT_PUBLIC_GOOGLE_MAPS_API_KEY

WORKDIR /app
COPY package*.json ./
RUN rm -rf node_modules package-lock.json || true
RUN npm cache clean --force
RUN npm install --no-package-lock

# Dev stage
FROM base AS dev
ENV NODE_ENV=development
ENV HOST=0.0.0.0
ENV PORT=3000
COPY . .
EXPOSE 3000
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "3000"]

# Build stage
FROM base AS build
COPY . .
RUN npm run build

# Production stage
FROM base AS production
ENV NODE_ENV=production
COPY --from=build /app/.output ./.output
CMD ["node", ".output/server/index.mjs"]