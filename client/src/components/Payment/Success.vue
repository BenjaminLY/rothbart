<template lang="pug">

</template>

<script>
import {CONFIGS} from '../../configs'
import mixins from '../../mixins'
export default {
    mixins: [mixins],
    mounted () {
        this.check()
    },
    methods: {
        check () {
            this.$http.get(CONFIGS.api.payment.check, {
                params: this.$route.query
            }).then(
                (response) => {
                    this.$toast.open({
                        duration: 5000,
                        message: 'Votre paiement a été accepté.',
                        type: 'is-success'
                    })
                    this.$router.push({name: 'LandingPage'})
                },
                () => {
                    this.$toast.open({
                        duration: 5000,
                        message: 'Votre paiement n\'a pas été effectué, veuillez réessayer.',
                        type: 'is-danger'
                    })
                    this.$router.push({name: 'LandingPage'})
                }
            )
        }
    }
}
</script>

<style lang="css">
</style>
