import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    retries: process.env.CI ? 2 : 0,
    reporter: process.env.CI ? [['github'], ['html', { open: 'never' }]] : 'list',
    use: {
        baseURL: process.env.E2E_BASE_URL ?? 'http://localhost:8003',
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
        ignoreHTTPSErrors: true,
    },
    projects: [
        {
            name: 'chromium',
            use: {
                ...devices['Desktop Chrome'],
                screen: { width: 1920, height: 1080 },
                viewport: { width: 1536, height: 864 },
            },
        },
        {
            name: 'mobile-chromium',
            use: {
                ...devices['Pixel 7'],
                screen: { width: 390, height: 844 },
                viewport: { width: 390, height: 844 },
            },
        },
    ],
});
