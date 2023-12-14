# Wir-Lernen-Online Theme

## Build

### Styles
```sh
# Install dependencies
npm install
# Compile styles
npm run build
# Compile styles (watch)
npm start
```

### React Blocks

In folder `blocks`:
```sh
# Install dependencies
npm install
# Compile all blocks
npm run build
# Compile all blocks (watch)
npm start
```

## Deploy

- Compile styles and blocks (see above)

```sh
# Deploy on https://wordpress.staging.openeduhub.net/
npm run deploy-staging
# Deploy on https://wirlernenonline.de/
npm run deploy-prod
```