# syntax=docker/dockerfile:1

# ビルド引数を受け取る
ARG NUXT_OPENROUTER_API_KEY
ARG NUXT_OPENROUTER_MODEL

# ビルド時の環境変数として設定
ENV NUXT_OPENROUTER_API_KEY=$NUXT_OPENROUTER_API_KEY
ENV NUXT_OPENROUTER_MODEL=$NUXT_OPENROUTER_MODEL

# Base image
FROM node:18-alpine AS base
WORKDIR /app
COPY package*.json ./
RUN npm install

# Dev stage
FROM base AS dev
ENV NODE_ENV=development
COPY . .
CMD ["npm", "run", "dev"]

# Build stage
FROM base AS build
COPY . .
RUN npm run build

# Production stage
FROM base AS production
ENV NODE_ENV=production
COPY --from=build /app/.output ./.output
CMD ["node", ".output/server/index.mjs"]