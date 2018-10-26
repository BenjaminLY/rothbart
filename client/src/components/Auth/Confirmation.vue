<template lang="pug">
div.form.confirmation(v-if="!loading")
    div.main-menu
        nav.navbar(role="navigation", aria-label="main navigation")
            div.container
                router-link.navbar-item.is-bold(:to="{name: 'LandingPage'}") {{movie.title}}
    div.container
        section.section
            div(v-if="success")
                h1.title Merci d'avoir validé votre inscription à notre site {{movie.title}}.
                br
                strong Pour continuer, connectez-vous dès maintenant.
                br
                div.has-text-centered
                    button.button.is-buy(@click="openSignin") Connectez-vous
            div(v-else)
                h1.has-text-danger Cette clef de validation n'existe pas.
</template>

<script>
import {CONFIGS} from '../../configs'
import mixins from '../../mixins'
export default {
    mixins: [mixins],
    data () {
        return {
            loading: true,
            success: false
        }
    },
    mounted () {
        this.confirmation()
    },
    methods: {
        confirmation () {
            this.loading = true
            this.$http.get(CONFIGS.api.auth.confirmation.replace(':token', this.$route.params.token)).then(
                (response) => {
                    this.success = true
                    this.loading = false
                },
                (error) => {
                    switch (error.response.status) {
                        case 404:
                        this.loading = false
                        break
                    }
                }
            )
        }
    }
}
</script>

<style lang="css">
</style>
