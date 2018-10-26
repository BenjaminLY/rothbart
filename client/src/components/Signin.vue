<template lang="pug">
div.form
    div.main-menu
        nav.navbar(role="navigation", aria-label="main navigation")
            div.container
                router-link.navbar-item.is-bold(:to="{name: 'LandingPage'}") {{movie.title}}
    div.scrollable
        div.container
            section.section
                div.columns
                    div.column.is-3
                        figure.image.is-hidden-mobile
                            img(:src="movie.cover")
                    div.column.is-9
                        form(@submit.prevent="signup")
                            div.step
                                span.badge 1
                                strong.label-step Confirmez votre choix
                                br
                                b-field(:type="errors.signup.type.planId", :message="errors.signup.message.planId")
                                    div.columns.signin
                                        div.column.is-6.has-text-centered
                                            button.button.is-buy(type="button", :class="{'is-active': selectedPlan === 'buy'}", @click="selectPlan('buy')")
                                                div.columns
                                                    div.column.is-2
                                                        span Acheter
                                                    div.column.is-8.price
                                                        span {{buyPrice}} &euro;
                                                    div.column.is-2
                                                        b-icon(icon="download", pack="fas")
                                            div Téléchargez le film complet
                                        div.column.is-6.has-text-centered
                                            button.button.is-rent(type="button", :class="{'is-active': selectedPlan === 'rent'}", @click="selectPlan('rent')")
                                                div.columns
                                                    div.column.is-2
                                                        span Louer
                                                    div.column.is-8.price
                                                        span {{rentPrice}} &euro;
                                                    div.column.is-2
                                                        b-icon(icon="play", pack="fas")
                                            div Visionnez en stream pendant 7 jours
                            div.step(v-if="!userConnected")
                                span.badge 2
                                strong.label-step Créez votre compte
                                small.is-clearfix Anonyme, seul un mail vous est demandé. Celui-ci ne sera jamais transmis à des partenaires.
                                div.columns
                                    div.column
                                        b-field(label="Adresse email", :type="errors.signup.type.email", :message="errors.signup.message.email")
                                            b-input(type="email", placeholder="Votre adresse email", v-model="planForm.email", :disabled="loading", required)
                                    div.column
                                        b-field(label="Mot de passe", :type="errors.signup.type.password", :message="errors.signup.message.password")
                                            b-input(type="password", placeholder="Votre mot de passe", v-model="planForm.password", :disabled="loading", required)
                                span Vous avez déjà un compte ?&nbsp;
                                span.signin-button(@click="openSignin") Connectez-vous
                            div.step
                                span.badge(v-if="!userConnected") 3
                                span.badge(v-else) 2
                                strong.label-step Effectuez votre réglement
                                br
                                div.columns.valid
                                    div.column.has-text-centered
                                        small En cliquant sur <strong>Régler votre commande</strong>, je confirme avoir plus de 18 ans.
                                        b-field
                                            button.button.is-buy(:disabled="loading") Régler votre commande
                                p.disclaimer Pour toute question ou problème spécifique lié aux paiements traités par VEROTEL, veuillez envoyer un e-mail à l'équipe d'assistance à l'adresse suivante: <a href="mailto:livesupport@verotel.com">livesupport@verotel.com</a> ou visitez le site d'assistance aux utilisateurs finaux <a href="https://vtsup.com" target="_BLANK">vtsup.com</a>
                            div.columns.top
                                div.column.is-4.has-text-centered
                                    b-icon(icon="user", pack="fas")
                                    span Anonyme et discret
                                div.column.is-4.has-text-centered
                                    b-icon(icon="lock", pack="fas")
                                    span Paiement sécurisé
                                div.column.is-4.has-text-centered
                                    b-icon(icon="thumbs-up", pack="fas")
                                    span Sans engagement
    footer
        div.columns
            div.column.is-4
            div.column.is-4
                div.content.has-text-centered.is-bold
                    span Ridleyprod 2018
                    a.separator(href="mailto:contact@mafioso-lefilm.com")
                        span Contact
                    span.separator.is-clickable(@click="openPaymentMention") Paiement
                    span.separator.is-clickable(@click="openLegalMention") Mentions légales
            div.column.is-4
                div.content.has-text-right.login.is-bold
                    span.is-clickable(v-if="!userConnected", @click="openSignin") Se connecter
                    span.is-clickable(v-if="userConnected", @click="logout") Se déconnecter

</template>

<script>
import MainMenu from './MainMenu'
import {CONFIGS} from '../configs'
import mixins from '../mixins'
export default {
    mixins: [mixins],
    components: {
        MainMenu
    },
    data () {
        return {
            error: null,
            loading: false,
            selectedPlan: null,
            planForm: {
                email: null,
                password: null,
                planId: 0,
                movieId: 0
            },
            errors: {
                signup: {
                    type: {
                        email: null,
                        password: null,
                        planId: null,
                        movieId: null
                    },
                    message: {
                        email: null,
                        password: null,
                        planId: null,
                        movieId: null
                    }
                }
            }
        }
    },
    mounted () {
        this.planForm.movieId = this.movie.id

        if (this.share !== null) {
            this.selectedPlan = this.share
            if (this.selectedPlan === 'buy') {
                this.planForm.planId = this.buyPlanId
            } else if (this.selectedPlan === 'rent') {
                this.planForm.planId = this.rentPlanId
            }
            this.$store.dispatch('shareDatas/updateShare', null)
        }
    },
    methods: {
        selectPlan (type) {
            this.selectedPlan = type
            if (type === 'buy') {
                this.planForm.planId = this.buyPlanId
            } else if (type === 'rent') {
                this.planForm.planId = this.rentPlanId
            }
        },
        signin () {
            this.error = null
            this.loading = true
            this.errors.signin.type = {
                email: null,
                password: null
            }
            this.$http.post(CONFIGS.api.auth.login, this.form).then(
                (response) => {
                    // store user auth informations (access_token & refresh_token)
                    this.$store.dispatch('auth/updateTokens', {
                        access_token: response.data.access_token,
                        refresh_token: response.data.refresh_token
                    }).then(
                        () => {
                            this.closeSignin()
                            this.launchVideo()
                        }
                    )
                },
                (error) => {
                    this.errors.signin.type = {
                        username: 'is-danger',
                        password: 'is-danger'
                    }
                    this.error = 'Adresse email ou mot de passe incorrect.'
                    this.loading = false
                }
            )
        },
        signup () {
            this.loading = true
            this.$http.post(CONFIGS.api.payment.url, this.planForm).then(
                (response) => {
                    if (response.data.token_datas !== null) {
                        // store user auth informations (access_token & refresh_token)
                        this.$store.dispatch('auth/updateTokens', {
                            access_token: response.data.token_datas.access_token,
                            refresh_token: response.data.token_datas.refresh_token
                        }).then(
                            () => {
                                if (response.data.payment_url !== null) {
                                    window.location = response.data.payment_url
                                } else {
                                    this.$toast.open({
                                        duration: 5000,
                                        message: 'Le paiement a déjà été effectué.',
                                        type: 'is-success'
                                    })
                                    this.$router.push({name: 'LandingPage'})
                                }
                            }
                        )
                    } else {
                        window.location = response.data.payment_url
                    }
                },
                (error) => {
                    switch (error.response.status) {
                    case 401:
                        this.errors.signup.type.password = this.errors.signup.type.email = 'is-danger'
                        this.errors.signup.message.email = 'Email ou mot de passe incorrect'
                        this.loading = false
                    break
                    case 422:
                        let message = []
                        let type = []
                        for (var key in error.response.data) {
                            message[key] = error.response.data[key][0]
                            type[key] = 'is-danger'
                        }
                        this.errors.signup = {
                            message: message,
                            type: type
                        }
                        this.loading = false
                    break
                    }
                }
            )
        }
    }
}
</script>

<style lang="scss">
.big-form {
    margin-top: 15px;
}
</style>
