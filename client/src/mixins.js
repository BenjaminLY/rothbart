import {EventBus} from './EventBus'
import {CONFIGS} from './configs'
import {mapGetters} from 'vuex'
export default {
    data () {
        return {
            swiperOptions: {
                width: 170,
                centeredSlides: false
            }
        }
    },
    filters: {
        getDate (value) {
            if (!value) return
            return new Date(value).getDate()
        },
        initials (string) {
            var names = string.split(' ')
            var initials = names[0].substring(0, 1).toUpperCase()
            if (names.length > 1) {
                initials += names[names.length - 1].substring(0, 1).toUpperCase()
            }
            return initials
        }
    },
    computed: {
        ...mapGetters({
            movie: 'movie/get',
            rightAccess: 'rightAccess/get',
            localData: 'shareDatas/getLocalData',
            tokens: 'auth/getTokens',
            user: 'user/getUser',
            shareDatas: 'shareDatas/getData',
            share: 'shareDatas/getShare',
            plans: 'plans/get'
        }),
        buyPlanId () {
            let id = 0
            this.plans.forEach((item) => {
                if (item.type === 'download_stream') {
                    id = item.id
                }
            })
            return id
        },
        buyPrice () {
            let price = 0
            this.plans.forEach((item) => {
                if (item.type === 'download_stream') {
                    price = item.price
                }
            })
            return price
        },
        rentPlanId () {
            let id = 0
            this.plans.forEach((item) => {
                if (item.type === 'stream') {
                    id = item.id
                }
            })
            return id
        },
        rentPrice () {
            let price = 0
            this.plans.forEach((item) => {
                if (item.type === 'stream') {
                    price = item.price
                }
            })
            return price
        },
		userConnected () {
			return this.tokens.access_token !== null && this.tokens.refresh_token !== null
		},
        canDownload () {
            let can = false
            if (this.rightAccess !== null) {
                this.rightAccess.forEach((right) => {
                    if (right === 'download' || right === 'download_stream') {
                        can = true
                    }
                })
            }
            return can
        },
        canStream () {
            let can = false
            if (this.rightAccess !== null) {
                this.rightAccess.forEach((right) => {
                    if (right === 'stream' || right === 'download_stream') {
                        can = true
                    }
                })
            }
            return can
        }
    },
    watch: {
        tokens (tokens) {
            if (tokens.access_token !== null) {
                this.initAuth()
            } else {
                this.$http.defaults.headers.common['Authorization'] = null
                if (this.$route.name === 'VideoPlayer') {
                    this.$toast.open({
                        duration: 5000,
                        message: 'Veuillez vous connecter afin de visionner la vidéo.',
                        type: 'is-danger'
                    })
                    this.$store.dispatch('shareDatas/updateShare', {auth: true})
                    this.$router.push({name: 'LandingPage'})
                }
            }
        }
    },
    methods: {
        openSignin () {
            EventBus.$emit('open-signin')
        },
        closeSignin () {
            EventBus.$emit('close-signin')
        },
        launchVideo () {
            this.$http.get(CONFIGS.api.movie.info, {
                params: {
                    m: this.movie.movie,
                    r: 'stream'
                }
            }).then(
                (response) => {
                    if (response.data.reference !== 'KO') {
                        // save in shared datas the video informations
                        let movie = this.movie
                        movie.src = CONFIGS.api.movie.watch +
                        '?m=' + response.data.reference + '&t=movie'
                        this.$store.dispatch('movie/update', movie).then(
                            () => {
                                this.closeSignin()
                                this.$router.push({name: 'VideoPlayer'})
                            }
                        )
                    } else {
                        this.$router.push({name: 'SigninPage'})
                    }
                },
                (error) => {
                    switch (error.response.status) {
                    case 401:
                    this.refreshToken(() => { this.launchVideo() })
                    break
                    default:
                    if (this.$route.name !== 'SigninPage') {
                        this.$router.push({name: 'SigninPage'})
                    } else {
                        this.closeSignin()
                    }
                    break
                    }
                }
            )
        },
        downloadVideo () {
            this.$http.get(CONFIGS.api.movie.info, {
                params: {
                    m: this.movie.movie,
                    r: 'download'
                }
            }).then(
                (response) => {
                    if (response.data.reference !== 'KO') {
                        // launch download
                        window.location = CONFIGS.api.movie.download +
                        '?m=' + response.data.reference
                    } else {
                        this.$router.push({name: 'SigninPage'})
                    }
                },
                (error) => {
                    switch (error.response.status) {
                    case 401:
                    this.refreshToken(() => { this.launchVideo() })
                    break
                    default:
                    if (this.$route.name !== 'SigninPage') {
                        this.$router.push({name: 'SigninPage'})
                    } else {
                        this.closeSignin()
                    }
                    break
                    }
                }
            )
        },
        base64 (string, action) {
            var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

            if (action === 'encode') {
                return Base64.encode(string)
            } else if (action === 'decode') {
                return Base64.decode(string)
            }
        },
        logout () {
            this.$http.defaults.headers.common['Authorization'] = null
            this.$store.dispatch('auth/deleteTokens')
            this.$store.dispatch('user/updateUser', null)
        },
        initAuth () {
            if (this.tokens.access_token !== null) {
                this.$http.defaults.headers.common['Authorization'] = 'Bearer ' + this.tokens.access_token
            } else {
                this.$store.dispatch('auth/deleteTokens')
                this.$store.dispatch('user/updateUser', null)
                if (this.$route.name === 'VideoPlayer') {
                    this.$toast.open({
                        duration: 5000,
                        message: 'Veuillez vous connecter afin de visionner la vidéo.',
                        type: 'is-danger'
                    })
                    this.$store.dispatch('shareDatas/updateShare', {auth: true})
                    this.$router.push({name: 'LandingPage'})
                }
            }
        },
        refreshToken (callback) {
            // try to call a refresh token
            if (this.tokens.refresh_token !== null) {
                this.$http.post(CONFIGS.api.auth.refresh, {
                    refresh_token: this.tokens.refresh_token
                }).then(
                    (response) => {
                        this.$store.dispatch('auth/updateTokens', {
                            access_token: response.data.access_token,
                            refresh_token: response.data.refresh_token
                        }).then(
                            () => {
                                callback() // try again
                            }
                        )
                    },
                    (error) => {
                        switch (error.response.status) {
                        case 401:
                            this.$store.dispatch('auth/deleteTokens')
                            break
                        }
                    }
                )
            } else {
                this.$store.dispatch('auth/deleteTokens')
            }
        },
        isPast (datetime) {
            return this.$moment().diff(datetime, 'hours') > 0
        },
        objectSize (obj) {
            var size = 0, key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) size++;
            }
            return size;
        },
        getFormatedDate (datetime, format) {
            return this.$moment(datetime).format(format)
        },
        getDayNumber (date) {
            return this.$moment(date).day()
        },
        getDay (datetime) {
            return this.$moment(datetime).format('dddd')
        },
        getMonthDateYear (datetime) {
            return this.$moment(datetime).format('MMMM DD YYYY')
        },
        getHour (datetime) {
            return this.$moment(datetime).format('hh:mm a')
        },
        numericDate (datetime) {
            return this.$moment(datetime).format('MM.DD')
        },
        categoryName (category) {
            if (this.objectSize(category.collection) > 0) {
                return category.collection.name + ' / ' + category.name
            } else {
                return category.name
            }
        },
        convertToAge (value) {
            var year = this.$moment().diff(value, 'years')
            var month = this.$moment().diff(value, 'months')
            if (year > 0) {
                return year + ' y.o.'
            } else {
                return month + ' m.'
            }
        },
        amount (amount, currency) {
            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2
            });
            return formatter.format(amount)
        },
        closeModal () {
            this.displayModal = false
        },
        modalSettings (settings) {
            this.settingsModal = settings
        },
        isEmail (string) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            return re.test(String(string).toLowerCase())
        },
        initModal (settings) {
            if (settings) {
                this.modalSettings(settings)
            }
            this.displayModal = true
        },
        getScrollTop () {
            if(window.pageYOffset!= undefined){
                return [pageXOffset, pageYOffset];
            }
            else{
                var sx, sy, d= document, r= d.documentElement, b= d.body;
                sx= r.scrollLeft || b.scrollLeft || 0;
                sy= r.scrollTop || b.scrollTop || 0;
                return [sx, sy];
            }
        },
        openPaymentMention () {
            EventBus.$emit('open-payment-mention')
        },
        openLegalMention () {
            EventBus.$emit('open-legal-mention')
        }
    }
}
