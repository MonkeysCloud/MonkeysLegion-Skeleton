###############################################################################
# MonkeysLegion v2 — Node.js / Vite Development Image
###############################################################################

FROM node:22-bookworm-slim

# ── Working directory ────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── Copy package files first for better layer caching ─────────────────────────
COPY package.json package-lock.json* ./

# ── Install dependencies ─────────────────────────────────────────────────────
RUN npm install --no-audit --no-fund

# ── Copy source (rest of the project) ────────────────────────────────────────
COPY . .

# ── Default: run Vite dev server with HMR ────────────────────────────────────
# For production builds, override CMD: docker compose run --rm node-build
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
