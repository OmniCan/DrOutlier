/** @type {import('next').NextConfig} */
const nextConfig = {
    images: { unoptimized: true },
    webpack: (config, { isServer }) => {
        // Handle canvas and other Node.js-specific modules
        config.resolve.alias.canvas = false;
        config.resolve.alias.encoding = false;
        
        // Add externals for pdfjs-dist in server-side rendering
        if (isServer) {
            config.externals.push({
                'canvas': 'canvas',
                'pdfjs-dist': 'pdfjs-dist'
            });
        }
        
        return config;
    }
};

export default nextConfig;
