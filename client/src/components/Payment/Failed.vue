<template lang="pug">

</template>

<script>
export default {
    mounted () {
        this.$toast.open({
            duration: 5000,
            message: 'Votre paiement n\'a pas été effectué, veuillez réessayer.',
            type: 'is-danger'
        })
        this.$router.push({name: 'LandingPage'})
    }
}
</script>

<style lang="css">
</style>
