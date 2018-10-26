let baseUrl = ''
if (window.location.hostname === 'localhost') {
    baseUrl = 'http://api.fred.test/'
} else {
    baseUrl = 'https://api.mafioso-lefilm.com/'
}
baseUrl = 'https://api.mafioso-lefilm.com/'

export const CONFIGS = {
    api: {
        auth: {
            confirmation: baseUrl + 'auth/confirmation/:token',
            me: baseUrl + 'me',
            login: baseUrl + 'auth/token',
            refresh: baseUrl + 'auth/refresh',
        },
        plan: {
            index: baseUrl + 'plans'
        },
        payment: {
            url: baseUrl + 'payments/url',
            check: baseUrl + 'payments/check'
        },
        movie: {
            rightAccess: baseUrl + 'movies/right-access/:reference',
            getInfo: baseUrl + 'movies/watch',
            info: baseUrl + 'movies/infos',
            watch: baseUrl + 'videos/watch',
            download: baseUrl + 'videos/download'
        }
    }
}
