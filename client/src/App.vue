<template lang="pug">
div.main
    disclaimer(v-if="!isAdult")
    router-view(v-if="!loading")
    b-modal(:active.sync="auth")
        signin
    b-modal(:active.sync="paymentMention")
        payment-mention
    b-modal(:active.sync="legalMention")
        legal-mention
</template>

<script>
import Disclaimer from './components/Disclaimer'
import Signin from './components/Auth/Signin'
import PaymentMention from './components/PaymentMention'
import LegalMention from './components/LegalMention'
import {EventBus} from './EventBus'
import {CONFIGS} from './configs'
import mixins from './mixins'
export default {
    mixins: [mixins],
    components: {
        Disclaimer,
        PaymentMention,
        LegalMention,
        Signin
    },
    data () {
        return {
            paymentMention: false,
            legalMention: false,
            isAdult: false,
            loading: true,
            auth: false
        }
    },
    mounted () {
        let show = window.sessionStorage.getItem('disclaimer')
        this.isAdult = show === 'OK'

        this.getMovie()
        EventBus.$on('isAdult', () => {
            window.sessionStorage.setItem('disclaimer', 'OK')
            this.isAdult = true
        })
        EventBus.$on('open-payment-mention', () => {
            this.paymentMention = true
        })
        EventBus.$on('open-legal-mention', () => {
            this.legalMention = true
        })
        EventBus.$on('open-signin', () => {
            this.auth = true
        })
        EventBus.$on('close-signin', () => {
            this.auth = false
        })
    },
    methods: {
        me () {
            this.loading = true
            this.$http.get(CONFIGS.api.auth.me).then(
                (response) => {
                    this.loading = false
                },
                (error) => {
                    switch (error.response.status) {
                    case 401:
                    this.$http.post(CONFIGS.api.auth.refresh, {
                        refresh_token: this.tokens.refresh_token
                    }).then(
                        (response) => {
                            this.$store.dispatch('auth/updateTokens', {
                                access_token: response.data.access_token,
                                refresh_token: response.data.refresh_token
                            }).then(
                                () => {
                                    this.me() // try again
                                }
                            )
                        },
                        (error) => {
                            switch (error.response.status) {
                            case 401:
                            this.$store.dispatch('auth/deleteTokens')
                            this.$store.dispatch('user/updateUser', null)
                            this.loading = false
                            break
                            }
                        }
                    )
                    break
                    }
                }
            )
        },
        getMovie () {
            this.loading = true
            this.$http.get(CONFIGS.api.movie.getInfo, {
                params: {
                    d: window.location.hostname
                }
            }).then(
                (response) => {
                    document.title = response.data.title
                    let movie = {
                        id: response.data.id,
                        title: response.data.title,
                        subtitle: response.data.description,
                        cover: response.data.cover,
                        disclaimer: response.data.disclaimer,
                        trailer: this.getReference(response.data.videos, 'trailer'),
                        movie: this.getReference(response.data.videos, 'movie'),
                        src: null
                    }
                    this.$store.dispatch('movie/update', movie).then(
                        () => {
                            this.getPlans()
                        }
                    )
                },
                (error) => {
                    this.loading = false
                }
            )
        },
        getPlans () {
            this.loading = true
            this.$http.get(CONFIGS.api.plan.index, {params: {full: true}}).then(
                (response) => {
                    this.$store.dispatch('plans/update', response.data).then(
                        () => {
                            this.me()
                        }
                    )
                },
                (error) => {
                    this.loading = false
                }
            )
        },
        getReference (videos, type) {
            let reference
            videos.forEach((item) => {
                if (item.type === type) {
                    reference = item.reference
                }
            })
            return reference
        }
    }
}
</script>

<style lang="css">
</style>
