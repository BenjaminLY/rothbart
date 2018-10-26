<template lang="pug">
div.signin
    form(@submit.prevent="signin")
        b-field(label="Adresse email", :type="errors.signin.type.username", :message="errors.signin.message.username")
            b-input(type="email", placeholder="Votre adresse email", v-model="form.username", :disabled="loading", required)
        b-field(label="Mot de passe", :type="errors.signin.type.password", :message="errors.signin.message.password")
            b-input(type="password", placeholder="Votre mot de passe", v-model="form.password", :disabled="loading", required)
        b-field
            button.button.is-danger(:disabled="loading") Se connecter
        b-field(v-if="error")
            p.has-text-danger {{error}}
</template>

<script>
import {CONFIGS} from '../../configs'
import mixins from '../../mixins'
export default {
    mixins: [mixins],
    data () {
        return {
            error: null,
            loading: false,
            form: {
                email: null,
                password: null
            },
            errors: {
                signin: {
                    type: {
                        email: null,
                        password: null
                    },
                    message: {
                        email: null,
                        password: null
                    }
                }
            }
        }
    },
    methods: {
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
                            if (this.$route.name !== 'LandingPage') {
                                this.$router.push({name: 'LandingPage'})
                            }
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
        }
    }
}
</script>

<style lang="css">
</style>
